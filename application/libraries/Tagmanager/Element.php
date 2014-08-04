<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
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
	 * Array of elements ID for which tags are already be defined by _create_element_tags()
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
		$items = self::$ci->element_model->get_fields_from_parent(
			$parent_type,
			$id_parent,
			Settings::get_lang('current'),
			$id_element_definition
		);

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
			self::_create_element_tags($definition);

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

			// Use lang key content if the fields is on translated one
			if ($field['translated'] == 1 && isset($field[Settings::get_lang()]))
				$field = $field[Settings::get_lang()];

			$value = $field['content'];

			switch ($field['type'])
			{
				// TextArea
				case '2':
				case '3':
					$value = self::$ci->url_model->parse_internal_links($value);
					self::load_model('media_model');
					$value = self::$ci->media_model->parse_content_media_url($value);
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

			// Availability of field for TagManager->tag_extend_field_medias()
			$tag->set('extend', $item['fields'][$field_key]);
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
	private static function _create_element_tags($definition)
	{
		// Get the fields from this element definition
		$id_element_definition = $definition['id_element_definition'];
		$definition_name = $definition['name'];

		if ( ! isset(self::$has_element_tags[$id_element_definition]))
		{
			$element_fields = self::$ci->element_model->get_fields_from_definition_id($definition['id_element_definition']);

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

				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':medias', array(__CLASS__, 'tag_extend_field_medias'));

				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':links', array(__CLASS__, 'tag_extend_field_links'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':links:url', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':links:title', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':links:subtitle', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':links:content', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:'.$definition_name.':items:'.$field['name'].':links:medias', array('TagManager_Media', 'tag_medias'));
			}

			self::$has_element_tags[$id_element_definition] = TRUE;
		}
	}
}
