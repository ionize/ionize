<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Module admin controller
*
*
*/
class Demo extends Module_Admin
{
	/**
	* Constructor
	*
	* @access	public
	* @return	void
	*/
	public function construct()
	{
	}

	/**
	* Admin panel
	* Called from the modules list.
	*
	* @access	public
	* @return	parsed view
	*/
	public function index()
	{
		$this->output('admin/demo');
	}

	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Adds "Addons" to core panels
	 * When set, this function will be automatically called for each core panel.
	 *
	 * One addon is a view from the module which will be displayed in a core panel,
	 * to add some interaction with the current edited element (page, article)
	 *
	 * 
	 * Core Panels which accepts addons :
	 *  - article : Article Edition Panel
	 *  - page : Page Edition Panel
	 *  - media : Media Edition Panel
	 *
	 * Placeholders :
	 * In each "Core Panel", some placeholder are defined :
	 *  - 'options_top' : 		Options panel, Top
	 *  - 'options_bottom' : 	Options panel, Bottom
	 *  - 'main_top' : 			Main Panel, Top
	 *  - 'main_bottom' : 		Main Panel, Bottom (Not implemented)
	 *  - 'toolbar' : 			Top toolbar (Not implemented)
	 * 
	 * @param	Array			The current edited object (page, article, ...)
	 *
	 */
	public function _addons($object = array())
	{
		$CI =& get_instance();
		$uri = $CI->uri->uri_string();

		// Send the article to the view
		$data['article'] = $object;
		
		// Options panel Top Addon
		if (strpos($uri, 'article/get_options') !== FALSE)
		{
			$CI->load_addon_view(
				'demo',								// Module folder
				'article',							// Parent panel code
				'options_top',						// Placehoder
				'admin/addons/article/options', 	// View to display in the placeholder
				$data								// Data send to the view
			);
		}
	}
}

