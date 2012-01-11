<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Session module's tags
 *
 * @author	Ionize Dev Team
 *
 */
class Session_Tags extends TagManager
{
	// Allowed session vars
	protected static $allowed_vars = NULL;
	
	public static $ci = NULL;
	
	
	// ------------------------------------------------------------------------
	

	public static function index(FTL_Binding $tag)
	{
		self::$ci = &get_instance();
		
		if( ! isset($ci->session)) self::$ci->load->library('session');
	
		return $tag->expand();
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Displays one session variable
	 *
	 * @usage	<ion:session:display var="session_var_name" [tag="h1" id="" class="" ] />
	 *
	 */
	public static function display(FTL_Binding $tag)
	{
		$var = $tag->getAttribute('var');

		if ( self::is_allowed($var) == TRUE)
		{
			return self::wrap($tag, self::$ci->session->userdata($var));
		}
	}

	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Set one session variable
	 *
	 * @usage	<ion:session:set var="session_var_name" [tag="h1" id="" class="" ] />
	 *
	 */
	public static function set(FTL_Binding $tag)
	{
		$var = $tag->getAttribute('var');
		$set = $tag->getAttribute('set');

		if ( self::is_allowed($var) == TRUE)
		{
			self::$ci->session->set_userdata($var, $set);
		}
	}

	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Check one session value
	 *
	 * Optionally, can check for one value AND set another value.
	 * In this case, the check will return TRUE and set the value after
	 *
	 * @usage	<ion:session var="session_var_name" is="session_var_value" [set="new_session_var_value"] >
	 *			...
	 *			</ion:session>
	 *
	 */
	public static function check(FTL_Binding $tag)
	{
		$var = $tag->getAttribute('var');
		$is = $tag->getAttribute('is');
		$set = $tag->getAttribute('set');

		if ( self::is_allowed($var) == TRUE)
		{
			if ( $is !== FALSE)
			{
				if (self::$ci->session->userdata($var) == $is)
				{
					if ( $set != FALSE)
						self::$ci->session->set_userdata($var, $set);
					
					return $tag->expand();
				}
			}
		}
	}

	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Checks if the session var is allowed
	 *
	 */
	protected static function is_allowed($var)
	{
		if ( is_null(self::$allowed_vars) )
			self::$allowed_vars = explode(',', config_item('module_session_allowed_variables'));
		
		return in_array($var, self::$allowed_vars);
	}
	
}

/* End of file tags.php */
/* Location: /modules/Session/libraries/tags.php */
