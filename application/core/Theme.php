<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------


/**
 * Ionize Theme Class
 *
 * This class creates the mocha based desktop
 *
 * @package		Ionize
 * @subpackage	Librairies
 * @category	Librairies
 * @author		Ionize Dev Team
 */
 
class Theme {
	
	// Themes base folder. All themes are stored in this folder in their own folder
	private static $theme_base_path = 'themes/';
	
	// Views folder
	private static $views_folder = 'views/';
	
	// Current theme folder.
	private static $theme = '';
	
	// Array of possible default views
	private static $default_views = array(
										'page' => array('core/page', 'page'),
										'article' => array('core/article', 'article')
									  );
	
	
	// ------------------------------------------------------------------------
	
	
	/** 
	 * Sets the theme
	 *
	 * @access	public
	 * @param	string	The theme folder
	 */ 
	public static function set_theme($t)
	{
		self::$theme = $t;
		
		// Add current theme path to Finder searching path
		Finder::add_path(self::get_theme_path());
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns the theme name
	 *
	 */
	public static function get_theme()
	{
		return self::$theme;		
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns the complete path to the theme
	 *
	 */
	public static function get_theme_path()
	{
		return self::$theme_base_path.self::$theme.'/';		
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Loads a view as a string
	 * Used by Base_Controller->render() method to load a view
	 *
	 * @param	string	View name to load
	 * @param	sring	Directory where is the view
	 *
	 * @return	string	The load view
	 *
	 */
	public static function load($name, $directory = 'views')
	{
		$file = Finder::find_file($name, $directory, true);

		if(empty($file))
		{
			show_error('Theme error : <b>The file "'.$directory.'/'.$name.'" cannot be found.</b>');
		}
		
		$string = file_get_contents(array_shift($file));
		
		return $string;
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Try to get the default view
	 * The view must be named "page.php", "article.php" etc.
	 *
	 * @return	String 	The relative path to the default page view
	 *
	 */
	public static function get_default_view($type)
	{
		if (isset(self::$default_views[$type]))
		{
			foreach(self::$default_views[$type] as $view)
			{
				if (file_exists(BASEPATH.'../'.self::get_theme_path().self::$views_folder.$view.EXT))
				{
					return $view;
				}
			}
		}
		// Returns the first page view as real default.
		return self::$default_views[$type][0];
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Outputs one view
	 *
	 * @access	public
	 * @param	string	Name of the view
	 * @param	array	View's data array
	 *
	 */
	public function output($view, $data)
	{
		$ci =  &get_instance();
		
		// Loads the view
		$output = $ci->load->view($view, $data, true);
		
		// Set character encoding
		$ci->output->set_header("Content-Type: text/html; charset=UTF-8");
		
		$ci->output->set_output($output);
	}

}


/* End of file Theme.php */
/* Location: ./application/libraries/Theme.php */