<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * System Check Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
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
        $this->load->model(
            array(
                'system_check_model',
                'menu_model',
                'page_model',
                'article_model',
                'media_model',
                'config_model',
                'url_model',
            ), '', TRUE);
		
		// Libraries
		$this->load->library('structure');
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/** 
	 * Displays the System Diagnostic Panel
	 * @param	string		Menu ID
	 *
	 */
	public function index()
	{
		// Write rights
		$folders = array(
			array(
				'path' => (config_item('cache_path') == '') ? FCPATH.'cache/' : config_item('cache_path'),
				'write' => FALSE
			),
			array(
				'path' => (config_item('files_path') == '') ? FCPATH.'files/' : FCPATH.config_item('files_path'),
				'write' => FALSE
			),
			array(
				'path' => FCPATH.'themes/'.Settings::get('theme'),
				'write' => FALSE
			),
		);

		foreach($folders as $key => $folder)
		{
			if ( is_dir($folder['path']) AND is_really_writable($folder['path']))
				$folders[$key]['write'] = TRUE;
		}

		$this->template['folders'] = $folders;

		$this->output('system/check');
	}
	
	
	/**
	 * Called through XHR
	 * Launches the checks
	 *
	 */
	public function start_check()
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
					'system_check/check_lang'
				)
			)
		);

		$this->response();
	}

	public function check_lang()
	{
		$result = array(
			'title' => lang('ionize_title_check_lang'),
			'status' => 'success',
			'message' => lang('ionize_message_check_ok')
		);

		// Get the languages : DB + config/language.php
		$db_languages = Settings::get_languages();
		$config_available_languages = config_item('available_languages');

		// Check differences between DB and config/language.php file
		$result_status = TRUE;

		foreach($db_languages as $lang)
		{
			if ( ! array_key_exists($lang['lang'], $config_available_languages))
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
			$available_languages = array();

			foreach($db_languages as $l)
			{
				// Set default lang code
				if ($l['def'] == '1')
					$def_lang = $l['lang'];

				$available_languages[$l['lang']] = $l['name'];
			}

			$this->config_model->change('language.php', 'default_lang_code', $def_lang);

			if ( ! empty($available_languages))
			{
				$this->config_model->change('language.php', 'available_languages', $available_languages);
			}

			$result['message'] = lang('ionize_message_check_corrected');

		}

		$this->xhr_output($result);
	}

	/**
	 * Check page level integrity
	 * Checks the page level inegrity, correct and chains the next check : article's contexts
	 *
	 */
	public function check_page_level()
	{
		$result = array(
			'title' => lang('ionize_title_check_page_level'),
			'status' => 'success'
		);

		$nb_wrong_levels = $this->system_check_model->check_page_level();
		
		// Correct
		if ($nb_wrong_levels > 0)
		{
			$corrected = $this->system_check_model->check_page_level($correct = TRUE);
		
			$result['message'] = $nb_wrong_levels .'/'. $corrected . lang('ionize_message_check_corrected');
		}
		else
		{
			$result['message'] = lang('ionize_message_check_ok');
		}

		$this->xhr_output($result);
	}
	
	
	/**
	 * Checks if all articles which have one page context have the page as "Main Parent"
	 * End of check. 
	 *
	 */
	public function check_article_context()
	{
		$result = array(
			'title' => lang('ionize_title_check_article_context'),
			'status' => 'success'
		);

		$nb_orphan_articles = $this->system_check_model->check_article_context();

		// Correct
		if ($nb_orphan_articles > 0)
			$result['message'] = lang('ionize_message_check_corrected');
		else
			$result['message'] = lang('ionize_message_check_ok');

		$this->xhr_output($result);
	}

	/**
	 * Removes not used media from the media tables.
	 *
	 */
	public function clean_media()
	{
		$result = array(
			'title' => lang('ionize_title_clean_media'),
			'status' => 'success'
		);

		// Check and correct page's views
		$nb_cleaned = $this->media_model->clean_table();

		$result['message'] = $nb_cleaned . lang('ionize_message_nb_media_cleaned');

		$this->xhr_output($result);
	}


	public function broken_media_report()
	{
		$report_message = lang('ionize_message_no_broken_media_links');

		$brokens = $this->media_model->get_brokens();
		if (!empty($brokens))
		{
			$report_message = '';
			foreach($brokens as $media)
			{
				$report_message .= $media['path'] . '<br/>';
			}
		}

		$this->xhr_output($report_message);
	}

	public function unused_media_report()
	{
		$report_message = lang('ionize_message_no_unused_media');

		$result = $this->media_model->get_unused_files();

		if ( ! empty($result['files']))
		{
			$report_message = $this->load->view('system/report/unused_media', $result, TRUE);
		}

		$this->xhr_output($report_message);
	}


	public function unused_media_delete()
	{
		// 'unusedMediaContainer'
		$files = $this->input->post('files');

		$nb = $this->media_model->delete_files($files);

		$this->unused_media_report();
	}


	/**
	 * Checks views of both pages and articles
	 *
	 */
	public function check_views()
	{
		$nb = 0;
		
		$result = array(
			'title' => lang('ionize_title_check_views'),
			'status' => 'success'
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
			$result['message'] = lang('ionize_message_check_corrected');
		else
			$result['message'] = lang('ionize_message_check_ok');
		
		// Result view
		$view = $this->load->view('system/check_result', $result, TRUE);


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
	public function rebuild_sitemap()
	{
		$this->structure->build_sitemap(TRUE);

		$result = array(
			'title' => lang('ionize_title_rebuild_sitemap_done'),
			'status' => 'success',
			'message' => lang('ionize_message_check_ok'),
		);
		
		$this->xhr_output($result);
	}


	/**
	 * Rebuilds the pages URLs
	 *
	 */
	public function rebuild_urls()
	{
		$this->url_model->clean_table();

		$nb = $this->page_model->rebuild_urls();
		$this->url_model->delete_empty_urls();

		$result = array(
			'title' => lang('ionize_title_rebuild_pages_urls'),
			'status' => 'success',
			'message' => lang('ionize_message_check_ok'),
		);

		$this->xhr_output($result);
	}

	/**
	 * Check, for each database registered picture, if all the defined thumbs exist
	 *
	 */
	public function check_thumbs()
	{
	
	}
	
	/**
	 * Check write rights on Sitemap files
	 *
	 */
	public function check_sitemap_file()
	{
	}
	
}
