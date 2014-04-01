<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 *
 */

/**
 * Picture Controller
 * Get picture from URL rather than through tags
 *
 */
class Picture extends Base_Controller
{
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model(
			array(
				'media_model'
			),
			'',
			TRUE
		);

		$this->load->library('medias');

	}

	public function index()
	{
		echo('');
		die();
	}

	public function get($id_media)
	{
		// Pictures data from database
		$picture = $id_media ? $this->media_model->get($id_media) : FALSE;

		$options = $this->uri->uri_to_assoc();
		unset($options['get']);

		if ( empty($options['size'])) $options['size'] = 120;

		// Path to the picture
		if ($picture && file_exists($picture_path = DOCPATH.$picture['path']))
		{
			$thumb_path = DOCPATH . Settings::get('files_path'). str_replace(Settings::get('files_path').'/', '/.thumbs/', $picture['base_path']);

			$thumb_file_path = $this->medias->get_thumb_file_path($picture, $options);

			$refresh = ! empty($options['refresh']) ? TRUE : FALSE;

			// If no thumb, try to create it
			if ( ! file_exists($thumb_file_path) OR $refresh == TRUE)
			{
				try
				{
					$thumb_file_path = $this->medias->create_thumb(
						DOCPATH . $picture['path'],
						$thumb_file_path,
						$options
					);
				}
				catch(Exception $e)
				{
					// $return_thumb_path = FCPATH.'themes/'.Settings::get('theme_admin').'/styles/'.Settings::get('backend_ui_style').'/images/icon_48_no_folder_rights.png';
				}
			}
			$mime = get_mime_by_extension($thumb_file_path);
			$content = read_file($thumb_file_path);

			$this->push_thumb($content, $mime, 0);
		}
		// No source file
		else
		{
			$mime = 'image/png';
			$content = read_file(FCPATH.'themes/'.Settings::get('theme_admin').'/styles/'.Settings::get('backend_ui_style').'/images/icon_48_no_source_picture.png');
			$this->push_thumb($content, $mime, 0);
		}
	}


	// ------------------------------------------------------------------------


	public function push_thumb($content, $mime = NULL, $expire = NULL)
	{
		if ($expire === NULL) $expire = self::$DEFAULT_EXPIRE;
		$expires = gmdate("D, d M Y H:i:s", time() + $expire) . " GMT";
		$size = strlen($content);

		header("Content-Type: $mime");
		header("Expires: $expires");
		header("Cache-Control: max-age=$expire");
		header("Content-Length: $size");

		echo $content;

		die();
	}

}





