<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Widget Plugin
*
* Modified by Partikule Studio to be used as library in Ionize
* Original lib : 
*
* @version:     0.2
* $copyright    Copyright (c) Wiredesignz 2009-08-24
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/

class Widget
{
	/**
	 * Run one widget
	 *
	 * @param	string	Widget name
	 *
	 * @return	mixed	Call of the widget "run" method
	 *
	 */
	function run($name) 
	{
		$ci =& get_instance(); 
		// Get widget args
		$args = func_get_args();
		
		// Depending on call (direct or from FTL), define args
		if (isset($args[1]))
		{
			if (is_array($args[1]))
				$args = $args[1];
			else
				array_shift($args);
		}

		// Get the widget class
		require_once APPPATH.'../themes/' . Settings::get('theme') . '/widgets/'.$name.'/'.$name.EXT;
		
		// Create a new instance of the widget class
		$class_name = ucfirst($name);
		$widget = new $class_name();

		// Loads the widget Languages translation files
		$lang_files = glob(Theme::get_theme_path().'widgets/'.$name.'/language/'.Settings::get_lang().'/*');

		if ( ! empty($lang_files))
		{
			foreach($lang_files as $lang_file)
			{
				// Widget language file is optional, so only include if it exists
				if (is_file($lang_file))
				{
					// Include widget language file
					include $lang_file;
					
					// Merge to the Lang array
					$ci->lang->language = array_merge($ci->lang->language, $lang);
					unset($lang);
				}
			}
		}

		// Call the widget "run" method
		return call_user_func_array(array(&$widget, 'run'), $args);
	}


	/**
	 * Renders the widget view
	 * As to be used with the FTL library.
	 * The view isn't directly outputed, but returned.
	 * 
	 * @param	string	View name
	 * @param	array	Widget data array
	 *
	 * @return	string	Parsed widget view
	 *
	 */
	function render($view, $data = array()) 
	{
		// Add the widget path to the finder, if it not already in
		if ( ! in_array(Theme::get_theme_path().'widgets/' . $view . '/', Finder::$paths))
			array_unshift(Finder::$paths, Theme::get_theme_path().'widgets/' . $view . '/');

		// Add the widget_path to data array
		$data['widget_path'] = base_url() . 'themes/'. Settings::get('theme') . '/widgets/' .$view . '/';

		return $this->load->view($view, $data, true);				
	}


	/**
	 * Get the CI object and all attached vars
	 *
	 */
	function __get($var) 
	{
		static $ci;
		isset($ci) OR $ci = get_instance();
		return $ci->$var;
	}

	
	function show_error($message)
	{
		$heading = '<h2 style="color:#c00;">Widget Error</h2>';
		$message = $heading . '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
		
		return $message;
	}
}

/* End of file Widget.php */
/* Location: ./application/libraries/Widget.php */