<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Settings Controller
 *
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Website users Settings
 * @author		Ionize Dev Team
 *
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
	}


	// ------------------------------------------------------------------------


	/**
	 * Shows standard settings
	 *
	 */
	function index()
	{
		$this->_get_settings();

		$this->output('setting');
	}


	// ------------------------------------------------------------------------


	function ionize()
	{
		$this->template['displayed_admin_languages'] = Settings::get('displayed_admin_languages');

		$this->_get_settings();
	
		$this->output('setting_ionize');
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

		/* 
		 * Text editor list
		 */
		foreach(config_item('texteditors') as $t)
		{
			$this->template['texteditors'][] = $t;
		}

		/* 
		 * Filemanager list
		 */
		foreach($this->config->item('filemanagers') as $f)
		{
//			if (file_exists(APPPATH.'../themes/admin/javascript/tinymce/jscripts/tiny_mce/plugins/'.strtolower($f)))
//			{
				$this->template['filemanagers'][] = $f;
//			}
		}
		
		// Mimes types
		$mimes = Settings::get_mimes_types();
		
		$this->template['mimes'] = $mimes;

		/* 
		 * Database settings
		 */
		$this->load->dbutil();

		// If the user is here, a valid database.php config file exists !
		include(APPPATH.'config/database'.EXT);

		$this->template['db_host'] = 	$db[$active_group]['hostname'];
		$this->template['db_name'] = 	$db[$active_group]['database'];
		$this->template['db_user'] = 	$db[$active_group]['username'];
		$this->template['db_pass'] = 	'';

		$this->template['databases'] =		$this->dbutil->list_databases();


		/* 
		 * Website Email settings
		 */
		if (file_exists(APPPATH.'config/email'.EXT))
		{
			include(APPPATH.'config/email'.EXT);
		}

		$this->template['protocol'] = 		isset($config['protocol']) ? $config['protocol'] : 'mail';
		$this->template['mailpath'] = 		isset($config['mailpath']) ? $config['mailpath'] : '/usr/sbin/sendmail';
		$this->template['smtp_host'] = 		isset($config['smtp_host']) ? $config['smtp_host'] : '';
		$this->template['smtp_user'] = 		isset($config['smtp_host']) ? $config['smtp_user'] : '';
		$this->template['smtp_pass'] = 		isset($config['smtp_pass']) ? $config['smtp_pass'] : '';
		$this->template['smtp_port'] = 		isset($config['smtp_port']) ? $config['smtp_port'] : '25';
		$this->template['charset'] = 		isset($config['charset']) ? $config['charset'] : 'utf-8';
		$this->template['mailtype'] = 		isset($config['mailtype']) ? $config['mailtype'] : 'text';


		/*
		 * Thumbs settings
		 */
		$this->template['thumbs'] = $this->settings_model->get_list(array('name like' => 'thumb_%'));
		
		
		// Cache
		$this->template['cache_enabled'] = config_item('cache_enabled');
		$this->template['cache_time'] = config_item('cache_time');
		
		// Antispam key
		$this->template['form_antispam_key'] = config_item('form_antispam_key');

		$this->template['article_allowed_tags'] = explode(',', Settings::get('article_allowed_tags') );

		$this->output('setting_technical');
	}


	// ------------------------------------------------------------------------


	/**
	 * Shows themes settings
	 *
	 */
	function themes()
	{
		/* 
		 * Get Themes list
		 *
		 */
		$themes = $themes_admin = array();
		$handle = opendir(APPPATH.'../themes');
		if ($handle)
		{
			while ( false !== ($theme = readdir($handle)) )
			{
				// make sure we don't map silly dirs like .svn, or . or ..
				if (substr($theme, 0, 1) != "." && $theme != 'index.html' && substr($theme,0,5) != 'admin')
					$themes[] = $theme;
				else if(substr($theme,0,5) == 'admin')
					$themes_admin[] = $theme;
			}
		}
		$this->template['themes'] = $themes;
		$this->template['themes_admin'] = $themes_admin;


		/* 
		 * Get Current theme views list
		 *
		 */
		// Filesystem files list
		$files = $this->_get_view_files();

		// Recorded views definitions 
		if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
			require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');

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

		
		/* 
		 * Get Special URI definition
		 *
		 */
		

		$this->output('setting_theme');
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
		$filepath = APPPATH.'../themes/'.Settings::get('theme').'/views/';

		// View sub-folder ?
		if ($path != '')
			$filepath .= $path.'/';

		// Get file content
		$content = file_get_contents($filepath.$view.'.php');
		$content = str_replace('<', '&lt;', $content);
		$content = str_replace('>', '&gt;', $content);

		$this->template['content'] = $content;

		$this->output('setting_edit_view');
	}

	// ------------------------------------------------------------------------


	/**
	 * Saves one view file
	 * 
	 *
	 */
	function save_view()
	{
		if ( $this->connect->is('super-admins'))
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


	/**
	 * Saves settings
	 *
	 */
	function save()
	{
		// Settings to save
		$lang_settings = array('meta_keywords', 'meta_description', 'site_title');

		// Save settings to DB
		$this->_save_settings(array(), $lang_settings);

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
			$displayed_admin_languages = config_item('default_lang');
		}

		$data = array(
					'name' => 'displayed_admin_languages',
					'content' => $displayed_admin_languages
				);
		
		$this->settings_model->save_setting($data);


		// Other Settings to save
		$settings = array('show_help_tips', 'display_connected_label', 'date_format', 'default_admin_lang');

		// Save settings to DB
		$this->_save_settings($settings);


		// Answer
		$this->callback = array(
			'fn' => 'ION.reload',
			'args' => array('url' => config_item('admin_url'))
		);

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


	/**
	 * Saves technical settings
	 *
	 */
	function save_technical()
	{
		$this->load->model('config_model', '', true);
		$this->load->helper('string_helper');

		// Settings to save
		$settings = array(	'texteditor', 'filemanager', 'files_path', 
							'ftp_dir', 'ftp_host', 'ftp_user', 'ftp_password', 
							'tinybuttons1','tinybuttons2','tinybuttons3','tinyblockformats',
							'google_analytics', 'system_thumb_list', 'system_thumb_edition','media_thumb_size', 'picture_max_width', 'picture_max_height',
							'use_extend_fields', 'filemanager_file_types');
							
		
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

		// Files path
		if ( $this->input->post('files_path') == FALSE)
		{
			$this->error(lang('ionize_message_error_no_files_path'));				
		}
		else
		{
			if (config_item('files_path') != $this->input->post('files_path') )
			{
				if ($this->config_model->change('ionize.php', 'files_path', trim_slashes($this->input->post('files_path')).'/') == FALSE)
				{
					$this->error(lang('ionize_message_error_writing_ionize_file'));				
				}
			}
		}		
		
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
		
		// Tags allowed in articles
		$tags = $this->input->post('article_allowed_tags');
		if ( ! $tags) $tags = array();
		if (in_array('table', $tags)) $tags = array_merge($tags, array('thead','tbody','tfoot','tr','th','td'));
		if (in_array('object', $tags)) $tags = array_merge($tags, array('param', 'embed'));
		if (in_array('dl', $tags)) $tags = array_merge($tags, array('dt','dd'));
		if (in_array('img', $tags)) $tags = array_merge($tags, array('map'));
		
		// Standard allowed tags
		$tags = array_merge($tags, array('p','a','ul','ol','li','br','b','strong'));
		
		$article_allowed_tags = array(
					'name' => 'article_allowed_tags',
					'content' => implode(',', $tags)
				);
		$this->settings_model->save_setting($article_allowed_tags);

		
		$this->callback = array(
			'fn' => 'ION.reload',
			'args' => array('url' => config_item('admin_url'))
		);

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
	 
		$conf .= "\$views = " . (String) var_export($viewsArray, true) .";\n";
		
		// files end
		$conf .= "\n\n";
		$conf .= '/* End of file views.php */'."\n";
		$conf .= '/* Auto generated by Themes Administration on : '.date('Y.m.d H:i:s').' */'."\n";
		$conf .= '/* Location: ' .APPPATH.'../themes/'.Settings::get('theme'). '/config/views.php */'."\n";

		// Writing problem
		if ( ! write_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php', $conf))
		{
			$this->error(lang('ionize_message_error_writing_file'));				
		}
		else
		{
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
					'url' => admin_url() . 'setting/technical'
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


	// ------------------------------------------------------------------------


	/**
	 * Saves the Cache settings
	 *
	 */
	function save_cache()
	{
		$this->load->model('config_model', '', true);

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
			'url' => admin_url() . 'setting/technical'
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
		$this->load->model('config_model', '', true);
		
		$admin_url = $this->input->post('admin_url');
		
		if(	$admin_url != "" && preg_match("/^([a-z0-9])+$/i", $admin_url))
		{
			if ($this->config_model->change('config.php', 'admin_url', $admin_url) == FALSE)
			{
				$this->error(lang('ionize_message_error_writing_config_file'));				
			}
			else
			{
				$this->callback = array(
					'fn' => 'ION.reload',
					'args' => array('url' => $admin_url)
				);

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
				'url' =>  admin_url() . 'setting/technical'
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
			'pconnect'    =>  true,
			'db_debug'    =>  true,
			'cache_on'    =>  false,
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
		$db = DB($dsn, true, true);
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
					$val = ($val === true ) ? "true" : "false";
				}
				
				$conf .= "\$db['default']['".$key."'] = ".$val.";\n";        
			} 
			
			// files end
			$conf .= "\n\n";
			$conf .= '/* End of file database.php */'."\n";
			$conf .= '/* Auto generated by Settings Administration on : '.date('Y.m.d H:i:s').' */'."\n";
			$conf .= '/* Location: ./application/config/database.php */'."\n";
				 
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
					'url' => admin_url() . 'tree'
				);

				$this->success(lang('ionize_message_database_saved'));				
			}
		}
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Saves SMTP settings
	 *
	 */
	function save_smtp()
	{
		/*
		 * Save the website email
		 *
		 */
		$settings = array('site_email');

		// Save settings to DB
		$this->_save_settings($settings);
	
	
		/*
		 * Save email sending settings
		 *
		 */
		$data = array(
			'smtp_host'		=> '',
			'smtp_user'		=> '',
			'smtp_pass'		=> '',
			'smtp_port'		=> '',
			'protocol'		=> '',
			'mailpath'		=> '',
			'charset'		=> '',
			'mailtype'		=> ''
		);
		
		// Post data
		foreach ($_POST as $key => $val)
		{
			if (isset($data[$key]))
				$data[$key] = $val;
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
			
			$db_config = array(
				'protocol'    =>  '"'.$data['protocol'].'"',
				'mailpath'    =>  '"'.$data['mailpath'].'"',
				'smtp_host'    =>  '"'.$data['smtp_host'].'"',
				'smtp_user'    =>  '"'.$data['smtp_user'].'"',
				'smtp_pass'    =>  '"'.$data['smtp_pass'].'"',
				'smtp_port'    =>  '"'.$data['smtp_port'].'"',
				'mailtype'    =>  '"'.$data['mailtype'].'"',
				'charset'    =>  '"'.$data['charset'].'"'
			);	
			
			$conf  = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n\n";
		 
			foreach ($db_config as $key => $val)
			{
				$conf .= "\$config['".$key."'] = ".$val.";\n";        
			} 
			
			// files end
			$conf .= "\n\n";
			$conf .= '/* End of file email.php */'."\n";
			$conf .= '/* Auto generated by Settings Administration on : '.date('Y.m.d H:i:s').' */'."\n";
			$conf .= '/* Location: ./application/config/email.php */'."\n";
				 
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
		
		$theme_path = APPPATH.'../themes/'.Settings::get('theme').'/views';

		if (is_dir($theme_path))
		{
			$dir_iterator = new RecursiveDirectoryIterator($theme_path);
			$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
			
			foreach ($iterator as $file)
			{
				if ($file->isFile() && (substr($file->getFilename(), 0, 1) != ".") )
				{
					// Set a human readable path
					$path = str_replace($theme_path, '', $file->getPath());
					$path = str_replace('\\', '/', $path) . '/';
					$path = substr($path,1);
					
					// Set the path
					$file->path = $path;
					
					// Set the view ame (filename without .php extension)
					$file->name = str_replace('.php', '', $file->getFilename());
					
					$views[] = $file;
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

/* End of file setting.php */
/* Location: ./application/controllers/admin/setting.php */