<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 */

/**
 * Ionize Item Definition Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Item definition
 * @author		Ionize Dev Team
 *
 */
class Item_definition_model extends Base_model
{
	private static $_EXTEND_FIELD = 'extend_field';
	private static $_EXTEND_FIELDS = 'extend_fields';

	private static $_ITEM = 'item';
	private static $_ITEMS = 'items';


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('item_definition');
		$this->set_pk_name('id_item_definition');
		$this->set_lang_table('item_definition_lang');
	}


	// ------------------------------------------------------------------------


	/**
	 * @param       $data
	 * @param array $lang_data
	 *
	 * @return int
	 */
	public function save($data, $lang_data)
	{
		// Name
		$data['name'] = url_title($data['name']);

		return parent::save($data, $lang_data);
	}


	// ------------------------------------------------------------------------


	/**
	 * Deletes one item definition
	 * Also deletes :
	 * - All items linked to this definition
	 * - All corresponding extend field & extend fields instances
	 * - Links between content & items
	 *
	 * @param null $id_item_definition
	 *
	 * @return int|void
	 */
	public function delete($id_item_definition)
	{
		// Items instances IDs
		$item_ids = $this->get_keys_array(
			'id_item',
			array($this->get_pk_name() => $id_item_definition),
			self::$_ITEM
		);

		// Items Definition & Item definition langs
		$where = array($this->get_pk_name() => $id_item_definition);
		parent::delete($where);
		parent::delete($where, $this->get_lang_table());

		// Extend fields instances
		if ( ! empty ($item_ids))
		{
			parent::delete(
				array(
					'parent' => 'item',
					'where_in' => array('id_parent' => $item_ids)
				),
				self::$_EXTEND_FIELDS
			);
		}

		// Extend Field definition
		parent::delete(
			array(
				'parent' => 'item',
				'id_parent' => $id_item_definition
			),
			self::$_EXTEND_FIELD
		);

		// Item definition
		parent::delete($where, self::$_ITEM);

		// Items links to content
		if ( ! empty ($item_ids))
		{
			parent::delete(
				array('where_in' => array('id_parent' => $item_ids)),
				self::$_ITEMS
			);
		}
	}
}
