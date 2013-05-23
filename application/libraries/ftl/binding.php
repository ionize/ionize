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

	/**
	 * Tag parent name
	 *
	 * @var null|string
	 */
	protected $parent_name = NULL;

	protected $data_parent = NULL;

	/**
	 * Is the tag one process tag ?
	 * Process tags are not considered as "parent" in the tag tree
	 *
	 * @var bool
	 */
	protected $process_tag = FALSE;


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

	public function getStack()
	{
		return $this->context->get_binding_stack();
	}

	/**
	 * Set the tag as process one
	 *
	 */
	public function setAsProcessTag()
	{
		$this->process_tag = TRUE;
	}

	/**
	 * Returns TRUE if the current tag is one processing tag
	 *
	 * @return bool
	 *
	 */
	public function isProcessTag()
	{
		return $this->process_tag;
	}


	/**
	 * Returns the tag's name
	 *
	 */
	public function getName()
	{
		return $this->name;
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

	/**
	 * Set multiple attributes
	 *
	 * @param array
	 * @param mixed
	 *
	 */
	public function setAttributes($attrs)
	{
		if (is_array($attrs))
		{
			foreach($attrs as $key => $value)
			{
				$this->setAttribute($key, $value);
			}
		}
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


	public function removeAttribute($key)
	{
		if (isset($this->attr[$key]))
			unset($this->attr[$key]);

		return $this;
	}


	public function removeAttributes($attrs)
	{
		foreach ($attrs as $attr)
		{
			$this->removeAttribute($attr);
		}

		return $this;
	}





	/**
	 * Return the current FTL_Binding parent
	 * If no name is given, return the very first parent.
	 *
	 * @param string/null
	 * @param boolean
	 *
	 * @return FTL_Binding
	 *
	 */
	public function getParent($parent_name = NULL, $all = TRUE)
	{
		$parent = NULL;

		if (is_null($parent_name))
		{
			// Correct the nesting, because it is depending on the context, not on the tag
			// means the found and returned parent will have the same nesting
			$nesting = explode(':', $this->nesting());
			while ( ! empty($nesting))
			{
				$last = array_pop($nesting);
				if ($last == $this->name)
				{
					break;
				}
			}
			$parent_name = ! empty($nesting) ? array_pop($nesting) : NULL;
		}

		// We're supposed to have one parent name
		if ( ! is_null($parent_name))
		{
			$stack = array_reverse($this->getStack());
			array_shift($stack);

			foreach($stack as $binding)
			{
				array_shift($stack);

				if ($binding->name == $parent_name)
				{
					if ($all == FALSE && $binding->isProcessTag() == TRUE && count($stack) > 2)
					{
						$parent = $binding->getParent(NULL, FALSE);
					}
					else
					{
						$parent = $binding;
					}

					break;
				}
			}
		}

		return $parent;
	}


	/**
	 * @param $parent_name
	 * @param $data_array_name
	 *
	 * @return null
	public function getDataFromParent($parent_name, $data_array_name)
	{
		$data = NULL;

		// If asked from parent_name 'article', get the first tag called 'article'
		$parent = $this->getParent($parent_name);

		if ( ! is_null($parent))
		{
			// The data array of the parent called 'article' is supposed to have the key 'article'
			$parent_data = $parent->get($parent_name);

			// If the asked data are 'medias', the $parent_data array is supposed to have this array
			if (isset($parent_data[$data_array_name]))
				$data = $parent_data[$data_array_name];
		}
		return $data;
	}

	 */


	/**
	 * Returns the first real data parent tag
	 *
	 * @param array|null		The reversed stack (start with the last grandchild
	 *
	 * @return FTL_Binding
	 *
	 */
	public function getDataParent()
	{
		if (is_null($this->data_parent))
			$this->data_parent = $this->getParent(NULL, FALSE);

		return $this->data_parent;
	}

	/**
	 * Return the tag's first parent name
	 *
	 * @param array|null			Stack, in normal order (not reversed)
	 * @return mixed|null|string
	 *
	 */
	public function getParentName($stack=NULL)
	{
		$nesting = explode(':', $this->nesting());
		while ( ! empty($nesting))
		{
			$last = array_pop($nesting);
			if ($last == $this->name)
			{
				break;
			}
		}

		if ( ! empty($nesting))
			return array_pop($nesting);

		return NULL;
	}

	/**
	 * Return
	 * @param string     $attribute
	 * @param null 		$parent_name
	 *
	 * @return mixed|null
	 */
	public function getParentAttribute($attribute, $parent_name = NULL)
	{
		$parent = $this->getParent($parent_name);

		if ($parent)
			return $parent->getAttribute($attribute);

		return NULL;
	}

	/**
	 * Returns the first data tag parent's name
	 *
	 * @return null|string
	 */
	public function getDataParentName()
	{
		if (is_null($this->data_parent))
		{
			$this->data_parent = $this->getDataParent();
		}
		if ( ! is_null($this->data_parent))
			return $this->data_parent->name;

		return NULL;
	}


	public function getData()
	{
		$tag_name = $this->getName();

		return $data = $this->get($tag_name);
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
		{
			$data_array_name = $this->getAttribute('from');

			if (is_null($data_array_name))
				$data_array_name = $this->getDataParentName();
		}

		if ( ! is_null($key) && ! is_null($data_array_name))
		{
			$data_array = $this->get($data_array_name);

			if (is_null($data_array))
				$data_array = $this->get('data');

			if (is_array($data_array) && isset($data_array[$key]))
				return $data_array[$key];
		}

		return NULL;
	}

	/**
	 * Returns one local var
	 *
	 * @param	String		Local tag var name
	 *
	 * @return	mixed		Local tag var value
	 *
	 */
	public function get($key, $scope='local')
	{
		if ($scope == 'global')
			return $this->globals->{$key};
		else
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
	public function set($key, $value, $scope='local')
	{
		if ($scope == 'global')
			$this->globals->{$key} = $value;
		else
			$this->locals->{$key} = $value;

		return $this;
	}

	/**
	 * Removes one key from the locals vars.
	 *
	 * @param	string			key to remove
	 *
	 * @return 	FTL_Binding
	 *
	 */
	public function remove($key)
	{
		unset($this->locals->{$key});
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

		$parser = new FTL_Parser(array
		(
			'context' => $this->context,
			'tag_prefix' => $tmp->tag_prefix,
			'php_data' => $php_data
		));

		$str = $parser->parse($string, $php_data);
		
		// reset
		$this->context->parser = $tmp;
		
		return $str;
	}

	/**
	 * Parses one tag as a standalone one.
	 *
	 * @param $tag_prefix
	 * @param $string
	 *
	 * @return string
	 */
	public function parse_as_standalone($tag_prefix, $string)
	{
		$parser = new FTL_Parser(array
		(
			'context' => $this->context,
			'tag_prefix' => $tag_prefix,
		));

		return $parser->parse($string);
	}
}


/* End of file binding.php */
/* Location: /application/libraries/ftl/binding.php */
