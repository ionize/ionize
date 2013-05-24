<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Extend Table Controller
 * Draft, not implemented
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.6
 */

class Extend_table extends MY_admin
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('extend_table_model', '', true);
	}
	
	
	/**
	 * Display the extend table field form.
	 * 
	 * @param	String		Table name to which add one extend field
	 *
	 */
	function add($table = FALSE)
	{
		$this->template = array
		(
			'table' => $table,
			'name' => '',
			'type' => '',
			'default' => '',
			'description' => '',
			'constraint' => '',
			'unsigned' => '',
			'auto_increment' => ''
		);

		$this->output('extend/table_field');
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves one extend field to the extend table
	 *
	 * @param	String		Parent table name
	 * @return 	Array		Array of table extend fields
	 *
	 */
	function save_extend()
	{
		$table = $this->input->post('table');
		
		$type = $this->input->post('type');
		
		// Dependencies : Exit if not OK
		if ( ! $this->input->post('name'))
			$this->error(lang('ionize_message_field_must_have_a_name'));

		if (($this->input->post('type') == 'VARCHAR' OR $this->input->post('type') == 'INT' ) && ! $this->input->post('constraint'))
			$this->error(lang('ionize_message_varchar_int_must_have_length'));
		
		// Check the name regarding the reserved words
		$_sql_reserved = array();
		require_once(APPPATH.'config/sql_reserved.php');
		if (in_array(strtoupper($this->input->post('name')), $_sql_reserved))
			$this->error(lang('ionize_message_field_name_sql_reserved'));
		
		// Field
		$field_details = array
		(
			'type' => $this->input->post('type')
		);
		
		// UNSIGNED
		if ($this->input->post('type') == 'INT' && $this->input->post('unsigned'))
			$field_details['unsigned'] = TRUE;
		
		// AUTO INCREMENT
		if ($this->input->post('type') == 'INT' && $this->input->post('auto_increment'))
			$field_details['auto_increment'] = TRUE;

		// CONSTRAINT
		if ($this->input->post('type') == 'INT' || $this->input->post('type') == 'VARCHAR')
			$field_details['constraint'] = $this->input->post('constraint');
		
		// NULL
		if ($this->input->post('null'))
			$field_details['null'] = TRUE;

		$field = array($this->input->post('name') => $field_details);	
		

		$this->extend_table_model->save_extend_field($table, $field);
		
		/*
		 * JSON Update array
		 * Update of the Table Extends list
		 *
		 */
		$this->update[] = array(
			'element' => 'extend_table',
			'url' =>  'extend_table/get_extend_fields_list/'.$table
		);

		
		$this->success(lang('ionize_message_extend_field_saved'));
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Edit one extend table field.
	 * 
	 * @param	String		Table name to which add one extend field
	 * @param	String		Field to edit
	 *
	 */
	function edit($table = FALSE, $field = FALSE)
	{
		$field = $this->extend_table_model->get_extend_table_field($field, $table);
		
		if ($field)
		{
			$this->template = array
			(
				'table' => $table,
				'name' => $field->name,
				'type' => $field->type,
				'default' => $field->default,
				'description' => '',
				'constraint' => '',
				'unsigned' => '',
				'auto_increment' => ''
			);
		}
		else
		{		
			$this->template = array
			(
				'table' => $table,
				'name' => '',
				'type' => '',
				'default' => '',
				'description' => '',
				'constraint' => '',
				'unsigned' => '',
				'auto_increment' => ''
			);
		}
	
		$this->output('extend/table_field');
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Delete one extend table field.
	 * 
	 * @param	String		Table name to which remove one extend field
	 * @param	String		Field to remove
	 *
	 */
	function delete($table = FALSE, $field = FALSE)
	{
		
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns The extend fields list from a given table
	 *
	 * @param	String		Parent table name
	 * @return	String		HTML table of extended fields
	 *						See /themes/admin/extend_table_fields_list.php for output view	
	 *
	 */
	public function get_extend_fields_list($table = FALSE)
	{
		if ($table !== FALSE)
		{
			$this->template['extends'] = $this->extend_table_model->get_extend_table_fields($table);
			$this->template['table'] = $table;

	    	$this->output('extend/table_fields_list');
	    }
	}
	
	
	
	
	
}