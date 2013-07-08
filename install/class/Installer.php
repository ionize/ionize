<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.90
 */

// ------------------------------------------------------------------------

/**
 * Ionize Installer
 *
 * @package		Ionize
 * @subpackage	Installer
 * @category	Installer
 * @author		Ionize Dev Team
 *
 */

class Installer
{
	private static $instance;

	private $template;
	
	public $lang = array();
	
	public $db;

	
	// --------------------------------------------------------------------

	
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Default language
		$lang_code = 'en';
		
		// Check GET language
		if (is_array($_GET) && isset($_GET['lang']) )
		{
			if (is_file(ROOTPATH.'install/language/'.$_GET['lang'].'/install_lang.php'))
				$lang = $_GET['lang'];
		}
		
		$this->template['lang'] = $lang_code;
		
		// Include language file and merge it to language var
		$lang = array();
		include(ROOTPATH.'install/language/'. $lang_code .'/install_lang.php');
		
		$this->lang = array_merge($this->lang, $lang);

		// Get all available translations
		$dirs = scandir(ROOTPATH.'install/language');
		
		$languages = array();
		foreach($dirs as $dir)
		{
			if (is_dir(ROOTPATH.'install/language/'.$dir))
			{
				if (is_file(ROOTPATH.'install/language/'.$dir.'/install_lang.php') and strpos($dir, '.') === false)
				{
					$languages[] = $dir;
				}
			}
		}
		$this->template['languages'] = $languages;
		
		// Put the current URL to template (for language selection)
		$this->template['current_url'] = (isset($_GET['step'])) ? '?step='.$_GET['step'] : '?step=checkconfig';
	}

	
	// --------------------------------------------------------------------


	/**
	 * Returns current instance of Installer
	 *
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

	
	// --------------------------------------------------------------------


	/**
	 * Checks the config settings
	 *
	 */
	function check_config()
	{
		// PHP version >= 5
		$this->template['php_version'] = version_compare(substr(phpversion(), 0, 3), '5.0', '>=');

		// MySQL support
		$this->template['mysql_support']  = function_exists('mysql_connect');
		
		// Safe Mode
		$this->template['safe_mode']  = (ini_get('safe_mode')) ? FALSE : TRUE;
		
		// Files upload
		$this->template['file_uploads'] = (ini_get('file_uploads')) ? TRUE : FALSE;
		
		// GD lib
		$this->template['gd_lib'] = function_exists('imagecreatetruecolor');
		
		// Check files rights
		$files = array(
			'application/config/config.php',
			'application/config/database.php',
			'application/config/email.php',
			'application/config/language.php',
			'application/config/modules.php'
		);

		$check_files = array();
		foreach($files as $file)
			$check_files[$file] = is_really_writable(ROOTPATH . $file);

		// Check folders rights
		$folders = array(
			'application/config',
			'files',
			'themes'
		);
		
		$check_folders = array();
		foreach($folders as $folder)
			$check_folders[$folder] = $this->_test_dir(ROOTPATH . $folder, true);
		
		$this->template['check_files'] = $check_files;
		$this->template['check_folders'] = $check_folders;

		// Message to user if one setting is false
		foreach($this->template as $config)
		{
			if ( ! $config)
			{
				$this->template['next'] = false;
				$this->_send_error('check_config', lang('config_check_errors'));
			}
		}
		
		// Outputs the view
		$this->output('check_config');
	}

	
	// --------------------------------------------------------------------


	/**
	 * Prints out the database form
	 *
	 */
	function configure_database()
	{
		if ( ! isset($_POST['action']))
		{
			$data = array('db_driver', 'db_hostname', 'db_name', 'db_username');

			$this->_feed_blank_template($data);
			
			$this->output('database');
		}
		else
		{
			$this->_save_database_settings();
		}
	}

	
	// --------------------------------------------------------------------


	/**
	 * Prints out the user form
	 *
	 */
	function configure_user()
	{
		// Check if an Admin user already exists in the DB
		$this->template['skip'] = FALSE;

		$this->db_connect();

		$this->db->where('id_role', '1');
		$query = $this->db->get('user');

		if ($query->num_rows() > 0)
		{
			$this->template['skip'] = TRUE;
		}


		if ( ! isset($_POST['action']))
		{
			// Skip TRUE and no POST = Admin user already exists
			if ($this->template['skip'] == TRUE)
			{
				$this->template['message_type'] = 'alert';
				$this->template['message'] = lang('user_info_admin_exists');
			}
			
			// Prepare data
			$data = array('username', 'screen_name', 'email', 'encryption_key');

			$this->_feed_blank_template($data);
			
			// Encryption key : check if one exists
			require(ROOTPATH . 'application/config/config.php');
			if ($config['encryption_key'] == '')
			{
				$this->template['encryption_key'] = $this->generateEncryptKey();
			}

			$this->output('user');
		}
		else
		{
			$this->_save_user();
			
			$this->db_connect();

			header("Location: ".BASEURL.'install/?step=data&lang='.$this->template['lang'], TRUE, 302);
		}
	}
	
	
	// --------------------------------------------------------------------


	/**
	 * Installs the example data
	 *
	 */
	function install_data()
	{
		if ( ! isset($_POST['action']))
		{
			$this->db_connect();

			// Check if the DB was migrated : If yes, no sample data install
			$query = $this->db->get('page');
			if ($query->num_rows() > 2)
			{
				header("Location: ".BASEURL.'install/?step=finish&lang='.$this->template['lang'], TRUE, 302);
			}

			$this->template['base_url'] = BASEURL;
			$this->output('data');
		}
		else
		{
			// Install DATABASE example data
			require(ROOTPATH . 'application/config/database.php');
			
			// Connect to DB
			$config = $db['default'];
			$dsn = $config['dbdriver'].'://'.$config['username'].':'.$config['password'].'@'.$config['hostname'].'/'.$config['database'];
			
			$this->db = DB($dsn, true, true);

			// Try connect or exit
			if ( ! $this->db->db_connect())
			{
				$this->_send_error('data', lang('database_error_could_not_connect'), $_POST);
			}
			
			// The database should exists, so try to connect
			if ( ! $this->db->db_select())
			{
				$this->_send_error('database', lang('database_error_database_dont_exists'), $_POST);
			}
			else
			{
				$file = read_file('./database/demo_data.sql');

				$requests = explode('--#--', $file);

				foreach($requests as $request)
				{
					$this->db->simple_query($request);
				}

				// Get languages and update the language config file
				$query = $this->db->get('lang');
				$data = $query->result_array();
				$this->_save_language_config_file($data);

				// Update the Demo content regarding the user's choosen language
				$default_lang = $this->_get_default_lang();
				$data = array('lang' => $default_lang['lang']);
				$where = array('lang' => 'en');

				$this->db->where($where);
				$this->db->update('page_lang', $data);

				$this->db->where($where);
				$this->db->update('article_lang', $data);

				$this->db->where($where);
				$this->db->update('category_lang', $data);

				$this->db->where($where);
				$this->db->update('media_lang', $data);

				$this->db->where($where);
				$this->db->update('url', $data);
			}

			header("Location: ".BASEURL.'install/?step=finish&lang='.$this->template['lang'], TRUE, 302);
		}
	}

	
	// --------------------------------------------------------------------

	
	/**
	 * Migrate the DB if needed
	 * No migration will be done if it is not needed, even this script is called.
	 *
	 */
	function migrate()
	{
		$migration_files = $this->_get_migration_files();

		// Migration not validated
		if ( ! isset($_POST['action']))
		{
		
			$this->template['database_migration_text'] = '';
			$this->template['button_label'] = lang('button_start_migrate');
		
			if ( ! empty($migration_files))
			{
				if (in_array('migration_0.9.9_1.0.0.xml', $migration_files)) $this->template['database_migration_from'] = lang('database_migration_from') . '<b class="highlight2">0.9.9</b>';
				if (in_array('migration_0.9.7_0.9.9.xml', $migration_files)) $this->template['database_migration_from'] = lang('database_migration_from') . '<b class="highlight2">0.9.7</b>';
				if (in_array('migration_0.9.6_0.9.7.xml', $migration_files)) $this->template['database_migration_from'] = lang('database_migration_from') . '<b class="highlight2">0.9.6</b>';
				if (in_array('migration_0.9.5_0.9.6.xml', $migration_files)) $this->template['database_migration_from'] = lang('database_migration_from') . '<b class="highlight2">0.9.5</b>';			
				if (in_array('migration_0.9.4_0.9.5.xml', $migration_files)) $this->template['database_migration_from'] = lang('database_migration_from') . '<b class="highlight2">0.9.4</b>';			
				if (in_array('migration_0.93_0.9.4.xml', $migration_files)) $this->template['database_migration_from'] = lang('database_migration_from') . '<b class="highlight2">0.9.3</b>';			
				if (in_array('migration_0.92_0.93.xml', $migration_files)) $this->template['database_migration_from'] = lang('database_migration_from') . '<b class="highlight2">0.9.2</b>';			
				if (in_array('migration_0.90_0.92.xml', $migration_files)) $this->template['database_migration_from'] = lang('database_migration_from') . '<b class="highlight2">0.9.0</b>';
				
				$this->template['database_migration_text'] = lang('database_migration_text');			
			}
			else
			{
				$this->template['button_label'] = lang('button_next_step');
				$this->template['database_migration_from'] = lang('database_no_migration_needed');
			}
			
			$this->output('migrate');
		}
		else
		{
			$this->db_connect();

			ini_set('allow_url_fopen', 'on');

			// Migration
			foreach ($migration_files as $file)
			{
				$xml = simplexml_load_file('./database/'.$file);

				$queries = $xml->xpath('/sql/query');
	
				foreach ($queries as $query)
				{
					$this->db->query($query);
				}
			}

			// Rebuild the config/language.php file for consistency
			$query = $this->db->get('lang');
			if ($query->num_rows() > 0)
			{
				$langs = $query->result_array();
				$this->_save_language_config_file($langs);
			}
	
			/*
			 * Migration to 0.9.4
			 * Users account migration
			 *
			 */
			if (in_array('migration_0.93_0.9.4.xml', $migration_files))
			{
				log_message('debug', 'Migration from 0.9.3');

				$query = $this->db->get('users');
				
				if ($query->num_rows() > 0)
				{
					foreach ($query->result_array() as $user)
					{
						if ($user['salt'] == '')
						{
							$user['salt'] = $this->get_salt();
							
							$user['password'] = $this->_encrypt094($this->_decrypt093($user['password'], $user), $user);
							
							$this->db->where('username', $user['username']);
							$this->db->update('users', $user);
						}
					}						
				}
			}
			
			/*
			 * Migration to 0.9.5
			 * Migration to Connect Lib
			 *
			 */
			if (in_array('migration_0.9.4_0.9.5.xml', $migration_files))
			{
				log_message('debug', 'Migration from 0.9.5');

				// Get the encryption key and move it to config/config.php
				$enc = false;
				$config = array();
				
				if (is_file(ROOTPATH . 'application/config/access.php'))
				{
					include(ROOTPATH . 'application/config/access.php');
				}
				
				if ( ! empty($config['encrypt_key']) &&  $config['encrypt_key'] != '')
				{
					$enc =  $config['encrypt_key'];
				}
				
				// Write the config file and migrates users accounts
				if ($enc !== false)
				{
					$ret = false;
					$config_file = file(APPPATH . 'config/config' . EXT);

					$buff = '';
					foreach ($config_file as $line)
					{
						if (strpos($line, "encryption_key") !== FALSE) 
						{
							$line = "\$config['encryption_key'] = '".$enc."';\n";
						}
					    $buff .= $line;
					}
					
					if ($buff != '')
						$ret = @file_put_contents(APPPATH . 'config/config' . EXT, $buff);
					
					if ( ! $ret)
					{
						$this->_send_error('migrate', lang('settings_error_write_rights_config'), $_POST);
					}

					// Updates the users account
					$query = $this->db->get('users');
					
					if ($query->num_rows() > 0)
					{
						foreach ($query->result_array() as $user)
						{
							$pass = $this->_decrypt094($user['password'], $user);
							$enc = $this->_encrypt($pass, $user);
											
							$user['password'] = $enc;
						
							$this->db->where('username', $user['username']);
							$this->db->update('users', $user);
						}						
					}
				}
				else
				{
					$this->_send_error('user', lang('no_encryption_key_found'), $_POST);
				}
			}

			/*
			 * Migration to 0.9.7
			 * Migration to CI2
			 *
			 */
			if (in_array('migration_0.9.6_0.9.7.xml', $migration_files))
			{
				log_message('debug', 'Migration from 0.9.6');

				// Updates the users account
				$query = $this->db->get('users');
				
				if ($query->num_rows() > 0)
				{
					foreach ($query->result_array() as $user)
					{
						$old_decoded_pass = $this->_decrypt096($user['password'], $user);
						$encoded_pass = $this->_encrypt($old_decoded_pass, $user);
						
						$user['password'] = $encoded_pass;
						$this->db->where('username', $user['username']);
						$this->db->update('users', $user);
					}						
				}
			}

			/*
			 * Migration to 0.9.9
			 * Put url_mode to 'short'
			 */
			if (in_array('migration_0.9.7_0.9.9.xml', $migration_files))
			{
				log_message('debug', 'Migration from 0.9.7');

				require_once('./class/Config.php');

				// Save version
				$conf = new ION_Config(APPPATH.'config/', 'ionize.php');
				$conf->set_config('url_mode', 'short');
				$conf->save();
			}

			/*
			 * Migration to 1.0
			 * Coming soon...
			 *
			 */
			if (in_array('migration_0.9.9_1.0.xml', $migration_files))
			{
			}

			header("Location: ".BASEURL.'install/?step=user&lang='.$this->template['lang'], TRUE, 302);
		}
	}

	function migrate_users_to_ci2()
	{
		$this->db_connect();
		
		// Updates the users account
		$query = $this->db->get('users');
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $user)
			{
				$old_decoded_pass = $this->_decrypt096($user['password'], $user);
				$encoded_pass = $this->_encrypt($old_decoded_pass, $user);
				
				$user['password'] = $encoded_pass;
				$this->db->where('username', $user['username']);
				$this->db->update('users', $user);
				
				echo($user['username'] . ' : ' . 'done<br/>');
			}						
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Saves the website default settings
	 * - Default lang
	 *
	 *
	 */
	function settings()
	{
		if ( ! isset($_POST['action']))
		{
			$this->template['lang_code'] = 'en';
			$this->template['lang_name'] = 'english';
			$this->template['admin_url'] = 'admin';

			$this->output('settings');
		}
		else
		{
			$ret = $this->_save_settings();

			if ($ret)
			{
				header("Location: ".BASEURL.'install/?step=user&lang='.$this->template['lang'], TRUE, 302);
			}
			else
			{
				$this->_send_error('settings', lang('settings_error_write_rights'), $_POST);
			}
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Finish installation
	 *
	 */
	function finish()
	{
		// Get the Language config file
		include(APPPATH.'config/language.php');

		$this->db_connect();

		// Create 404
		if ( ! $this->_exists(array('name'=>'404'), 'page'))
		{
			$page_code = '404';
			$data = array('id_menu'=>'2', 'name'=>$page_code, 'online'=>'1', 'appears'=>'0', 'url'=>$page_code, 'title'=>$page_code, 'subtitle'=>"Can't find requested page." );
			$id_page = $this->_create_page($data);

			// Article
			$data = array('name' =>$page_code, 'url'=>$page_code, 'title'=>$page_code, 'content'=>'<p>The content you asked for was not found !</p>');
			$this->_create_article($data, $id_page);
		}

		// Create 401
		if ( ! $this->_exists(array('name'=>'401'), 'page'))
		{
			$page_code = '401';
			$data = array('id_menu'=>'2', 'name'=>$page_code, 'online'=>'1', 'appears'=>'0', 'url'=>$page_code, 'title'=>$page_code, 'subtitle'=>"Login needed" );
			$id_page = $this->_create_page($data);

			// Article
			$data = array('name' =>$page_code, 'url'=>$page_code, 'title'=>$page_code, 'subtitle'=>"Please login", 'content'=>'<p>Please login to see this content.</p>');
			$this->_create_article($data, $id_page);
		}

		// Create 403
		if ( ! $this->_exists(array('name'=>'403'), 'page'))
		{
			$page_code = '403';
			$data = array('id_menu'=>'2', 'name'=>$page_code, 'online'=>'1', 'appears'=>'0', 'url'=>$page_code, 'title'=>$page_code, 'subtitle'=>"Forbidden" );
			$id_page = $this->_create_page($data);

			// Article
			$data = array('name' =>$page_code, 'url'=>$page_code, 'title'=>$page_code, 'subtitle'=>"Forbidden", 'content'=>'<p>This content is forbidden.</p>');
			$this->_create_article($data, $id_page);
		}

		// Default minimal welcome page
		if ( ! $this->_exists(array('id_menu'=>'1'), 'page'))
		{
			$data = array('id_menu'=>'1', 'name'=>'home', 'url'=>'home', 'online'=>'1', 'appears'=>'1', 'home'=>'1', 'title'=>'Welcome to ionize' );
			$id_page = $this->_create_page($data);

			// Article
			$data = array(
				'name'=>'welcome',
				'url'=>'welcome',
				'title'=>'Welcome',
				'content'=>'<p>For more information about building a website with Ionize, you can:</p> <ul><li>Download & read <a href="http://www.ionizecms.com">the Documentation</a></li><li>Visit <a href="http://www.ionizecms.com/forum">the Community Forum</a></li></ul><p>Have fun !</p>'
			);
			$this->_create_article($data, $id_page);
		}

		// Default settings
		$langs = array_keys($config['available_languages']);

		foreach ($langs as $lang)
		{
			if ( ! $this->_exists(array('lang' => $lang, 'name' => 'site_title'), 'setting'))
			{
				$this->db->insert(
					'setting',
					array(
						'name' => 'site_title',
						'lang' => $lang,
						'content' => 'My website'
					)
				);
			}
		}

		$this->template['base_url'] = BASEURL;
		$this->output('finish');
	}


	public function _create_page($data)
	{
		include(APPPATH.'config/language.php');
		$langs = array_keys($config['available_languages']);

		$page_data = $this->_clean_data($data, 'page');
		$this->db->insert('page', $page_data);
		$id_page = $this->db->insert_id();

		// Page lang data
		if ($id_page)
		{
			foreach ($langs as $lang)
			{
				$lang_data = $this->_clean_data($data, 'page_lang');
				$lang_data['id_page'] = $id_page;
				$lang_data['lang'] = $lang;
				$this->db->insert('page_lang', $lang_data);
			}
		}
		return $id_page;
	}


	public function _create_article($data, $id_page='0')
	{
		include(APPPATH.'config/language.php');
		$langs = array_keys($config['available_languages']);

		$article_data = $this->_clean_data($data, 'article');

		$this->db->insert('article', $article_data);
		$id_article = $this->db->insert_id();

		// link to page
		$link_data = array(
			'id_page' => $id_page,
			'id_article' => $id_article,
			'online' => isset($data['online']) ? $data['online'] : '1'
		);
		$this->db->insert('page_article', $link_data);

		// Article lang data
		$lang_data = $this->_clean_data($data, 'article_lang');

		foreach ($langs as $lang)
		{
			$lang_data['id_article'] = $id_article;
			$lang_data['lang'] = $lang;

			$this->db->insert('article_lang', $lang_data);
		}

		return $id_article;
	}



	// --------------------------------------------------------------------


	/**
	 * Saves database settings
	 *
	 */
	function _save_database_settings()
	{
		$fields = array('db_driver', 'db_hostname', 'db_name', 'db_username');

		// Migration ? If yes, it will be set to true before the installer try to create the tables
		$this_is_a_migration = FALSE;

		// Post data
		$data = array();
		
		// Check each mandatory POST data
		foreach ($fields as $key)
		{
			if (isset($_POST[$key]))
			{
				$val = $_POST[$key];
				
				// Break if $val == ''
				if ($val == '')
				{
					$this->_send_error('database', lang('database_error_missing_settings'), $_POST);
				}
				
				if ( ! get_magic_quotes_gpc())
					$val = addslashes($val);
			
				$data[$key] = trim($val);
			}
		}


		// Try connect or exit
		if ( ! $this->_db_connect($data))
		{
			$this->_send_error('database', lang('database_error_coud_not_connect'), $_POST);
		}
		

		// If database doesn't exists, create it !
		if ( ! $this->db->db_select())
		{
			// Loads CI DB Forge class
			require_once(BASEPATH.'database/DB_forge'.EXT);
			require_once(BASEPATH.'database/drivers/'.$this->db->dbdriver.'/'.$this->db->dbdriver.'_forge'.EXT);
			
			$class = 'CI_DB_'.$this->db->dbdriver.'_forge';
	
			$this->dbforge = new $class();
			
			if ( ! $this->dbforge->create_database($data['db_name']))
			{
				$this->_send_error('database', lang('database_error_coud_not_create_database'), $_POST);
			}
			else
			{
				// Put information about database creation to view
				$this->template['database_created'] = lang('database_created');
				$this->template['database_name'] = $data['db_name'];
			}
		}

		
		// Select database, save database config file and launch SQL table creation script
		// The database should exists, so try to connect
		if ( ! $this->db->db_select())
		{
			$this->_send_error('database', lang('database_error_database_dont_exists'), $_POST);
		}
		else
		{
			// Everything's OK, save config/database.php
			if ( ! $this->_save_database_settings_to_file($data))
			{
				$this->_send_error('database', lang('database_error_writing_config_file'), $_POST);
			}

			// Check if one Ionize table already exists. If yes, this is a migration
			if ($this->db->table_exists('setting') == true)
			{
				$this_is_a_migration = TRUE;
			}
			
			// Load database XML script
			$xml = simplexml_load_file('./database/database.xml');

			// Get tables & content
			$tables = $xml->xpath('/sql/tables/query');
			$content = $xml->xpath('/sql/content/query');
			
			// Create tables
			// In case of migration, this script will only create the missing tables
			foreach ($tables as $table)
			{
				$this->db->query($table);
			}
			
			// Checks the write rights of the MySQL user
			// by insertion of dummy data in the settings table
			if ($this->db->query("INSERT INTO setting ('name', 'content') values('test', 'test')"))
			{
				$this->_send_error('database', lang('database_error_coud_not_write_database'), $_POST);			
			}
			else
			{
				$this->db->query("DELETE FROM setting WHERE name='test'");
			}
			
			// Basis content insert
			// In case of migration (content already exists), the existing content will not be overwritten
			foreach ($content as $sql)
			{
				$this->db->query($sql);
			}
			
			// Users message
			$this->template['database_installation_message'] = lang('database_success_install');
		}
		
		// Check for migration and redirect
		$migration_files = $this->_get_migration_files();

		if ( ! empty($migration_files))
		{
			header("Location: ".BASEURL.'install/?step=migrate&lang='.$this->template['lang'], TRUE, 302);
		}
		else
		{
			// If the installer just created the tables go to the Settings panel
			if ($this_is_a_migration == FALSE)
			{
				header("Location: ".BASEURL.'install/?step=settings&lang='.$this->template['lang'], TRUE, 302);
			}
			// Else, go to the user creation step
			else
			{
				header("Location: ".BASEURL.'install/?step=user&lang='.$this->template['lang'], TRUE, 302);
			}
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Saves the user informations
	 *
	 */
	function _save_user()
	{
		// Config library
		require_once('./class/Config.php');

		// Saves the new encryption key
		if ( !empty($_POST['encryption_key']) && strlen($_POST['encryption_key']) > 31)
		{
			include(APPPATH.'config/config.php');
			include(APPPATH.'config/user.php');

			if ($config['encryption_key'] == '')
			{
				$conf = new ION_Config(APPPATH.'config/', 'config.php');
		
				$conf->set_config('encryption_key', $_POST['encryption_key']);
		
				if ($conf->save() == FALSE)
				{
					$this->_send_error('user', lang('settings_error_write_rights_config'), $_POST);
				}
			}
		}

		// Saves the users data
		$fields = array('username', 'screen_name', 'email', 'password', 'password2');
		
		// Post data
		$data = array();
		
		// Check each mandatory POST data
		foreach ($fields as $key)
		{
			if (isset($_POST[$key]))
			{
				$val = $_POST[$key];
				
				// Exit if $val == ''
				if ($val == '')
				{
					$this->_send_error('user', lang('user_error_missing_settings'), $_POST);
				}
				
				// Exit if username or password < 4 chars
				if (($key == 'username' OR $key == 'password') && strlen($val) < 4)
				{
					$this->_send_error('user', lang('user_error_not_enough_char'), $_POST);
				}
				
				if ( ! get_magic_quotes_gpc())
					$val = addslashes($val);
			
				$data[$key] = trim($val);
			}
		}
		
		// Check email
		if ( ! valid_email($data['email']) )
		{
			$this->_send_error('user', lang('user_error_email_not_valid'), $_POST);
		}
		
		// Check password
		if ( ! ($data['password'] == $data['password2']) )
		{
			$this->_send_error('user', lang('user_error_passwords_not_equal'), $_POST);
		}
		
		// Here is everything OK, we can create the user
		$data['join_date'] = date('Y-m-d H:i:s');
		$data['salt'] = $this->get_salt();
		$data['password'] = $this->_encrypt($data['password'], $data);
		$data['id_role'] = '1';
		
		// Clean data array
		unset($data['password2']);

		// DB save
		$this->db_connect();
		
		// Check if the user exists
		$this->db->where('username', $data['username']);
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0)
		{
			// updates the user
			$this->db->where('username', $data['username']);
			$this->db->update('user', $data);
		}
		else
		{
			// insert the user
			$this->db->insert('user', $data);
		}
	}

	
	// --------------------------------------------------------------------


	/**
	 * Saves the website settings
	 *
	 */
	function _save_settings()
	{
		// Config library
		require_once('./class/Config.php');
		
	
		// Check if data are empty
		if (empty($_POST['lang_code'])) { $this->_send_error('settings', lang('settings_error_missing_lang_code'), $_POST);}
		if (empty($_POST['lang_name'])) { $this->_send_error('settings', lang('settings_error_missing_lang_name'), $_POST);}
		
		// Lang code must be on 2 or 3 chars
		if (strlen($_POST['lang_code']) > 3) { $this->_send_error('settings', lang('settings_error_lang_code_2_chars'), $_POST);}
		
		// Check if admin URL is correct
		if ( ! preg_match("/^([a-z0-9])+$/i", $_POST['admin_url']) OR (empty($_POST['admin_url'])) ) { $this->_send_error('settings', lang('settings_error_admin_url'), $_POST);}
		
		// Save the Admin URL
		$conf = new ION_Config(APPPATH.'config/', 'config.php');

		$conf->set_config('admin_url', $_POST['admin_url']);

		if ($conf->save() == FALSE)
		{
			$this->_send_error('settings', lang('settings_error_write_rights_config'), $_POST);
		}

		// DB save
		$this->db_connect();
		
		$data = array(
			'lang' => $_POST['lang_code'],
			'name' => $_POST['lang_name'],
			'online' => '1',
			'def' => '1',
			'ordering' => '1'
		);
		
		// Check if the lang exists
		$this->db->where('lang', $_POST['lang_code']);
		$query = $this->db->get('lang');
	
		if ($query->num_rows() > 0)
		{
			// updates the lang
			$this->db->where('lang', $_POST['lang_code']);
			$this->db->update('lang', $data);
		}
		else
		{
			// insert the lang
			$this->db->insert('lang', $data);	
		}
		
		$data = array(0 => $data);
		
		return $this->_save_language_config_file($data);
	}


	// --------------------------------------------------------------------


	/**
	 * Outputs the view
	 *
	 */
	function output($_view)
	{
		GLOBAL $config;
		if (!isset($this->template['next'])) {$this->template['next'] = true; }
		
		$this->template['version'] = $config['version'];
		
		extract($this->template);
		
		include('./views/header.php');
		include('./views/' . $_view . '.php');
		include('./views/footer.php');
	}


	// --------------------------------------------------------------------


	/**
	 * Generates a random salt value.
	 *
	 * @return String	Hash value
	 *
	 **/	
	function get_salt()
	{
		require('../application/config/user.php');
		return substr(md5(uniqid(rand(), true)), 0, $config['salt_length']);
	}

	
	// --------------------------------------------------------------------


	/**
	 * Get one translation 
	 *
	 */
	public function get_translation($line)
	{
		return (isset($this->lang[$line])) ? $this->lang[$line] : '#'.$line ;
	}


	// --------------------------------------------------------------------
	
	/**
	 * Connects to the DB with the database.php config file
	 *
	 */	
	function db_connect()
	{
		include(APPPATH.'config/database'.EXT);

		$this->db = DB('default', true);

		$this->db->db_connect();
		$this->db->db_select();
	}
	
	
	// --------------------------------------------------------------------


	/**
	 * Check needed migration and returns a migration array containing the XML files to execute.
	 *
	 */
	function _get_migration_files()
	{
		// Array of XML migration files
		$migration_xml = array();

		$this->db_connect();

		// Try to get one table fields data : If not possible, the table doesn't exist : 
		// The database doesn't contains correct tables -> error !
		if (($test = $this->db->query('select count(1) from setting')) != false)
		{
			// From Ionize 0.90 or 0.91
			// page_lang does not contains the 'online' field
			$migrate_from = true;
	
			$fields = $this->db->field_data('page_lang');
	
			foreach ($fields as $field)
			{
			   if ($field->name == 'online')
			   {
					$migrate_from = false;
			   }
			} 
			
			if ($migrate_from == true)
			{
				$migration_xml[] = 'migration_0.90_0.92.xml';
				$migration_xml[] = 'migration_0.92_0.93.xml';
				$migration_xml[] = 'migration_0.93_0.9.4.xml';
				$migration_xml[] = 'migration_0.9.4_0.9.5.xml';
				$migration_xml[] = 'migration_0.9.5_0.9.6.xml';
				$migration_xml[] = 'migration_0.9.6_0.9.7.xml';
				$migration_xml[] = 'migration_0.9.7_0.9.9.xml';
				$migration_xml[] = 'migration_0.9.9_1.0.0.xml';
			}
	
			// From Ionize 0.92
			// The 'extend_field' table does not contains the 'value' field
			// If it contains this field, we are already in a 0.93 verion, so no migration
			// If the 'migration_xml' array isn't empty, we migrate from an earlier version, so no need to make this test
			if (empty($migration_xml))
			{
				$migrate_from = true;
				
				$fields = $this->db->field_data('extend_field');
	
				foreach ($fields as $field)
				{
				   if ($field->name == 'value')
				   {
						$migrate_from = false;
				   }
				}
				
				if ($migrate_from == true)
				{
					$migration_xml[] = 'migration_0.92_0.93.xml';
					$migration_xml[] = 'migration_0.93_0.9.4.xml';
					$migration_xml[] = 'migration_0.9.4_0.9.5.xml';
					$migration_xml[] = 'migration_0.9.5_0.9.6.xml';
					$migration_xml[] = 'migration_0.9.6_0.9.7.xml';
					$migration_xml[] = 'migration_0.9.7_0.9.9.xml';
					$migration_xml[] = 'migration_0.9.9_1.0.0.xml';
				}
			}
	
	
			// From Ionize 0.93
			// if the 'users' table field 'join_date' has the TIMESTAMP type, we will migrate the accounts.
			// If the 'migration_xml' array isn't empty, we migrate from an earlier version, so no need to make this test
			if (empty($migration_xml))
			{
				$migrate_from = true;

				if ($this->db->table_exists('users') == true)
				{
					$fields = $this->db->field_data('users');

					foreach ($fields as $field)
					{
						if ($field->name == 'salt')
							$migrate_from = false;
					}

					if ($migrate_from == true)
					{
						$migration_xml[] = 'migration_0.93_0.9.4.xml';
						$migration_xml[] = 'migration_0.9.4_0.9.5.xml';
						$migration_xml[] = 'migration_0.9.5_0.9.6.xml';
						$migration_xml[] = 'migration_0.9.6_0.9.7.xml';
						$migration_xml[] = 'migration_0.9.7_0.9.9.xml';
						$migration_xml[] = 'migration_0.9.9_1.0.0.xml';
					}
				}
			}

			// From Ionize 0.9.4 : the users.id_user field does not exists
			if (empty($migration_xml))
			{
				$migrate_from = false;

				if ($this->db->table_exists('users') == true)
				{
					$fields = $this->db->field_data('users');

					foreach ($fields as $field)
					{
						if ($field->name == 'user_PK')
							$migrate_from = true;
					}

					if ($migrate_from == true)
					{
						$migration_xml[] = 'migration_0.9.4_0.9.5.xml';
						$migration_xml[] = 'migration_0.9.5_0.9.6.xml';
						$migration_xml[] = 'migration_0.9.6_0.9.7.xml';
						$migration_xml[] = 'migration_0.9.7_0.9.9.xml';
						$migration_xml[] = 'migration_0.9.9_1.0.0.xml';
					}
				}
			}

			// From Ionize 0.9.5 : the table article hasn't the 'flag' field
			if (empty($migration_xml))
			{
				$migrate_from = true;
				
				$fields = $this->db->field_data('article');

				foreach ($fields as $field)
				{
					if ($field->name == 'flag')
						$migrate_from = false;
				}

				if ($migrate_from == true)
				{
					$migration_xml[] = 'migration_0.9.5_0.9.6.xml';
					$migration_xml[] = 'migration_0.9.6_0.9.7.xml';
					$migration_xml[] = 'migration_0.9.7_0.9.9.xml';
					$migration_xml[] = 'migration_0.9.9_1.0.0.xml';
				}
			}
			
			// From Ionize 0.9.6 : the table extend_field does not contains the field id_element_definition
			if (empty($migration_xml))
			{
				$migrate_from = true;
				
				$fields = $this->db->field_data('extend_field');
	
				foreach ($fields as $field)
				{
					if ($field->name == 'id_element_definition')
						$migrate_from = false;
				}
				
				if ($migrate_from == true)
				{
					$migration_xml[] = 'migration_0.9.6_0.9.7.xml';
					$migration_xml[] = 'migration_0.9.7_0.9.9.xml';
					$migration_xml[] = 'migration_0.9.9_1.0.0.xml';
				}
			}
			
			// From 0.9.7
			if (empty($migration_xml))
			{
				$version = $this->db->query("select content from setting where name='ionize_version'")->row_array();
				$version = isset($version['content']) ? $version['content'] : '';
				$version = str_replace('.', '', $version);
				
				if (intval($version) <= 97)
				{
					$migration_xml[] = 'migration_0.9.7_0.9.9.xml';
					$migration_xml[] = 'migration_0.9.9_1.0.0.xml';
				}
			}

			// From 0.9.9
			if (empty($migration_xml))
			{
				$version = $this->db->query("select content from setting where name='ionize_version'")->row_array();
				$version = isset($version['content']) ? $version['content'] : '';
				$version = explode('.', $version);
				$test_version = '';
				for($i=0;$i<3;$i++)
					$test_version .= $version[$i];

				if (intval($test_version) < 100)
				{
					$migration_xml[] = 'migration_0.9.9_1.0.0.xml';
				}
			}
		}

		return $migration_xml;
	}


	// --------------------------------------------------------------------


	/**
	 * Tests if a dir is writable
	 *
	 * @param	string		folder path to test
	 * @param	boolean		if true, check all directories recursively
	 *
	 * @return	boolean		true if every tested dir is writable, false if one is not writable
	 *
	 */
	function _test_dir($dir, $recursive = false)
	{
		if ( ! is_really_writable($dir) OR !$dh = opendir($dir))
			return false;
		if ($recursive)
		{
			while (($file = readdir($dh)) !== false)
				if (@filetype($dir.$file) == 'dir' && $file != '.' && $file != '..')
					if (!$this->_test_dir($dir.$file, true))
						return false;
		}
		
		closedir($dh);
		return true;
	}

	
	// --------------------------------------------------------------------

	/**
	 * Tests if a file is writable
	 *
	 * @param	Mixed		folder path to test
	 * @param	boolean		if true, check all directories recursively
	 *
	 * @return	boolean		true if every tested dir is writable, false if one is not writable
	 *
	 */
	function _test_file($files)
	{
		foreach ($files as $file)
		{
			if ( ! is_really_writable($file)) return false;
		}
		return true;
	}

	
	// --------------------------------------------------------------------


	/**
	 * Try to connect to the DB
	 *
	 */
	function _db_connect($data)
	{
		// $dsn = 'dbdriver://username:password@hostname/database';
		$dsn = $data['db_driver'].'://'.$data['db_username'].':'.$_POST['db_password'].'@'.$data['db_hostname'].'/'.$data['db_name'];
			
		$this->db = DB($dsn, true, true);

		return $this->db->db_connect();
	}

	
	// --------------------------------------------------------------------


	/**
	 * Feed the templates data with blank values
	 * @param	array	Array of key to fill
	 */
	function _feed_blank_template($data)
	{
		foreach($data as $key)
		{
			$this->template[$key] = '';
		}
	}

	
	// --------------------------------------------------------------------


	/**
	 * Feed the templates data with provided values
	 * @param	array	Array of key to fill
	 */
	function _feed_template($data)
	{
		foreach($data as $key => $value)
		{
			$this->template[$key] = $value;
		}
	}

	function _clean_data($data, $table)
	{
		$cleaned_data = array();

		if ( ! empty($data))
		{
			$fields = $this->db->list_fields($table);
			$fields = array_fill_keys($fields,'');
			$cleaned_data = array_intersect_key($data, $fields);
		}
		return $cleaned_data;
	}

	public function _exists($where, $table)
	{
		$query = $this->db->get_where($table, $where, FALSE);

		if ($query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}

	public function _get_default_lang()
	{
		$query = $this->db->get_where('lang', array('def' => '1'), FALSE);

		if ($query->num_rows() > 0)
			return $query->row_array();
		else
			return FALSE;

	}


	// --------------------------------------------------------------------


	/**
	 * Creates an error message and displays the submitted view
	  * @param	string	View name
	  * @param	string	Error message content
	  * @param	array	Data to feed to form. Optional.
	 
	 */
	function _send_error($view, $msg, $data = array())
	{
		$this->template['message_type'] = 'error';
		$this->template['message'] = $msg;
		
		if ( !empty($data))
		{
			$this->_feed_template($data);
		}

		$this->output($view);

		exit();
	}
	
	
	// --------------------------------------------------------------------


	/**
	 * Saves database settings to config/database.php file
	 *
	 */
	function _save_database_settings_to_file($data)
	{
		// Files begin
		$conf  = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n\n";
		
		$conf .= "\$active_group = 'default';\n";
		$conf .= "\$active_record = TRUE;\n\n";
		
		$conf .= "\$db['default']['hostname'] = '".$data['db_hostname']."';\n";
		$conf .= "\$db['default']['username'] = '".$data['db_username']."';\n";
		$conf .= "\$db['default']['password'] = '".$_POST['db_password']."';\n";
		$conf .= "\$db['default']['database'] = '".$data['db_name']."';\n";
		$conf .= "\$db['default']['dbdriver'] = '".$data['db_driver']."';\n";
		$conf .= "\$db['default']['dbprefix'] = '';\n";
		$conf .= "\$db['default']['swap_pre'] = '';\n";
		$conf .= "\$db['default']['pconnect'] = FALSE;\n";
		$conf .= "\$db['default']['db_debug'] = TRUE;\n";
		$conf .= "\$db['default']['cache_on'] = FALSE;\n";
		$conf .= "\$db['default']['cachedir'] = '';\n";
		$conf .= "\$db['default']['char_set'] = 'utf8';\n";
		$conf .= "\$db['default']['dbcollat'] = 'utf8_unicode_ci';\n";
		
		// files end
		$conf .= "\n";
		$conf .= '/* End of file database.php */'."\n";
		$conf .= '/* Auto generated by Installer on '. date('Y.m.d H:i:s') .' */'."\n";
		$conf .= '/* Location: ./application/config/database.php */'."\n";

		return @file_put_contents(APPPATH . '/config/database' . EXT, $conf);
	}

	
	function _save_language_config_file($data)
	{
		// Default language
		$def_lang = '';

		// Available / Online languages array
		$available_languages = array();
		$online_languages = array();

		foreach($data as $l)
		{
			// Set default lang code
			if ($l['def'] == '1')
				$def_lang = $l['lang'];

			$available_languages[$l['lang']] = $l['name'];

			if($l['online'] == '1')
				$online_languages[$l['lang']] = $l['name'];
		}

		// Language file save
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
		$conf .= "\$config['default_admin_lang'] = 'en';\n\n";

		$conf .= "// Default language code\n";
		$conf .= "// This code depends on the language defined through the Ionize admin panel\n";
		$conf .= "// and will never change during the request process \n";
		$conf .= "\$config['default_lang_code'] = '".$def_lang."';\n\n";

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
		$conf .= '/* End of file language.php */'."\n";
		$conf .= '/* Auto generated by Ionize Installer on : '.date('Y.m.d H:i:s').' */'."\n";
		$conf .= '/* Location: ./application/config/language.php */'."\n";

		return @file_put_contents(APPPATH . 'config/language' . EXT, $conf);
		
	}
	
	// --------------------------------------------------------------------


	/**
	 * Encrypts one password, based on the encrypt key set in config/ascess.php
	 *
	 * @param	string		Password to encrypt
	 * @param	array		User data array
	 * @return	string		Encrypted password
	 *
	 */
	function _encrypt094($str, $data)
	{
		// Get the Access lib config file
		include(APPPATH.'config/access.php');
	
		$hash = sha1($data['username'] . $data['salt']);
		$key = sha1($config['encrypt_key'] . $hash);

		return base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, substr($key, 0, 56), $str, MCRYPT_MODE_CFB, substr($config['encrypt_key'], 0, 8)));
	}

	
	// --------------------------------------------------------------------


	function _decrypt($str, $data)
	{
		require_once('./class/Encrypt.php');
		
		include(APPPATH.'config/config.php');
		
		$encrypt = new ION_Encrypt($config);

		$hash 	= $encrypt->sha1($data['username'] . $data['salt']);
		$key 	= $encrypt->sha1($config['encryption_key'] . $hash);
		
		return $encrypt->decode($str, substr($key, 0, 56));
	}

	function _decrypt096($str, $data)
	{
		require_once('./class/Encrypt.php');
		
		include(APPPATH.'config/config.php');
		
		$encrypt = new ION_Encrypt($config);

		$hash 	= $encrypt->sha1($data['username'] . $data['salt']);
		$key 	= $encrypt->sha1($config['encryption_key'] . $hash);
		
		return $encrypt->old_decode($str, substr($key, 0, 56));
	}


	/**
	 * Encrypts one password, based on the encrypt key set in config file
	 *
	 * @param	string		Password to encrypt
	 * @param	array		User data array
	 * @return	string		Encrypted password
	 *
	 */
	function _encrypt($str, $data)
	{
		require_once('./class/Encrypt.php');
		
		include(APPPATH.'config/config.php');

		$encrypt = new ION_Encrypt($config);

		$hash 	= $encrypt->sha1($data['username'] . $data['salt']);
		$key 	= $encrypt->sha1($config['encryption_key'] . $hash);
		
		return $encrypt->encode($str, substr($key, 0, 56));
	}


	// --------------------------------------------------------------------


	function _decrypt094($str, $data)
	{
		// Get the Access lib config file
		include(APPPATH.'config/config.php');
	
		$hash = sha1($data['username'] . $data['salt']);
		$key = sha1($config['encryption_key'] . $hash);

		return mcrypt_decrypt(MCRYPT_BLOWFISH, substr($key, 0, 56), base64_decode($str), MCRYPT_MODE_CFB, substr($config['encryption_key'], 0, 8));
	}

	// --------------------------------------------------------------------


	function _decrypt093($str, $data)
	{
		// Get the Access lib config file
		include(APPPATH.'config/access.php');
	
		$hash = sha1($data['username'] . $data['join_date']);
		$key = sha1($config['encrypt_key'] . $hash);

		return mcrypt_decrypt(MCRYPT_BLOWFISH, substr($key, 0, 56), base64_decode($str), MCRYPT_MODE_CFB, substr($config['encrypt_key'], 0, 8));
	}


	// --------------------------------------------------------------------


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


function &get_instance()
{
	return Installer::get_instance();
}

/**
 * Dumps the content of a variable into correct PHP.
 *
 * Attention!
 * Cannot handle objects!
 *
 * Usage:
 * <code>
 * $str = '$variable = ' . dump_variable($variable);
 * </code>
 *
 * @param  mixed
 * @param  int
 * @return string
 */
function dump_variable($data, $indent = 0)
{
	$ind = str_repeat("\t", $indent);
	$str = '';

	switch(gettype($data))
	{
		case 'boolean':
			$str .= $data ? 'true' : 'false';
			break;

		case 'integer':
		case 'double':
			$str .= $data;
			break;

		case 'string':
			$str .= "'". addcslashes($data, '\'\\') . "'";
			break;

		case 'array':
			$str .= "array(\n";

			$t = array();
			foreach($data as $k => $v)
			{
				$s = '';
				if( ! is_numeric($k))
				{
					$s .= $ind . "\t'".addcslashes($k, '\'\\')."' => ";
				}

				$s .= dump_variable($v, $indent + 1);

				$t[] = $s;
			}

			$str .= implode(",\n", $t) . "\n" . $ind . "\t)";
			break;

		default:
			$str .= 'NULL';
	}

	return $str . ($indent ? '' : ';');
}

