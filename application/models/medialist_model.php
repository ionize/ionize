<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Medialist Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Media management
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


	public function get_list($where = NULL, $table = NULL)
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

		$this->{$this->db_group}->group_by($this->table.'.id_media');
		$this->{$this->db_group}->order_by($this->table.'.id_media', 'DESC');

		return parent::get_list($where, $this->table);
	}


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
