<?php 
/*
 * Created on 2009 Jan 02
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

/**
 * The representation of the tag which is passed to the tag functions.
 * 
 * @package FTL_Parser
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @copyright Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
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
	
	/**
	 * Init.
	 * 
	 * @param  FTL_Context	 The context this tag binding is attached to
	 * @param  FTL_VarStack	 The local vars
	 * @param  string		 The tag name
	 * @param  array 		 The tag attributes (name => value)
	 * @param  array 		 The nested block
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
	 */
	public function expand()
	{
		return $this->context->parser->compile($this->block);
	}
	
	/**
	 * Returns true if the current tag is a single tag (ends with "/>").
	 * 
	 * @return bool
	 */
	public function is_single()
	{
		return $this->block == null;
	}
	
	/**
	 * Returns true if the current tag is a block.
	 * 
	 * @return bool
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
	 */
	public function nesting()
	{
		return $this->context->current_nesting();
	}
	
	/**
	 * Fires a tag missing error for the current tag.
	 * 
	 * @return string
	 */
	public function missing()
	{
		return $this->context->tag_missing($this->name, $this->attr, $this->block);
	}
	
	
	/**
	 * Returns one attribute or FALSE if not found
	 *
	 */
	public function getAttribute($attr)
	{
		if (isset($this->attr[$attr]) && strtolower($this->attr[$attr]) == 'true')
			return TRUE;
		
		return (isset($this->attr[$attr]) && strtolower($this->attr[$attr]) != 'false') ? $this->attr[$attr] : FALSE;
	}
	
	
	/**
	 * Returns one local var
	 *
	 */
	public function get($local)
	{
		return $this->locals->{$local};
	}
	
	
	/**
	 * Set one local var
	 *
	 */
	public function set($key, $value)
	{
		$this->locals->{$key} = $value;
	}
	
	
	
	
	/**
	 * Renders another tag.
	 * 
	 * @param  string		The tag name
	 * @param  array 		The arguments passed
	 * @param  array|string	The block data
	 * @return string
	 */
	public function render($tag, $args = array(), $block = null)
	{
		return $this->context->render_tag($tag, $args, $block);
	}
	
	
	public function set_context_data($data)
	{
		$this->context->set_data($data);
	}
	
	/**
	 * Parses a template fragment as a nested block.
	 * 
	 * @param  string
	 * @return string
	 */
	public function parse_as_nested($string, $php_data = false)
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


/* End of file tag_binding.php */
/* Location: /cygdrive/x/LOL/0web_dev/wwwroot/CMS2/application/libraries/tag_binding.php */
