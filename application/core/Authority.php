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

	private static $initialized = FALSE;

	private static $session;

	private static $has_all = FALSE;

	/**
	 * All rules Array
	 * Independently from any role
	 *
	 * @var null
	 */
	private static $all_rules = NULL;


	/**
	 * Simple array of all resources keys linked to rules
	 * Storage array.
	 *
	 * @var array
	 */
	private static $all_rules_keys = array();


	/**
	 * @param $user	Ionize\User
	 */
	public static function initialize($user)
    {
		log_message('debug', "Authority Class Initialized");

		static::$initialized = TRUE;

		self::$ci =& get_instance();

		self::$ci->load->library('session');
		self::$session =& self::$ci->session;

		Event::register('User.logout', array(__CLASS__, 'on_logout'));
		Event::register('User.login', array(__CLASS__, 'on_login'));

		if ($user->logged_in())
		{
			// Super Admin shortcut : Will never depend on DB.
			if ($user->is('super-admin'))
			{
				Authority::allow('manage', 'all');
			}
			else
			{
				// Set Rules from DB or session
				self::set_rules($user);
			}
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
		// self::on_logout();

		// Rules : From Session
		if( self::$session->userdata('authority_rules') )
		{
			$rules = self::$session->userdata('authority_rules');
		}
		// Rules : User's rules from DB
		else
		{
            // Models
            self::$ci->load->model(
                array(
                    'role_model',
                    'rule_model'
                ), '', TRUE);

			// Roles rules
			$rules = self::$ci->rule_model->get_from_role($user->get_role());

			// To Session
			self::$session->set_userdata('authority_rules', $rules);
		}

		// Check for Super Admin role
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
	 *
	 * @param      $action
	 * @param      $resource
	 * @param null $resource_val
	 * @param bool $check_has_rule
	 *
	 * @return bool
	 *
	 */
	public static function can($action, $resource, $resource_val = NULL, $check_has_rule = FALSE)
	{
		if ( ! static::$initialized && empty(static::$_rules)) {
			static::initialize(static::current_user());
		}

		if ($check_has_rule == TRUE && ! static::resource_has_rule($resource))
		{
			return TRUE;
		}

		// See if the action has been aliased to something else
		$true_action = static::determine_action($action);

		$matches = static::find_matches($true_action, $resource);

		if ($matches && ! empty($matches))
		{
			$results = array();
			$resource_value = ($resource_val) ?: $resource;

			foreach ($matches as $matched_rule)
			{
				$results[] = !($matched_rule->callback($resource_value) xor $matched_rule->allowed());
			}

			// Last rule overrides others
			return $results[count($results)-1];
		}
		else
		{
			return FALSE;
		}
	}


	public static function cannot($action, $resource, $resource_val = NULL, $check_has_rule = FALSE)
	{
		return ! static::can($action, $resource, $resource_val, $check_has_rule);
	}


	/**
	 * Return TRUE if the resource has at least one rule,
	 * independently from any role
	 *
	 * self::$ci->load->model('resource_model', '', TRUE);
	 * return self::$ci->resource_model->has_rule($resource);
	 *
	 * @param $resource
	 *
	 * @return mixed
	 */
	public static function resource_has_rule($resource)
	{
		if (is_null(static::$all_rules))
		{
			self::$ci->load->model('rule_model', '', TRUE);
			static::$all_rules = self::$ci->rule_model->get_list();

			foreach(static::$all_rules as $rule)
				static::$all_rules_keys[] = $rule['resource'];
		}
		return (in_array($resource, static::$all_rules_keys));
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
