<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 *
 */

/**
 * Ionize Tagmanager Element Class
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	TagManager Libraries
 *
 */
class TagManager_Element extends TagManager
{

	private static $allowed_parents = array ('articles','article','page');

	private static $got_elements_def = false;
	
	private static $elements_def = array();

	public static $tag_definitions = array
	(
		'elements' => 'tag_elements',
		'elements:index' => 'tag_element_index',
		'elements:count' => 'tag_element_count',
		'elements:field' => 'tag_element_field',
		'elements:fields' => 'tag_element_fields',
		'elements:attribute' => 'tag_element_attribute',
		'elements:fields:attribute' => 'tag_element_fields_attribute'	
	);
	

	// ------------------------------------------------------------------------
	
	
	/**
	 * Get the elements definition and store them in the private property "elements_def"
	 *
	 * @param	String	Parent type
	 * @return	Array	Extend fields definition array
	 */
	private function set_elements_definition($lang)
	{
		self::$ci->load->model('element_definition_model', '', true);
		
		// Get the extend fields definition if not already got
		if (self::$got_elements_def == false)
		{
			// Store the extend fields definition
			self::$elements_def = self::$ci->element_definition_model->get_lang_list(FALSE, $lang);
			
			// Set this to true so we don't get the extend field def a second time for an object of same kind
			self::$got_elements_def = true;
		}
	}
	
	// ------------------------------------------------------------------------

	
	protected static function get_definition_id_from_name($definition_name)
	{
		foreach(self::$elements_def as $ed)
		{
			if ($ed['name'] == $definition_name)
				return $ed['id_element_definition'];
		}
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Returns 
	 *
	 */
	public static function tag_elements($tag)
	{
		// Returned string
		$str = '';
		
		// Wished element definition name
		$element_definition_name = (!empty($tag->attr['type'])) ? $tag->attr['type'] : FALSE ;

		// Limit ?
		$limit = (!empty($tag->attr['limit'])) ? (int)$tag->attr['limit'] : FALSE ;
		
		// Parent. can be set or not
		$parent = (!empty($tag->attr['from'])) ? $tag->attr['from'] : FALSE ;
		$parent_name = NULL;
		$parent_object = NULL;
		$id_parent = NULL;

		if ($element_definition_name !== FALSE)
		{
			// Current page parent
			if ($parent == FALSE)
			{
				$obj_tag = NULL;
				
				// Get the tag path
				$tag_path = explode(':', $tag->nesting());

				// Remove the current tag from the path
				while ( ! empty($tag_path))
				{
					$obj_tag = array_pop($tag_path);
					if (in_array($obj_tag, self::$allowed_parents))
						break;
				}

				// If no parent, the default parent is 'page'
				// $obj_tag = (count($tag_path) > 0) ? array_pop($tag_path) : 'page';
				if ($obj_tag == NULL OR $obj_tag == 'elements') $obj_tag = 'page';
				
				// Parent name. Removes plural from parent tag name if any.
				if (substr($obj_tag, -1) == 's')
					$parent = substr($obj_tag, 0, -1);
				else
					$parent = $obj_tag;

				// The Parent object
				$parent_object = $tag->locals->{$parent};
				$id_parent = $parent_object['id_' . $parent];
			}
			// Get the parent from another page
			else
			{
				$parent_def = explode(':', $parent);
				$parent = $parent_def[0];
				$parent_name = $parent_def[1];

				switch($parent)
				{
					case 'page' :
						
						if (isset($tag->locals->{$parent}))
						{
							foreach($tag->globals->pages as $page)
							{
								if($page['url'] == $parent_name)
								{
									$id_parent = $page['id_page'];
								}
							}
						}
						break;
					
					// Get the article
					case 'article' :
						
						$article = 	$this->ci->article_model->get(array('name' => $parent_name));
						
						if ( ! empty($article))
						{
							$id_parent = $article['id_article'];
						}
						
						break;
				}
			}

			// Allowed parent ? Great, let's get the definition
			if ( ! is_null($id_parent) && in_array($parent, self::$allowed_parents) )
			{
				// CI Model
				self::$ci->load->model('element_model', '', true);
			
				// Get only one time the definition
				self::set_elements_definition(Settings::get_lang('current'));
				
				// Get the corresponding element definition ID
				$id_element_definition = self::get_definition_id_from_name($element_definition_name);

				$elements = self::$ci->element_model->get_fields_from_parent($parent, $id_parent, Settings::get_lang(), $id_element_definition);

				// Process the elements
				if (!empty($elements['elements']))
				{
					$count = count($elements['elements']);
					$limit = ($limit == FALSE OR $limit > $count) ? $count : $limit;
					
					for($i = 0; $i < $limit; $i++)
					{
						$element = $elements['elements'][$i];
						$element['title'] =  $elements['title'];
						$element['name'] =  $elements['name'];
						
						$tag->locals->element = $element;
						$tag->locals->index = $i +1;
						$tag->locals->count = $limit;
						$str .= $tag->expand();
					}
				}
			}
	
			return $str;					
		
		}
		return self::show_tag_error($tag->name, '<b>The "type" attribute is mandatory</b>');
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns 
	 *
	 */
	public static function tag_element_field($tag)
	{
		// Wished element definition name
		$field_name = (!empty($tag->attr['name'])) ? $tag->attr['name'] : FALSE ;

		if ($field_name !== FALSE)
		{
			$element = $tag->locals->element;

			if (!empty($element['fields'][$field_name]))
			{
				$field = $element['fields'][$field_name];
				
				// Date
				if ($field['type'] == '7')
				{
					$field['content'] = self::format_date($tag, $field['content']);
				}
				
				// Translated Element
				if ($field['translated'] == '1')
				{
					if (isset($field[Settings::get_lang('current')]['content']))
					{
						$field['content'] = $field[Settings::get_lang('current')]['content'];
					}
				}
				
				// Textarea or Rich Text content
				/*
				if ($field['type'] == '2' OR $field['type'] == '3')
				{
					$field['content'] = auto_link($field['content'], 'both', TRUE);
				}
				*/
				
				return self::wrap($tag, $field['content'] );
			}
			return '';
		}
		return self::show_tag_error($tag->name, '<b>The "name" attribute is mandatory</b>');
	}


	// ------------------------------------------------------------------------
	
	
	public static function tag_element_fields($tag)
	{
		$element = $tag->locals->element;
		$str = '';
		if (!empty($element['fields']))
		{
			foreach($element['fields'] as $field)
			{
				$tag->locals->field = $field;
				$str .= $tag->expand();
			}
		}
		return $str;
	}
	

	// ------------------------------------------------------------------------
	
	
	public static function tag_element_attribute($tag)
	{
		// Wished field attribute
		$attr = (!empty($tag->attr['name'])) ? $tag->attr['name'] : FALSE ;

		if ($attr !== FALSE)
		{
			if (isset($tag->locals->element[$attr]))
			{
				return $tag->locals->element[$attr];
			}
			return self::show_tag_error($tag->name, '<b>The attribute "'.$attr.'" doesn\'t exists.</b>');
		}

		return self::show_tag_error($tag->name, '<b>The "name" attribute is mandatory</b>');
	}
	
	public static function tag_element_fields_attribute($tag)
	{
		// Wished field attribute
		$attr = (!empty($tag->attr['name'])) ? $tag->attr['name'] : FALSE ;

		if ($attr !== FALSE)
		{
			if (isset($tag->locals->field[$attr]))
			{
				return $tag->locals->field[$attr];
			}
		}

		return self::show_tag_error($tag->name, '<b>The "name" attribute is mandatory</b>');
	}
	
	
	public static function tag_element_index($tag)
	{
		return $tag->locals->index;
	}

	public static function tag_element_count($tag)
	{
		return $tag->locals->count;
	}

	
}

/* End of file Element.php */
/* Location: /application/libraries/Tagmanager/Element.php */