<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* RSS model
*
* The model that handles actions 
* related to RSS feed generating.
*
* @author	Ionize Dev Team
*/
class Rss_model extends Article_model 
{
	/**
	* Constructor
	*
	* @access	public
	* @return	void
	*/
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get the articles
	 *
	 * The "date" field is added by the article_lmodel get_lang_list method
	 * as well as the "published" filter
	 *
	 */
	function get_articles($id_pages, $lang)
	{
		// Only include "indexed" articles. Means real "content" article.
		$this->db->where($this->table.'.indexed', '1');

		// Add the ID pages filter to the query
		$this->db->where_in('page_article.id_page', $id_pages);

		$data = parent::get_lang_list(FALSE, $lang);
		
		return $data;
	}
	
}

/* End of file rss_model.php */
/* Location: /modules/RSS/models/rss_model.php */
