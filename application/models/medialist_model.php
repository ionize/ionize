<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Medialist Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Media
 * @author		Ionize Dev Team
 *
 */
class Medialist_model extends Base_model
{

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'media';
		$this->pk_name = 	'id_media';
		$this->lang_table = 'media_lang';

		log_message('debug', __CLASS__ . " Class Initialized");
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns enhanced media list
	 *
	 * @param null  $where
	 * @param array $filters
	 * @param bool  $addon_data
	 *
	 * @return array
	 */
	public function get_list($where = NULL, $filters = array(), $addon_data = TRUE)
	{
		// Pages
		$this->{$this->db_group}->select("group_concat(url.path separator ';') as page_paths");
		$this->{$this->db_group}->join(
			'page_media',
			'page_media.id_media = ' . $this->table.'.id_media',
			'left'
		);
		$this->{$this->db_group}->join(
			'url',
			"url.id_entity = page_media.id_page AND url.type='page' and url.active=1 and url.lang='".Settings::get_lang('default')."'",
			'left'
		);

		// Articles
		$this->{$this->db_group}->select("group_concat(url2.path separator ';') as article_paths");
		$this->{$this->db_group}->join(
			'article_media',
			'article_media.id_media = ' . $this->table.'.id_media',
			'left'
		);
		$this->{$this->db_group}->join(
			'url as url2',
			"url2.id_entity = article_media.id_article AND url2.type='article' and url2.active=1 and url2.lang='".Settings::get_lang('default')."'",
			'left'
		);

		// Filters
		if (in_array('alt_missing', $filters))
		{
			$this->{$this->db_group}->join(
				'media_lang',
				'media_lang.id_media = ' . $this->table.'.id_media',
				'left'
			);
			$this->{$this->db_group}->where("(media_lang.alt is null or media_lang.alt='')");
		}

		if (in_array('used', $filters))
		{
			$this->{$this->db_group}->where("(url.path is not null or url2.path is not null)");
		}

		if (in_array('not_used', $filters))
		{
			$this->{$this->db_group}->where("(url.path is null and url2.path is null)");
		}

		$this->{$this->db_group}->group_by($this->table.'.id_media');
		$this->{$this->db_group}->order_by($this->table.'.id_media', 'DESC');

		$result = parent::get_list($where, $this->table);

		if ($addon_data)
			$result = $this->_add_lang_data_to_media_list($result);

		return $result;
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds info about each media
	 *
	 * @param $medias
	 *
	 * @return mixed
	 */
	private function _add_lang_data_to_media_list($medias)
	{
		if ( ! empty($medias))
		{
			$media_ids = array();
			foreach($medias as $media)
			{
				$media_ids[] = $media['id_media'];
			}

			// Fields of media_lang
			$media_lang_fields = $this->list_fields('media_lang');

			// Get media lang data
			$this->{$this->db_group}->where_in('id_media', $media_ids);
			$query = $this->{$this->db_group}->get('media_lang');

			$medias_lang = array();
			if ( $query->num_rows() > 0 )
				$medias_lang = $query->result_array();

			// Enrich each media
			foreach($medias as &$media)
			{
				$media['alt_missing'] = FALSE;
				$media['is_used'] = TRUE;
				$media['has_source'] = (empty($media['provider'])) ? file_exists(DOCPATH.$media['path']) : TRUE;
				$media['lang'] = array();

				// Is linked to page or article
				if (empty($media['page_paths']) && empty($media['article_paths']))
					$media['is_used'] = FALSE;

				foreach(Settings::get_languages() as $lang)
				{
					$media['lang'][$lang['lang']] = array_fill_keys($media_lang_fields, '');
					$media['lang'][$lang['lang']]['id_media'] = $media['id_media'];
				}

				foreach($medias_lang as $media_lang)
				{
					if ($media_lang['id_media'] == $media['id_media'])
					{
						$media['lang'][$media_lang['lang']] = $media_lang;
					}
				}

				// Alt missing
				foreach(Settings::get_languages() as $lang)
				{
					if (empty($media['lang'][$lang['lang']]['alt']))
						$media['alt_missing'] = TRUE;
				}
			}
		}

		return $medias;
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves media meta data
	 *
	 * @param $post
	 *
	 * @return int|void
	 */
	public function save($post)
	{
		foreach($post as $key => $media)
		{
			if (substr($key,0,5) == 'media')
			{
				$data_lang = $media['lang'];
				parent::save($media, $data_lang);
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Remove one media
	 * Unlink it from all content
	 * Does not remove the source file
	 *
	 * @param $id_media
	 */
	public function remove($id_media)
	{
		$where = array(
			'id_media' => $id_media
		);

		parent::delete($where, 'media');
		parent::delete($where, 'media_lang');
		parent::delete($where, 'page_media');
		parent::delete($where, 'article_media');
	}
}
