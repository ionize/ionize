<<<<<<< HEAD
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
	private static $elements_def = NULL;

	/**
	 * Array of elements ID for which tags are already be defined by create_element_tags()
	 *
	 * @var array
	 */
	private static $has_element_tags = array();

	public static $tag_definitions = array
	(
		'element' => 				'tag_element',
		'element:items' => 			'tag_element_items',
		'element:items:item' => 	'tag_expand',

		// TODO : Add description to backend, by lang
		'element:description' => 'tag_simple_value',
	);
	

	// ------------------------------------------------------------------------
	
	
	/**
	 * Get the elements definition and store them in the private property "elements_def"
	 *
	 * @param	String	lang code
	 * @return	Array	Extend fields definition array
	 */
	private function get_elements_definition($lang)
	{
		// Get the extend fields definition if not already got
		if (self::$elements_def == NULL)
		{
			self::$ci->load->model('element_definition_model', '', TRUE);

			// Store the extend fields definition
			self::$elements_def = self::$ci->element_definition_model->get_lang_list(NULL, $lang);
		}

		return self::$elements_def;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns one Element definition from its name (key)
	 *
	 * @param $definition_name
	 *
	 * @return null
	 */
	protected static function get_definition_from_name($definition_name)
	{
		$elements_definitions = self::get_elements_definition(Settings::get_lang('current'));

		foreach($elements_definitions as $ed)
		{
			if ($ed['name'] == $definition_name)
				return $ed;
		}

		return NULL;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_element_detail(FTL_Binding $tag)
	{
		// Returned string
		$str = '';

		$definition = self::get_definition_from_name($tag->name);

		// "title" value : Processed by the default title tag
		$tag->set('title', $definition['title']);
		$tag->set('description', $definition['description']);

		// Get the parent
		$parent = $tag->get('__element_parent__');
		$parent_type = $parent->getName();
		$id_parent = $parent->getValue('id_'.$parent_type, $parent_type);

		// Get the items
		$id_element_definition = $definition['id_element_definition'];
		self::$ci->load->model('element_model', '', TRUE);
		$items = self::$ci->element_model->get_fields_from_parent($parent_type, $id_parent, Settings::get_lang(), $id_element_definition);

		$tag->set('items', $items);

		if ( ! empty($items) OR $tag->getAttribute('display') == TRUE)
		{
			// Set "title" value : Processed by the default title tag
			$tag->set('title', $definition['title']);
			$tag->set('description', $definition['description']);

			// Internal data : Used by sub tags
			$tag->set('parent', $parent_type);
			$tag->set('id_parent', $id_parent);
			$tag->set('id_element_definition', $id_element_definition);

			// Create dynamic child tags for <ion:element />
			self::create_element_tags($definition);

			$str = self::wrap($tag, $tag->expand());
		}

		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_element(FTL_Binding $tag)
	{
		// Store the parent
		$parent = $tag->getParent();
		$tag->set('__element_parent__', $parent);

		// Get all the element definition potentially linked to the parent.
		$elements_definitions = self::get_elements_definition(Settings::get_lang('current'));

		// Create dynamical tags
		foreach($elements_definitions as $definition)
		{
			log_message('error', 'element:'.$definition['name']);
			self::$context->define_tag('element:'.$definition['name'], array(__CLASS__, 'tag_element_detail'));
		}

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	/**
	 * Element's items tag
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_element_items(FTL_Binding $tag)
	{
		$str = '';

		// Limit ?
		$limit = ($tag->getAttribute('limit')) ? (int)$tag->getAttribute('limit') : FALSE;

		$items = $tag->get('items');

		// Process the elements
		if ( ! empty($items['elements']))
		{
			$count = count($items['elements']);
			$limit = ($limit == FALSE OR $limit > $count) ? $count : $limit;

			$tag->set('count', $limit);

			for($i = 0; $i < $limit; $i++)
			{
				$item = $items['elements'][$i];

				// item : One element instance
				$tag->set('item', $item);
				$tag->set('index', $i+1);
				$tag->set('count', $limit);

				$str .= $tag->expand();
			}

			$str = self::wrap($tag, $str);
		}

		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the value of one field (content)
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_element_item_field_value(FTL_Binding $tag)
	{
		$value = NULL;

		$item = $tag->get('item');

		$field_name = $tag->getParentName();

		if (isset($item['fields'][$field_name]))
		{
			$field = $item['fields'][$field_name];

			$value = $field['content'];

			switch ($field['type'])
			{
				// TextArea
				case '2':
				case '3':
					$value = self::$ci->url_model->parse_internal_links($value);
					break;

				case '7':
					$value = self::format_date($tag, $value);
					break;

				default:
					break;
			}
		}
		return self::output_value($tag, $value);
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 * @TODO : 	Modify this method for the 1.0 release
	 * 			Should return the select, radio, checkbox values
	 *
	 * @param 	FTL_Binding $tag
	 * @return	string
	 */
	public static function tag_element_item_field_values(FTL_Binding $tag)
	{
		$str = '';

		$item = $tag->get('item');

		$field_name = $tag->getParentName();

		if (isset($item['fields'][$field_name]))
		{
			$field = $item['fields'][$field_name];

			// All available values for this multi-value field
			$all_values = explode("\n", $field['value']);
			$values = array();
			foreach($all_values as $value)
			{
				$key_val = explode(':', $value);
				$values[$key_val[0]] = $key_val[1];
			}
			// Values selected by the user
			$selected_values = explode(',', $field['content']);

			foreach($selected_values as $selected_value)
			{
				foreach($values as $value => $label)
				{
					if ($value == $selected_value)
					{
						$tag->set('value', $value);
						$tag->set('label', $label);
						$str .= self::wrap($tag, $tag->expand());
					}
				}
			}
		}
		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_element_item_field_options(FTL_Binding $tag)
	{
		$str = '';

		$item = $tag->get('item');

		$field_name = $tag->getParentName();

		if (isset($item['fields'][$field_name]))
		{
			$field = $item['fields'][$field_name];

			// All available values for this multi-value field
			$all_values = explode("\n", $field['value']);

			foreach($all_values as $value)
			{
				$val_label = explode(':', $value);
				$tag->set('value', $val_label[0]);
				$tag->set('label', $val_label[1]);
				$str .= self::wrap($tag, $tag->expand());
			}
		}
		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * Element field generic tag
	 * 
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_element_item_field(FTL_Binding $tag)
	{
		$item = $tag->get('item');

		$field_key = $tag->getName();

		if (isset($item['fields'][$field_key]))
		{
			$tag->set($field_key, $item['fields'][$field_key]);
		}

		return self::wrap($tag, $tag->expand());
	}


	// ------------------------------------------------------------------------


	/**
	 * Creates, for each field of the element, the tags :
	 * 	<ion:field />
	 * 	<ion:field:label />
	 * 	<ion:field:default_value />
	 * 	<ion:field:type />
	 * 	<ion:field:content />
	 *
	 * @param $definition
	 *
	 */
	private static function create_element_tags($definition)
	{
		// Get the fields from this element definition
		$id_element_definition = $definition['id_element_definition'];
		$definition_name = $definition['name'];

		$element_fields = self::$ci->element_model->get_fields_from_definition_id($definition['id_element_definition']);

		if ( ! isset(self::$has_element_tags[$id_element_definition]))
		{
			foreach ($element_fields as $field)
			{
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'], array(__CLASS__, 'tag_element_item_field'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':default_value', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':type', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':options', array(__CLASS__, 'tag_element_item_field_options'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':options:label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':options:value', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':values', array(__CLASS__, 'tag_element_item_field_values'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':values:label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':values:value', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':value', array(__CLASS__, 'tag_element_item_field_value'));
			}

			self::$has_element_tags[$id_element_definition] = TRUE;
		}
	}
}

/* End of file Element.php */
=======
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
	 * Returns one field value
	 * 
	 * @usage : 
	 *
	 *			<ion:elements type="my_element_name" [from="page:my_page" limit="5"]>
	 *			    
	 *			    <select>
	 *			    	<!-- Loop through all the element's fields -->
	 *			    	<ion:fields>
	 *			    		<!-- Display the value of the field
	 *			    		<option value="<ion:attribute name="content" />"><ion:attribute name="label" /></option>
	 *			    	</ion:fields>
	 *			    </select>
	 *			</ion:elements>
	 *
	 */
	public static function tag_element_fields_attribute($tag)
	{
		// Wished field attribute
		$attr = ( ! empty($tag->attr['name'])) ? $tag->attr['name'] : FALSE ;
		$autolink = ( ! empty($tag->attr['autolink'])) ? TRUE : FALSE ;
		
		if ($attr !== FALSE)
		{
			if (isset($tag->locals->field[$attr]))
			{
				$field = $tag->locals->field;
				$value = $field[$attr];

				// Date
				if ($field['type'] == '7')
				{
					$value = self::format_date($tag, $value);
				}
				
				// Translated Element
				if ($field['translated'] == '1' && $attr != 'label')
				{
					if (isset($field[Settings::get_lang('current')]['content']))
					{
						$value = $field[Settings::get_lang('current')][$attr];
					}
				}
				
				// Autolink				
				if ($autolink == TRUE)
					$value = auto_link($value, 'both', TRUE);

				return self::wrap($tag, $value );
			}
			return '';
		}

		return self::show_tag_error($tag->name, '<b>The "name" attribute is mandatory</b>');
	}
	
	
	/**
	 * Returns the value of one element field
	 *
	 * @usage : 
	 *
	 *			<ion:elements type="my_element_name" [from="page:my_page" limit="5"]>
	 *		
	 *				<span class="date"><ion:field name="date" format="d" /></span> 
	 *				<span class="month"><ion:field name="date" format="M" /></span>
	 *
	 *				<span class="location">
	 *					<ion:field name="city"/>
	 *					<small><ion:field name="location" /> | <ion:field name="country" /></small>
	 *				</span>
	 *
	 *			</ion:elements>
	 *
	 *
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
				if ($field['type'] == '2' OR $field['type'] == '3')
				{
					$field['content'] = auto_link($field['content'], 'both', TRUE);
				}
				
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
>>>>>>> 37ae275c480b6d3e0b24d07a92920ce8f2b8b12e
/* Location: /application/libraries/Tagmanager/Element.php */