<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Translation Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Translation extends MY_admin
{
	// Reg Expression used to find translation items in views files.
	private $reg_keys = array(
		"%ion:lang[\s]*term=\"([-_ \w:]+?)\" *\/>%",
		"%ion:lang[\s]*key=[\"']([-_ \w:]+?)[\"']([^>]*?) \/>%",
		'% Lang.get\(([-_ \w:\']+?)\)%'
	);

    /**
     * Translation Terms
     *
     * @var null
     */
    private $terms = NULL;

    /**
     * Maximum length of form text input element.
     * Text longer than this is entered in a textarea.
     *
     */
    static private $textarea_line_break = 60;
    static private $textarea_rows = 3;

    /**
     * Language Files array
     *
     * @var array
     */
    protected $lang_files = array();

    /**
     * Default Translation Lang Code
     */
    protected $default_lang_code;
	

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

        $this->load->model('config_model', '', TRUE);

        self::_check_default_lang_code();
	}

	// ------------------------------------------------------------------------

    /**
     * Controller Index
     */
    function index()
	{
        $this->output('translation/index');
	}

	// ------------------------------------------------------------------------

    /**
     * Welcome Message
     */
    function welcome()
    {
        $this->template['default_lang_code'] = $this->default_lang_code;

        $this->output('translation/welcome');
    }

    // ------------------------------------------------------------------------

    /**
     * Get Language File List
     */
    function get_list()
    {
        self::get_lang_files();

        $this->template['lang_files'] = $this->lang_files;

        $this->output('translation/list');
    }

    // ------------------------------------------------------------------------

	/**
     * Edit Language File
	 *
	 */
    function edit()
    {
        $default_lang = Settings::get_lang('default');
        $type = $this->input->post('type');
        $path = $this->input->post('path');
        $lang_path = $this->input->post('lang_path');
        $filename = $this->input->post('filename');

        if(  ! is_null($type) &&  ! is_null($path) && ! is_null($lang_path) && ! is_null($filename) )
	{
            $items = array();

            $this->load->helper('file');

            /**
             * If default language file not exist try to create
             */

            // $terms = self::_get_terms($path);

            // Read the template language directory
            foreach(Settings::get_languages() as $language)
            {
                $lang_code = $language['lang'];
                $items[$lang_code] = array();
		
                // Translation file name. Named [theme_name]_lang.php
                $file = $lang_path . $lang_code . DIRECTORY_SEPARATOR . $filename;
		
                // Include the file if it exists
                if ( file_exists($file) )
                {
                    $lang = array();
                    include($file);
		
                    if ( ! empty($lang))
		{
                        $items[$lang_code] = $lang;
		}
                }

                if(! file_exists($file))
                {
                    $source_file = $lang_path . $default_lang . DIRECTORY_SEPARATOR . $filename;
                    $destination_path = $lang_path . $lang_code . DIRECTORY_SEPARATOR;
                    $lfile = $lang_path . $lang_code . DIRECTORY_SEPARATOR . $filename;

                    if(@mkdir($destination_path, 0777, TRUE))
		{
                        if(@copy($source_file, $lfile))
                        {
                            $lang = array();

                            include($lfile);

                            if ( ! empty($lang))
                            {
                                $items[$lang_code] = $lang;
                            }
                        }
                        else
                        {
                            // @TODO Add translation term...
                            $this->error("File copy failed to '$source_file' => '$lfile' please check folder permissions !");
                        }
                    }
                    else
                    {
                        // @TODO Add translation term...
                        $this->error("Could not create '$file' please check file permissions !");
                    }
                }

            }

            $this->load->helper('text');

            // Default Translation Language
            $this->template['default_lang_code'] = $this->default_lang_code;

            $this->template['filename'] = $filename;
            $this->template['path'] = $path;
            $this->template['lang_path'] = $lang_path;
            $this->template['type'] = $type;

            $file = array(
                'filename' => $filename,
                'path' => $path,
                'lang_path' => $lang_path,
                'type' => $type
            );

            $this->template['textarea_line_break'] = self::$textarea_line_break;
            $this->template['textarea_rows'] = self::$textarea_rows;

            $this->template['items'] = self::_compare_items($items, $file);
            $this->template['languages'] = self::_order_languages_by_default();

            $this->output('translation/edit');
		}
		else
		{
            // @TODO Return Error Message
            $this->error("type, path, lang_path, filename missing...");
            return;
		}
		
    }

    // ------------------------------------------------------------------------

    /**
     * Compare Translation Files for missing terms
     *
     * @param array $items
     * @param array $file
     * @return array
     */
    function _compare_items($items = array(), $file=array())
    {
        /**
         * Check if wanted file is "theme_lang.php", add missing view terms data to "$items"
         */
        if( $file['type'] == 'theme' && $file['filename'] == 'theme_lang.php' )
		{
            $view_terms = self::_get_terms_from_theme();

            $items[$this->default_lang_code] += $view_terms['terms'];
            $items['views'] = $view_terms['views'];
        }
        else
			{
            $items['views'] = array();
        }
				
				
        if( ! empty($items) )
				{
            foreach(Settings::get_languages() as $key => $lang)
            {
                if($lang['lang'] != $this->default_lang_code)
                {
                    // Compare "default lang" data with "current lang" data
                    $compare = array_diff_key($items[$this->default_lang_code], $items[$lang['lang']]);
                    $items[$lang['lang']] = array_merge($compare, $items[$lang['lang']]);
                    ksort($items[$lang['lang']]);

                    // Compare "current lang" data with "default lang" data
                    $compare = array_diff_key($items[$lang['lang']], $items[$this->default_lang_code]);
                    $items[$this->default_lang_code] = array_merge($compare, $items[$this->default_lang_code]);
                    ksort($items[$this->default_lang_code]);
				}
			}
		}
        else
        {
            log_message('ERROR', 'Items is empty !');
        }

        return $items;
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

        // URL helper
        $this->load->helper('url');

        $filename       = $this->input->post('filename');
        $path           = $this->input->post('path');
        $lang_path      = $this->input->post('lang_path');
        $type           = $this->input->post('type');

		$error = FALSE;

		foreach(Settings::get_languages() as $language)
		{
			$lang = $language['lang'];

			// Creates the lang folder if it doesn't exists
            $path = $lang_path . $lang;

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
					
                    $term = url_title($_REQUEST[$key], 'underscore');;

					if ($term != '')
					{
						$value = $_REQUEST[str_replace(' ', '_', 'value_'.$lang.'_'.$idx)];
						$value = stripslashes($value);

						if ( ! get_magic_quotes_gpc())
						{
							$value = addslashes($value);
						}
						$value = str_replace("\\'", "'", $value);
						$value = str_replace("'", "\'", $value);

						$data .= "\$lang['".$term."'] = '".$value."';\n";
					}
				}
			}
			
			// Finish the file data
			$data .= "\n".'?'.'>';

			// Try writing the language file
            $file = $path . DIRECTORY_SEPARATOR . $filename;

			if ( ! file_exists($file))
				write_file($file, $data);

			if ( ! is_really_writable($file))
			{
                $this->error(lang('ionize_message_message_no_write_rights'). ' : ' . $file);
				$error = TRUE;
			}
			else
			{
				write_file($file, $data);
			}
		}

		if ( ! $error)
		{
            $this->callback = array(
                array(
                    'fn' => 'ION.HTML',
                    'args' => array(
                        'translation/edit',
                        array(
                            'type' => $type,
                            'filename' => $filename,
                            'path' => $path,
                            'lang_path' => $lang_path
                        ),
                        array(
                            'update' => 'splitPanel_mainPanel_pad'
                        )
                    )
                )
			);

			$this->success(lang('ionize_message_language_files_saved'));
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * Get Modules Language File List
     */
    function get_lang_files()
	{
        $theme_translations = self::_get_lang_files('theme');

        if( ! empty($theme_translations) ) {
            $this->lang_files += $theme_translations;
        }

        $module_translations = self::_get_module_lang_files();

        if( ! empty($module_translations) ) {
            $this->lang_files += $module_translations;
        }

        /**
            $application_translations = self::_get_lang_files('application');

            if( ! empty($application_translations) ) {
                $this->lang_files += $application_translations;
            }
		
            $system_translations = self::_get_lang_files('system');

            if( ! empty($system_translations) ) {
                $this->lang_files += $system_translations;
            }
         **/

        return;
    }
    // ------------------------------------------------------------------------

    /**
     * Get Modules Language File List
     *
     * @param null $type | "theme", "application", "system"
     */
    function _get_lang_files($type=NULL)
			{
        $path = NULL;
        $lang = $this->default_lang_code;

        if( ! is_null($type) )
			{
            switch($type)
				{
                case 'theme':
                    $path = FCPATH . 'themes' . DIRECTORY_SEPARATOR . Settings::get('theme') . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR;
                    break;
                case 'application':
                    $path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR;
                    break;
                case 'system':
                    $path = FCPATH . 'system' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR;
                    break;
            }
					
            if( ! is_null($path) && file_exists($path) )
            {
                // Theme static translations
                $language_files = glob($path . '*_lang.php');
					
                $lfiles[$type] = array(
                    'title' => lang('ionize_title_translation_' . $type),
                    'type'  => $type,
                    'files' => array()
                );
						
                foreach($language_files as $key => $lf)
						{
                    $lfiles[$type]['files'][] = array(
                        'path' => $lf,
                        'lang_path' => str_replace($lang . DIRECTORY_SEPARATOR, '', $path),
                        'filename' => str_replace($path, '', $lf)
                    );
						}
						
                return $lfiles;
					}
            else
            {
                // @TODO Add translation term...
                log_message('ERROR', 'Missing language file or path !');
                // $this->error("We don't have a path !");
                // $this->response();
                // return;
				}
			}
        else
        {
            // @TODO Add translation term...
            log_message('ERROR', "We don't have a type !");
            // $this->error("We don't have a type !");
            // $this->response();
			}			
		}

    // ------------------------------------------------------------------------

    /**
     * Get Modules Language Files
     */
    function _get_module_lang_files()
    {
        $paths['module'] = array(
            'title' => lang('ionize_title_translation_module'),
            'type'  => 'module',
            'files' => array()
		);
		
        $lang = $this->default_lang_code;

        $installed_modules = Modules()->get_installed_modules();

        foreach($installed_modules as $key => $imodule)
        {
            // Module Language Folder Path
            $mlpath = $imodule['path'] . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . strtolower($key) . '_lang.php';
	
            if( file_exists($mlpath) )
                $paths['module']['files'][] = array(
                    'path' => $mlpath,
                    'lang_path' => str_replace($lang . DIRECTORY_SEPARATOR . strtolower($key) . '_lang.php', '', $mlpath),
                    'filename' => strtolower($key) . '_lang.php'
                );
	}
	
        return $paths;
    }

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
			'terms' => array(),		// array of terms and their translations
			'views' => array()		// array of view in which each term appears, key : term
		);
		
		// Get the modules term as a flat array of terms
		$modules_data = $this->_get_all_terms();
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
									if ( ! in_array($term, $items['terms']))
										$items['terms'][$term] = '';
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
	
    // ------------------------------------------------------------------------
	
	/**
     * Get the array of items to translate, per module, theme translation files
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
    function _get_all_terms()
	{
        if ( ! is_null($this->terms))
		{
            return $this->terms;
		}
		
		$items = array();
		
        $module_lang_files  = self::_get_module_lang_files();
        $theme_lang_files   = self::_get_lang_files('theme');
		
        $lang_files = array_merge($module_lang_files, $theme_lang_files);

        foreach($lang_files as $lang_file)
        {
            foreach($lang_file['files'] as $file)
		{
			// Include the $lang var of the translation file
                if (is_file($file['path']))
			{
				$lang = array();
                    include($file['path']);
	
				if ( ! empty($lang))
				{
                        $items[str_replace('_lang.php', '', $file['filename'])] = array_keys($lang);
                    }
				}
			}
		}

		return $items;

	}
	
    // ------------------------------------------------------------------------
		
	/**
     * Set Default Translation Language Code
	 */
    function set_default_lang_code()
	{
        if ($this->config_model->change('language.php', 'default_translation_lang_code', $this->input->post('default_translation_lang_code')) == FALSE)
            $this->error(lang('ionize_message_error_writing_ionize_file'));

        // Answer
        $this->success(lang('ionize_message_operation_ok'));
    }

    // ------------------------------------------------------------------------

    /**
     * Check Default Language Code
     */
    function _check_default_lang_code()
		{
        $default_lang_code = config_item('default_translation_lang_code');
			
        if( ! empty($default_lang_code) )
				{
            $this->default_lang_code = $default_lang_code;
				}
        else
				{
            $this->default_lang_code = Settings::get_lang('default');
			
            $this->config_model->change('language.php', 'default_translation_lang_code', $this->default_lang_code);
				}
			}
	
	// ------------------------------------------------------------------------

	/**
     * Re-Order "Settings::get_languages()" by default translation language code
	 *
     * @return array
	 */
    function _order_languages_by_default()
	{
        $languages = array();
	
        $i = 1;

        foreach(Settings::get_languages() as $key => $lang)
		{
            if($lang['lang'] == $this->default_lang_code)
                $languages[0] = $lang;
            else
                $languages[$i++] = $lang;
				}
	
        ksort($languages);

        return $languages;
	}
}
