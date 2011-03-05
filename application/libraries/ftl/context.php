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
	 * @param  string    The name of the tags (nestings are separated with ":")
	 * @param  callable  The function/method to be called
	 * @return void
	 */
	public function define_tag($name, $callable)
	{
		$this->definitions[$name] = $callable;
		
		if(strpos($name, ':') === false)
		{
			// No nesting, no need for a reverse mapping tree
			return;
		}
		
		// Create reverse mapping tree, for tags with more than one segment
		$l = explode(':', $name);
		
		$c = count($l);
		
		// # key is a tag name
		
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
		
		// TODO: To support more segments, add more rows like this
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Renders the tags with the name and args supplied.
	 * 
	 * @param  string  The tag name
	 * @param  array   The args
	 * @param  array   The nested block
	 */
	public function render_tag($name, $args = array(), $block = null)
	{
		// do we have a compund tag?
		if(($pos = strpos($name, ':')) != 0)
		{
			// split them and parse them separately, as if they are nested
			$name1 = substr($name, 0, $pos);
			$name2 = substr($name, $pos + 1);

			return $this->render_tag($name1, array(), array(
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
	 * @return string
	 */
	protected function stack($name, $args, $block, $call)
	{
		// get previous locals, to let the data "stack"
		$previous = end($this->tag_binding_stack);
		$previous_locals = $previous == null ? $this->globals : $previous->locals;
		
		// create the stack and binding
		$locals = new FTL_VarStack($previous_locals);
		$binding = new FTL_Binding($this, $locals, $name, $args, $block);
		
		$this->tag_binding_stack[$name] = $binding;
		
		// Check if we have a function or a method
		if(is_callable($call))
		{
			$result = call_user_func($call, $binding);
		}
		else
		{
			show_error('Error in definition of tag "'.$name.'", the associated callable cannot be called.');
		}
		
		// jump out
		array_pop($this->tag_binding_stack);
		
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Makes a qualified guess of the tag definition requested depending on the current nesting.
	 * 
	 * @param  string  The name of the tag
	 * @return string
	 */
	function qualified_tag_name($name)
	{
		// Get the path array
		$path_chunks = array_merge(array_keys($this->tag_binding_stack), array($name));
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
	 * @return string Or abort if needed (default)
	 */
	public function tag_missing($name, $args = array(), $block = null)
	{
		show_error('Tag missing: "'.$name.'", scope: "'.$this->current_nesting().'".');
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
		return implode(':', array_keys($this->tag_binding_stack));
	}
}

/* End of file context.php */
/* Location: ./application/libraries/xt_parser/context.php */