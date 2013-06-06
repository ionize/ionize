<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */

/**
 * Default Ionize Controller
 * Displays all pages
 *
 */

class Page extends Base_Controller
{
	public function index()
	{
		// Init the Page TagManager
		TagManager_Page::init();
	}
}


/* End of file page.php */
/* Location: ./application/controllers/page.php */