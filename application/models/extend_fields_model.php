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

	private static $_TYPE_TABLE = 'extend_field_type';

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

		$this->_join_to_extend_types();


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


	// ------------------------------------------------------------------------


	public function _join_to_extend_types()
	{
		// Join to types
		$this->{$this->db_group}->select(
			self::$_TYPE_TABLE . '.type_name,'
			.self::$_TYPE_TABLE . '.active,'
			.self::$_TYPE_TABLE . '.validate,'
			.self::$_TYPE_TABLE . '.html_element,'
			.self::$_TYPE_TABLE . '.html_element_type,'
			.self::$_TYPE_TABLE . '.html_element_class,'
			.self::$_TYPE_TABLE . '.html_element_pattern'
		);

		$this->{$this->db_group}->join(
			self::$_TYPE_TABLE,
			self::$_TYPE_TABLE . '.id_extend_field_type = ' . self::$_EXTEND . '.type',
			'inner'
		);
	}


	// ------------------------------------------------------------------------


	public function get_extend_link_list_from_content($content, $lang=NULL)
	{
		$data = array();

		if ($lang == NULL) $lang = Settings::get_lang('current');

		if (strlen($content) > 0)
		{
			$values = explode(',', $content);

			$types = array();

			foreach($values as $val)
			{
				$arr = explode(':', $val);

				if ( ! empty($arr[1]))
				{
					if ( ! isset($types[$arr[0]])) $types[$arr[0]] = array();

					$types[$arr[0]][] = array_pop(explode('.', $arr[1]));
				}
			}

			$types_names = array_keys($types);

			$sql = "
				select
					COALESCE(" . implode('_lang.title,', $types_names) . '_lang.title' . ") as title,
					url.id_entity,
					url.type,
					url.full_path_ids,
					url.path,
					REPLACE(url.full_path_ids, '/', '.' ) as rel
				from url
			";

			$join = "";
			$where_arr = array();

			foreach($types as $type => $entities)
			{
				$join .= "
					left join ".$type." on (".$type.".id_".$type." = url.id_entity and url.type = '".$type."')
					left join ".$type."_lang on ".$type."_lang.id_".$type." = ".$type.".id_".$type." and ".$type."_lang.lang = '".$lang."'
				";

				$where_arr[] = "(type='".$type."' and id_entity in (" . implode(',', $entities). "))";
			}

			$sql .= $join;
			$sql .= "where (" . implode(' or ', $where_arr) . ")";
			$sql .= "
				and url.lang = '".$lang."'
				and active = 1
			";

			$query = $this->{$this->db_group}->query($sql);

			if ( $query->num_rows() > 0) $data = $query->result_array();
			$query->free_result();
		}

		return $data;
	}

}
