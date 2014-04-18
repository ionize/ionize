<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajaxform_Process
{
	public static $ci;

	public function __construct()
	{
		self::$ci =& get_instance();

		self::$ci->load->library('email');
	}

	/**
	 * Processes the Contact Form
	 * Default behavior
	 *
	 * To replace by your method, simply copyr this class into /themes/your_theme/libraries/
	 * and chnage this method
	 *
	 * @param $form		Form settings array
	 *
	 */
	public static function process_contact($form)
	{
		$post = self::$ci->input->post();

		// ionize dedicated method, added to the orginal CI Email library
		return self::$ci->email->send_form_emails($form, $post);
	}

}
