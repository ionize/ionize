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

// ------------------------------------------------------------------------

/**
 * Ionize Extend Fields Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Extend Field
 * @author		Ionize Dev Team
 *
 */
class Extend_fields_model extends Base_model
{

	private static $_EXTEND = 'extend_field';

	private static $_EXTEND_LANG = 'extend_field_lang';


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		// Stores the extend fields definition
		$this->set_table('extend_fields');
		$this->set_pk_name('id_extend_field');
	}


	/**
	 * Returns list of extend fields, with extend_field data
	 *
	 */
	public function get_detailled_lang_list($where=array(), $lang=NULL)
	{
		$this->{$this->db_group}->select(
			self::$_EXTEND.'.name,'
			.self::$_EXTEND.'.type,'
			.self::$_EXTEND.'.description,'
			.self::$_EXTEND.'.ordering,'
			.self::$_EXTEND.'.value as default_value,'
			.self::$_EXTEND.'.main'
		);

		$this->{$this->db_group}->join(
			self::$_EXTEND,
			self::$_EXTEND.'.id_extend_field = ' .$this->get_table().'.id_extend_field',
			'inner'
		);

		if ( ! is_null($lang))
		{
			// Add extend_field label
			$this->{$this->db_group}->select(self::$_EXTEND_LANG.'.label');
			$this->{$this->db_group}->join(
				self::$_EXTEND_LANG,
				self::$_EXTEND_LANG.'.id_extend_field = ' .$this->get_table().'.id_extend_field'
				. ' AND ' . self::$_EXTEND_LANG . '.lang = \'' . $lang . '\'',
				'left'
			);

			// Limit extend_fields to the asked lang
			$this->{$this->db_group}->where(
				"(" .$this->get_table().".lang = '".$lang."' OR "
					.$this->get_table().".lang is NULL  OR "
					.$this->get_table().".lang =''"
				.")"
			);
		}

		return parent::get_lang_list($where);
	}
}
