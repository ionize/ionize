<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 *
 */

/**
 * Ionize Tagmanager Item Class
 *
 * Display the Static items on front-end
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	TagManager Libraries
 *
 */
class TagManager_Item extends TagManager
{
	private static $_DEFINITIONS = NULL;

	private static $_ITEMS = array();


	/**
	 * Array of elements ID for which tags are already be defined by create_item_tags()
	 *
	 * @var array
	 */
	private static $has_item_tags = array();

	public static $tag_definitions = array
	(
		'static' => 				'tag_static',
		'static:items' => 			'tag_static_items',
		'static:items:item' => 		'tag_expand',
	);


	// ------------------------------------------------------------------------


	/**
	 * Get the items definition and store them in the private property "$_DEFINITIONS"
	 *
	 * @param	String	lang code
	 * @return	Array	Extend fields definition array
	 */
	private function _get_definitions_with_items($lang)
	{
		// Get the extend fields definition if not already got
		if (self::$_DEFINITIONS == NULL)
		{
			self::$ci->load->model('item_model', '', TRUE);

			// Store the extend fields definition
			self::$_DEFINITIONS = self::$ci->item_model->get_definitions_with_items($lang);
		}

		return self::$_DEFINITIONS;
	}


	// ------------------------------------------------------------------------


	private function _get_items_type_from_parent($definition, $parent, $id_parent, $lang=NULL)
	{
		$result = array();

		if (! isset(self::$_ITEMS[$parent]))
		{
			self::$ci->load->model('items_model', '', TRUE);

			self::$_ITEMS[$parent] = self::$ci->items_model->get_list(
				array(
					'parent' => $parent,
					'order_by' => 'ordering ASC'
				)
			);
		}

		foreach ($definition['items'] as $item)
		{
			foreach(self::$_ITEMS[$parent] as $lk_item)
			{
				if ($lk_item['id_item'] ==  $item['id_item'] && $lk_item['id_parent'] == $id_parent)
				{
					// Fix ordering : From parent
					$item['ordering'] = $lk_item['ordering'];
					$result[] = $item;
				}
			}
		}

		// Sorting items regarding parent
		$index = array();
		foreach ($result as $key => $row)
			$index[$key]  = $row['ordering'];
		array_multisort($index, SORT_ASC, $result);

		return $result;
	}


	// ------------------------------------------------------------------------


	private function _get_item_field($item, $field_key)
	{
		foreach ($item['fields'] as $field)
		{
			if ($field['name'] == $field_key)
				return $field;
		}
		return NULL;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns one Item definition from its name (key)
	 *
	 * @param $definition_name
	 *
	 * @return null
	 */
	protected static function _get_definition_from_name($definition_name)
	{
		$items_definitions = self::_get_definitions_with_items(Settings::get_lang('current'));

		foreach($items_definitions as $ed)
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
	 * @usage   With "stickers" as your definition name :
	 * 			<ion:static:stickers />
	 *
	 */
	public static function tag_static(FTL_Binding $tag)
	{
		// Store the parent
		$parent = $tag->getDataParent();
		$tag->set('__static_parent__', $parent);

		$items_definitions  = self::_get_definitions_with_items(Settings::get_lang('current'));

		// Create dynamical tags
		foreach($items_definitions as $definition)
		{
			self::$context->define_tag('static:'.$definition['name'], array(__CLASS__, 'tag_static_detail'));
		}

		return $tag->expand();
	}


	// ------------------------------------------------------------------------


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 * @usage   With "stickers" as your definition name :
	 * 			<ion:static:stickers />
	 */
	public static function tag_static_detail(FTL_Binding $tag)
	{
		// Returned string
		$str = '';

		// Contains also items as nested array
		$definition = self::_get_definition_from_name($tag->name);

		// "title" value : Processed by the default title tag
		$tag->set('title_definition', $definition['title_definition']);
		$tag->set('title', $definition['title_item']);
		$tag->set('description', $definition['description']);

		// Get the parent
		$parent = $tag->get('__static_parent__');

		if (is_object($parent))
		{
			$parent_type = $parent->getName();
			$id_parent = $parent->getValue('id_'.$parent_type, $parent_type);
		}
		else{
			$parent_type = $id_parent = NULL;
		}

		// $items = self::_get_items_from_parent($parent_type, $id_parent, Settings::get_lang('current'));
		if ($tag->getAttribute('all') == TRUE)
		{
			$items = $definition['items'];
		}
		else
		{
			if (($parent_type != 'article' && $parent_type != 'page'))
			{
				$entity = self::get_entity();
				if (empty($entity))
				{
					$parent = self::registry('page');
					$parent_type = 'page';
					$id_parent = $parent['id_page'];
				}
				else
				{
					$parent_type = $entity['type'];
					$id_parent = $entity['id_entity'];
				}
			}
			$items = self::_get_items_type_from_parent(
				$definition,
				$parent_type,
				$id_parent,
				Settings::get_lang('current')
			);
		}

		$tag->set('items', $items);

		if ( ! empty($items) OR $tag->getAttribute('display') == TRUE)
		{
			// Set "title" value : Processed by the default title tag
			$tag->set('title_definition', $definition['title_definition']);
			$tag->set('title', $definition['title_item']);
			$tag->set('description', $definition['description']);

			// Internal data : Used by sub tags
			$tag->set('parent', $parent_type);
			$tag->set('id_parent', $id_parent);
			$tag->set('id_item_definition', $definition['id_item_definition']);

			$tag->set('count', count($items));

			// Create dynamic child tags for <ion:static />
			self::_create_static_tags($definition);

			$str = self::wrap($tag, $tag->expand());
		}
		else
		{
			$tag->set('count', 0);
		}

		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * Element's items tag
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 * @usage   With "stickers" as your definition name :
	 * 			<ion:static:stickers:items />
	 */
	public static function tag_static_items(FTL_Binding $tag)
	{
		$return = $tag->getAttribute('return');

		$str = '';

		// Limit ?
		$limit = ($tag->getAttribute('limit')) ? (int)$tag->getAttribute('limit') : FALSE;

		$items = $tag->get('items');

		$tag->set('count', 0);


		// Process the elements
		if ( ! empty($items))
		{
			if (is_null($return))
			{
				$count = count($items);
				$limit = ($limit == FALSE OR $limit > $count) ? $count : $limit;

				$tag->set('count', $limit);

				for($i = 0; $i < $limit; $i++)
				{
					$item = $items[$i];

					// item : One element instance
					$tag->set('item', $item);
					$tag->set('index', $i+1);
					$tag->set('count', $limit);

					$str .= $tag->expand();
				}

				$str = self::wrap($tag, $str);
			}
			else if ($return == 'json')
			{
// Ugly process of links in case
// of link type extend fields
// @todo: rewrite

				self::$ci->load->model('extend_fields_model', '', TRUE);

				if ( count(Settings::get_online_languages()) > 1 OR Settings::get('force_lang_urls') == '1' )
					$base_url = base_url() . Settings::get_lang('current'). '/';
				else
					$base_url = base_url();

				foreach($items as $k1 => $item)
				{
					foreach($item['fields'] as $k2 => $field)
					{
						if ($field['html_element_type'] == 'link')
						{
							$items[$k1]['ion_urls'] = array();
							$links = self::$ci->extend_fields_model->get_extend_link_list_from_content($field['content']);

							foreach($links as $link)
							{
								$items[$k1]['ion_urls'][] = $base_url . $link['path'];
							}
						}
					}
				}
// End @todo
				$str = json_encode($items, TRUE);
				$str = str_replace("'", "\\'", $str);
			}
		}

		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * Static field generic tag
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_static_item_field(FTL_Binding $tag)
	{
		$item = $tag->get('item');

		$field_key = $tag->getName();

		$field = self::_get_item_field($item, $field_key);

		if ( ! is_null($field))
		{
			$tag->set($field_key, $field);

			// Availability of field for TagManager->tag_extend_field_medias()
			$tag->set('extend', $field);
		}

		return self::wrap($tag, $tag->expand());
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the value of one field (content)
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_static_item_field_value(FTL_Binding $tag)
	{
		$value = NULL;

		$item = $tag->get('item');

		$field_key = $tag->getParentName();

		$field = self::_get_item_field($item, $field_key);

		if ( ! is_null($field))
		{
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
	public static function tag_static_item_field_values(FTL_Binding $tag)
	{
		$str = '';

		$item = $tag->get('item');

		$field_key = $tag->getParentName();

		$field = self::_get_item_field($item, $field_key);

		if ( ! is_null($field))
		{
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
	public static function tag_static_item_field_options(FTL_Binding $tag)
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
	 * Creates, for each field of the element, the tags :
	 * 	<ion:field />
	 * 	<ion:field:label />
	 * 	<ion:field:default_value />
	 * 	<ion:field:type />
	 * 	<ion:field:content />
	 *
	 * @param $definition		Definition array
	 *                          Contains also items and item's fields
	 *
	 */
	private static function _create_static_tags($definition)
	{
		// Get the fields from this element definition
		$id_definition = $definition['id_item_definition'];
		$definition_name = $definition['name'];

		if ( ! isset(self::$has_item_tags[$id_definition]))
		{
			self::$ci->load->model('extend_field_model', '', TRUE);

			$fields = self::$ci->extend_field_model->get_lang_list(
				array(
					'parent' => 'item',
					'id_parent' => $id_definition
				),
				Settings::get_lang('default')
			);

			foreach ($fields as $field)
			{
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'], array(__CLASS__, 'tag_static_item_field'));

				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':type', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':options', array(__CLASS__, 'tag_static_item_field_options'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':options:label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':options:value', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':values', array(__CLASS__, 'tag_static_item_field_values'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':values:label', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':values:value', array(__CLASS__, 'tag_simple_value'));

				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':value', array(__CLASS__, 'tag_static_item_field_value'));

				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':medias', array(__CLASS__, 'tag_extend_field_medias'));

				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':links', array(__CLASS__, 'tag_extend_field_links'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':links:url', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':links:title', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':links:subtitle', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':links:content', array(__CLASS__, 'tag_simple_value'));
				self::$context->define_tag('static:'.$definition_name.':items:'.$field['name'].':links:medias', array('TagManager_Media', 'tag_medias'));
			}

			self::$has_item_tags[$id_definition] = TRUE;
		}
	}
}
