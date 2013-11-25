<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

/**
 * Ionize Category Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Category
 * @author		Ionize Dev Team
 *
 */
class Category_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'category';
		$this->pk_name 	=	'id_category';
		$this->lang_table = 'category_lang';
	}


	// ------------------------------------------------------------------------


	/** 
	 * Gets category list as array (id => name)
	 *
	 * @return	array
	 *
	 */
	public function get_categories_select()
	{
		return $this->get_items_select($this->table, 'name', NULL, NULL, lang('ionize_select_no_category'), 'ordering ASC');
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current categories from parent element
	 *
	 * @param	string	parent name
	 * @param	int		parent ID
	 *
	 * @return array
	 *
	 */
	public function get_current_categories($parent, $parent_id)
	{
		return $this->get_joined_items_keys($this->table, $parent, $parent_id);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns categories list
	 * If id_page is set, returns those which are used by the page articles.
	 * If id_page is not set, return the whole used categories
	 *
	 * @param null $id_page
	 *
	 * @param null $lang
	 *
	 * @return array
	 *
	 */
	public function get_categories_list($id_page=NULL, $lang=NULL)
	{
		$this->{$this->db_group}->select('category.name, category_lang.*, count(1) as nb', FALSE);

		$this->{$this->db_group}->join(
			'page_article',
			'page_article.id_article = article.id_article',
			'inner'
		);
		$this->{$this->db_group}->join(
			'article_category',
			'article_category.id_article = page_article.id_article',
			'inner'
		);
		$this->{$this->db_group}->join(
			'category',
			'category.id_category = article_category.id_category',
			'inner'
		);
		$this->{$this->db_group}->join(
			'category_lang',
			'category_lang.id_category = category.id_category',
			'left'
		);

		// Filter on published
		$this->_filter_on_published(self::$publish_filter, $lang);

		if ( ! is_null($id_page))
			$this->{$this->db_group}->where('page_article.id_page', $id_page);

		$this->{$this->db_group}->where('category_lang.lang', $lang);

		$this->{$this->db_group}->group_by('category.id_category');
		$this->{$this->db_group}->order_by('category.ordering');

		$data = array();

		$query = $this->{$this->db_group}->get('article');

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Filters the article  on published one
	 *
	 * @param bool $on
	 * @param null $lang
	 *
	 */
	protected function _filter_on_published($on = TRUE, $lang = NULL)
	{
		if ($on === TRUE)
		{
			$this->{$this->db_group}->join(
				'article_lang',
				'article_lang.id_article = article.id_article',
				'left'
			);

			if ( ! is_null($lang))
				$this->{$this->db_group}->where('article_lang.lang', $lang);

			$this->{$this->db_group}->where('page_article.online', '1');

			if ($lang !== NULL && count(Settings::get_online_languages()) > 1)
				$this->{$this->db_group}->where('article_lang.online', '1');

			$this->{$this->db_group}->where('((article.publish_off > ', 'now()', FALSE);
			$this->{$this->db_group}->or_where('article.publish_off = ', '0)' , FALSE);

			$this->{$this->db_group}->where('(article.publish_on < ', 'now()', FALSE);
			$this->{$this->db_group}->or_where('article.publish_on = ', '0))' , FALSE);
		}
	}
}
