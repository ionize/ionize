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

		// Get the already translated items from languages files
		$translated_items = $this->_get_translated_items();

		// Get terms from views files
		$views_terms = $this->_get_terms_from_views();

		if ( ! empty($views_terms['term']))
		{
			$terms = array_fill_keys($views_terms['term'],'');
		}

		// Add terms from lang file to the terms in views
		if ( ! empty($translated_items[Settings::get_lang('default')]))
		{
			// Simple array of all terms
			$terms = array_keys(array_merge($terms, $translated_items[Settings::get_lang('default')]));
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
				if ( ! empty($translated_items[$lang]))
					$translated_items[$lang] = array_merge($terms, $translated_items[$lang]);
				
				foreach($terms as $term)
				{
					if ( ! empty($translated_items[$lang][$term]))
						$translated_items[$lang][$term] = stripslashes($translated_items[$lang][$term]);
					else
						$translated_items[$lang][$term] = '';						
				}
			}
		}

		$this->template['terms'] = $terms;
		
		$this->template['views_terms'] = $views_terms;
				
		$this->template['translated_items'] = $translated_items;
		
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
						$value = str_replace("\'", "'", $_REQUEST[str_replace(' ', '_', 'value_'.$lang.'_'.$idx)]);
						$value = str_replace('"', '\"', $value);
						
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
	 */
	function _get_items()
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
	
	
	// ------------------------------------------------------------------------


	/**
	 * Get the array of items to translate, per file
	 * Parses the current theme views
	 *
	 * @return	array	Items to translate, by view
	 * 					Array(
	 *						'terms' => terms to translate,
	 *						'views' => array(
	 *									term => views list
	 *						)
	 *					)
	 *
	 */
	function _get_terms_from_views()
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
								// Add the view to the term view list
								if ( ! isset($items['views'][$term]) || ! in_array($file->getFilename(), $items['views'][$term]))
									$items['views'][$term][] = $file->getFilename();
	
								// Add the term to the term array
								if (!in_array($term, $items['term']))
									$items['term'][] = $term;
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


	// ------------------------------------------------------------------------


	/**
	 * Gets already translated items from language files
	 *
	 * @return	array	Array of already translated terms
	 */
	function _get_translated_items()
	{
		$items = array();
	
		$this->load->helper('file');

		// Theme folder
		$path = FCPATH.'themes/'.Settings::get('theme');

		// Modules
//		require(APPPATH.'config/modules.php');
//		$installed_modules = $modules;



		// Read the template language directory
		foreach(Settings::get_languages() as $language)
		{
			$lang_files = array();
			$lang_code = $language['lang'];
			$items[$lang_code] = array();
			
			// Translation file name. look like [theme_name]_lang.php
			$file = $path.'/language/'.$lang_code.'/'.Settings::get('theme').'_lang.php';

			// Include the file if it exists
			if (file_exists($file))
			{
//				array_push($lang_files, $file);
						
				include($file);

				if (isset($lang))
				{
					$items[$lang_code] = $lang;
					
					unset($lang);
				}

			}
			// Modules languages files : Including. Can be empty
/*			foreach($installed_modules as $module)
			{
				$lang_file = MODPATH.$module.'/language/'.$lang_code.'/'.strtolower($module).'_lang.php';
				array_push($lang_files, $lang_file);
			}
trace($lang_files);		
			foreach($lang_files as $l)
			{
				if (is_file($l) && '.'.end(explode('.', $l)) == EXT )
				{
					include $l;
					if ( ! empty($lang))
					{
						$items[$lang_code] = array_merge($items[$lang_code], $lang);
						unset($lang);
					}
				}
			}
*/			
						
		}
		return $items;
	}	

}

/* End of file translation.php */
/* Location: ./application/controllers/admin/translation.php */