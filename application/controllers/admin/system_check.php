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
 * Ionize System Check Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	System
 * @author		Ionize Dev Team
 *
 */

class System_check extends MY_admin
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model('system_check_model', '', true);
		$this->load->model('menu_model', '', true);
		$this->load->model('page_model', '', true);
		$this->load->model('article_model', '', true);
		$this->load->model('config_model', '', true);
		$this->load->model('url_model', '', true);
		
		// Libraries
		$this->load->library('structure');
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/** 
	 * Displays the System Diagnostic Panel
	 * @param	string		Menu ID
	 *
	 */
	function index() 
	{
		$this->output('system_check');
	}
	
	
	/**
	 * Called through XHR
	 * Launches the checks
	 *
	 */
	function start_check()
	{
		// Start the first check : pages levels
		$this->callback = array
		(
			// Clear the report ODM container
			array (
				'fn' => 'ION.emptyDomElement',
				'args' => array	('system_check_report')
			),
			// Call the first check
			array (
				'fn' => 'ION.JSON',
				'args' => array	(
					'system_check/check_folder_right'
				)
			)
		);

		$this->response();
	}
	

	/**
	 * Checks write rights on Ionize's used folders
	 *
	 */
	function check_folder_right()
	{
		$results = array();
		
		$folders = array(
			'cache_path' => (config_item('cache_path') == '') ? FCPATH.'cache/' : config_item('cache_path'),
			'files_path' => (config_item('files_path') == '') ? FCPATH.'files/' : config_item('files_path'),
			'theme_path' => FCPATH.'themes/'.Settings::get('theme')
		);

		foreach($folders as $folder)
		{
			$result = array(
				'title' => lang('ionize_title_check_folder').' : '.$folder,
				'result_status' => 'success',
				'result_text' => lang('ionize_message_check_ok')
			);
			
			if ( ! is_dir($folder) OR ! is_really_writable($folder))
			{
				$result['result_status'] = 'error';
				$result['result_text'] = lang('ionize_message_check_folder_nok');
			}
			
			$results[] = $result;
		}
		
		// Result view
		$view = '';
		foreach($results as $result)
		{
			$view .= $this->load->view('system_check_result', $result, TRUE);
		}


		$this->callback = array(
			array (
				'fn' => 'ION.appendDomElement',
				'args' => array	(
					'system_check_report',
					$view
				)
			),
			array (
				'fn' => 'ION.JSON',
				'args' => array	(
					'system_check/check_page_level'
				)
			)
		);
		
		$this->response();
	}


	/**
	 * Check if all langs defined in DB are set in the config file
	 *
	 */
	function check_lang()
	{
		$result = array(
			'title' => lang('ionize_title_check_lang'),
			'result_status' => 'success',
			'result_text' => lang('ionize_message_check_ok')
		);
		
		// Get the languages : DB + config/language.php
		$db_languages = Settings::get_languages();
		$config_languages = config_item('lang_uri_abbr');
		
		// Check differences between DB and config/language.php file
		$result_status = TRUE;
		
		foreach($db_languages as $lang)
		{
			if ( ! array_key_exists($lang['lang'], $config_languages))
			{
				$result_status = FALSE;
			}
		}
		
		// Correct if needed
		if ($result_status == FALSE)
		{
			// Default language
			$def_lang = '';
			
			// Available languages array
			$lang_uri_abbr = array();
			
			foreach($db_languages as $l)
			{
				// Set default lang code
				if ($l['def'] == '1')
					$def_lang = $l['lang'];
				
				$lang_uri_abbr[$l['lang']] = $l['name'];
			}

			$this->config_model->change('language.php', 'language_abbr', $def_lang);

			if ( ! empty($lang_uri_abbr))
			{
				$this->config_model->change('language.php', 'lang_uri_abbr', $lang_uri_abbr);
			}

			$result['result_text'] = lang('ionize_message_check_corrected');
			
		}
		
		// Result view
		$view = $this->load->view('system_check_result', $result, TRUE);
		
		$this->callback = array(
			array (
				'fn' => 'ION.appendDomElement',
				'args' => array	(
					'system_check_report',
					$view
				)
			)
			,
			array (
				'fn' => 'ION.JSON',
				'args' => array	(
					'system_check/check_page_level'
				)
			)
		);

		$this->response();
	}

	
	/**
	 * Check page level integrity
	 * Checks the page level inegrity, correct and chains the next check : article's contexts
	 *
	 */
	function check_page_level()
	{
		$result = array(
			'title' => lang('ionize_title_check_page_level'),
			'result_status' => 'success'
		);

		$nb_wrong_levels = $this->system_check_model->check_page_level();
		
		// Correct
		if ($nb_wrong_levels > 0)
		{
			$corrected = $this->system_check_model->check_page_level($correct = TRUE);
		
			$result['result_text'] = $nb_wrong_levels .'/'. $corrected . lang('ionize_message_check_corrected');
		}
		else
		{
			$result['result_text'] = lang('ionize_message_check_ok');
		}
		
		// Result view
		$view = $this->load->view('system_check_result', $result, TRUE);

		$this->callback = array(
			array (
				'fn' => 'ION.appendDomElement',
				'args' => array	(
					'system_check_report',
					$view
				)
			),
			array (
				'fn' => 'ION.JSON',
				'args' => array	(
					'system_check/check_article_context'
				)
			)			
		);
		
		$this->response();

	}
	
	
	/**
	 * Checks if all articles which have one page context have the page as "Main Parent"
	 * End of check. 
	 *
	 */
	function check_article_context()
	{
		$result = array(
			'title' => lang('ionize_title_check_article_context'),
			'result_status' => 'success'
		);

		$nb_orphan_articles = $this->system_check_model->check_article_context();

		// Correct
		if ($nb_orphan_articles > 0)
			$result['result_text'] = lang('ionize_message_check_corrected');
		else
			$result['result_text'] = lang('ionize_message_check_ok');


		// Result view
		$view = $this->load->view('system_check_result', $result, TRUE);


		$this->callback = array(
			array (
				'fn' => 'ION.appendDomElement',
				'args' => array	(
					'system_check_report',
					$view
				)
			),
			array (
				'fn' => 'ION.JSON',
				'args' => array	(
					'system_check/rebuild_pages_urls'
				)
			)			

			/* TODO : Add this correction in another "system tools block"
			array (
				'fn' => 'ION.JSON',
				'args' => array	(
					'system_check/check_views'
				)
			)
			*/			
		);
		
		$this->response();
	}
	
	
	/**
	 * Checks views of both pages and articles
	 *
	 */
	function check_views()
	{
		$nb = 0;
		
		$result = array(
			'title' => lang('ionize_title_check_views'),
			'result_status' => 'success'
		);
		
		$views_folder = (FCPATH.'themes/'.Settings::get('theme').'/views/');
		
		// Check and correct page's views
		$pages = $this->page_model->get_list();
		
		foreach($pages as $page)
		{
			if ( ! empty($page['view']) &&  ! is_file($views_folder.$page['view'].EXT))
			{
				$this->db->set('view', '');
				$this->db->where('id_page', $page['id_page']);
				$nb += $this->db->update('page');
			}
		}
		
		// Check and correct article's views
		$article_contexts = $this->article_model->get_all_context();
		
		foreach($article_contexts as $context)
		{
			if ( ! empty($context['view']) && ! is_file($views_folder.$page['view'].EXT))
			{
				$this->db->set('view', '');
				$this->db->where(array(
					'id_page' => $context['id_page'],
					'id_article' => $context['id_article']
				));
				$nb += $this->db->update('page_article');
			}
		}
		
		// Correct
		if ($nb > 0)
			$result['result_text'] = lang('ionize_message_check_corrected');
		else
			$result['result_text'] = lang('ionize_message_check_ok');
		
		// Result view
		$view = $this->load->view('system_check_result', $result, TRUE);


		$this->callback = array(
			array (
				'fn' => 'ION.appendDomElement',
				'args' => array	(
					'system_check_report',
					$view
				)
			),
			array (
				'fn' => 'ION.notification',
				'args' => array	(
					'success',
					'Check complete !'
				)
			)			
		);
		
		$this->response();
	}
	
	
	/**
	 * Rebuilds the pages URLs
	 *
	 */
	function rebuild_pages_urls()
	{
		$nb = $this->page_model->rebuild_urls();
		$this->url_model->delete_empty_urls();
		
		$result = array(
			'title' => lang('ionize_title_rebuild_pages_urls'),
			'result_status' => 'success'
		);
		
		// Correct
		if ($nb > 0)
			$result['result_text'] = lang('ionize_message_check_corrected');
		else
			$result['result_text'] = lang('ionize_message_check_ok');
		
		// Result view
		$view = $this->load->view('system_check_result', $result, TRUE);

		$this->callback = array(
			array (
				'fn' => 'ION.appendDomElement',
				'args' => array	(
					'system_check_report',
					$view
				)
			),
			array (
				'fn' => 'ION.notification',
				'args' => array	(
					'success',
					'Check complete !'
				)
			)			
		);

		$this->response();
		
	}
	
	
	/**
	 * Rebuilds the articles URLs
	 *
	function rebuild_articles_urls()
	{
		$nb = $this->article_model->rebuild_urls();
		$this->url_model->delete_empty_urls();

		$result = array(
			'title' => lang('ionize_title_rebuild_articles_urls'),
			'result_status' => 'success'
		);
		
		// Correct
		if ($nb > 0)
			$result['result_text'] = lang('ionize_message_check_corrected');
		else
			$result['result_text'] = lang('ionize_message_check_ok');
		
		// Result view
		$view = $this->load->view('system_check_result', $result, TRUE);

		$this->callback = array(
			array (
				'fn' => 'ION.appendDomElement',
				'args' => array	(
					'system_check_report',
					$view
				)
			),
			array (
				'fn' => 'ION.notification',
				'args' => array	(
					'success',
					'Check complete !'
				)
			)			
		);

		$this->response();
		
	}
	 */
	
	
	/**
	 * Check, for each database registered picture, if all the defined thumbs exist
	 *
	 */
	function check_thumbs()
	{
	
	}
	
	/**
	 * Check write rights on Sitemap files
	 *
	 */
	function check_sitemap_file()
	{
	}
	
}


/* End of file system_check.php */
/* Location: ./application/controllers/admin/system_check.php */