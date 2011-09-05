<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Ionize Translation Controller
 * Manage Static Translations
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Translation files management
 * @author		Ionize Dev Team
 *
 */

class Translation extends MY_admin 
{
	// Reg Expression used to find translation items in views files.
//	private $reg_key = '% term=\"([- \w:]+?)\" *\/>%';

	private $reg_keys = array(
		'% term=\"([- \w:]+?)\" *\/>%',
		'% Lang.get\(([- \w:\']+?)\)%'
	);

	private $modules_terms = NULL;
	

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
		// All terms
		$this->template['terms'] = $terms = array();

		// Get terms from views files
		$theme_terms = $this->_get_terms_from_theme();

		// Get translated items from theme languages files
		$theme_translations = $this->_get_theme_translations();
		
		// Get terms form installed modules
		$modules_terms = $this->_get_terms_from_modules();
		
		// Get translations for installed languages
		$module_translations = $this->_get_modules_translations();
		
		// Get, for each module, the lang codes for which a default translation file exists
		$module_translation_files = $this->_get_modules_translation_files();
		

		if ( ! empty($theme_terms['term']))
		{
			$terms = array_fill_keys($theme_terms['term'],'');
		}

		// Add terms from lang file to the terms in views
		if ( ! empty($theme_translations[Settings::get_lang('default')]))
		{
			// Simple array of all terms
			$terms = array_keys(array_merge($terms, $theme_translations[Settings::get_lang('default')]));
		}
		else
		{
			$terms = array_keys($terms);
		}
		
		// Natural case sorted
		natcasesort($terms);

		if ( ! empty($terms))
		{
			foreach(Settings::get_languages() as $language)
			{
				$lang = $language['lang'];
				
				// Merge
				if ( ! empty($theme_translations[$lang]))
					$theme_translations[$lang] = array_merge($terms, $theme_translations[$lang]);
				
				foreach($terms as $term)
				{
					if ( ! empty($theme_translations[$lang][$term]))
						$theme_translations[$lang][$term] = stripslashes($theme_translations[$lang][$term]);
					else
						$theme_translations[$lang][$term] = '';						
				}
			}
		}


		$this->template['terms'] = $terms;
		
		$this->template['theme_terms'] = $theme_terms;
		$this->template['theme_translations'] = $theme_translations;

		$this->template['modules_terms'] = $modules_terms;
		$this->template['module_translations'] = $module_translations;
		$this->template['module_translation_files'] = $module_translation_files;

		
		$this->output('translation');
	}


	// ------------------------------------------------------------------------

	/**
	 * Saves the translation language files
	 *
	 */
	function save()
	{
		// Clear the cache
		Cache()->clear_cache();

		$file_name = strtolower($this->input->post('file_name'));

		foreach(Settings::get_languages() as $language)
		{
			$lang = $language['lang'];

			// Creates the lang folder if it doesn't exists
			$path = FCPATH.'themes/'.Settings::get('theme').'/language/'.$lang;

			if ( ! is_dir($path) )
			{
				try {	
					@mkdir($path, 0777, TRUE);
				}
				catch (Exception $e) {
					$this->error(lang('ionize_message_language_dir_creation_fail'));
				}
			}

			// Build the file data
			$data  = "<?php\n\n";

			foreach($_REQUEST as $key => $value)
			{
				if (substr($key, 0, 3) == 'key')
				{
					$idx = substr($key,4);
					
					$term = $_REQUEST[$key];
					
					if ($term != '')
					{
						$value = $_REQUEST[str_replace(' ', '_', 'value_'.$lang.'_'.$idx)];
						
						if ( ! get_magic_quotes_gpc())
						{
							$value = addslashes($value);
						}
						$value = str_replace("\'", "'", $value);
						
						$data .= "\$lang['".$term."'] = \"".$value."\";\n"; 
					}
				}
			}
			
			// Finish the file data
			$data .= "\n".'?'.'>';

			// Try writing the language file
			try
			{
				write_file($path.'/'.$file_name.'_lang.php', $data);
			}
			catch (Exception $e) {
				$this->error(lang('ionize_message_language_file_creation_fail'));
			}			
		}

		$this->update[] = array(
			'element' => 'mainPanel',
			'url' => admin_url() . 'translation',
			'title' => lang('ionize_title_translation')
		);

		
		// If method arrives here, everything was OK
		$this->success(lang('ionize_message_language_files_saved'));
	}
	
	
	// ------------------------------------------------------------------------

	
	function save_module_translations()
	{
		// Clear the cache
		Cache()->clear_cache();

		$module = $this->input->post('module');
		
		foreach(Settings::get_languages() as $language)
		{
			$lang = $language['lang'];

			// Creates the lang folder if it doesn't exists
			$path = FCPATH.'themes/'.Settings::get('theme').'/language/'.$lang;

			if ( ! is_dir($path) )
			{
				try {	
					@mkdir($path, 0777, TRUE);
				}
				catch (Exception $e) {
					$this->error(lang('ionize_message_language_dir_creation_fail'));
				}
			}

			// Build the file data
			$data  = "<?php\n\n";

			foreach($_REQUEST as $key => $value)
			{
				if (substr($key, 0, 3) == 'key')
				{
					$idx = substr($key,4);
					
					$term = $_REQUEST[$key];
					
					if ($term != '')
					{
						$value = $_REQUEST[str_replace(' ', '_', 'value_'.$lang.'_'.$idx)];
						
						if ( ! get_magic_quotes_gpc())
						{
							$value = addslashes($value);
						}
						$value = str_replace("\'", "'", $value);
						
						$data .= "\$lang['".$term."'] = \"".$value."\";\n"; 
					}
				}
			}
			
			// Finish the file data
			$data .= "\n".'?'.'>';

			// Try writing the language file
			try  {
				write_file($path.'/'.Settings::get('theme').'_lang.php', $data);
			}
			catch (Exception $e) {
				$this->error(lang('ionize_message_language_file_creation_fail'));
			}			
		}

		$this->update[] = array(
			'element' => 'mainPanel',
			'url' => admin_url() . 'translation',
			'title' => lang('ionize_title_translation')
		);
		
		// If method arrives here, everything was OK
		$this->success(lang('ionize_message_language_files_saved'));
	
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Get the array of items to translate
	 * 
	 * @return array	A simple array of unique items to translate, used for saving
	 *
	function _get_terms()
	{
		// File helper
		$this->load->helper('file');

		// Theme views folder
		$path = FCPATH.'themes/'.Settings::get('theme').'/views';
		
		// Returned items array
		$items = array();

		if (is_dir($path))
		{
			$dir_iterator = new RecursiveDirectoryIterator($path);
			$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
	
			foreach ($iterator as $file)
			{
				if ($file->isFile() && (substr($file->getFilename(), 0, 1) != ".") )
				{
					$content = read_file($file->getPath() . '/' . $file->getFilename());
					
					if (preg_match_all($this->reg_key, $content, $matches))
					{
						foreach($matches[1] as $term)
						{
							if (!in_array($term, $items))
							{
								$items[] = $term;
							}
						}
					}
				}
			}
		}
		
		return $items;
	}
	 */
	
	
	// ------------------------------------------------------------------------


	/**
	 * Get the array of items to translate, per file
	 * Parses the current theme views
	 *
	 * @return	array	Items to translate, by view
	 * 					Array(
	 *						'term' => terms to translate,
	 *						'views' => array(
	 *									term => views list
	 *						)
	 *					)
	 *
	 */
	function _get_terms_from_theme()
	{
		// File helper
		$this->load->helper('file');

		// Theme views folder
		$path = FCPATH.'themes/'.Settings::get('theme').'/views';
		
		// Returned items array
		$items = array (
			'term' => array(),		// array of terms and their translations
			'views' => array()		// array of view in which each term appears, key : term
		);
		
		// Get the modules term as a flat array of terms
		$modules_data = $this->_get_terms_from_modules();
		$modules_terms = array();
		foreach($modules_data as $module => $terms)
		{
			$modules_terms = array_merge($modules_terms, array_values($terms));
		}
		
		
		// Only do something if dir exists !
		if (is_dir($path))
		{
			// Recursive walk in the views folder
			$dir_iterator = new RecursiveDirectoryIterator($path);
			$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
	
			foreach ($iterator as $file)
			{
				if ($file->isFile() && (substr($file->getFilename(), 0, 1) != ".") )
				{
					$content = read_file($file->getPath() . '/' . $file->getFilename());
					
					// Check for each term="something" in tags
					foreach($this->reg_keys as $reg_key)
					{
						if (preg_match_all($reg_key, $content, $matches))
						{
							foreach($matches[1] as $term)
							{
								// Only add the term if it is not a module one
								if ( ! in_array($term, $modules_terms))
								{
									// Add the view to the term view list
									if ( ! isset($items['views'][$term]) || ! in_array($file->getFilename(), $items['views'][$term]))
										$items['views'][$term][] = $file->getFilename();
		
									// Add the term to the term array
									if ( ! in_array($term, $items['term']))
										$items['term'][] = $term;
								}
							}
						}
					}
				}
			}
	
			// Make a string list from 'views' array
			foreach ($items['views'] as $term => $views)
			{
				$items['views'][$term] = implode(', ', $views);
			}
			
		}
		
		return $items;
	}
	
	
	/**
	 * Get the array of items to translate, per module
	 *
	 * @notice	The english translation file is the reference 
	 *			and MUST exist for each module
	 *
	 * @return	array	Items to translate, by view
	 * 					Array(
	 *						<module_name> => Array (<terms to translate>)
	 *					)
	 *
	 */
	function _get_terms_from_modules()
	{
		if ( ! is_null($this->modules_terms))
		{
			return $this->modules_terms;
		}
		
		$items = array();
		
		// Installed Modules : includes the $modules var
		require(APPPATH.'config/modules.php');
		
		// Sort modules by name
		natcasesort($modules);

		// Modules languages files : Including. Can be empty
		foreach($modules as $module)
		{
			$file = MODPATH.$module.'/language/en/'.strtolower($module).'_lang.php';
			
			// Include the $lang var of the translation file
			if (is_file($file))
			{
				include($file);
	
				if (isset($lang))
				{
					$items[$module] = array_keys($lang);
					
					unset($lang);
				}
			}
		}

		return $items;

	}
	
		
	/**
	 * Returns Modules translations
	 *
	 * @return	array	Modules translation, by module
	 * 					Array(
	 *						<module_name> => Array (
	 *							<term> => Array (
	 *								<lang_code> => Array (
	 *									'default' => <translation>
	 *									'theme' => <translation>
	 *								)
	 *							)
	 *						)
	 *					)
	 */
	function _get_modules_translations()
	{
		// Theme views folder
		$theme_path = FCPATH.'themes/'.Settings::get('theme').'/language/';

		$modules_data = $this->_get_terms_from_modules();

		$items = array();

		foreach($modules_data as $module => $terms)
		{
			$items[$module] = array();
			
			// Try to get the module translation file
			foreach(Settings::get_languages() as $language)
			{
				// Init the translations arrays for this lang
				$theme_lang = $module_lang = array();

				// Default translation file for this language (if exists)
				$module_file = MODPATH.$module.'/language/'.$language['lang'].'/'.strtolower($module).'_lang.php';
				
				// Theme translation file for this module
				$theme_file = $theme_path.$language['lang'].'/module_'.strtolower($module).'_lang.php';
				
				// Feed $module_lang and theme_lang
				if (is_file($module_file))
				{
					// Get the module $lang var
					include($module_file);
					$module_lang = $lang;
					unset($lang);
				}
				if (is_file($theme_file))
				{
					// Get the theme $lang var
					include($theme_file);
					$theme_lang = $lang;
					unset($lang);
				}
			
				
				// Build the $items array
				foreach($terms as $term)
				{
					$items[$module][$term][$language['lang']] = array
					(
						'default' => (!empty($module_lang[$term])) ? $module_lang[$term] : '',
						'theme' => (!empty($theme_lang[$term])) ? $theme_lang[$term] : ''
					);
				}
			}
		}
		
		return $items;
	}
	
	
	
	// ------------------------------------------------------------------------


	/**
	 * Gets already translated items from language files
	 *
	 * @return	array	Array of already translated terms
	 *
	 */
	function _get_theme_translations()
	{
		$items = array();
	
		$this->load->helper('file');

		// Theme folder
		$path = FCPATH.'themes/'.Settings::get('theme');


		// Read the template language directory
		foreach(Settings::get_languages() as $language)
		{
			$lang_files = array();
			$lang_code = $language['lang'];
			$items[$lang_code] = array();
			
			// Translation file name. Named [theme_name]_lang.php
			$file = $path.'/language/'.$lang_code.'/'.Settings::get('theme').'_lang.php';

			// Include the file if it exists
			if (file_exists($file))
			{
				include($file);

				if (isset($lang))
				{
					$items[$lang_code] = $lang;
					
					unset($lang);
				}
			}
		}
		return $items;
	}	
	
	
	function _get_modules_translation_files()
	{
		$data = array();
		
		// Installed Modules : includes the $modules var
		require(APPPATH.'config/modules.php');
		
		// Sort modules by name
		natcasesort($modules);

		// Modules languages files : Including. Can be empty
		foreach($modules as $module)
		{
			$paths = glob(MODPATH.$module.'/language/*/');

			if (is_array($paths))
			{
				foreach($paths as $path)
				{
					if (is_file($path.strtolower($module).'_lang'.EXT))
					{
						$path = substr($path, 0, -1);
						$data[$module][] = array_pop(explode('/', $path));
					}
					
				}
			}
		}

		return $data;
	}
	
}

/* End of file translation.php */
/* Location: ./application/controllers/admin/translation.php */