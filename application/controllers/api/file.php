<?php defined('BASEPATH') OR exit('No direct script access allowed');


class File extends API_Controller
{
	/**
	 * If TRUE, Filemanager will clean the uploaded file name
	 *
	 * @var bool
	 *
	 */
	private static $upload_clean_file_name = TRUE;


	/**
	 * Get Method
	 *
	 * @url : 		/api/file/exists/
	 * 				Checks if the file exists on this server.
	 * @vars :		path		Relative "files" folder path (excluding file name) to the media to link,
	 * 							Must be send by $param and not through URL
	 * 				name 		File name
	 * 				full_path 	Complete "files" folder relative path (including file name)
	 *
	 *
	 * @url : 		/api/file/uploadname/
	 * 				Returns the "clean" file name as it will be renamed during the upload process
	 * @vars :		name		File name
	 *
	 *
	 */
	public function index_get()
	{
		$command = $this->get_segment(1);

		switch($command)
		{
			// Checks if one media exists or not
			case 'exists':
				$full_path = $this->get('full_path');

				// Try to get the path from URL
				if (empty($full_path))
					$full_path = $this->get_composed_segments('exists', 'user');

				if ( ! $this->exists($full_path))
					$this->set_error();

				break;


			// Returns the local filename of the sent file name
			case 'uploadname':
				$name = $this->get('name');

				// Try to get the path from URL
				if (empty($name))
					$name = $this->get('uploadname');

				$local_file_name = basename($this->get_local_file_path($name));

				$this->set_success($local_file_name);

				break;

			// Inits all hashes of all existing media in DB
			// Development only
			/*
			case 'inithashes':

				$this->load->model('media_model');
				$nb = $this->media_model->init_hashes();
				$this->set_success($nb);

			default:
				break;
			*/
		}

		$this->send_response();
	}


	// ------------------------------------------------------------------------


	/**
	 * POST
	 *
	 * @url 		/api/file/upload/
	 * @vars		The file content is posted as request body content.
	 *
	 * @url 		/api/file/link/
	 * @vars		Array
	 *				(
	 * 					// New file path, as perhaps adapted to this server (after upload for example)
	 *					[path] => folder/sub_folder/file_name.jpg
	 *					[element] => article
	 *					[id_element] => 3585
	 * 					// Original Media data, as on the Caller App
	 *					[media] => Array
	 *					(
	 *						[id_media] => 212
	 *						[type] => picture
	 *						[file_name] => file_name.jpg
	 *						[path] => files/folder/subfolder/file_name.jpg
	 *						[base_path] => files/folder/subfolder/
	 *						[date] => 0000-00-00 00:00:00
	 *					)
	 *				)
	 *
	 * @url 		/api/file/unlink/
	 * @vars		Array
	 *				(
	 *					[element] => article
	 *					[id_element] => 3585
	 *					[path] => folder/sub_folder/file_name.jpg
	 *				)
	 *
	 * @url 		/api/file/unlink-type/
	 * @vars		Array
	 *				(
	 *					[element] => article
	 *					[id_element] => 3585
	 *					[tye] => picture
	 *				)
	 *
	 * @url 		/api/file/unlink-all/
	 * @vars		Array
	 *				(
	 *					[element] => article
	 *					[id_element] => 3585
	 *				)
	 *
	 *
	 *
	 */
	public function index_post()
	{
		$command = $this->get_segment(1);

		switch($command)
		{
			case 'upload':
				try
				{
					$fm_settings = $this->_getFilemanagerSettings();
					$this->load->library('filemanager', $fm_settings);
					$response = $this->filemanager->HTML5_upload();

					if ($response['error'] != '0')
						$this->set_error($response['error']);
					else
						$this->set_success($response);

				}
				catch(Exception $e)
				{
					$this->set_error($e->getMessage(), 500);
				}

				break;

			// Links one media with one resource
			case 'link':

				$result = $this->link(
					$this->post('element'),
					$this->post('id_element'),
					$this->post('path')
				);

				if ( ! $result)
					$this->set_error();

				break;

			// Unlink one media from one element
			case 'unlink':

				$result = $this->unlink(
					$this->post('element'),
					$this->post('id_element'),
					$this->post('path')
				);

				if ( ! $result)
					$this->set_error();

				break;

			// Unlinks all media from element
			case 'unlink-all' :

				$result = $this->unlink_all(
					$this->post('element'),
					$this->post('id_element')
				);

				if ( ! $result)
					$this->set_error();

				break;


			default:
				break;
		}

		$this->send_response();
	}


	// ------------------------------------------------------------------------


	/**
	 * Links one media with one resource
	 *
	 * @param $element
	 * @param $element_id
	 * @param $file_path		File path relative to the "files" folder (exclusing the "files" folder name)
	 *
	 * @return bool
	 */
	private function link($element, $element_id, $file_path)
	{
		// Correct the remote file name regarding the local naming convention
		$file_path = $this->get_local_file_path($file_path);

		$relative_file_path = Settings::get('files_path').'/'.$file_path;
		$full_file_path = DOCPATH . $relative_file_path;

		// 1. If file exists on server
		if (file_exists($full_file_path))
		{
			// Models
			$this->load->model('media_model');

			// 2. Allowed to link ?
			if ($this->media_model->has_allowed_extension($file_path))
			{
				// Media ID : Get it or insert it in the DB
				$id_media = $this->media_model->insert_media($relative_file_path);

				// Link
				return $this->media_model->attach_media($element, $element_id, $id_media);
			}
		}

		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $element
	 * @param $element_id
	 * @param $file_path
	 *
	 * @return bool
	 */
	private function unlink($element, $element_id, $file_path)
	{
		// Correct the remote file name regarding the local naming convention
		$file_path = $this->get_local_file_path($file_path);

		$relative_file_path = Settings::get('files_path').'/'.$file_path;
		$full_file_path = DOCPATH . $relative_file_path;

		// 1. If file exists on server
		if (file_exists($full_file_path))
		{
			// Models
			$this->load->model('media_model');

			// Media detail
			$media = $this->media_model->get(array(
				'path' => $relative_file_path,
			));

			// 2. Unlink
			if ( ! empty($media))
			{
				return $this->media_model->delete_simple_link($element, $element_id, 'media', $media['id_media']);
			}
		}

		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $element
	 * @param $element_id
	 *
	 * @return bool
	 */
	private function unlink_all($element, $element_id)
	{
		// Models
		$this->load->model('media_model');

		return $this->media_model->detach_all_media($element, $element_id);
	}


	// ------------------------------------------------------------------------


	/**
	 * Return TRUE if the file exists, FALSE if not.
	 *
	 * @param string $path		File path. Relative to the "files" folder of Ionize
	 *
	 * @return bool
	 *
	 */
	private function exists($path)
	{
		// Get the path which will be uploaded
		if (self::$upload_clean_file_name == TRUE)
		{
			$fm_settings = $this->_getFilemanagerSettings();
			$this->load->library('filemanager', $fm_settings);
			$file_name = $this->filemanager->cleanFilename($path);

			$path_segments = explode('/', $path);
			array_pop($path_segments);

			$path = implode('/', $path_segments) .'/'. $file_name;
		}

		$full_file_path = DOCPATH . Settings::get('files_path') . '/' . $path;

		log_message('error', 'exists() $full_file_path : ' . $full_file_path);

		// 1. If file exists on server
		return file_exists($full_file_path);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the local file path, relative to the local "files" directory
	 *
	 * @param $file_path
	 *
	 * @return string
	 *
	 */
	private function get_local_file_path($file_path)
	{
		$file_name = basename($file_path);
		$path = substr($file_path, 0, strrpos($file_path, '/'));

		$fm_settings = $this->_getFilemanagerSettings();
		$this->load->library('filemanager', $fm_settings);
		$file_name = $this->filemanager->cleanFilename($file_name);

		log_message('error', 'path : ' . $path);
		log_message('error', '$file_name : ' . $file_name);

		return $path . '/' . $file_name;
	}


	// ------------------------------------------------------------------------


	/**
	 * Inits all hashes for existing media in DB.
	 *
	 */
	private function initHashes()
	{
		// Models
		$this->load->model('media_model');

		return $this->media_model->init_hashes();
	}


	// ------------------------------------------------------------------------


	private function _getFilemanagerSettings()
	{
		// Get allowed mimes
		$mimes = Settings::get_allowed_mimes();
		$allowed_mimes = implode(',', $mimes);

		$params = array (
			'filesDir' => ''.Settings::get('files_path') . '/',
			'thumbsDir' => ''.Settings::get('files_path') . '/.thumbs/.backend/',
			'assetsDir' => 'themes/admin/javascript/filemanager/assets/',
			'documentRoot' => DOCPATH,
			'baseUrl' => base_url(),
			'upload' => TRUE,
			'destroy' => TRUE,
			'create' => TRUE,
			'move' => TRUE,
			'cleanFileName' => self::$upload_clean_file_name,
			'download' => FALSE,
			'thumbSmallSize' => (Settings::get('media_thumb_size') !='') ? Settings::get('media_thumb_size') : 120,
			'thumbBigSize' => 500,
			'maxImageDimension' => array(
				'width' => (Settings::get('picture_max_width') !='') ? Settings::get('picture_max_width') : 2000,
				'height' => (Settings::get('picture_max_height') !='') ? Settings::get('picture_max_height') : 2000
			),
			'maxUploadSize' => intval(substr(ini_get('upload_max_filesize'), 0, -1)) * 1024 * 1024,
			'filter' => $allowed_mimes,
 			'allowed_extensions' => Settings::get_allowed_extensions(),
		);

		return $params;
	}

}
