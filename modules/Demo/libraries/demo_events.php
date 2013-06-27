<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Demo_Events
{
	protected static $ci;


	public function __construct()
	{
		// If the CI object is needed :
		self::$ci =& get_instance();

		// Register the Event :
		// Event::register(<event_name>, array($this, 'on_public_load'));
		Event::register('Ionize.front.load', array($this, 'on_front_load'));
		Event::register('Article.save.before', array($this, 'on_article_save_before'));
	}


	// ------------------------------------------------------------------------


	/**
	 * This method will be called when one controller (or other lib)
	 * will fire the event called "ionize.front.load"
	 *
	 */
	public function on_front_load()
	{
		$message = 'Demo module catched Ionize public load event !';

		// Log the message in the /application/logs/log-YYYY-MM-DD.php log file
		//	log_message('error', $message);

		// Log the success message in the "event_log" table
		//	Event::log_success($message);

		// To log error :
		// Event::log_error($message);

	}


	public function on_article_save_before($data)
	{
	//	return $data;
	}
}
