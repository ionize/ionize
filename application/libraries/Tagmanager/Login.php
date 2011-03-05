<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.92
 *
 */

/**
 * Ionize Tagmanager Login Class
 *
 * Manage users login Form
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	TagManager Libraries
 *
 */
class TagManager_Login extends TagManager
{

	public function __construct($controller)
	{
		$this->ci = $controller;
		
		$this->tag_definitions = array_merge($this->tag_definitions, array
		(
			'login' => 			'tag_login',
			'login:user' => 	'tag_login_user',
			'login:group' => 	'tag_login_group'
		));
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds global values to the context.
	 * 
	 * @param  FTL_Context
	 * @return void
	 */
	public function add_globals(FTL_Context $con)
	{
		parent::add_globals($con);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the login form if the user is not logged
	 * Returns user's info if the user is connected
	 *
	 * @usage	<ion:login form_view="forms/login" logged_view="forms/logged" />
	 *
	 * @return 	String	
	 *
	 */
	public function tag_login($tag)
	{
		// Form view : Displayed when user isn't connected
		$form_view = (isset($tag->attr['form_view']) ) ? $tag->attr['form_view'] : false;
		
		// Logged view : Displayed when user is connected
		$logged_view = (isset($tag->attr['logged_view']) ) ? $tag->attr['logged_view'] : false;
	
		// View
		$view = '';
		
		// The user is logged in
		if ($this->ci->connect->logged_in())
		{
			// Use the logged view
		 	$view = $logged_view;
		}
		else
		{
			// Use the form view
			$view = $form_view;
		}
		
		// View rendering
		if (empty($view))
		{
			return $tag->expand();
		}
		else
		{
			if ( ! file_exists(Theme::get_theme_path().'views/'.$view.EXT))
			{
				show_error('TagManager_Page Error : <b>Cannot find view file "'.Theme::get_theme_path().'views/'.$view.EXT.'".');
			}

			return $tag->parse_as_nested(file_get_contents(Theme::get_theme_path().'views/'.$view.EXT));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the current logged user screen name (long name)
	 *
	 */
	public function tag_login_user($tag)
	{
		if ($this->ci->connect->logged_in())
		{
			$user = $this->ci->connect->get_current_user();
			
			return $user['screen_name'];
		}
		return '';
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Returns the current logged user group name
	 *
	 */
	public function tag_login_group($tag)
	{
		if ($this->ci->connect->logged_in())
		{
			$user = $this->ci->connect->get_current_user();
			
			return $user['group']['group_name'];
		}
		return '';
	}
	
	
}

/* End of file Login.php */
/* Location: /application/libraries/Tagmanager/Login.php */