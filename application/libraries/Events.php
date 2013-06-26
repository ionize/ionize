<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events
{
	protected static $ci;

	public function __construct()
	{
		// If the CI object is needed :
		self::$ci =& get_instance();

		// Register the Event :
		// Event::register(<event_name>, array($this, 'on_public_load'));
		Event::register('Filemanager.move.success', array($this, 'on_filemanager_move_success'));
		Event::register('Filemanager.destroy.success', array($this, 'on_filemanager_destroy_success'));
	}


	/**
	 * After one Filemanager file move
	 *
	 * @param $args		array(
	 * 						'old_path' => '',
	 * 						'new_path' => '',
	 * 						'is_dir' => boolean
	 * 					)
	 *
	 */
	public function on_filemanager_move_success($args)
	{
		self::$ci->load->model('media_model');

		self::$ci->media_model->update_path($args['old_path'], $args['new_path'], $args['is_dir']);
	}


	/**
	 * After one Filemanager file or folder destroy
	 *
	 * @param $args		array(
	 * 						'path' => '',
	 * 						'is_dir' => boolean
	 * 					)
	 *
	 */
	public function on_filemanager_destroy_success($args)
	{
		self::$ci->load->model('media_model');

		self::$ci->media_model->unlink_path($args['path'], $args['is_dir']);
	}

}

