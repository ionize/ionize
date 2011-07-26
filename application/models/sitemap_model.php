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
	
	public function get_pages()
	{
		$data = array();
		
		$this->db->select('IF(url !=\'\', url, name ) AS url');
		$this->db->select('created, updated, publish_on, logical_date');
		$this->db->select('lang, priority');

		$this->db->where(array(
			'appears' => '1',
			'page.online' =>'1',
			'page_lang.online' =>'1'
		));
		
		$this->db->join('page_lang', 'page.id_page = page_lang.id_page');
		
		$query = $this->db->get('page');

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		$query->free_result();
		
		return $data;
	}
	
	
}
/* End of file sitemap_model.php */
/* Location: ./application/models/sitemap_model.php */