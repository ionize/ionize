<?php 
/*
 * Created on 2009 Jan 02
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

/**
 * A context which renders the tags for the parser.
 * 
 * @package FTL_Parser
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @copyright Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class FTL_Context
{	
	/**
	 * Contains tag definitions.
	 * 
	 * @var array
	 */
	public $definitions = array();
	
	/**
	 * Reverse tag finder tree.
	 * 
	 * @var array
	 */
	public $tree = array();
	
	/**
	 * The global data.
	 * 
	 * @var FTL_VarStack
	 */
	public $globals;
	
	/**
	 * A stack of the tag bindings.
	 * 
	 * @var FTL_Binding
	 */
	protected $tag_binding_stack = array();
	
	/**
	 * A stack of tag names.
	 * 
	 * @var array(string)
	 */
	protected $tag_name_stack = array();

	/**
	 * The just rendered tag name
	 * Stored by render_tag() for memory
	 *
	 * @var string
	 */
	// protected $rendered_tags = array();


	// --------------------------------------------------------------------


	/**
	 * Init.
	 * 
	 * Creates a var_stack.
	 */
	function __construct()
	{
		$this->globals = new FTL_VarStack();
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Defines a tag.
	 * 
	 * @param  string	Name of the tags (nestings are separated with ":")
	 * @param  String	Method to be called
	 * @return void
	 */
	public function define_tag($name, $callable)
	{
		$this->definitions[$name] = $callable;
		
		if(strpos($name, ':') === FALSE)
		{
			// No nesting, no need for a reverse mapping tree
			return;
		}
		
		// Create reverse mapping tree, for tags with more than one segment
		$l = explode(':', $name);
		
		$c = count($l);

		// key is a tag name
		
		// Fast and nice (currently only up to 5 segment tags):
		if($c == 2)
		{
			$this->tree[$l[1]][$l[0]]['#'] = $name;
		}
		elseif($c == 3)
		{
			$this->tree[$l[2]][$l[1]][$l[0]]['#'] = $name;
		}
		elseif($c == 4)
		{
			$this->tree[$l[3]][$l[2]][$l[1]][$l[0]]['#'] = $name;
		}
		elseif($c == 5)
		{
			$this->tree[$l[4]][$l[3]][$l[2]][$l[1]][$l[0]]['#'] = $name;
		}
		
		elseif($c == 6)
		{
			$this->tree[$l[5]][$l[4]][$l[3]][$l[2]][$l[1]][$l[0]]['#'] = $name;
		}

		// TODO: To support more segments, add more rows like this
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Renders the tags with the name and args supplied.
	 * 
	 * @param  string  The tag name
	 * @param  array   The args
	 * @param  array/null   The nested block
	 *
	 * @return mixed
	 *
	 */
	public function render_tag($name, $args = array(), $block = NULL)
	{
		// do we have a compund tag?
		if(($pos = strpos($name, ':')) != 0)
		{
			// split them and parse them separately, as if they are nested
			$name1 = substr($name, 0, $pos);
			$name2 = substr($name, $pos + 1);

//			return $this->render_tag($name1, array(), array(
			return $this->render_tag($name1, $args, array(
					'name' => $name2,
					'args' => $args,
					'content' => $block
				));
		}
		else
		{
			$qname = $this->qualified_tag_name($name);

			if(is_string($qname) && array_key_exists($qname, $this->definitions))
			{
				// $this->rendered_tags[] = $qname;

				// render
				return $this->stack($name, $args, $block, $this->definitions[$qname]);
			}
			else
			{
				return $this->tag_missing($name, $args, $block);
			}
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Traverses the stack and handles the bindings and var_stack(s).
	 * 
	 * @param  string	The tag name
	 * @param  array	The tag args
	 * @param  array 	The nested block
	 * @param  callable	The function/method to call
	 *
	 * @return string
	 */
	protected function stack($name, $args, $block, $call)
	{
		// get previous locals, to let the data "stack"
		$previous = end($this->tag_binding_stack);
		$previous_locals = $previous == NULL ? $this->globals : $previous->locals;

		// create the stack and binding
		$locals = new FTL_VarStack($previous_locals);
		$binding = new FTL_Binding($this, $locals, $name, $args, $block);
		$this->tag_binding_stack[] = $binding;
		$this->tag_name_stack[]    = $name;
		
		// Check if we have a function or a method
		if(is_callable($call))
		{
			$result = call_user_func($call, $binding);
		}
		else
		{
			return $this->show_error(
				'Error in definition of tag "'.$name.'"',
				'the associated <b>static function</b> "'.$call.'" cannot be called.'
			);
		}
		
		// jump out
		array_pop($this->tag_binding_stack);
		array_pop($this->tag_name_stack);
		
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Makes a qualified guess of the tag definition requested depending on the current nesting.
	 * 
	 * @param  string  The name of the tag
	 *
	 * @return string
	 */
	function qualified_tag_name($name)
	{
		// Get the path array
		$path_chunks = array_merge($this->tag_name_stack, array($name));
		// For literal matches
		$path = implode(':', $path_chunks);

		// Check if we have a tag or a variable
		if( ! isset($this->definitions[$path]) && ! isset($this->globals->hash[$name]))
		{
			// Reverse the chunks, we're starting with the most precise name
			$path_chunks = array_reverse($path_chunks);
			
			// Set default tag, which is the last one
			$last = current($path_chunks);
			
			// Do we have a tag which ends at the correct name?
			if( ! isset($this->tree[$last]))
			{
				// Nope
				return $last;
			}
			
			// Start
			$c =& $this->tree;

			// Go through the whole name
			while( ! empty($path_chunks))
			{
				// Get next
				$e = array_shift($path_chunks);
				
				// Do we have a matching segment?
				if(isset($c[$e]))
				{
					// Yes
					
					// Do we have a tag here?
					if(isset($c[$e]['#']))
					{
						// Yes
						$last = $c[$e]['#'];
					}
					
					// Move deeper, to make sure that we don't have any more specific tags
					$c =& $c[$e];
				}
			}
			
			return $last;
		}
		else
		{
			return $path;
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Raises a tag missing error.
	 * 
	 * @param  string The tag name
	 * @param  array  The tag parameters
	 * @param  array  The nested block
	 *
	 * @return string Or abort if needed (default)
	 */
	public function tag_missing($name, $args = array(), $block = NULL)
	{
		// Config item from CI
		$log_threshold = config_item('log_threshold');

		if ($log_threshold)
		{
			$title = 'Tag missing';
			$message = '<b>'.$name.'</b>, scope: <b>'.$this->current_nesting().'</b>';
			return $this->show_error($title, $message);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the state of the current render stack.
	 * 
	 * Useful from inside a tag definition. Normally just use XT_Binding::nesting().
	 * 
	 * @return string
	 */
	function current_nesting()
	{
		return implode(':', $this->tag_name_stack);
	}

	// --------------------------------------------------------------------

	/**
	 * Set one data as global.
	 * The tag corresponding to the key will be usable from every context
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return FTL_Context
	 */
	function set_global($key, $value)
	{
		$this->globals->{$key} = $value;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Return one global key value
	 *
	 * @param $key
	 *
	 * @return mixed
	 *
	 */
	function get_global($key)
	{
		return $this->globals->{$key};
	}

	// --------------------------------------------------------------------

	function show_error($title, $message)
	{
		return '<p><span style="color:#b00;font-weight:bold;">' . $title . ':</span> ' . $message . '.</p>';
	}
}

/* End of file context.php */
/* Location: ./application/libraries/ftl/context.php */