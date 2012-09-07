<?php 
/*
 * Created on 2009 Jan 02
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

/**
 * The representation of the tag which is passed to the tag functions.
 * 
 * @package		FTL_Parser
 * @author		Martin Wernstahl <m4rw3r@gmail.com>
 * @modified	Ionize team
 * @copyright	Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class FTL_Binding
{
	/**
	 * The associated context.
	 * 
	 * @var FTL_Context
	 */
	protected $context;
	
	/**
	 * The local variables.
	 * 
	 * @var object
	 */
	public $locals;
	
	/**
	 * The name of this tag.
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Attributes passed to this tag.
	 * 
	 * @var array
	 */
	public $attr;
	
	/**
	 * Global variables.
	 * 
	 * @var object
	 */
	public $globals;
	
	/**
	 * The block containing children for this tag.
	 * 
	 * @var array|string
	 */
	public $block;


	protected $parent_name = NULL;

	protected $parents = array();


	/**
	 * Constructor
	 * 
	 * @param  FTL_Context	 The context this tag binding is attached to
	 * @param  FTL_VarStack	 The local vars
	 * @param  string		 The tag name
	 * @param  array 		 The tag attributes (name => value)
	 * @param  array 		 The nested block
	 *
	 */
	function __construct($context, $locals, $name, $attr, $block)
	{
		list($this->context, $this->locals, $this->name, $this->attr, $this->block) = array($context, $locals, $name, $attr, $block);
		$this->globals = $context->globals;
	}
	
	/**
	 * Returns the value of the containing data.
	 *
	 * Evaluates all tags inside the block (if any), and then returns the result.
	 * 
	 * @return string
	 *
	 */
	public function expand()
	{
		return $this->context->parser->compile($this->block);
	}
	
	/**
	 * Returns true if the current tag is a single tag (ends with "/>").
	 * 
	 * @return bool
	 *
	 */
	public function is_single()
	{
		return $this->block == NULL;
	}
	
	/**
	 * Returns true if the current tag is a block.
	 * 
	 * @return bool
	 *
	 */
	public function is_double()
	{
		return ! $this->is_single();
	}
	
	/**
	 * Returns the current nesting.
	 * 
	 * Returns it like this: "parent:child:grandchild", including the current tag.
	 * 
	 * @return string
	 *
	 */
	public function nesting()
	{
		return $this->context->current_nesting();
	}
	
	/**
	 * Fires a tag missing error for the current tag.
	 * 
	 * @return string
	 *
	 */
	public function missing()
	{
		return $this->context->tag_missing($this->name, $this->attr, $this->block);
	}

	/**
	 * Return all the attributes of the tag
	 *
	 * @return array
	 *
	 */
	public function getAttributes()
	{
		return $this->attr;
	}

	public function getStack()
	{
		return $this->context->get_binding_stack();
	}

	/**
	 * Returns one attribute value
	 * returns NULL if the attribute isn't set.
	 *
	 * @param	String		key
	 * @param	mixed		Value to return if the attribute is not set. NULL by default.
	 * @return	mixed		NULL is the attribute isn't set, TRUE if the attribute is 'true', FALSE if the attribute is 'false'
	 *
	 */
	public function getAttribute($attr, $return_if_null = NULL)
	{
		if ( ! isset($this->attr[$attr]))
			return $return_if_null;
		
		if (isset($this->attr[$attr]) && strtolower($this->attr[$attr]) == 'true')
			return TRUE;
		
		return (isset($this->attr[$attr]) && strtolower($this->attr[$attr]) != 'false') ? $this->attr[$attr] : FALSE;
	}

	/**
	 * Set one tag attribute
	 *
	 * @param	String		key
	 * @param	mixed		value
	 *
	 * @return FTL_Binding	Current tag
	 *
	 */
	public function setAttribute($key, $value)
	{
		$this->attr[$key] = $value;

		return $this;
	}


	/**
	 * Return the current FTL_Binding parent
	 * If no name is given, return the very first parent.
	 *
	 * @param string/null
	 *
	 * @return FTL_Binding
	 *
	 */
	public function getParent($parent_name = NULL)
	{
		$stack = $this->getStack();

		if (is_null($parent_name))
		{
			$parent_name = $this->getParentName();
			if ( ! isset($this->parents[$parent_name]))
			{
				array_pop($stack);
				$this->parents[$parent_name] = end($stack);
			}
		}
		else
		{
			if ( ! isset($this->parents[$parent_name]))
			{
				foreach($stack as $binding)
				{
					if ($binding->name == $parent_name)
					{
						$this->parents[$parent_name] = $binding;
						break;
					}
				}
				if (!isset($this->parents[$parent_name]))
					$this->parents[$parent_name] = NULL;
			}
		}
		return $this->parents[$parent_name];
	}


	public function getParentName()
	{
		if (is_null($this->parent_name))
		{
			$parents = array_reverse(explode(':', $this->nesting()));
			array_shift($parents);
			$this->parent_name = array_shift($parents);
		}
		return $this->parent_name;
	}

	/**
	 * Return the expected value from the data array of the tag.
	 * The data array has the same name than the tag's parent tag
	 * The key, if not set, it supposed to be the current tag name.
	 *
	 * Example :
	 * 		<t:user:name />
	 *
	 * The callback function of the tag "user" is supposed to set one data
	 * array called "user" to the tag :
	 * $tag->set('user', array('name'=>'Josh', 'group'=>'admin');
	 *
	 * the callback function of the tag "name" returns the name value like this :
	 * return $tag->getValue();
	 *
	 *
	 * @param null
	 * @param null
	 *
	 * @return null
	 */
	public function getValue($key = NULL, $data_array_name = NULL)
	{
		if (is_null($key))
			$key = $this->name;

		if (is_null($data_array_name))
			$data_array_name = $this->getParentName();

		$data_array = $this->get($data_array_name);

		if (isset($data_array[$key]))
			return $data_array[$key];

		return NULL;
	}

	public function setValue($value)
	{

	}

	/**
	 * Returns one local var
	 *
	 * @param	String		Local tag var name
	 *
	 * @return	mixed		Local tag var value
	 *
	 */
	public function get($key)
	{
		return $this->locals->{$key};
	}
	
	/**
	 * Set one local var
	 *
	 * @param	String			key
	 * @param	String			value
	 *
	 * @return	FTL_Binding		The current tag
	 *
	 */
	public function set($key, $value)
	{
		$this->locals->{$key} = $value;

		return $this;
	}
	
	/**
	 * Renders another tag.
	 * 
	 * @param  string		The tag name
	 * @param  array 		The arguments passed
	 * @param  array|string	The block data
	 *
	 * @return string
	 *
	 */
	public function render($tag, $args = array(), $block = NULL)
	{
		return $this->context->render_tag($tag, $args, $block);
	}

	/**
	 * @param mixed	$data
	 *
	 */
	public function set_context_data($data)
	{
		$this->context->set_data($data);
	}

	/**
	 * Parses a template fragment as a nested block.
	 * 
	 * @param	string
	 * @param	boolean		Has the template PHP data
	 * @return	string
	 *
	 */
	public function parse_as_nested($string, $php_data = FALSE)
	{
		// unset the current parser, so we won't interfere and maybe replace it
		$tmp = $this->context->parser;
		unset($this->context->parser);
		
		$parser = new FTL_Parser(array('context' => $this->context, 'tag_prefix' => $tmp->tag_prefix, 'php_data' => $php_data));
		
		$str = $parser->parse($string, $php_data);
		
		// reset
		$this->context->parser = $tmp;
		
		return $str;
	}
}


/* End of file binding.php */
/* Location: /application/libraries/ftl/binding.php */
