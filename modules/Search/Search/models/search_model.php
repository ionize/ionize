<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 1.02
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

	public $article_table =				'article';
	public $article_lang_table =		'article_lang';
	public $page_table =				'page';
	public $page_lang_table =			'page_lang';
	public $parent_table =				'page_article';
	public $url_table =					'url';
	public $menu_table = 				'menu';


	/**
	 * Get all the article_lang data + the page URL of the current language (page_url in the output array)
	 *
	 * @param	string	String to search
	 * @return	array	Array of articles
	 *
	 */
	function get_articles($realm)
	{
		$realm = '\'%'.$realm.'%\'';

		$this->set_table('article');
		$this->set_lang_table('article_lang');
		$this->set_pk_name('id_article');

		$lang = Settings::get_lang();

		
		// Page_Article table
		$this->{$this->db_group}->select($this->parent_table.'.*', FALSE);
		$this->{$this->db_group}->join(
			$this->parent_table,
			$this->parent_table.'.id_article = ' .$this->table.'.id_article',
			'left'
		);

		// Page table
		$this->{$this->db_group}->select('page.online');
		$this->{$this->db_group}->join(
			$this->page_table,
			$this->page_table.'.id_page = ' .$this->parent_table.'.id_page',
			'left'
		);

		// Page lang table
		$this->{$this->db_group}->select('page_lang.lang');
		$this->{$this->db_group}->join(
			$this->page_lang_table,
			$this->page_lang_table.'.id_page = ' .$this->page_table.'.id_page',
			'left'
		);

		// Menu table
		$this->{$this->db_group}->select('menu.id_menu, menu.name as menu_name');
		$this->{$this->db_group}->join(
			$this->menu_table,
			$this->menu_table.'.id_menu = ' .$this->page_table.'.id_menu',
			'left'
		);

		// URL table : For Article's URL building
		$this->{$this->db_group}->select('url.path');
		$this->{$this->db_group}->join(
			$this->url_table,
			$this->url_table.'.id_entity = ' .$this->table.'.id_article'.
				' AND ' . $this->url_table. '.active=1 '.
				' AND ' . $this->url_table. '.lang = \'' . $lang . '\'',
			'left'
		);

		// Published filter
		$this->filter_on_published(self::$publish_filter, $lang);

		// Add the 'date' field to the query
		$this->{$this->db_group}->select('IF(article.logical_date !=0, article.logical_date, IF(article.publish_on !=0, article.publish_on, article.created )) AS date');

		// Search where
		$this->{$this->db_group}->where
		(
			'('.
			' article_lang.title LIKE ' . $realm .
			' OR article_lang.subtitle LIKE '. $realm .
			' OR article_lang.content LIKE ' . $realm .
			')'
		);

		$where = array(
			"page.online" => 1,
			"article.indexed" => 1,
			"page_article.online" => 1,
			"page_article.main_parent" => 1,
			"article_lang.online" => 1,
			"article_lang.lang" => $lang,
			"page_lang.lang" => $lang,
		);

		// Base_model->get_lang_list()
		$articles =  parent::get_lang_list($where, $lang);

		return $articles;
	}


	/**
	 * Adds Published filtering on articles get_lang_list() call
	 *
	 * @param bool
	 * @param null
	 *
	 */
	protected function filter_on_published($on = TRUE, $lang = NULL)
	{
		if ($on === TRUE)
		{
			$this->{$this->db_group}->where($this->parent_table.'.online', '1');

			if ($lang !== NULL && count(Settings::get_online_languages()) > 1)
				$this->{$this->db_group}->where($this->lang_table.'.online', '1');

			$this->{$this->db_group}->where('((article.publish_off > ', 'now()', FALSE);
			$this->{$this->db_group}->or_where('article.publish_off = ', '0)' , FALSE);

			$this->{$this->db_group}->where('(article.publish_on < ', 'now()', FALSE);
			$this->{$this->db_group}->or_where('article.publish_on = ', '0))' , FALSE);
		}
	}

}