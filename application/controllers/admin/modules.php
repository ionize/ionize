<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Modules Controller
 * Displays Modules list
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Modules management
 * @author		Ionize Dev Team
 *
 */

class Modules extends MY_admin 
{

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
	 * Default : List exiting modules
	 *
	 */
	function index()
	{
		// Get all modules config files in modules folder
		$config_files = glob(MODPATH . '/*/config.xml');

		// Get the modules config file
		$modules = array();
		include APPPATH . 'config/modules.php';
		
		// Module data to put to template
		$moddata = array();
		
		// Get all modules from folders
		foreach($config_files as $file)
		{
			$xml = simplexml_load_file($file);
			
			$uri = (String) $xml->uri_segment;

			// Module folder
			preg_match('/\/([^\/]*)\/config.xml$/i', $file, $matches);
			$folder = $matches[1];

			// Does the module install tables in DB ?
			$mod_use_database = FALSE;

			// Module's tables
			$mod_database_tables = array();
			
			// List module's tables
			if ( !empty($xml->database) )
			{
				$mod_use_database = TRUE;
				
				foreach($xml->database->table as $table)
				{
					$table_attr = $table->attributes();

					$mod_database_tables[] = $table_attr['name'];
				}
			}

			// Store data
			$moddata[$uri] = array(
					'name'				=> (String) $xml->name,
					'uri_segment'		=> (String) $xml->uri_segment,		// The install URI segment, defined by default by the XML config file
					'uri_user_segment'	=> (String) $xml->uri_segment,		// The user URI segment, can be defined by user in Ionize
					'description'		=> (String) $xml->description,
					'folder'			=> $folder,
					'file'				=> $file,
					'has_admin'			=> (String) $xml->has_admin,
					'installed'			=> FALSE,
					'database'			=> $mod_use_database,				
					'tables'			=> $mod_database_tables				// Array of tables installed by the module
					
			);
			
			// If module is installed (config data existing in /application/config/modules.php), set "installed" to true
			// Note : A module is identified by its folder name, which MUST be the same as the main module controller

			if (in_array($folder, $modules))
			{
				// Set installed to true
				$moddata[$uri]['installed'] = TRUE;

				// Get the user segment
				foreach($modules as $segment => $f)
				{
					if ($f == $folder)
						$moddata[$uri]['uri_user_segment'] = $segment; 
				}
			}
		}
		
		$this->template['modules'] = $moddata;
		
		$this->output('modules');
	}


	// ------------------------------------------------------------------------


	/**
	 * Installs one module
	 *
	 * @param	string	Module Folder name
	 * @param	string	Module User's choosen URI (by default, the "uri_segment" value in config.xml
	 *
	 */
	function install($module_folder, $module_uri)
	{
		/*
		 * Add the module in $module config array : /application/config/modules.php
		 *
		 */
		
		// Get the modules config file : $modules, $aliases
		$modules = array();
		$disable_controller = array();				// Array of disabled modules controllers. Got from config/modules
		$aliases = array();
		include APPPATH . 'config/modules.php';

		// Load the module XML config file
		if ( ! $xml = simplexml_load_file(MODPATH . $module_folder . '/config.xml') )
		{
			$this->error(lang('ionize_message_module_install_error_no_config'));
		}
		else
		{
			// Get the pages
			$this->load->model('page_model', '', TRUE);
			$pages = $this->page_model->get_list();

			$disable_module_controller =  ( strtolower((String)$xml->disable_controller) == 'true') ? TRUE : FALSE;
			
			// Check if conflict with existing pages
			$conflict = array();
			if ($disable_module_controller == FALSE)
				$conflict = array_filter($pages, create_function('$page','return $page[\'name\'] == "'. $module_uri .'";') );

			if ( ! empty($conflict))
			{
				$this->error(lang('ionize_message_module_page_conflict'));
			}
			else
			{
				// uri => Module Folder 
				$modules[$module_uri] = $module_folder;
				
				// The module controller is disabled : Not possible to call this module from controller.
				if ( ! isset($disable_controller)) $disable_controller = array();
				if ($disable_module_controller == TRUE)
					if ( ! in_array($module_uri, $disable_controller)) $disable_controller[] = $module_uri;

				// Install module database tables
				$db = $xml->database;
				
				if ( (bool)$xml->database == 1)
				{
					$errors = array();
					
					$db = $xml->database;
					
					$database_attr = $db->attributes();

					// Install through a dedicated XML script
					try {

						if ( ! empty($database_attr['script']))
						{
							$script = $database_attr['script'];
						
//							if (is_string($script))
//								$errors = $this->install_database_script(strval($script), $module_folder);
//							else
								$errors = $this->install_database($db);				
						}
						else
						{
							$errors = $this->install_database($db);				
						}
					}
					catch(Exception $e)
					{
						if ( !empty($errors))
						{
							$this->error(lang('ionize_message_module_install_database_error') . ' : ' . implode(', ', $errors));
						}
					}
				}
				
				// Write config file : /application/config/modules.php
				if ( ! $this->save_config($modules, $aliases, $disable_controller))
				{
					$this->error(lang('ionize_message_module_install_error_config_write'). ' : ' .APPPATH.'/config/modules.php');
				}
				else
				{
					// Reload the panel
					$this->update[] = array(
						'element' => 'mainPanel',
						'url' => 'modules'
					);
					
					// OK Answer
					$this->success(lang('ionize_message_module_saved'));
				}
			}
		}
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Uninstall one module
	 *
	 * @param	string	Module Folder
	 *
	 */
	function uninstall($module_folder)
	{
		// Get the modules config file
		$modules = array();
		$disable_controller = array();
		$aliases = array();
		include APPPATH . 'config/modules.php';

		// Filter the module array
		unset($modules[array_search($module_folder, $modules)]);

		// Find the module's uri and unset it
		$key = ((array_search($module_folder, $modules)));
		unset($disable_controller[array_search($key, $disable_controller)]);

		// Write config file
		if ($this->save_config($modules, $aliases, $disable_controller))
		{
			// Reload the panel
			$this->update[] = array(
				'element' => 'mainPanel',
				'url' => 'modules'
			);
			
			// Answer
			$this->success(lang('ionize_message_module_uninstalled'));
		}
		else
		{
			$this->error(lang('ionize_message_module_install_error_config_write'));
		}
	}


	// ------------------------------------------------------------------------


	function save_config($modules, $aliases, $disable_controller, $moddata = NULL)
	{
		$str = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); \n\n";

		$str .= '$modules = '.dump_variable($modules)."\n\n";

		$str .= '$aliases = '.dump_variable($aliases)."\n\n";
		
		$str .= '$disable_controller = '.dump_variable($disable_controller)."\n\n";

//		$str .= '$moddata = '.dump_variable($moddata)."\n\n";

		// add end
		$str .= "\n\n";
		$str .= '/* End of file modules.php */'."\n";
		$str .= '/* Auto generated by Themes Administration on : '.date('Y.m.d H:i:s').' */'."\n";
		$str .= '/* Location: ' .APPPATH.'/config/modules.php */'."\n";

		// write
		$ret = @file_put_contents(APPPATH . '/config/modules' . EXT, $str);

		// num bytes written > 0
		if($ret)
			return true;

		return false;
	}


	// ------------------------------------------------------------------------


	/**
	 * Installs the database tables from the config.xml file
	 * 
	 * @param	SimpleXMLElement	Database XML object
	 *
	 * @returns Array		Array of tables names which failed to install
	 *
	 */
	function install_database($database)
	{
		// Walk through tables
		$tables_cnt = count($database->table);
		
		// Tables in error
		$errors = array();

		for($i = 0; $i < $tables_cnt; $i++)
		{
			// Current table and table attributes
			$table = $database->table[$i];
			$table_attr = $table->attributes();
	
			// Array of columns items
			$script_body = array();

			if ( ! empty($table_attr))
			{
				/* 
				 * Columns script
				 *
				 */
				// Number of column items
				$columns_cnt = count($table->column);

				for($j = 0; $j < $columns_cnt; $j++)
				{
					// Column & column attributes
					$column = $table->column[$j];
					$column_attr = $column->attributes();

					// Column create string
					$str = $column_attr['name'].' '.$column_attr['type'];
					$str .= isset($column_attr['length']) ?		'('.$column_attr['length'].')' :			'';
					$str .= isset($column_attr['attributes']) ?	' '.$column_attr['attributes'].' ' :								'';
					$str .= isset($column_attr['zerofill']) ?	' '.$column_attr['zerofill'].' ' :			'';
					$str .= isset($column_attr['default']) ?	' DEFAULT \''.$column_attr['default'].'\' ' :	'';
					$str .= isset($column_attr['auto_increment']) ?	' AUTO_INCREMENT ' :	'';

					// Add string to script body array
					$script_body[] = $str;
				}

				// Primary key
				if ($table->primary_key)
					$script_body[] = ' PRIMARY KEY (' . $table->primary_key . ') ';

				/* 
				 * Table creation script
				 *
				 */
				
				// Table header
				$script = 'CREATE TABLE ';
				$script .= (isset($table_attr['if_not_exists']) && $table_attr['if_not_exists'] == 'true') ? ' IF NOT EXISTS ' : '' ;
				$script .= $table_attr['name'] . '( ';

				// Table body
				$script .= implode(',', $script_body);

				$script .= ')';
				
				// Table options
				$script .= (isset($table_attr['engine'])) ?				' ENGINE=' . $table_attr['engine'] . ' ' : '';
				$script .= (isset($table_attr['default_charset'])) ?	' DEFAULT CHARSET=' . $table_attr['default_charset'] . ' ' : '';
				$script .= (isset($table_attr['collate'])) ?			' COLLATE=' . $table_attr['collate'] . ' ' : '';
				$script .= (isset($table_attr['auto_increment'])) ?		' AUTO_INCREMENT=' . $table_attr['auto_increment'] . ' ' : '';

				$script .= ';';
				
				if ( ! $this->db->simple_query($script))
				{
					$errors[] = $table_attr['name'];
				}
			}
		}

		return $errors;
	}


	// ------------------------------------------------------------------------
	

	/**
	 * Installs the module database based on a separated simple XML script
	 * 
	 * @param	String	File name
	 *
	 * The database.xml script must look like the database.xml install file :
	 *
	 * 	<?xml version="1.0" ?>
	 *	<sql>
	 *		<name>Ionize Shop Module Database Creation script</name>
	 *		<version>0.9.7</version>
	 *		<license>Open Source MIT license</license>
	 *		
	 *		<!-- Tables definition -->
	 *		<tables>
	 *			<query>
	 *				CREATE  TABLE IF NOT EXISTS `my_table` (
	 *				  `id_my_table` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	 *				  `field1` VARCHAR(30) ,
	 *				  `default_value` DECIMAL(12,3) NULL ,
	 *				  PRIMARY KEY (`id_my_table`)
	 *				)
	 *				ENGINE = InnoDB
	 *				AUTO_INCREMENT = 1
	 *				DEFAULT CHARACTER SET = utf8
	 *				COLLATE = utf8_unicode_ci;
	 *			</query>
	 *			<query>
	 *				...
	 *			</query>
	 *		</tables>
	 *			
	 *		<!-- Basic Content -->
	 *		<content>
	 *			<query>INSERT INTO my_table VALUES (1, 'EU', 10.2);</query>
	 *			<query>INSERT INTO my_table VALUES (2, 'AR', 5);</query>
	 *		</content>				
	 *			
	 *	</sql>		
	 *
	 *
	 */
	function install_database_script($script, $module_folder)
	{
		$errors = array();
		
		if ( ! $xml = simplexml_load_file(MODPATH . $module_folder . '/' . $script) )
		{
			$errors[] = 'SQL File ' . $script . ' cannot be found.';
		}
		else
		{
			// Get tables & content
			$tables = $xml->xpath('/sql/tables/query');
			$content = $xml->xpath('/sql/content/query');
			
			// Create tables
			foreach ($tables as $sql)
			{
				if ( ! $this->db->simple_query($sql))
				{
					$errors[] = $sql;
				}
			}
			
			// Add content
			foreach ($content as $sql)
			{
				$this->db->simple_query($sql);
			}
		}

		return $errors;
	}


	// ------------------------------------------------------------------------
	
	
	function xml_attribute($object, $attribute)
	{
		if(isset($object[$attribute]))
			return (string) $object[$attribute];
		
		return NULL;
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * @deprecated
	 *
	 */
	function save_route($module_folder, $module)
	{
		$str = "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); \n\n";
	
		$str .= '$route[\'' . (String) $module->uri_segment . '\'] = \''.(String) $module->uri_segment."'; \n\n";

		// add end
		$str .= "\n\n";
		$str .= '/* End of file routes.php */'."\n";
		$str .= '/* Auto generated by Themes Administration on : '.date('Y.m.d H:i:s').' */'."\n";
		$str .= '/* Location: ' . MODPATH . '/' . $module_folder .'/config/routes'. EXT." */ \n";

		// write
		$ret = @file_put_contents( MODPATH . '/' . $module_folder . '/config/routes' . EXT, $str);

		// num bytes written > 0
		if($ret)
			return true;

		return false;
	}
}

/* End of file modules.php */
/* Location: ./application/controllers/admin/modules.php */