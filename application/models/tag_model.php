<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize, creative CMS Page Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Tags
 * @author		Ionize Dev Team
 *
 */

class Tag_model extends Base_model 
{
	/**
	 * Max tag length
	 * @var int
	 */
	private static $_MAX_TAG_LENGTH = 50;


	// ------------------------------------------------------------------------


	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('tag');
		$this->set_pk_name('id_tag');
	}


	// ------------------------------------------------------------------------


	/**
	 * @param null $parent
	 * @param null $id_parent
	 *
	 * @return array|void
	 *
	 */
	public function get_list($parent=NULL, $id_parent=NULL)
	{
		// Get tags from one parent
		if ( ! is_null($parent) && $this->table_exists($parent.'_tag'))
		{
			$this->{$this->db_group}->join(
				$parent.'_tag',
				$parent.'_tag.id_tag = ' . $this->get_table() .'.id_tag and ' .
					$parent.'_tag.id_'.$parent . ' = '.  $id_parent,
				'inner'
			);

		}

		return parent::get_list(array('order_by' => 'tag_name ASC'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns tags list
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
	public function get_page_articles_list($id_page=NULL, $lang=NULL)
	{
		$this->{$this->db_group}->select(
			'tag.id_tag, tag.tag_name, tag.tag_name as title, count(1) as nb'
			, FALSE
		);

		$this->{$this->db_group}->join(
			'page_article',
			'page_article.id_article = article.id_article',
			'inner'
		);
		$this->{$this->db_group}->join(
			'article_tag',
			'article_tag.id_article = page_article.id_article',
			'inner'
		);
		$this->{$this->db_group}->join(
			'tag',
			'tag.id_tag = article_tag.id_tag',
			'inner'
		);

		// Filter on published
		$this->_filter_on_published(self::$publish_filter, $lang);

		if ( ! is_null($id_page))
			$this->{$this->db_group}->where('page_article.id_page', $id_page);

		$this->{$this->db_group}->group_by('tag.id_tag');

		$data = array();

		$query = $this->{$this->db_group}->get('article');

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $tag
	 *
	 * @return bool|int|the
	 */
	public function save($tag)
	{
		$id_tag = $this->tag_exists($tag);

		if ( ! $id_tag)
		{
			$tag = trim($tag);
			return $this->insert(array('tag_name' => $tag));
		}
		return $id_tag;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_tag
	 *
	 * @return bool|int|the
	 */
	public function delete_all($id_tag)
	{
		$affected_rows = parent::delete(array('id_tag' => $id_tag));

		$this->{$this->db_group}->where('id_tag', $id_tag);
		$this->{$this->db_group}->delete('article_tag');

		return $affected_rows;
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves all provided tags
	 * Deletes the others.
	 *
	 * @param $tags
	 *
	 */
	public function save_tag_list($tags)
	{
		$tags = explode(',', $tags);

		$tag_ids = $this->_get_tags_ids_array();

		foreach($tags as $tag)
		{
			// New tag ? Add it !
			if( FALSE == preg_match( '/^\d*$/'  , $tag))
			{
				if (strlen($tag) > self::$_MAX_TAG_LENGTH)
					continue;

				$id_tag = $this->save($tag);

				if ( in_array($id_tag, $tag_ids))
					$tag_ids = array_diff($tag_ids, array($id_tag));
			}
			else
			{
				// Keep existing tags : Remove them from $tag_ids array;
				if ( in_array($tag, $tag_ids))
					$tag_ids = array_diff($tag_ids, array($tag));
			}
		}

		// Delete remaining ids : they are not in saved list anymore
		if ( ! empty($tag_ids))
		{
			$this->{$this->db_group}->where_in('id_tag', $tag_ids);
			$this->{$this->db_group}->delete($this->get_table());
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Save tags linked to a parent element
	 * 
	 * @param	string		Tags as string (coma separated or ; separated, depending on what the user inputs...)
	 * @param	string		Parent type. Can be 'article, 'page', etc.
	 * @param	int			Parent ID
	 *
	 */
	public function save_element_tags($tags, $element, $id_element)
	{
		if ($element && $id_element)
		{
			$data = array();
			$tag_ids = array_filter(explode(',', $tags), 'strlen');
			$join_table = $element.'_tag';
			$element_pk = 'id_'.$element;

			foreach($tag_ids as $id_tag)
			{
				// New tag ? Add it !
				if( FALSE == preg_match( '/^\d*$/'  , $id_tag))
				{
					if (strlen($id_tag) > self::$_MAX_TAG_LENGTH)
						continue;

					$id_tag = $this->save($id_tag);
				}

				$data[] = array(
					'id_tag' => $id_tag,
					$element_pk => $id_element
				);
			}

			if ( ! empty($data) && $this->table_exists($join_table))
			{
				$this->delete(array($element_pk => $id_element), $join_table);

				$this->{$this->db_group}->insert_batch($join_table, $data);
			}
		}
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


	// ------------------------------------------------------------------------


	/**
	 * Returns the tags IDs, in one array
	 *
	 * @return array
	 */
	private function _get_tags_ids_array()
	{
		$data = array();

		$sql = "select group_concat(id_tag) as ids from " . $this->get_table();

		$query = $this->{$this->db_group}->query($sql);

		if ( $query->num_rows() > 0)
		{
			$result = $query->row_array();
			if ( ! is_null($result['ids']))
			{
				$data = explode(',', $result['ids']);
			}
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $tag
	 *
	 * @return bool
	 */
	public function tag_exists($tag)
	{
		$sql = "select id_tag from ". $this->get_table() . " where LOWER(tag_name) = " . $this->{$this->db_group}->escape($tag);

		$query = $this->{$this->db_group}->query($sql);

		if ( $query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result['id_tag'];
		}

		return FALSE;
	}
}
