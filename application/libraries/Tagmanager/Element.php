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
	 * Array of elements ID for which tags are already be defined by set_element_tags()
	 *
	 * @var array
	 */
	private static $has_element_tags = array();

	public static $tag_definitions = array
	(
		'element' => 'tag_element',
		'element:items' => 'tag_element_items',
	);
	

	// ------------------------------------------------------------------------
	
	
	/**
	 * Get the elements definition and store them in the private property "elements_def"
	 *
	 * @param	String	Parent type
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
	 * Return the Element Definition ID from its key or NULL if no one was found.
	 *
	 * @param $definition_name
	 *
	 * @return null
	 */
	protected static function get_definition_id_from_name($definition_name)
	{
		$elements_definitions = self::get_elements_definition(Settings::get_lang('current'));

		foreach($elements_definitions as $ed)
		{
			if ($ed['name'] == $definition_name)
				return $ed['id_element_definition'];
		}

		return NULL;
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
	 * Returns one Element definition from its ID
	 *
	 * @param $id_element_definition
	 *
	 * @return null
	 */
	protected static function get_definition_from_id($id_element_definition)
	{
		$elements_definitions = self::get_elements_definition(Settings::get_lang('current'));

		foreach($elements_definitions as $ed)
		{
			if ($ed['id_element_definition'] == $id_element_definition)
				return $ed;
		}

		return NULL;
	}


	// ------------------------------------------------------------------------


	/**
	 * Element tag
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_element(FTL_Binding $tag)
	{
		// Returned string
		$str = '';

		// Wished element definition name
		$element_definition_name = $tag->getAttribute('key');

		if (!is_null($element_definition_name))
		{
			$parent = $tag->getParent();
			$parent_type = $parent->getName();

			$id_parent = $parent->getValue('id_'.$parent_type, $parent_type);

			// CI Model
			self::$ci->load->model('element_model', '', TRUE);

			// Get the corresponding element definition ID
			$id_element_definition = self::get_definition_id_from_name($element_definition_name);
			$element_definition = self::get_definition_from_id($id_element_definition);

			// Get the items
			$items = self::$ci->element_model->get_fields_from_parent($parent_type, $id_parent, Settings::get_lang(), $id_element_definition);

			$tag->set('items', $items);

			if ( ! empty($items) OR $tag->getAttribute('display') == TRUE)
			{
				// Set "title" value : Processed by the default title tag
				$tag->set('title', $element_definition['title']);

				// Internal data : Used by sub tags
				$tag->set('parent', $parent_type);
				$tag->set('id_parent', $id_parent);
				$tag->set('id_element_definition', $id_element_definition);

				// Create dynamic child tags for <ion:element />
				self::set_element_tags($id_element_definition);

				$str = self::wrap($tag, $tag->expand());
			}

			return $str;
		}
		else
		{
			show_error('TagManager : Please use the attribute <b>"key"</b> when using the tag <b>elements</b>');
		}
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
				$element = $items['elements'][$i];

				$tag->set('element', $element);
				$tag->set('index', $i);
				$tag->set('count', $limit);

				$str .= $tag->expand();
			}

			$str = self::wrap($tag, $str);
		}

		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 * @TODO : Modify this method for the 1.0 release
	 *
	 * @param FTL_Binding $tag
	 */
	public static function tag_element_item_field_values(FTL_Binding $tag)
	{
		$str = '';

		$element = $tag->get('element');

		$field_name = $tag->getParentName();

		if (isset($element['fields'][$field_name]))
		{
			$field = $element['fields'][$field_name];

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
	 * Element field generic tag
	 * 
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_element_item_field(FTL_Binding $tag)
	{
		$element = $tag->get('element');

		$field_key = $tag->getName();

		if (isset($element['fields'][$field_key]))
		{
			$tag->set($field_key, $element['fields'][$field_key]);
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
	 * @param $id_element_definition
	 */
	private static function set_element_tags($id_element_definition)
	{
		// Get the fields from this element definition
		$element_fields = self::$ci->element_model->get_fields_from_definition_id($id_element_definition);

		if ( ! isset(self::$has_element_tags[$id_element_definition]))
		{
			foreach ($element_fields as $field)
			{
				self::$context->define_tag('element:items:'.$field['name'], array(__CLASS__, 'tag_element_item_field'));
				self::$context->define_tag('element:items:'.$field['name'].':label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:items:'.$field['name'].':default_value', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:items:'.$field['name'].':type', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag('element:items:'.$field['name'].':values', array(__CLASS__, 'tag_element_item_field_values'));
				self::$context->define_tag('element:items:'.$field['name'].':values:label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('element:items:'.$field['name'].':values:value', array(__CLASS__, 'tag_simple_value'));

				if ($field['type'] != '7')
					self::$context->define_tag('element:items:'.$field['name'].':content', array(__CLASS__, 'tag_simple_value'));
				else
					self::$context->define_tag('element:items:'.$field['name'].':content', array(__CLASS__, 'tag_simple_date'));
			}

			self::$has_element_tags[$id_element_definition] = TRUE;
		}
	}
}

/* End of file Element.php */
/* Location: /application/libraries/Tagmanager/Element.php */