<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usermanager_Global {

	function __construct()
    {
	}

	public function admin_url($tag)
	{
//		return base_url()./*$ci->settings->get_lang()."/".*/"admin";
		return admin_url();
	}

	public function profile_url($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci = &get_instance();

/* TODO : 

	- Ajouter paramètre "Forcer code langue dans URL"
	  Si paramètre = 1, alors le code langue sera retourné même s'il n'y a qu'une seule langue
	  Paramètre inactif si plusieurs langues
	  


*/
		return base_url().$ci->settings->get_lang()."/".$config['usermanager_profile_url'];
	}

	public function register_url($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci = &get_instance();
		return base_url().$ci->settings->get_lang()."/".$config['usermanager_register_url'];
	}

	public function login_url($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci = &get_instance();
		return base_url().$ci->settings->get_lang()."/".$config['usermanager_login_url'];
	}

	public function url($tag)
	{
		return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	public function login_field_name($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		return $config['usermanager_email_as_username'] ? "email" : "username";
	}

	public function login_field_label($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		return $config['usermanager_email_as_username'] ? lang("module_usermanager_field_email") : lang("module_usermanager_field_username");
	}

	public function email_as_username($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		return $config['usermanager_email_as_username'] ? $tag->expand() : "";
	}

	public function not_email_as_username($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		return $config['usermanager_email_as_username'] ? "" : $tag->expand();
	}
}
