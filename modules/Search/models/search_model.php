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
 * Ionize Search Model
 *
 * @author		Guido Smit / DevInet
 *
 */

class Search_model extends Base_model 
{
	/**
	 * Get all the article_lang data + the page URL of the current language (page_url in the output array)
	 *
	 * @param	string	String to search
	 * @return	array	Array of articles
	 *
	 */
	function get_articles($realm)
	{
		$realm = '%'.$realm.'%';
	
		$sql = "	SELECT article_lang.* , page_lang.url as page_url, page_lang.title as page_title
					FROM (article)
					JOIN article_lang ON article.id_article = article_lang.id_article
					JOIN page ON page.id_page = article.id_page
					JOIN page_lang ON page_lang.id_page = page.id_page
					WHERE article_lang.online = 1
					AND article_lang.lang = ?
					AND page_lang.lang = ?
					AND (
						article_lang.title LIKE ?
						OR article_lang.subtitle LIKE ?
						OR article_lang.summary LIKE ?
						OR article_lang.content LIKE ?
					)
		";

		// Current language
		$lang = Settings::get_lang();
		
		$query = $this->db->query($sql, array($lang, $lang, $realm, $realm, $realm, $realm));
		
		return $query->result_array();
	}
}