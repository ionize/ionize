<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Settings Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Setting extends MY_admin
{
	/**
	 * Fields on wich no XSS filtering is done
	 * 
	 * @var array
	 */
	protected $no_xss_filter = array('google_analytics');


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->config('sitemaps');
		$this->load->model('config_model', '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Shows standard settings
	 *
	 */
	function index()
	{
		$this->_get_settings();
		
		$this->output('setting/index');
	}


	// ------------------------------------------------------------------------


	function ionize()
	{
		$this->template['displayed_admin_languages'] = Settings::get('displayed_admin_languages');

		$this->_get_settings();

		if ( ! Settings::get('backend_ui_style'))
			Settings::set('backend_ui_style', 'original');

		$styles = array();
		$handle = opendir(FCPATH.'themes/admin/styles');
		if ($handle)
		{
			while ( FALSE !== ($style = readdir($handle)) )
			{
				// make sure we don't map silly dirs like .svn, or . or ..
				if (substr($style, 0, 1) != "." && $style != 'index.html' && $style != '@eaDir')
					$styles[] = $style;
			}
		}

		$this->template['styles'] = $styles;

		$this->output('setting/ionize');
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Shows technical settings
	 * These settings are managed only by Super Admins
	 *
	 */
	function technical()
	{
		// Get settings from DB and put them to the Settings library
		// (Settings are displayed in view from this library)
		$this->_get_settings();


		// Text editor list
		foreach(config_item('texteditors') as $t)
		{
			$this->template['texteditors'][] = $t;
		}


		// Filemanager list
		foreach($this->config->item('filemanagers') as $f)
		{
			$this->template['filemanagers'][] = $f;
		}
		

		// Mimes types
		$mimes = Settings::get_mimes_types();
		$this->template['mimes'] = $mimes;


		// Database settings
		$this->load->dbutil();

		// If the user is here, a valid database.php config file exists !
		$db = array();
		$active_group = '';
		include(APPPATH.'config/database'.EXT);

		$this->template['db_host'] = 	$db[$active_group]['hostname'];
		$this->template['db_name'] = 	$db[$active_group]['database'];
		$this->template['db_user'] = 	$db[$active_group]['username'];
		$this->template['db_pass'] = 	'';

		// Website Email settings
		$config = array();
		if (file_exists(APPPATH.'config/email'.EXT))
			include(APPPATH.'config/email'.EXT);

		$this->template['protocol'] = 		isset($config['protocol']) ? $config['protocol'] : 'mail';
		$this->template['mailpath'] = 		isset($config['mailpath']) ? $config['mailpath'] : '/usr/sbin/sendmail';
		$this->template['smtp_host'] = 		isset($config['smtp_host']) ? $config['smtp_host'] : '';
		$this->template['smtp_user'] = 		isset($config['smtp_host']) ? $config['smtp_user'] : '';
		$this->template['smtp_pass'] = 		isset($config['smtp_pass']) ? $config['smtp_pass'] : '';
		$this->template['smtp_port'] = 		isset($config['smtp_port']) ? $config['smtp_port'] : '25';
        $this->template['smtp_timeout'] =   isset($config['smtp_timeout']) ? $config['smtp_timeout'] : '30';
		$this->template['charset'] = 		isset($config['charset']) ? $config['charset'] : 'utf-8';
		$this->template['mailtype'] = 		isset($config['mailtype']) ? $config['mailtype'] : 'text';
		$this->template['newline'] = 		isset($config['newline']) ? $config['newline'] : '\n';

		// Thumbs settings
		$this->template['thumbs'] = $this->settings_model->get_list(array('name like' => 'thumb_%'));

		// Media : Resize on Upload
		$this->template['resize_on_upload'] = Settings::get('resize_on_upload');
		$this->template['media_thumb_unsharp'] = Settings::get('media_thumb_unsharp');
		$this->template['upload_autostart'] = Settings::get('upload_autostart');
		$this->template['upload_mode'] = Settings::get('upload_mode');

		
		// Cache
		$this->template['cache_enabled'] = config_item('cache_enabled');
		$this->template['cache_time'] = config_item('cache_time');
		
		
		// Antispam key
		$this->template['form_antispam_key'] = config_item('form_antispam_key');
		
		
		// Allowed HTML tags in content
		$this->template['article_allowed_tags'] = explode(',', Settings::get('article_allowed_tags') );

		// Maintenance IPs
		$this->template['maintenance_ips'] = implode("\n", config_item('maintenance_ips'));


		$this->output('setting/technical');
	}


	// ------------------------------------------------------------------------


	/**
	 * Shows themes settings
	 *
	 */
	function themes()
	{
		// Themes list
		$themes = $themes_admin = array();
		$handle = opendir(FCPATH.'themes');
		if ($handle)
		{
			while ( FALSE !== ($theme = readdir($handle)) )
			{
				// make sure we don't map silly dirs like .svn, or . or ..
				if (substr($theme, 0, 1) != "." && $theme != 'index.html' && $theme != '@eaDir' && substr($theme,0,5) != 'admin')
					$themes[] = $theme;
				else if(substr($theme,0,5) == 'admin')
					$themes_admin[] = $theme;
			}
		}
		
		$this->template['themes'] = $themes;
		$this->template['themes_admin'] = $themes_admin;


		// Get Current theme views list

		// Filesystem files list
		$files = $this->_get_view_files();

		// Recorded views definitions 
		$views = array();
		$theme_path = FCPATH.'themes/'.Settings::get('theme');

		if (is_file($theme_path.'/config/views.php'))
			require_once($theme_path.'/config/views.php');

		// Try to match each file with found config file
		foreach($files as $file)
		{
			// $views is set in the config file (auto writed by this class)
			if (isset($views))
			{
				foreach($views as $type => $def)
				{
					foreach($def as $key => $definition)
					{
						if ($key == ($file->path . $file->name))
						{
							$file->type = $type;
							$file->definition = $definition;
						}
					}
				}
			}
			if (! isset($file->type))
			{
					$file->type = '';
					$file->definition = '';
			}
		}
		$this->template['files'] = $files;

		
		$this->output('setting/theme');
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Gets the maintenance page
	 *
	 */
	function get_maintenance_page()
	{
		if (Settings::get('maintenance_page') != '')
		{
			$this->load->model('page_model', '', TRUE);
			$this->template['page'] = $this->page_model->get_by_id(Settings::get('maintenance_page'), Settings::get_lang('default'));
		}

		$this->output('setting/maintenance_page');
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Sets the maintenance page
	 *
	 */
	function set_maintenance_page()
	{
		// $id_page = $this->input->post('id_page');

		$page_id = explode('.', $this->input->post('id_page'));

		$id_page = (count($page_id) > 1) ? $page_id[0] : $this->input->post('id_page');
		
		$data = array(
			'name' => 'maintenance_page',
			'content' => $id_page
		);

		$this->settings_model->save_setting($data);

		if ($id_page)
		{
			$this->load->model('page_model', '', TRUE);

			$page = $this->page_model->get_by_id($id_page, Settings::get_lang('default'));
		
			$options = array(
				CURLOPT_RETURNTRANSFER => TRUE, // return web page
				CURLOPT_HEADER => FALSE, // don't return headers
				CURLOPT_ENCODING => "", // handle all encodings
				CURLOPT_USERAGENT => "ionize", // who am i
				CURLOPT_AUTOREFERER => TRUE, // set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
				CURLOPT_TIMEOUT => 120, // timeout on response
				CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
				CURLOPT_SSL_VERIFYHOST => 0, // don't verify ssl
				CURLOPT_SSL_VERIFYPEER => FALSE, //
				CURLOPT_VERBOSE => 1 //
			);

			$open_basedir_restriction = ini_get('open_basedir');
			$safe_mode = ini_get('safe_mode');

			if ( empty($open_basedir_restriction) && ! $safe_mode)
				$options[CURLOPT_FOLLOWLOCATION] = TRUE;		// follow redirects
 
			$ch = curl_init(base_url().$page['name']);
			curl_setopt_array($ch,$options);
			$content = curl_exec($ch);
			$err = curl_errno($ch);
			$errmsg = curl_error($ch) ;
			$header = curl_getinfo($ch);
			curl_close($ch);

			write_file(FCPATH.'maintenance.html', $content);
		}
		else
		{
			@unlink(FCPATH.'maintenance.html');
		}
		
		Settings::set('maintenance_page', $id_page);
		
		$this->get_maintenance_page();
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Edits one view file
	 * @param	string	Optionnal. Path of the view
	 * @param	string	View name.
	 *
	 */
	function edit_view()
	{
		// View sub-folder
		$path = '';
		
		// Functions argumets
		$args = func_get_args();
		
		// If path is defined, get the path
		// Only one sub-folder in views folder
		if (func_num_args() > 1)
		{
			$view = $args[func_num_args() - 1];
			array_pop($args);
			$path = implode('/', $args);
		}
		else 
			$view = $args[0];
		
		$this->template['path'] = $path;
		$this->template['view'] = $view;

		// file path
		// $filepath = APPPATH.'../themes/'.Settings::get('theme').'/views/';
		$filepath = FCPATH.'themes/'.Settings::get('theme').'/views/';

		// View sub-folder ?
		if ($path != '')
			$filepath .= $path.'/';

		// Get file content
		$content = file_get_contents($filepath.$view.'.php');
		$content = str_replace('<', '&lt;', $content);
		$content = str_replace('>', '&gt;', $content);

		$this->template['content'] = $content;

		$this->output('setting/edit_view');
	}

	// ------------------------------------------------------------------------


	/**
	 * Saves one view file
	 * 
	 *
	 */
	function save_view()
	{
		if ( Authority::can('edit', 'admin/settings/themes'))
		{
			$view = $this->input->post('view');
			$path = $this->input->post('path');
			
			// Get the path if there is one
			$path = ($path) ? $path.'/'.$view : $view;
			
			// File Content
			if ( !empty($_REQUEST['content']))
			{
				$content = stripslashes($_REQUEST['content']);
		
				// Writing problem
				if ( ! write_file(APPPATH.'../themes/'.Settings::get('theme').'/views/'.$path.'.php', $content))
				{
					$this->error(lang('ionize_message_error_writing_file'));				
				}
				else
				{
					$this->success(lang('ionize_message_view_saved'));				
				}
			}
			$this->error(lang('ionize_message_view_not_saved'));				
		}
		else
		{
			$this->error(lang('ionize_message_not_enough_privileges'));
		}		
	}


	// ------------------------------------------------------------------------


	public function save_quicksettings()
	{
		$keys = $this->input->post('keys');
		$post = $this->input->post();

		if (isset($post['keys'])) unset($post['keys']);

		$keys = explode(',', $keys);
		$this->_save_settings($keys);

		$this->success(lang('ionize_message_settings_saved'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves Website basics settings
	 *
	 */
	function save()
	{
		// Settings to save
		// Settings to save
		$settings = array(
			'google_analytics',
			'google_analytics_id',
			'google_analytics_profile_id',
			'google_analytics_url',
			'google_analytics_email',
			'google_analytics_password',
			'dashboard_google',
			'email_technical', 'email_contact', 'email_info'
		);
		$lang_settings = array(
			'meta_keywords',
			'meta_description',
			'site_title'
		);

		// Save settings to DB
		$this->_save_settings($settings, $lang_settings);

		$this->reload(
			'mainPanel',
			admin_url(TRUE) . 'setting',
			lang('ionize_menu_site_settings')
		);


		// Answer
		$this->success(lang('ionize_message_settings_saved'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves Ionize settings
	 *
	 */
	function save_ionize()
	{
		// Save Admin panel displayed languages
		if ($this->input->post('displayed_admin_languages'))
		{
			$displayed_admin_languages = implode(',', $this->input->post('displayed_admin_languages'));
		}
		else
		{
			$displayed_admin_languages = config_item('default_admin_lang');
		}

		$data = array(
					'name' => 'displayed_admin_languages',
					'content' => $displayed_admin_languages
				);
		
		$this->settings_model->save_setting($data);


		// Other Settings to save
		$settings = array(
			'show_help_tips',
			'display_connected_label',
			'display_dashboard_shortcuts',
			'display_dashboard_modules',
			'display_dashboard_users',
			'display_dashboard_content',
			'display_dashboard_quick_settings',
			'display_front_offline_content',
			'notification',
			'date_format',
			'default_admin_lang',
			'enable_backend_tracker',
			'backend_ui_style',
		);

		// Save settings to DB
		$this->_save_settings($settings);
		
		// Admin lang for backend reload URL
		$default_admin_lang = $this->input->post('default_admin_lang');
		
		// Correct the default Admin panel language
		if ( ! in_array($default_admin_lang, explode(',',$displayed_admin_languages)))
			$default_admin_lang = config_item('default_admin_lang');
		
		// Update the language config file
		if ( FALSE == $this->config_model->change('language.php', 'default_admin_lang', $default_admin_lang))
		{
			$this->error(lang('ionize_message_lang_file_not_saved'));
		}

		// Set the reload CB
		$this->_callback_reload_backend();

		$this->success(lang('ionize_message_settings_saved'));
	}


	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves markers (flags)
	 *
	 */
	function save_flags()
	{
		// Settings to save
		$settings = array('flag1', 'flag2', 'flag3', 'flag4', 'flag5');

		// Save settings to DB
		$this->_save_settings($settings);

		// Answer
		$this->success(lang('ionize_message_operation_ok'));
	}
	
	
	// ------------------------------------------------------------------------
	
	
	function save_keys()
	{
		// Antispam key
		$config_items = array('form_antispam_key');

		foreach($config_items as $config_item)
		{
			if (config_item($config_item) != $this->input->post($config_item) )
			{
				if ($this->config_model->change('ionize.php', $config_item, $this->input->post($config_item)) == FALSE)
					$this->error(lang('ionize_message_error_writing_ionize_file'));				
			}
		
		}

		// UI panel to update after saving
		$this->update[] = array(
			'element' => 'mainPanel',
			'url' => 'setting/technical'
		);

		$this->success(lang('ionize_message_settings_saved'));
	}


	// ------------------------------------------------------------------------


	function save_article()
	{
		// Settings to save
		$settings = array(
			'tinybuttons1','tinybuttons2','tinybuttons3','tinyblockformats',
			'smalltinybuttons1','smalltinybuttons2','smalltinybuttons3',
		);

		// Save settings to DB
		$this->_save_settings($settings);

		// Tags allowed in articles
		$tags = $this->input->post('article_allowed_tags');
		if ( ! $tags) $tags = array();
		if (in_array('table', $tags)) $tags = array_merge($tags, array('thead','tbody','tfoot','tr','th','td','caption','colgroup','col'));
		if (in_array('object', $tags)) $tags = array_merge($tags, array('param', 'embed'));
		if (in_array('dl', $tags)) $tags = array_merge($tags, array('dt','dd'));
		if (in_array('img', $tags)) $tags = array_merge($tags, array('map','area'));
		if (in_array('form', $tags)) $tags = array_merge($tags, array('input','button','fieldset','label','textarea','legend','optgroup','option','select'));
		if (in_array('audio', $tags)) $tags = array_merge($tags, array('source'));
		if (in_array('video', $tags)) $tags = array_merge($tags, array('source'));

		// Standard allowed tags
		$tags = array_merge($tags, array('p','a','ul','ol','li','br','b','strong','i',));
		
		$article_allowed_tags = array(
			'name' => 'article_allowed_tags',
			'content' => implode(',', $tags)
		);
		$this->settings_model->save_setting($article_allowed_tags);

		// Set the reload CB
		$this->_callback_reload_backend();

		$this->success(lang('ionize_message_settings_saved'));
	}


	// ------------------------------------------------------------------------


	function save_thumbs()
	{
		// Thumbs update
		$thumbs  = $this->settings_model->get_list(array('name like' => 'thumb_%'));
		foreach($thumbs as $thumb)
		{
			$sizeref = 	$this->input->post('thumb_sizeref_'.$thumb['id_setting']);
			$size = 	$this->input->post('thumb_size_'.$thumb['id_setting']);
			$square = 	$this->input->post('thumb_square_'.$thumb['id_setting']);
			$unsharp = 	$this->input->post('thumb_unsharp_'.$thumb['id_setting']);

			$data = array(
						'name'	=> 'thumb_'.$this->input->post('thumb_name_'.$thumb['id_setting']),
						'content' => $sizeref.','.$size.','.$square.','.$unsharp
					);
			$this->settings_model->update($thumb['id_setting'], $data);
		}

		// UI panel to update after saving
		$this->update[] = array(
			'element' => 'mainPanel',
			'url' => 'setting/technical'
		);

		$this->success(lang('ionize_message_settings_saved'));
		
	}
	

	// ------------------------------------------------------------------------


	function save_medias()
	{
		// Settings to save
		$settings = array(	'files_path', 
							'media_thumb_size', 'resize_on_upload', 'upload_mode', 'picture_max_width', 'picture_max_height', 'upload_autostart',
							'filemanager_file_types', 'no_source_picture', 'media_thumb_unsharp');
		
		// Allowed filemanager file extensions
		$filemanager_file_types = $this->input->post('allowed_type');
		
		if (is_array($filemanager_file_types))
			$this->input->set_post('filemanager_file_types', implode(',', $filemanager_file_types));
		else
			$this->input->set_post('filemanager_file_types', '');
	
		// Get the old media path before saving
		$old_files_path = Settings::get('files_path');

		// Update the media table regarding to the new files path
		$new_files_path = $this->input->post('files_path');
		
		if ($new_files_path != '' && $new_files_path != '/' && ($old_files_path != $new_files_path))
		{
			$this->settings_model->update_media_path($old_files_path, $new_files_path);
			
			$dir = FCPATH . $old_files_path;
			$new_dir = FCPATH . $new_files_path;
			
			// Rename the physical folder
			if (is_dir($dir) && (! is_dir($new_dir)))
			{
				$new_dir = FCPATH . $new_files_path;
				rename($dir, $new_dir);
			}
		}
		else
		{
			// Preserve the old files_path value
			// $this->input->set_post() is a extended function from /application/libraries/MY_Input.php extended lib.
			$this->input->set_post('files_path', $old_files_path);
		}

		// Save settings to DB
		$this->_save_settings($settings);

		// Set the reload CB
		$this->_callback_reload_backend();

		$this->success(lang('ionize_message_settings_saved'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves themes
	 *
	 */
	function save_themes()
	{
		// Settings to save
		$settings = array('theme', 'theme_admin');

		// Save settings to DB
		$this->_save_settings($settings);
		
		// Update Views table 
		$this->update[] = array(
			'element' => 'mainPanel',
			'url' =>  'setting/themes'
		);
		
		// Answer
		$this->success(lang('ionize_message_settings_saved'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves views definition config file
	 * File located in current theme folder : config/views.php
	 *
	 */
	function save_views()
	{
		// Get the views informations
		$views = $this->_get_view_files();

		// Array of view types 
		$viewsTypes = array();

		// View Array
		$viewsArray = array();
		foreach($views as $view)
		{
			$key = $view->path.$view->name;

			// If type is defined
			if (isset($_POST['viewtype_'.$key]) && $_POST['viewtype_'.$key] != '')
			{
				$viewsArray[$_POST['viewtype_'.$key]][$view->path . $view->name] = $_POST['viewdefinition_'.$key];
				
				// Add the view type to the viewTypes array. View type is : "article", "page", etc....
				if ( ! in_array($_POST['viewtype_'.$key], $viewsTypes))	$viewsTypes[] = $_POST['viewtype_'.$key];
			}
		}

		// Sort each array of view type by logical name
		foreach($viewsTypes as $vt)
		{
			if ( ! empty($viewsArray[$vt]))
				asort($viewsArray[$vt]);	
		}

		
		$conf  = "<?php if ( ! defined('BASEPATH')){exit('Invalid file request');}\n\n";
	 
		$conf .= "\$views = " . (String) var_export($viewsArray, TRUE) .";\n";
		
		// files end
		$conf .= "\n\n";
		$conf .= '/* Auto generated by Themes Administration on : '.date('Y.m.d H:i:s').' */'."\n";

		// Writing problem
		if ( ! write_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php', $conf))
		{
			$this->error(lang('ionize_message_error_writing_file'));				
		}
		else
		{
			$this->reload(
				'mainPanel',
				admin_url(TRUE) . 'setting/themes',
				lang('ionize_title_theme')
			);

			$this->success(lang('ionize_message_views_saved'));
		}
		
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Saves one new thumb setting
	 *
	 */
	function save_thumb()
	{
		if(	$this->input->post('thumb_name_new') != "" && $this->input->post('thumb_size_new') != "" )
		{
			$sizeref = 	$this->input->post('thumb_sizeref_new');
			$size = 	$this->input->post('thumb_size_new');
			$square = 	($this->input->post('thumb_square_new')) ? $this->input->post('thumb_square_new') : 'false';
			$unsharp = 	($this->input->post('thumb_unsharp_new')) ? $this->input->post('thumb_unsharp_new') : 'false';

			$data = array(
						'name'	=> 'thumb_'.$this->input->post('thumb_name_new'),
						'content' => $sizeref.','.$size.','.$square.','.$unsharp
					);

			// If this thumb doesn't exists : Save to DB
			if ( ! $this->settings_model->exists(array('name'=>$data['name'])) )
			{
				// DB insert
				$this->settings_model->insert($data);

				// UI panel to update after saving
				$this->update[] = array(
					'element' => 'mainPanel',
					'url' => 'setting/technical'
				);

				// Answer
				$this->success(lang('ionize_message_thumb_saved'));				
				
				// Exit method
				exit();
			}
		}
		
		// If the method arrive here, something failed
		$this->error(lang('ionize_message_thumb_not_saved'));				
	}

	
	
	/**
	 * Saves Maintenance Mode
	 *
	 */
	function save_maintenance()
	{
		if($this->input->post('maintenance_ips') != '') {
			// Maintenance Mode
	/*		$data = array(
						'name' => 'maintenance',
						'content' => $this->input->post('maintenance')
					);
			
			$this->settings_model->save_setting($data);
	*/
	//		$maintenance = 
			if ($this->config_model->change('ionize.php', 'maintenance', $this->input->post('maintenance')) == FALSE)
				$this->error(lang('ionize_message_error_writing_ionize_file'));				


			// Allowed IPs
			$ips = explode("\n", $this->input->post('maintenance_ips'));
			
			if ($this->config_model->change('ionize.php', 'maintenance_ips', $ips) == FALSE)
				$this->error(lang('ionize_message_error_writing_ionize_file'));				
			
			
			// UI panel to update after saving
			$this->update[] = array(
				'element' => 'mainPanel',
				'url' => 'setting/technical'
			);

			// Answer
			$this->success(lang('ionize_message_operation_ok'));
		} else {
			// Send Error Message
            $this->callback[] = array
                (
                'fn' => 'ION.notification',
                'args' => array('error', lang('ionize_message_error_maintenance_ip_required'))
            );

            $this->response();
		}
	
	}
	
    // ------------------------------------------------------------------------

    /**
     * Saves Compress HTML Output Setting
     *
     */
    function save_compress_html_output()
    {
        if ($this->config_model->change('ionize.php', 'compress_html_output', $this->input->post('compress_html_output')) == FALSE)
            $this->error(lang('ionize_message_error_writing_ionize_file'));

        // UI panel to update after saving
        $this->update[] = array(
            'element' => 'mainPanel',
            'url' => 'setting/technical'
        );

        // Answer
        $this->success(lang('ionize_message_operation_ok'));

    }
	
	
	// ------------------------------------------------------------------------
	
	
	function save_seo_urls()
	{
		$type = $this->input->post('type');
		
		$urls = explode("\n", str_replace("\r\n", "\n", $_REQUEST['urls']));

		foreach($urls as &$url)
		{
			$url = prep_url($url);
		}
		
		$urls = implode("|", $urls);
		
		$data = array(
			'name' => $type.'_urls',
			'content' => $urls
		);
		
		$this->settings_model->save_setting($data);
		

		// UI panel to update after saving
		$this->update[] = array(
			'element' => 'mainPanel',
			'url' => 'setting'
		);

		// Answer
		$this->success(lang('ionize_message_urls_saved'));				
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves one setting to DB or in a config file
	 *
	 * @usage	In the setting view, the form must have 1 hidden field called "setting" :
	 *
	 *			<input type="hidden" name="setting" value="sitemaps_gzip" />
	 *
	 *			The hidden field "config_file" is optional.
	 *			If set, it define the config file to use to store the setting.
	 *			If not set, the setting will be saved in DB
	 *
	 *			<input type="hidden" name="config_file" value="sitemaps" />
	 *
	 *			If "type" is set, the function try to convert the value to the givven type
	 *			<input type="hidden" name="type" value="<array>" />
	 *
	 */
	function save_setting()
	{
		$setting = $this->input->post('setting');
		$type = $this->input->post('type');
		$value = (isset($_REQUEST['setting_value'])) ? $_REQUEST['setting_value'] : FALSE;

		// Where to save ?
		$where = ($this->input->post('config_file') != FALSE) ? $this->input->post('config_file') .EXT : 'database';
		
		if ($value != FALSE)
		{
			if ($type == 'array' && ! is_array($value))
			{
				$value = str_replace("\r\n", "\n", $value);
				$value = explode("\n", $value);
				foreach ($value as $key=>$val)
				{
					if ($val == '')
					{
						unset($value[$key]);
					}
				}
			}
		}
		
		// Save in config file
		if ($where != 'database')
		{
			if ($this->config_model->change($where, $setting, $value) == FALSE)
				$this->error(lang('ionize_message_error_writing_ionize_file'));				
		}
		// Save in DB
		else
		{
			if (is_array($value))
			{
				// Arrays are stored in DB as strings separated by "|"
				$value = implode("|", $value);
			}

			$data = array(
				'name' => $setting,
				'content' => $value
			);
			
			$this->settings_model->save_setting($data);
		}
		
		// Answer
		$this->success(lang('ionize_message_setting_saved'));				
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Saves the Cache settings
	 *
	 */
	function save_cache()
	{
		if (config_item('cache_expiration') !== $this->input->post('cache_expiration') )
		{
			if ($this->config_model->change('ionize.php', 'cache_expiration', $this->input->post('cache_expiration')) == FALSE)
				$this->error(lang('ionize_message_error_writing_ionize_file'));				
		}

		if ( ! $this->input->post('cache_expiration'))
		{
			Cache()->clear_cache();
		}

		// UI panel to update after saving
		$this->update[] = array(
			'element' => 'mainPanel',
			'url' => 'setting/technical'
		);

		// Answer
		$this->success(lang('ionize_message_cache_saved'));				
	}

	function clear_cache()
	{
		Cache()->clear_cache();

		// Answer
		$this->success(lang('ionize_message_cache_cleared'));				
		
	}

	// ------------------------------------------------------------------------


	function save_admin_url()
	{
		$admin_url = $this->input->post('admin_url');
		
		if(	$admin_url != "" && preg_match("/^([a-z0-9])+$/i", $admin_url))
		{
			if ($this->config_model->change('config.php', 'admin_url', $admin_url) == FALSE)
			{
				$this->error(lang('ionize_message_error_writing_config_file'));				
			}
			else
			{
				// Set the reload CB
				$this->_callback_reload_backend($admin_url);

				$this->success(lang('ionize_message_settings_saved'));
			}
		}	

		// Empty or incorrect chars
		$this->error(lang('ionize_message_admin_url_error'));				
	}


	// ------------------------------------------------------------------------


	/**
	 * Delete one thumb setting
	 *
	 * @param	boolean		if true, the transport is through XHR
	 *
	 */
	function delete_thumb($id)
	{
		if ($this->settings_model->delete($id) > 0)
		{
			// UI panel to update after saving
			$this->update[] = array(
				'element' => 'mainPanel',
				'url' =>  'setting/technical'
			);

			$this->success(lang('ionize_message_thumb_deleted'));				
		}		
		else
		{
			$this->error(lang('ionize_message_thumb_not_deleted'));				
		}
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Saves database settings
	 *
	 */
	function save_database()
	{
		// DB Config
		$db_config = array(
			'hostname'    =>  $_POST['db_host'],
			'username'    =>  $_POST['db_user'],
			'password'    =>  $_POST['db_pass'],
			'database'    =>  $_POST['db_name'],
			'dbdriver'    =>  $_POST['db_driver'],
			'dbprefix'    =>  '',
			'pconnect'    =>  TRUE,
			'db_debug'    =>  TRUE,
			'cache_on'    =>  FALSE,
			'cachedir'    =>  '',
			'char_set'    =>  'utf8',
			'dbcollat'    =>  'utf8_unicode_ci'
		);

		// If data are missing : Redirect || error
		if ($db_config['hostname'] == '' ||
			$db_config['dbdriver'] == '' ||
			$db_config['database'] == '' ||
			$db_config['username'] == '')
		{
			$this->error(lang('ionize_message_database_not_saved'));				
		}
		
		// Try to connect to the DB
		$dsn = $db_config['dbdriver'].'://'.$db_config['username'].':'.$db_config['password'].'@'.$db_config['hostname'].'/'.$db_config['database'];
		$db = DB($dsn, TRUE, TRUE);
		$db->db_connect();
		
		// Check if database exists !
		if ( ! $db->db_select()  )
		{
			$this->error(lang('ionize_message_database_connection_error'));
		}
		
		// Everything OK : Saving data to database config file
		else
		{
			// Write the config/database.php file
			$this->load->helper('file');

			$conf  = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n\n";
		 
			$conf .= "\$active_group = \"default\";\n";
			$conf .= "\$active_record = TRUE;\n\n";
		 
			foreach ($db_config as $key => $val)
			{
				if ( ! is_bool($val))
					$val = '"'.$val.'"';
				else
				{
					$val = ($val === TRUE ) ? "true" : "false";
				}
				
				$conf .= "\$db['default']['".$key."'] = ".$val.";\n";        
			} 
			
			// files end
			$conf .= "\n\n";
			$conf .= '/* Auto generated by Settings Administration on : '.date('Y.m.d H:i:s').' */'."\n";

			// Writing problem
			if ( ! write_file(APPPATH.'config/database.php', $conf))
			{
				$this->error(lang('ionize_message_error_writing_database_file'));				
			}
			else
			{
				// UI panel to update after saving : Structure panel
				$this->update[] = array(
					'element' => 'structurePanel',
					'url' => 'tree'
				);

				$this->success(lang('ionize_message_database_saved'));				
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Backups and force download of the backup zip file.
	 *
	 */
	function backup_database()
	{
		// Needed helpers
		$this->load->helper('download');

		$this->load->dbutil();

		// Backup the DB
		$prefs = array(
			'format' => 'zip',
			'filename' => 'dbbackup.sql',
			'newline' => "\r\n"
		);

		$backup = $this->dbutil->backup($prefs);

		force_download('dbbackup.gz', $backup);
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Saves SMTP settings
	 *
	 */
	function save_emails_settings()
	{
		// Save the website emails
		$settings = array('site_email');

		// Save settings to DB
		$this->_save_settings($settings);

		$protocol = $this->input->post('protocol');

		// Save email sending settings
		$data = array(
			'smtp_host'		=> '',
			'smtp_user'		=> '',
			'smtp_pass'		=> '',
			'smtp_port'		=> '',
            'smtp_timeout'  => '',
			'protocol'		=> $this->input->post('protocol'),
			'mailpath'		=> '',
			'charset'		=> $this->input->post('charset'),
			'mailtype'		=> $this->input->post('mailtype'),
            'newline'       => $this->input->post('newline')
		);

		switch ($protocol)
		{
			case 'mail':
				break;

			case 'sendmail':
				$data['mailpath'] = $this->input->post('mailpath');
				break;

			case 'smtp':
				$data['smtp_host'] = $this->input->post('smtp_host');
				$data['smtp_user'] = $this->input->post('smtp_user');
				$data['smtp_pass'] = $this->input->post('smtp_pass');
				$data['smtp_port'] = $this->input->post('smtp_port');
				$data['smtp_timeout'] = $this->input->post('smtp_timeout') ? $this->input->post('smtp_timeout') : '30';
				break;
		}

		// If data are missing : Redirect || error
		if ($data['protocol'] == '' )
		{
			$this->error(lang('ionize_message_smtp_not_saved'));				
		}
		// Everything OK : Saving data to database config file
		else
		{
			// Write the config/database.php file
			$this->load->helper('file');

			$conf  = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n\n";
		 
			foreach ($data as $key => $val)
			{
				$conf .= "\$config['".$key."'] = \"".$val."\";\n";
			} 
			
			// files end
			$conf .= "\n\n";
			$conf .= '/* Auto generated by Settings Administration on : '.date('Y.m.d H:i:s').' */'."\n";

			// Writing problem
			if ( ! write_file(APPPATH.'config/email.php', $conf))
			{
				$this->error(lang('ionize_message_error_writing_email_file'));				
			}
			else
			{
				$this->success(lang('ionize_message_smtp_saved'));				
			}
		}
	}


	private function _callback_reload_backend($admin_url = NULL)
	{
		$admin_url = (is_null($admin_url)) ? config_item('admin_url') : $admin_url;

		$this->callback = array(
			'fn' => 'ION.reload',
			'args' => array('url' => $admin_url)
		);
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Get settings and put them to the Settings library
	 *
	 */
	function _get_settings()
	{
		$settings = $this->settings_model->get_list();

		/* Lang settings to Settings
		 */
		$callback = create_function('$v', 'return ($v["lang"]!="") ? true : false;');
		$lang_settings = array_filter($settings, $callback );

		Settings::set_lang_settings($lang_settings, 'name', 'content');
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the views file list as an array
	 *
	 * @return array	Files names list
	 *
	 */
	function _get_view_files()
	{
		$views = array();
		
		$theme_path = FCPATH.'themes/'.Settings::get('theme').'/views';

		if (is_dir($theme_path))
		{
			$dir_iterator = new RecursiveDirectoryIterator($theme_path);
			$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
			
			foreach ($iterator as $file)
			{
				if ($file->isFile() && (substr($file->getFilename(), 0, 1) != "." && strpos($file->getFilename(), '@') === FALSE && substr($file->getFilename(), -3) == 'php') )
				{
					// Set a human readable path
					$path = str_replace($theme_path, '', $file->getPath());
					$path = str_replace('\\', '/', $path) . '/';
					$path = substr($path,1);
					
					// Set the path
					$file->path = $path;
					
					// Set the view ame (filename without .php extension)
					$file->name = str_replace('.php', '', $file->getFilename());
					
					if(substr($path, 0, 1) != '.')
					{
						$views[] = $file;
					}
				}
			}
		}
		return $views;
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves settings according to the passed settings tables
	 *
	 * @param	array	Settings keys array
	 * @param	array	Lang Settings keys array
	 *
	 */
	function _save_settings($settings=array(), $lang_settings=array())
	{
		/* 
		 * Save the lang settings first 
		 */
		if (!empty($lang_settings))
		{
			foreach(Settings::get_languages() as $language)
			{
				foreach ($lang_settings as $setting)
				{
					$data = array(
								'name' => $setting,
								'content' => ($content = $this->input->post($setting.'_'.$language['lang'])) ? $content : '',
								'lang' => $language['lang']
							);
					$this->settings_model->save_setting($data);
				}
			}
		}
		
		/*
		 * Saves settings
		 */
		foreach ($settings as $setting)
		{
			$content = '';
			
			if ($this->input->post($setting))
			{
				// Avoid or not security XSS filter
				if ( ! in_array($setting, $this->no_xss_filter))
					$content = $this->input->post($setting);
				else
				{
					$content = stripslashes($_REQUEST[$setting]);
				}
			}				
		
			$data = array(
						'name' => $setting,
						'content' => $content
					);
			
			$this->settings_model->save_setting($data);
		}
	}
	
	
	/**
	 * Generates an encrypt key
	 *
	 * @param		int		Size of he generated key
	 * @returns 	string	The generated key
	 *
	 */
	function generateEncryptKey($size=32)
	{
		$vowels = 'aeiouyAEIOUY';
		$consonants = 'bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ1234567890@#$!()';
	 
		$key = '';
		
		$alt = time() % 2;
		for ($i = 0; $i < $size; $i++) {
			if ($alt == 1) {
				$key .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$key .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $key;
	}
}
