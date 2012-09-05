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
	
		$sql = "	SELECT 	distinct article_lang.title, article_lang.content, article_lang.url,
							IF(article.logical_date !=0, article.logical_date, IF(article.publish_on !=0, article.publish_on, article.created )) AS date,
							page_lang.url as page_url, 
							page_lang.title as page_title,
							url.path
					FROM (article)
						JOIN article_lang ON article.id_article = article_lang.id_article
						JOIN page_article ON article.id_article = page_article.id_article
						JOIN page ON page.id_page = page_article.id_page
						JOIN page_lang ON page_lang.id_page = page.id_page
						LEFT JOIN url ON (
							url.id_entity = article_lang.id_article
							AND url.type='article'
							AND url.active = 1
							AND url.lang= ?
						)
					WHERE 
						page.online = 1
						AND article.indexed = 1
						AND page_article.online = 1
						AND page_article.main_parent = 1
						AND article_lang.online = 1
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
		
		$query = $this->db->query($sql, array($lang, $lang, $lang, $realm, $realm, $realm, $realm));
// trace($this->db->last_query());
		return $query->result_array();
	}
}