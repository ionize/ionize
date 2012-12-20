<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

// ------------------------------------------------------------------------

/**
 * Ionize Sitemap Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Admin settings
 * @author		Ionize Dev Team
 *
 */

class Sitemap_model extends Base_model 
{

	public $context_table =	'page_article';


	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('page');
		$this->set_pk_name('id_page');
		$this->set_lang_table('page_lang');
		
	}
	
	public function get_pages($lang = FALSE)
	{
		$data = array();
		
		$this->{$this->db_group}->select('IF(url !=\'\', url, name ) AS url');
		$this->{$this->db_group}->select('created, updated, publish_on, logical_date');
		$this->{$this->db_group}->select('lang, priority');

		$this->{$this->db_group}->where(array(
			'appears' => '1',
			'page.online' =>'1',
			'page_lang.online' =>'1'
		));
		
		(!empty($lang)) ? $this->{$this->db_group}->where(array('page_lang.lang' => $lang)) : '';
		
		$this->{$this->db_group}->join('page_lang', 'page.id_page = page_lang.id_page');
		
		$query = $this->{$this->db_group}->get('page');

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		$query->free_result();
		
		return $data;
	}
	
	
}
/* End of file sitemap_model.php */
/* Location: ./application/models/sitemap_model.php */