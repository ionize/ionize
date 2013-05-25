<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Events
 *
 * A simple events system for CodeIgniter.
 * Adapted to Ionize
 *
 * @package		CodeIgniter
 * @subpackage	Events
 * @version		1.1
 * @author		Partikule, base on the work of Dan Horrigan <http://dhorrigan.com>
 * @license		Apache License v2.0
 * @copyright	2010 Dan Horrigan
 *
 */

/**
 * Event Library
 */
class Event {

	/**
	 * @var	array	An array of listeners
	 */
	protected static $_listeners = array();

	protected static $_event_log = FALSE;

	protected static $ci;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		self::$ci =& get_instance();

		self::$ci->load->config('events');
		self::$_event_log = config_item('event_log');

		self::_load_modules_events();
	}

	// ------------------------------------------------------------------------


	/**
	 * Load Modules Events
	 *
	 */
	private static function _load_modules_events()
	{
		$modules = array();
		require(APPPATH.'config/modules.php');

		if ( empty($modules))
			return FALSE;

		// Add the module path to the Finder
		$module_paths = glob(MODPATH.'*');

		if ( ! empty($module_paths))
		{
			foreach ($module_paths as $module_path)
			{
				$folder = basename($module_path);
				if (in_array($folder, $modules))
				{
				if ( ! $details_class = self::_start_events_class($module_path))
					continue;
				}
			}
		}

		return TRUE;
	}


	/**
	 * Start Events Class
	 *
	 * @param	string	$path	Path to the module
	 * @access	private
	 * @return	array
	 *
	 */
	private static function _start_events_class($path)
	{
		$class_file = $path . '/libraries/events'.EXT;

		if ( ! is_file($class_file))
			return FALSE;

		include_once $class_file;

		// Now call the details class
		$class = ucfirst(strtolower(basename($path))).'_Events';

		// Now we need to talk to it
		return class_exists($class) ? new $class : FALSE;
	}


	/**
	 * Register
	 *
	 * Registers a Callback for a given event
	 *
	 * @access	public
	 * @param	string	The name of the event
	 * @param	array	The callback for the Event
	 * @return	void
	 */
	public static function register($event, array $callback)
	{
		$class = is_object($callback[0]) ? get_class($callback[0]) : $callback[0];
		$key = $class.'::'.$callback[1];
		self::$_listeners[$event][$key] = $callback;
		// log_message('error', 'Event::register() - Registered "'.$key.' with event "'.$event.'"');
	}


	/**
	 * Fire
	 *
	 * Fires an event and returns the results.  The results can be returned
	 * in the following formats:
	 *
	 * 'array'
	 * 'json'
	 * 'serialized'
	 * 'string'
	 *
	 * @access	public
	 * @param	string	The name of the event
	 * @param	mixed	Any data that is to be passed to the listener
	 * @param	string	The return type
	 * @return	mixed	The return of the listeners, in the return type
	 */
	public static function fire($event, $data = '', $return_type = NULL)
	{
		log_message('debug', 'Event::fire() : ' . $event );

		$calls = array();

		if (self::has_listeners($event))
		{
			foreach (self::$_listeners[$event] as $listener)
			{
				if (is_callable($listener))
				{
					$class = is_object($listener[0]) ? get_class($listener[0]) : $listener[0];
					// log_message('error', 'Event:: Call ' . $class . '->' . $listener[1] . '()' );

					$calls[] = call_user_func($listener, $data);
				}
			}
		}

		return self::_format_return($calls, $return_type);
	}

	/**
	 * Format Return
	 *
	 * Formats the return in the given type
	 *
	 * @access	protected
	 * @param	array	The array of returns
	 * @param	string	The return type
	 * @return	mixed	The formatted return
	 */
	protected static function _format_return(array $calls, $return_type = NULL)
	{
		// log_message('debug', 'Event::_format_return() - Formating calls in type "'.$return_type.'"');

		switch ($return_type)
		{
			case 'array':
				return $calls;
				break;

			case 'json':
				return json_encode($calls);
				break;

			case 'serialized':
				return serialize($calls);
				break;

			case 'string':
				$str = '';
				foreach ($calls as $call)
				{
					$str .= $call;
				}
				return $str;
				break;

			default:
				return $calls;
		}
	}

	/**
	 * Has Listeners
	 *
	 * Checks if the event has listeners
	 *
	 * @access	public
	 * @param	$event string	Name of the event
	 * @return	bool			Whether the event has listeners
	 */
	public static function has_listeners($event)
	{
		// log_message('debug', 'Event::has_listeners() - Checking if event "'.$event.'" has listeners.');

		if (isset(self::$_listeners[$event]) AND count(self::$_listeners[$event]) > 0)
		{
			return TRUE;
		}
		return FALSE;
	}


	public static function log_success($message='')
	{
		return self::log_message('success', $message);
	}

	public static function log_error($message='')
	{
		return self::log_message('error', $message);
	}

	private static function log_message($status = 'error', $message='')
	{
		if (self::$_event_log == TRUE)
		{
			if ( ! isset(self::$ci->event_model))
				self::$ci->load->model('event_model', '', TRUE);

			$user = User()->get_user();
			$data = array(
				'status' => $status,
				'message' => $message,
				'id_user' => $user['id_user'],
				'email' => $user['email'],
				'date_log' => date('Y-m-d H:i:s'),
				'ip_address' => self::$ci->input->ip_address(),
			);
			return self::$ci->event_model->insert($data);
		}
		return FALSE;
	}

}

