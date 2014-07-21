<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Lang Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Lang extends MY_admin
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
                'lang_model',
                'settings_model'
            ), '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Shows the existing languages
	 *
	 */
	function index()
	{

		$languages = $this->lang_model->get_list(array('order_by' => 'ordering ASC'));

		$this->template['languages'] = $languages;

		foreach($languages as $lang)
		{
			$this->template['online_'.$lang['lang']] = $lang['online'];
		}

		$this->output('lang/lang');
	}


	// ------------------------------------------------------------------------

	public function get_options()
	{
		$this->output('lang/options');

	}

	// ------------------------------------------------------------------------

	function get_form()
	{
		$this->output('lang/lang_new');
	}

	/**
	 * Saves a new language
	 *
	 */
	function save()
	{
		if( $this->input->post('lang_new') != "" && $this->input->post('name_new') != "" )
		{
			// Basic lang data
			$data = array(
						'lang' => $this->input->post('lang_new'),
						'name' => $this->input->post('name_new'),
						'online' => $this->input->post('online_new')
					);

			// Ordering : New lang at last position 
			$this->db->select_max('ordering', 'ordering');
			$query = $this->db->get('lang');

			if ($query->num_rows() > 0)
			{
				$row = $query->row(); 
				$data['ordering'] = $row->ordering + 1;
			}

			// Save to DB
			if ($this->lang_model->exists( array( 'lang' => $this->input->post('lang_new') ) ) )
			{
				$this->lang_model->update($this->input->post('lang_new'), $data);
			}
			else
			{
				$this->lang_model->insert($data);
				
				/* Insert in lang tables (page_lang, article_lang) the basic lang data for this new created lang
				 * see lang_model->insert_lang_data() for more info.
				 */
				$this->lang_model->insert_lang_data(
					array('page', 'article'), 
					$fields = array('url'), 
					$from = Settings::get_lang('default'), 
					$to = $this->input->post('lang_new')
				);
				
				/* Insert lang URL in URL table
				 * Does not erase existing URL, to prevent URL change in case of lang re-creation after 
				 * user error.
				 *
				 */
				$this->lang_model->copy_lang_urls(
					$from = Settings::get_lang('default'), 
					$to = $this->input->post('lang_new')
				);
			}
			
			// Update the language config file
			if ( false == $this->_update_config_file())
			{
				$this->error(lang('ionize_message_lang_file_not_saved'));
			}
	
			// UI panel to update after saving
			$this->_reload_panel();

			// Answer send
			$this->success(lang('ionize_message_lang_saved'));
		}
		else
		{
			$this->error(lang('ionize_message_lang_not_saved'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Cleans content lang tables from non existing language.
	 * To be used after a lang delete, if these lang data will not be used anymore
	 *
	 */
	function clean_tables()
	{
		//$tables = array('page', 'article', 'media');
		// Some other content language tables need to be cleaned up too. -- Kochin
		// Retrieve a list of all content language table names.
		$tables = $this->lang_model->list_lang_tables();
		if ($tables != FALSE)
		{
			// Remove the postfix _lang.
			$tables = preg_replace('/_lang$/', '', $tables);
			log_message('debug', 'Content language tables w/o _lang: '.print_r($tables, TRUE));

			$deleted_rows = $this->lang_model->clean_lang_tables($tables);
		}

		// Also delete rows belong to unused languages in the setting table. -- Kochin
		$deleted_rows = $this->settings_model->clean_lang_settings();

		$result = array(
			'title' => lang('ionize_button_clean_lang_tables'),
			'status' => 'success',
			'message' => lang('ionize_message_lang_tables_cleaned'),
		);

		$this->xhr_output($result);
	}


	// ------------------------------------------------------------------------


	/**
	 * Copy one language content to another
	 * Let the user choose wich content will be copied
	 * 
	 * @TODO...
	 * 
	 */
	function copy_lang_content()
	{

		$case = $this->input->post('case');
		
		$id_page = $this->input->post('id_page');
		$id_article = $this->input->post('id_article');
		$include_articles =  $this->input->post('include_articles');

		// lang codes
		$from = $this->input->post('from');
		$to = $this->input->post('to');
		
		// REL : id_page.id_article (ex : 2.14)
		$rel = 	$this->input->post('rel');
		
		if ($from != $to)
		{
		
			// From where was the copy asked ?
			switch ($case)
			{

				// Article : Copy only this article content
				case 'article' :
					$this->load->model('article_model', '', true);

					$rel = explode(".",  $rel);
					$id_page = $rel[0];
					$id_article = $rel[1];
					
					// Copy
					$this->lang_model->copy_lang_content($from, $to, 'article', $id_article);

					$article_lang = $this->article_model->get_by_id($id_article, Settings::get_lang('default'));
					$title = empty($article_lang['title']) ? $article_lang['name'] : $article_lang['title'];

					$this->callback[] =	array(
						'fn' => 'ION.splitPanel',
						'args' => array(
							'urlMain'=> admin_url() . 'article/edit/'.$id_page.'.'.$id_article,
							'urlOptions'=> admin_url() . 'article/get_options/'.$id_page.'.'.$id_article,
							'title'=> lang('ionize_title_edit_article') . ' : ' . $title
						)
					);

					$this->success(lang('ionize_message_article_content_copied'));

					break;
				

				// Page : Copy this page content. Articles content optional
				case 'page' :
					$this->load->model('page_model', '', true);

					$message = lang('ionize_message_page_content_copied');

					// Copy
					$this->lang_model->copy_lang_content($from, $to, 'page', $id_page);
					
					// Copy linked articles content ?
					if ($include_articles == 'true')
					{
						$this->load->model('article_model', '', true);
						
						$articles = $this->article_model->get_lang_list(array('id_page' => $id_page));
						
						foreach($articles as $article)
							$this->lang_model->copy_lang_content($from, $to, 'article', $article['id_article']);

						$message = lang('ionize_message_page_article_content_copied');
					}

					$page = $this->page_model->get_by_id($id_page, Settings::get_lang('default'));
					$title = empty($page['title']) ? $page['name'] : $page['title'];

					$this->callback[] =	array(
						'fn' => 'ION.splitPanel',
						'args' => array(
							'urlMain'=> admin_url() . 'page/edit/'.$id_page,
							'urlOptions'=> admin_url() . 'page/get_options/'.$id_page,
							'title'=> lang('ionize_title_edit_page') . ' : ' . $title
						)
					);

					$this->success($message);

					break;
				
				// Copy the whole website content
				case 'lang' :
					
					$this->load->model('page_model', '', true);
					$this->load->model('article_model', '', true);

					// Pages content copy
					$pages = $this->page_model->get_lang_list();
					
					foreach($pages as $page)
						$this->lang_model->copy_lang_content($from, $to, 'page', $page['id_page']);

					// Articles content copy
					$articles = $this->article_model->get_lang_list();
					
					foreach($articles as $article)
						$this->lang_model->copy_lang_content($from, $to, 'article', $article['id_article']);

					$this->success(lang('ionize_message_lang_content_copied'));
					
					break;
			}
		}
		else
		{
			$this->error(lang('ionize_message_source_destination_lang_not_different'));
		}		
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates all the existing languages
	 *
	 */
	function update()
	{
		foreach(Settings::get_languages() as $lang)
		{
			// Update existing languages
			$data = array(
				'lang' =>		$this->input->post('lang_'.$lang['lang']),
				'name' =>		$this->input->post('name_'.$lang['lang']),
				'online' =>		$this->input->post('online_'.$lang['lang']),
				'direction' =>		$this->input->post('direction_'.$lang['lang']),
			);

			($this->input->post('default_lang') == $lang['lang']) ? $data['def'] = '1' : $data['def'] = '0';

			if (($lang['lang'] != $data['lang']) && $this->lang_model->exists( array( 'lang' =>  $data['lang'] ) ) )
			{
				$this->error(lang('ionize_message_lang_code_already_exists'));
			}
			
			// If the default lang is different from the current one, pages need to be checked
			
			// Update the lang
			$this->lang_model->update($lang['lang'], $data);
			
			// If the lang code changed, update all the pages and articles content translations
			if ($lang['lang'] != $data['lang'])
			{
				$tables  = array('article','page', 'media', 'category');
				$this->lang_model->update_lang_tables($tables, $from = $lang['lang'], $to = $data['lang']);
			}
		}

		// Update the language config file
		if ( false == $this->_update_config_file())
		{
			$this->error(lang('ionize_message_lang_file_not_saved'));
		}

		// UI update panels
		$this->_reload_panel();

		$this->success(lang('ionize_message_lang_updated'));
	}


	// ------------------------------------------------------------------------

	function save_options()
	{
		// Force lang URLs ?
		$data = array('name' => 'force_lang_urls', 'content' => $this->input->post('force_lang_urls'));
		$this->settings_model->save_setting($data);

		// UI update panels
		$this->_reload_panel();

		$this->success(lang('ionize_message_lang_updated'));
	}


	/** 
	 * Saves ordering
	 * 
	 */
	function save_ordering()
	{
		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Saves the new ordering
			$this->lang_model->save_ordering($order);
			
			// Answer send
			$this->success(lang('ionize_message_lang_ordered'));
		}
		else 
		{
			// Answer send
			$this->error(lang('ionize_message_operation_nok'));
		}
	}
	
	
	// ------------------------------------------------------------------------


	/** 
	 * Delete a lang
	 *
	 * @param	string		lang code
	 * @param	boolean		if true, the transport is through XHR
	 *
	 */
	function delete($lang)
	{
		$affected_rows = $this->lang_model->delete($lang);

		if ($affected_rows > 0)
		{
			$this->id = $lang;

			// Updates the default lang if needed
			$this->_update_default_lang();

			$this->_reload_panel();

			// Answer send
			$this->success(lang('ionize_message_lang_deleted'));
		}
		else
		{
			// Answer send
			$this->error(lang('ionize_message_lang_not_deleted'));			
		}
	}


	// ------------------------------------------------------------------------


	function _reload_panel()
	{
		$this->reload(
			'mainPanel',
			admin_url(TRUE) . 'lang',
			lang('ionize_menu_languages')
		);
	}


	// ------------------------------------------------------------------------


	function _update_default_lang($default_lang_code = NULL)
	{
		$languages = $this->lang_model->get_list(array('order_by' => 'ordering ASC'));

		if ( ! empty($languages))
		{
			$found_default_lang = FALSE;
			foreach ($languages as $language)
			{
				if ($language['def'] == '1')
					$found_default_lang = TRUE;
			}

			if ($found_default_lang == FALSE)
			{
				$languages = $this->lang_model->get_all();
				$language = array_shift($languages);
				if (!empty($language))
				{
					$this->lang_model->update($language->lang, array('def'=>1));
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates the language config file
	 *
	 */
	function _update_config_file()
	{
		$languages = $this->lang_model->get_list(array('order_by' => 'ordering ASC'));

		// Default language
		$def_lang = '';
		
		// Available / Online languages array
		$available_languages = array();
		$online_languages = array();
		
		foreach($languages as $l)
		{
			// Set default lang code
			if ($l['def'] == '1')
				$def_lang = $l['lang'];

			$available_languages[$l['lang']] = $l['name'];
 
			if($l['online'] == '1')
				$online_languages[$l['lang']] = $l['name'];
		}

		// Files begin
		$conf  = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n\n";
		
		$conf .='/*'."\n";
		$conf .='| -------------------------------------------------------------------'."\n";
		$conf .='| IONIZE LANGUAGES'."\n";
		$conf .='| -------------------------------------------------------------------'."\n";
		$conf .='| Contains the available languages definitions for the front-end.'."\n";
		$conf .='| Auto-generated by Ionizes Language administration.'."\n";
		$conf .='| Changes made in this file will be overwritten by languages save in Ionize.'."\n";
		$conf .='|'."\n";
		$conf .='|'."\n";
		$conf .='*/'."\n\n";

		$conf .= "// Default admin language code\n";
		$conf .= "\$config['default_admin_lang'] = '".config_item('default_admin_lang')."';\n\n";

		$conf .= "// Default language code\n";
		$conf .= "// This code depends on the language defined through the Ionize admin panel\n";
		$conf .= "// and will never change during the request process \n";
		$conf .= "\$config['default_lang_code'] = '".$def_lang."';\n\n";

        $conf .= "// Default Translation Language Code\n";
        $conf .= "\$config['default_translation_lang_code'] = '".config_item('default_translation_lang_code')."';\n\n";

		$conf .= "// Used language code\n";
		$conf .= "// Dynamically changed by the Router depending on the browser, cookie or asked URL\n";
		$conf .= "// By default, Ionize set it to the default lang code.\n";
		$conf .= "\$config['detected_lang_code'] = '".$def_lang."';\n\n";

		$conf .= "// Available languages\n";
		$conf .= "// Languages set through Ionize. Includes offline languages\n";
		$conf .= "\$config['available_languages'] = ".dump_variable($available_languages)."\n\n";

		$conf .= "// Online languages\n";
		$conf .= "// Languages set online through Ionize.\n";
		$conf .= "\$config['online_languages'] = ".dump_variable($online_languages)."\n\n";

		// files end
		$conf .= "\n\n";
		$conf .= '/* Auto generated by Language Administration on : '.date('Y.m.d H:i:s').' */'."\n";

		$ret = @file_put_contents(APPPATH . 'config/language' . EXT, $conf);
		
		if ($ret)
			return TRUE;
		else
			return FALSE;
	}
}
