<?php 
/**
 * Authority
 *
 * Authority is an authorization library for CodeIgniter 2+ and PHPActiveRecord
 * This library is inspired by, and largely based off, Ryan Bates' CanCan gem
 * for Ruby on Rails.  It is not a 1:1 port, but the essentials are available.
 * Please check out his work at http://github.com/ryanb/cancan/
 *
 * @package     Authority
 * @version     0.0.3
 * @author      Matthew Machuga
 * @license     MIT License
 * @copyright   2011 Matthew Machuga
 * @link        http://github.com/machuga
 *
 **/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require 'authority/rule.php';
require 'authority/ability.php';

class Authority extends Authority\Ability {

	private static $ci;

	private static $session;

	private static $has_all = FALSE;


	/**
	 * @param $user
	 */
	public static function initialize($user)
    {
		log_message('debug', "Authority Class Initialized");

		self::$ci =& get_instance();

		self::$ci->load->library('session');
		self::$session =& self::$ci->session;

		Event::register('User.logout', array(__CLASS__, 'on_logout'));
		Event::register('User.login', array(__CLASS__, 'on_login'));

		// Super Admin shortcut : Will never depend on DB.
		if ($user->is('super-admins'))
		{
			Authority::allow('manage', 'all');
		}
		else
		{
			// Set Rules from DB or session
			self::set_rules($user);
		}
    }


	/**
	 * @param $user
	 *
	 */
	protected static function set_rules($user)
	{
		// Always get again the rules
		// To comment if rules should be placed in session
		// (will need logout / login) to set new rules.
		self::on_logout();

		// Rules : From Session
		if( self::$session->userdata('authority_rules') )
		{
			$rules = self::$session->userdata('authority_rules');
		}
		// Rules : User's rules from DB
		else
		{
			self::$ci->load->model('role_model', '', TRUE);
			self::$ci->load->model('rule_model', '', TRUE);

			// Roles rules
			$rules = self::$ci->rule_model->get_from_role($user->get_role());

			// To Session
			self::$session->set_userdata('authority_rules', $rules);
		}

		// Check for Admin role
		foreach($rules as $rule)
		{
			if ($rule['resource'] == 'all')
			{
				self::$has_all = TRUE;
				Authority::allow('manage', 'all');
				break;
			}
		}

		// Other role
		if ( ! self::$has_all )
		{
			foreach($rules as $rule)
			{
				// Read action
				$rule['permission'] == 1 ?  Authority::allow('access', $rule['resource']) : Authority::deny('access', $rule['resource']);

				// Other actions
				if ( ! empty($rule['actions']))
				{
					$actions = explode(',', $rule['actions']);

					foreach($actions as $action)
					{
						$rule['permission'] == 1 ?  Authority::allow($action, $rule['resource']) : Authority::deny($action, $rule['resource']);
					}
				}
			}
		}
	}


	/**
	 * @return Ionize\User
	 *
	 */
	protected static function current_user()
    {
		// Global function which returns instance of \Ionize\User
		return User();
    }


	/**
	 * Remove session rules on logout
	 *
	 */
	public function on_logout()
	{
		self::$ci->session->unset_userdata('authority_rules');
	}


	/**
	 * Remove previous session rules on login
	 *
	 */
	public function on_login()
	{
		self::$ci->session->unset_userdata('authority_rules');
	}

}
