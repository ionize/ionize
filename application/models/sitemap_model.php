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

		$sql = "
			select
				IF(url !='', url, name ) AS url,
				url.path,
				created, updated, publish_on, logical_date,
				page_lang.lang, priority
			from
				page
				left join page_lang on page_lang.id_page = page.id_page
				left join url on (url.id_entity = page_lang.id_page and url.lang = page_lang.lang)
			where
			 	appears = '1'
			 	and page.online = '1'
			 	and page_lang.online = '1'
			 	and url.type = 'page'
		";

		if ($lang)
			$sql .= " and page_lang.lang = '".$lang."'";

		$query = $this->{$this->db_group}->query($sql);

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		$query->free_result();
		
		return $data;
	}
	
	
}
/* End of file sitemap_model.php */
/* Location: ./application/models/sitemap_model.php */