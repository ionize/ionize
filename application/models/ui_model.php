<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ui_model extends Base_model
{
	private static $_TBL_LK_EXTEND = 'ui_element_lk_extend';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'ui_element';
		$this->pk_name 	=	'id_ui_element';
	}


	public function get_panel_elements($id_company, $panel, $type=NULL)
	{
		$where = array(
			'id_company' => $id_company,
			'panel' => $panel,
			'order_by' => 'ordering ASC'
		);

		if ( ! is_null($type)) $where['type'] = $type;

		return parent::get_list($where);
	}


	public function create_element($id_company, $panel, $type, $title, $ordering=0)
	{
		$data = array(
			'id_company' => $id_company,
			'panel' => $panel,
			'type' => $type,
			'title' => $title,
			'ordering' => $ordering,
		);

		$id = parent::insert($data);

		return $id;
	}


	public function delete_element($id_ui_element)
	{
		parent::delete($id_ui_element);

		parent::delete(array('id_ui_element' => $id_ui_element), self::$_TBL_LK_EXTEND);
	}


	public function get_element_fields($id_ui_element)
	{
		$extends = array();

		self::$ci->load->model('extend_field_model');

		$sql = "
			select
				uie.*,
				ef.*,
				efl.label,
				eft.*
			from ".self::$_TBL_LK_EXTEND." uie
			inner join extend_field ef on ef.id_extend_field = uie.id_extend
			inner join extend_field_lang efl on efl.id_extend_field = ef.id_extend_field and efl.lang='".Settings::get_lang('default')."'
			inner join extend_field_type eft on eft.id_extend_field_type = ef.type
			where uie.id_ui_element = ".$id_ui_element."
			order by uie.ordering ASC
		";

		$query = $this->{$this->db_group}->query($sql);
		if ($query->num_rows() > 0)	$extends = $query->result_array();

		return $extends;
	}


	public function link_extend_to_element($id_extend, $id_ui_element)
	{
		$data = array(
			'id_extend' => $id_extend,
			'id_ui_element' => $id_ui_element,
		);

		parent::insert_ignore($data, self::$_TBL_LK_EXTEND);
	}


	public function unlink_extend_from_element($id_extend, $id_ui_element)
	{
		$where = array(
			'id_extend' => $id_extend,
			'id_ui_element' => $id_ui_element,
		);

		parent::delete($where, self::$_TBL_LK_EXTEND);
	}


	public function save_element_fields_ordering($id_ui_element, $ordering)
	{
		while (list ($rank, $id) = each ($ordering))
		{
			$this->{$this->db_group}->where(
				array(
					'id_ui_element' => $id_ui_element,
					'id_extend' => $id,
				)
			);
			$this->{$this->db_group}->set('ordering', $rank+1);
			$this->{$this->db_group}->update(self::$_TBL_LK_EXTEND);

		}
		return TRUE;
	}






}