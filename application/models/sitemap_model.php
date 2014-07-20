<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

// ------------------------------------------------------------------------

/**
 * Ionize Sitemap Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Settings
 * @author		Ionize Dev Team
 *
 */

class Sitemap_model extends Base_model 
{
	/**
	 * Page Articles Context table
	 * @var string
	 */
	public $context_table =	'page_article';


	// ------------------------------------------------------------------------


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


	// ------------------------------------------------------------------------


	/**
	 * @param bool $lang
	 *
	 * @return array
	 */
	public function get_pages($lang = FALSE)
	{
		$data = array();

		$sql = "
			select
				home,
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

// log_message('app', print_r($sql, TRUE));

		$query = $this->{$this->db_group}->query($sql);

		if ( $query->num_rows() > 0 )
			$data = $query->result_array();

		$query->free_result();
		
		return $data;
	}


	public function get_urls()
	{
		$get_all_lang = FALSE;

		$langs = Settings::get_online_languages();

		if (Settings::get('force_lang_urls') OR count($langs) > 1)
			$get_all_lang = TRUE;

		$sql ="
			select
				u.lang,
				u.path,
				p.priority,
				p.created,
				p.updated,
				p.publish_on,
				p.publish_off,
				p.logical_date
			from url u
			inner join page p on p.id_page = u.id_entity and p.has_url = 1
			where
				u.type = 'page'
				and u.active = 1
				and u.canonical = 1
				and p.priority > 0
				and (p.publish_off = '0000-00-00 00:00:00' OR p.publish_off > now())
		";

		if ( ! $get_all_lang)
			$sql .= "
				and u.lang='".Settings::get_lang('default')."'
			";

		$sql .="
			union

			select
				u.lang,
				u.path,
				a.priority,
				a.created,
				a.updated,
				a.publish_on,
				a.publish_off,
				a.logical_date
			from url u
			inner join article_lang al on al.id_article= u.id_entity and al.lang=u.lang and al.online=1
			inner join article a on a.id_article = al.id_article
			where
				u.type = 'article'
				and u.active = 1
				and u.canonical = 1
				and a.indexed = 1
				and a.priority > 0
				and (a.publish_off = '0000-00-00 00:00:00' OR a.publish_off > now())
		";

		if ( ! $get_all_lang)
			$sql .= "
				and u.lang='".Settings::get_lang('default')."'
			";

		$query = $this->{$this->db_group}->query($sql);

		$data = $query->result_array();

		return $data;
	}
}
