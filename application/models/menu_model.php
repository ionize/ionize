<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 */

// ------------------------------------------------------------------------

/**
 * Ionize Menu Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Menu
 * @author		Ionize Dev Team
 *
 */

class Menu_model extends Base_model 
{

	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		
		$this->table =		'menu';
		$this->pk_name = 	'id_menu';
	}


	// ------------------------------------------------------------------------


	function get_select()
	{
 		$data = array();
 		
 		$this->{$this->db_group}->order_by('ordering', 'ASC');
			
		$query = $this->{$this->db_group}->get($this->table);

		if($query->num_rows() > 0)
		{
			$menus = $query->result_array();
			
			foreach($menus as $menu)
			{
				$data[$menu['id_menu']] = $menu['title'];
			}
		}			

		return $data;
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Get one menu array from a givven page
	 * @param	int		Page ID
	 * @returns	array	The menu or an empty array
	 *
	 */
	function get_from_page($id_page)
	{
		$data = array();
		
		$this->{$this->db_group}->join('page', $this->table.'.id_menu = page.id_menu', 'left');
		$this->{$this->db_group}->select('menu.*');
		$this->{$this->db_group}->where('page.id_page', $id_page);
		
		$query = $this->{$this->db_group}->get($this->table);
		
		if($query->num_rows() > 0)
		{
			$data = $query->row_array();
		}
		
		return $data;
	}
	
}
/* End of file menu_model.php */
/* Location: ./application/models/menu_model.php */