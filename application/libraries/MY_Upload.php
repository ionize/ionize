<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 1.1.0
 *
 */

/**
 * Ionize MY_Upload Class
 *
 * Handles uploads with the DropZone JS lib
 *
 * DropZone allows :
 * - Auto-detection of browser capabilities (HTML5 / HTML4)
 * - Upload of large files in HTML5 mode (files are uploaded in small parts)
 * - Multiple files upload (in HTML4 mode, each file is a request, to limit the weight of sent data)
 *
 * DropZone needs some dedicated return data.
 * These data are returned after each upload process (full file or part file)
 * The controller called by DropZone must send back to DropZone these data
 * - file_content_length : In HTML5 mode, size of the file part sent during last part upload
 * - key : HTML4 mode, index of the file input processed
 * - finish : Status of the file upload process (1, 0)
 * - error : Mixed, FALSE or the error message
 * - size : In HTML5 mode, size of the sent file part; In HTML4 mode : Not relevant
 *
 *
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	Libraries
 *
 */


class MY_Upload extends CI_Upload
{
	/**
	 * Removes foreign chars, based on application/config/foreign_chars.php
	 * @var bool
	 */
	public $clean_foreign_chars = TRUE;

	/**
	 * Upload mode.
	 * Can be 'auto', 'html5', 'html4'
	 * @var string
	 */
	public $mode = 'auto';

	/**
	 * Allowed extensions
	 * @var array
	 */
	public $allowed_extensions = array();

	/**
	 * Override $allowed_extension. If TRUE, all extensions are allowed.
	 * @var bool
	 */
	public $safe = FALSE;

	/**
	 * Resize the picture file if set to TRUE
	 * @var bool
	 */
	public $resize = FALSE;

	/**
	 * Root path to the documents
	 * By default, website folder, from root.
	 * @var string
	 */
	public $document_root = '';

	/**
	 * Index of the file sent, starting at 0.
	 * Needed by DropZone : it must be returned after upload
	 *
	 * @var
	 */
	public $file_key;

	/**
	 * Upload status indicator
	 * Needed by DropZone (HTML5 mode)
	 *
	 * @var bool
	 */
	public $upload_finish = FALSE;

	/**
	 * Upload error indicator
	 * Needed by DropZone
	 *
	 * @var bool
	 */
	public $upload_error = FALSE;

	/**
	 * New folder chmod
	 * @var int
	 */
	public $new_folder_chmod = 0777;

	/**
	 * Resize width & height
	 * Must be set to allow resize, even $resize_image = TRUE
	 * @var int
	 */
	public $resize_width = 0;
	public $resize_height = 0;

	/**
	 * Resize images after upload ?
	 * @var bool
	 */
	public $resize_image = FALSE;

	public $file_content_length = 0;


	private $_http_headers = NULL;


	// --------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct($props = array())
	{
		parent::__construct($props);
	}


	// --------------------------------------------------------------------


	/**
	 * Initialize preferences
	 *
	 * $config = array(
	 * 		'document_root'			Absolute path. Root from where all other paths will be defined.
	 * 								No tampering to parent folder possible
	 * 								Cannot be send by the request
	 * 		'upload_path'			Relative path from the document_root in where the uploaded file will be stored
	 * 								Completes the document_root. No tampering to parent folder possible
	 * 								Cannot be send by the request
	 * 								Ionize : 'files/'
	 * 		'directory'				Relative path from '/document_root/upload_path/'
	 * 								Can be send by request.
	 * 								Overwritten by the do_upload() $config settings if it exists (to avoid user send)
	 * 								No tampering to parent folder possible
	 * 		'safe'					If set to TRUE, all file types will be allowed
	 * 								Else, only the extensions set in 'allowed_extensions' are allowed
	 * 		'allowed_extensions'	'*' : Allow all extensions
	 * 								Array : Array of allowed extensions
	 * 								Used by is_allowed_extension() if 'safe' = true
	 * 								and to prepare the file name : _prep_filename() (remove of the part extensions
	 * 		'clean_foreign_chars'	Set to TRUE, converts all foreign chars during the filename cleaning process
	 * 		'resize_image'			Resize the image to 'resize_width' and 'resize_height' if the file is one image
	 * 								'resize_width' and 'resize_height' must be set.
	 * 								The resize maintains image proportions.
	 * )
	 *
	 * @param	array
	 * @return	void
	 */
	public function initialize($config = array())
	{
		parent::initialize($config);

		$defaults = array(
			'safe' => FALSE,
			'clean_foreign_chars' => TRUE,
			'allowed_extensions' => array(),
			'document_root' => $_SERVER['DOCUMENT_ROOT'],
			'new_folder_chmod' => 0777,
			'resize_image' => FALSE,
			'resize_width' => 0,
			'resize_height' => 0,
		);

		foreach ($defaults as $key => $val)
		{
			if (isset($config[$key]))
			{
				$this->$key = $config[$key];
			}
			else
			{
				$this->$key = $val;
			}
		}

		// Foreign Chars : Text Helper
		if ($this->clean_foreign_chars)
		{
			$CI =& get_instance();
			$CI->load->helper('text');
		}

		// Sanitize of the upload path
		$this->document_root = $this->normalize($this->document_root);
		$this->upload_path = $this->normalize($this->document_root . $this->upload_path);

		log_message('debug', 'MY_Upload initialized');
	}


	// --------------------------------------------------------------------


	/**
	 * Init before upload
	 * Gives the user ability to change some preferences depending on his file
	 * Eg. directory
	 *
	 * Forbidden keys :
	 * - document_root
	 * - safe
	 *
	 * @param array $config
	 *
	 */
	public function before_upload_initialization($config = array())
	{
		// Forbidden before upload config key
		$forbidden = array(
			'document_root',
			'safe'
		);

		foreach ($config as $key => $val)
		{
			// if (isset($this->{$key}) && ! in_array($key, $forbidden))
			if (isset($this->{$key}) && ! in_array($key, $forbidden))
			{
				$this->$key = $config[$key];

				if ($key == 'upload_path')
					$this->upload_path = $this->normalize($this->document_root . $this->upload_path);
			}
		}
	}


	// --------------------------------------------------------------------


	/**
	 * @param array $config
	 *
	 * @return array|bool
	 */
	public function do_upload($config = array())
	{
		// Sets the config before one upload (useful if some config element has to be set without reloading the lib)
		if ( ! empty($config))
			$this->before_upload_initialization($config);

		if ($this->is_HTML5_upload($config))
		{
			return $this->HTML5_upload($config);
		}
		else
		{
			return $this->HTML4_upload($config);
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Performs the Upload in HTML5 mode
	 *
	 * @param array $config
	 *
	 * @return bool
	 */
	public function HTML5_upload($config=array())
	{
		// Get headers
		$headers = $this->get_http_headers();

		$directory = ! empty($headers['X-Directory']) ? $headers['X-Directory'] : '';
		$this->overwrite = ! empty($headers['X-Replace']) ? $headers['X-Replace'] : $this->overwrite;
		$this->resize_image = (bool) ! empty($headers['X-Resize']) ? $headers['X-Resize'] : $this->resize_image;

		// Forces the user directory through config
		if ( isset($config['directory']))
			$directory = $config['directory'];

		if ( isset($config['replace']))
			$this->overwrite = $config['replace'];

		// 0 at first send, 1 for next send
		$resume_flag = ! empty($headers['X-File-Resume']) ? FILE_APPEND : 0;

		// Fill all the Class file vars
		$this->set_file_data($headers, TRUE);

		// Set the upload path depending on the directory sent for this file
		$this->upload_path = $this->get_full_upload_path($directory);

		try
		{
			// Sanitize the file name for security
			$this->file_name = $this->clean_file_name($this->file_name);

			// Sanitize the file name for security
			$this->file_name = $this->clean_file_name($this->file_name);

			// Truncate the file name if it's too long
			if ($this->max_filename > 0)
				$this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);

			// Overwrite the file ?
			if ( ! $resume_flag && $this->overwrite == FALSE)
			{
				$this->file_name = $this->set_filename($this->upload_path, $this->file_name);

				if ($this->file_name === FALSE)
					$this->set_error('upload_bad_filename', TRUE);
			}

			// Allowed extension ?
			// Original : is_allowed_filetype()
			// @todo : See impact of http://httpd.apache.org/docs/1.3/mod/mod_mime.html#multipleext
			if ( ! $this->is_allowed_extension($this->file_name))
				$this->set_error('upload_invalid_filetype', TRUE);

			// Size OK regarding the PHP ini ?
			// !! No need in HTML5 mode
//			if ($this->file_size > $this->get_max_upload_size())
//				$this->set_error('upload_file_exceeds_limit', TRUE);

			// Convert the file size to kilobytes
			// !! Must not be done in HTML5 : DropZone need the size in bytes
			/*
			if ($this->file_size > 0)
				$this->file_size = round($this->file_size/1024, 2);
			*/

			// Is the file size within the allowed maximum?
//			if ( ! $this->is_allowed_filesize())
//				$this->set_error('upload_invalid_filesize', TRUE);

			// Are the image dimensions within the allowed size?
			// Note: This can fail if the server has an open_basdir restriction.
			// !! Cannot be done in HTML5 mode (reads the uploaded file, and in this case, the file is not completely uploaded)
			/*
			if ( ! $this->is_allowed_dimensions())
				$this->set_error('upload_invalid_dimensions', TRUE);
			*/

			// Run the file through the XSS hacking filter
			// !! Cannot be done in HTML5 mode (the file is partially loaded)
			/*
			if ($this->xss_clean)
			{
				if ($this->do_xss_clean() === FALSE)
					$this->set_error('upload_unable_to_write_file', TRUE);
			}
			*/

			// Creates directory if it doesn't exists
			if ( ! is_dir($this->upload_path))
			{
				if ( ! @mkdir($this->upload_path, $this->new_folder_chmod, TRUE))
					$this->set_error('MY_Upload : mkdir_failed:' . $this->upload_path, TRUE);

				@chmod($this->upload_path, $this->new_folder_chmod);
			}

			// Ahhhh.... Write the file part
			if (@file_put_contents($this->upload_path.$this->file_name, file_get_contents('php://input'), $resume_flag) === FALSE)
			{
				$this->set_error('upload_destination_error', TRUE, $this->upload_path.$this->file_name);
			}
			else
			{
				// Upload finished ?
				if (filesize($this->upload_path.$this->file_name) >= $this->file_size)
				{
					$this->upload_finish = TRUE;

					// Resize if image and asked to.
					if ($this->is_image() && $this->resize_image)
						$this->resize_image($this->upload_path.$this->file_name);

					Event::fire('Upload.success', $this->data());
				}
				else
				{
					$this->upload_finish = FALSE;
				}
			}
		}
		catch(Exception $e)
		{
			// DropZone needed statuses
			$this->upload_error = $e->getMessage();
			$this->upload_finish = TRUE;

			Event::fire('Upload.error', $this->data());

			return FALSE;
		}
		return TRUE;
	}


	// --------------------------------------------------------------------


	/**
	 * Perform the upload in HTML4 mode
	 *
	 * @param array $config
	 *
	 * @return array|bool
	 */
	public function HTML4_upload($config=array())
	{
		$directory = ! empty($_POST['directory']) ? $_POST['directory'] : '';
		$this->overwrite = ! empty($_POST['replace']) ? $_POST['replace'] : $this->overwrite;

		// Forces the user directory through config : can be set to '' to avoid $_POST directory
		if ( isset($config['directory']))
			$directory = $config['directory'];

		// Forces the file overwrite
		if ( isset($config['replace']))
			$this->overwrite = $config['replace'];

		// File to upload : first element of $_FILES
		$file = reset($_FILES);

		if ( ! empty($file))
		{
			// Fill all the Class file vars
			$this->set_file_data($file);

			// Set the upload path depending on the directory sent for this file
			$this->upload_path = $this->get_full_upload_path($directory);

			try
			{
				// Was the file able to be uploaded ? If not, determine the reason why.
				if ( ! is_uploaded_file($file['tmp_name']))
					$this->set_upload_error($file, TRUE);

				// Sanitize the file name for security
				$this->file_name = $this->clean_file_name($this->file_name);

				// Truncate the file name if it's too long
				if ($this->max_filename > 0)
					$this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);

				// Overwrite the file ?
				if ($this->overwrite == FALSE)
				{
					$this->file_name = $this->set_filename($this->upload_path, $this->file_name);

					if ($this->file_name === FALSE)
						$this->set_error('upload_bad_filename', TRUE);
				}

				// Allowed extension ?
				// Original : is_allowed_filetype()
				// @todo : See impact of http://httpd.apache.org/docs/1.3/mod/mod_mime.html#multipleext
				if ( ! $this->is_allowed_extension($this->file_name))
					$this->set_error('upload_invalid_filetype', TRUE);

				// Size OK regarding the PHP ini ?
				if ($this->file_size > $this->get_max_upload_size())
					$this->set_error('upload_file_exceeds_limit', TRUE);

				// Convert the file size to kilobytes
				// Why ?
//				if ($this->file_size > 0)
//					$this->file_size = round($this->file_size/1024, 2);

				// Is the file size within the allowed maximum?
//				if ( ! $this->is_allowed_filesize())
//					$this->set_error('upload_invalid_filesize', TRUE);

				// Are the image dimensions within the allowed size?
				// Note: This can fail if the server has an open_basdir restriction.
				if ( ! $this->is_allowed_dimensions())
					$this->set_error('upload_invalid_dimensions', TRUE);

				// Run the file through the XSS hacking filter
				if ($this->xss_clean)
				{
					if ($this->do_xss_clean() === FALSE)
						$this->set_error('upload_unable_to_write_file', TRUE);
				}

				// Creates directory if it doesn't exists
				if ( ! is_dir($this->upload_path))
				{
					if ( ! @mkdir($this->upload_path, $this->new_folder_chmod, TRUE))
						$this->set_error('MY_Upload : mkdir_failed:' . $this->upload_path, TRUE);

					@chmod($this->upload_path, $this->new_folder_chmod);
				}

				// Ahhhh.... Move the file
				if ( ! @copy($this->file_temp, $this->upload_path.$this->file_name))
				{
					if ( ! @move_uploaded_file($this->file_temp, $this->upload_path.$this->file_name))
					{
						$this->set_error('upload_destination_error', TRUE, $this->upload_path.$this->file_name);
					}
				}

				// Resize if image and asked to.
				if ($this->is_image() && $this->resize_image)
					$this->resize_image($this->upload_path.$this->file_name);

				$this->set_image_properties($this->upload_path.$this->file_name);

				$this->upload_finish = TRUE;
			}
			catch(Exception $e)
			{
				// DropZone needed statuses
				$this->upload_error = $e->getMessage();
				$this->upload_finish = TRUE;

				return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}


	// --------------------------------------------------------------------


	/**
	 * Detect if upload method is HTML5
	 *
	 * @return	boolean
	 *
	 */
	public function is_HTML5_upload()
	{
		if ($this->mode != 'html4')
		{
			return (empty($_FILES));
		}
		else
		{
			return FALSE;
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Returns one sent var, whatever the upload mode is
	 * In HTML5 mode, all vars in the HTTP header, user's vars start with 'X-'
	 * In HTML4 mode, they are send by POST.
	 *
	 * @param $key		var, lowercase.
	 * 					If the asked var is 'content-length', it will returns $_SERVER['Content-Length']
	 *
	 * @return mixed	FALSE if no var is found. (compat. with $this->input->post())
	 *
	 */
	public function get_var($key)
	{
		if ($this->is_HTML5_upload())
		{
			$headers = $this->get_http_headers();
			$headers_keys = array_keys($headers);

			// Simple values, like content-length
			foreach($headers_keys as $pos => $key2)
			{
				if ($key == $key2)
					return $headers[$pos];
			}

			// Try with X-Something values
			$headers = array_values($headers);
			$headers_keys2 = $headers_keys;
			array_walk ( $headers_keys2 , function(&$n) {$n = strtolower($n); } );

			foreach($headers_keys2 as $pos => $key2)
			{
				if (substr($key2, 2) == $key)
					return $headers[$pos];
			}

			// Finally, try with X- keys
			// Custom headers are prefixed with "X-" and "_" is replaced by "-".
			// $headers_keys2 has already lowercase keys values
			foreach($headers_keys2 as $pos => $key2)
			{
				$key2 = str_replace('-', '_', $key2);
				if (substr($key2, 0, 2) == 'x_' && substr($key2, 2) == $key)
					return $headers[$pos];
			}
		}
		else
		{
			$val = isset($_POST[$key]) ? $_POST[$key] : FALSE;
			return $val;
		}

		return FALSE;
	}


	// --------------------------------------------------------------------


	/**
	 * Set current upload file's data
	 *
	 * @param      $data
	 * @param bool $from_headers
	 */
	public function set_file_data($data, $from_headers = FALSE)
	{
		if ( ! $from_headers)
		{
			$this->file_key = $this->get_file_key();
			$this->file_size = $data['size'];
			$this->file_temp = $data['tmp_name'];
			$this->file_type = $this->get_file_type($data);
			$this->orig_name = $data['name'];
			$this->file_name = $this->_prep_filename($data['name']);
			$this->file_ext	 = $this->get_extension($this->file_name);
			$this->client_name = $this->file_name;
		}
		else
		{
			$filename = basename($data['X-File-Name']);

			$this->file_key = isset($data['X-File-Id']) ? $data['X-File-Id'] : NULL;
			$this->file_size = ! empty($data['X-File-Size']) ? $data['X-File-Size'] : '0';
			$this->file_content_length = ! empty($data['Content-Length']) ? $data['Content-Length'] : '0';
			$this->file_temp = $filename;
			$this->file_type = $this->get_mime_from_extension($filename);
			$this->orig_name = $filename;
			$this->file_name = $this->_prep_filename($filename);
			$this->file_ext	 = $this->get_extension($this->file_name);
			$this->client_name = $this->file_name;
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Finalized Data Array
	 *
	 * Returns an associative array containing all of the information
	 * related to the upload, allowing the developer easy access in one array.
	 *
	 * @param bool $json	If set to TRUE, returns the data in JSON format
	 *
	 * @return array|string
	 */
	public function data($json=FALSE)
	{
		// Common file data
		$data = array (
			'key'				=> $this->file_key,					// HTML4 mode
			'finish'			=> $this->upload_finish,
			'error'				=> $this->upload_error,
			'file_name'			=> $this->file_name,				// Used by DropZone to send new file chunk
			'file_type'			=> $this->file_type,
			'file_path'			=> $this->upload_path,
			'full_path'			=> $this->upload_path.$this->file_name,
			'raw_name'			=> str_replace($this->file_ext, '', $this->file_name),
			'orig_name'			=> $this->orig_name,
			'client_name'		=> $this->client_name,
			'file_ext'			=> $this->file_ext,
			'file_size'			=> $this->file_size,
			'size'				=> $this->file_content_length,		// HTML5 mode
			'is_image'			=> $this->is_image(),
			'image_width'		=> $this->image_width,
			'image_height'		=> $this->image_height,
			'image_type'		=> $this->image_type,
			'image_size_str'	=> $this->image_size_str,
		);

		// User set data
		$user_data = $this->get_user_data();

		// $data last so the request cannot overwritte common file data
		$data = array_merge($user_data, $data);

		if ($json)
			return json_encode($data);
		else
			return $data;
	}


	// --------------------------------------------------------------------


	/**
	 * Returns array of user's sent data
	 * by $_POST or through headers
	 *
	 */
	public function get_user_data()
	{
		$data = array();

		if ($this->is_HTML5_upload())
		{
			$headers = $this->get_http_headers();

			foreach($headers as $key => $value)
			{
				if (strpos($key, 'X-') === 0)
				{
					$new_key = strtolower($key);
					$new_key = explode('-', $new_key);
					$new_key = array_slice($new_key, 1);
					$new_key = implode('-', $new_key);

					$data[$new_key] = $value;
				}
			}
		}
		else
		{
			$data = $_POST;
		}

		return $data;
	}


	// --------------------------------------------------------------------


	/**
	 * Set one file upload error
	 * Fires one exception is asked to.
	 *
	 * @param      $file
	 * @param bool $throw_exception
	 */
	public function set_upload_error($file, $throw_exception=FALSE)
	{
		$error_code = ( ! isset($file['error'])) ? 4 : $file['error'];

		switch($error_code)
		{
			case 1:	// UPLOAD_ERR_INI_SIZE
				$this->set_error('upload_file_exceeds_limit', $throw_exception);
				break;
			case 2: // UPLOAD_ERR_FORM_SIZE
				$this->set_error('upload_file_exceeds_form_limit', $throw_exception);
				break;
			case 3: // UPLOAD_ERR_PARTIAL
				$this->set_error('upload_file_partial', $throw_exception);
				break;
			case 4: // UPLOAD_ERR_NO_FILE
				$this->set_error('upload_no_file_selected', $throw_exception);
				break;
			case 6: // UPLOAD_ERR_NO_TMP_DIR
				$this->set_error('upload_no_temp_directory', $throw_exception);
				break;
			case 7: // UPLOAD_ERR_CANT_WRITE
				$this->set_error('upload_unable_to_write_file', $throw_exception);
				break;
			case 8: // UPLOAD_ERR_EXTENSION
				$this->set_error('upload_stopped_by_extension', $throw_exception);
				break;
			default :   $this->set_error('upload_no_file_selected', $throw_exception);
			break;
		}
	}


	// --------------------------------------------------------------------


	/**
	 * Prep Filename
	 *
	 * Prevents possible script execution from Apache's handling of files multiple extensions
	 * http://httpd.apache.org/docs/1.3/mod/mod_mime.html#multipleext
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _prep_filename($filename)
	{
		if (strpos($filename, '.') === FALSE OR $this->allowed_extensions == '*')
		{
			return $filename;
		}

		$parts		= explode('.', $filename);
		$ext		= array_pop($parts);
		$filename	= array_shift($parts);

		foreach ($parts as $part)
		{
			if ( ! in_array(strtolower($part), $this->allowed_extensions) )
			{
				$filename .= '.'.$part.'_';
			}
			else
			{
				$filename .= '.'.$part;
			}
		}

		$filename .= '.'.$ext;

		return $filename;
	}


	// --------------------------------------------------------------------


	/**
	 * Checks if the file extension is allowed
	 * Only done if safe = FALSE
	 *
	 * @param $filename
	 *
	 * @return bool
	 */
	public function is_allowed_extension($filename)
	{
		if ( ! $this->safe)
		{
			$fi = pathinfo($filename);

			if ( ! isset($fi['extension']))
				return FALSE;

			if ( ! in_array(strtolower($fi['extension']), $this->allowed_extensions))
				return FALSE;
		}

		return TRUE;
	}


	// --------------------------------------------------------------------


	/**
	 * Cleans the file name
	 *
	 * @param $filename
	 *
	 * @return string
	 */
	public function clean_file_name($filename)
	{
		// Remove bad chars
		$filename = parent::clean_file_name($filename);

		// Spaces
		if ($this->remove_spaces == TRUE)
		{
			$filename = preg_replace("/\s+/", "_", $filename);
		}

		// Foreign chars
		if ($this->clean_foreign_chars)
		{
			$filename = convert_accented_characters($filename);
		}

		$filename  = preg_replace('/[^a-zA-Z0-9\/_.|+ -]/', "_", $filename);
		$filename  = preg_replace("/[\/_|+ -]+/", "_", $filename);

		// Remove first and last not wanted chars
		$filename  = trim($filename, '_-. ');

		return $filename;
	}


	// --------------------------------------------------------------------


	/**
	 * Normalizes one path
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public function normalize($path)
	{
		$path = preg_replace('/(\\\|\/)+/', '/', $path);

		// FIRST clean out the './' directories to prevent 'a/./.././etc/' from succeeding
		$path = preg_replace('#/(\./)+#', '/', $path);

		// special fix: now strip trailing '/.' section;
		// MUST replace by '/' (trailing) or path won't be accepted as legal when this is the '.' requested for root '/'
		$path = preg_replace('#/\.$#', '/', $path);

		// Temporarily strip off the leading part up to prevent entries like '../d:/dir' to succeed when the site root is 'c:/', for example:
		$lead = '';

		// the leading part may NOT contain any directory separators, as it's for drive letters only.
		// So we must check in order to prevent malice like /../../c:/dir from making it through.
		if (preg_match('#^([A-Za-z]:)?/(.*)$#', $path, $matches))
		{
			$lead = $matches[1];
			$path = '/' . $matches[2];
		}

		while (($pos = strpos($path, '/..')) !== FALSE)
		{
			$prev = substr($path, 0, $pos);
			/*
			 * on Windows, you get:
			 *
			 * dirname("/") = "\"
			 * dirname("y/") = "."
			 * dirname("/x") = "\"
			 *
			 * so we'd rather not use dirname()
			 */
			$p2 = strrpos($prev, '/');
			if ($p2 === FALSE)
			{
				log_message('error', 'MY_Upload ERROR : Path tampering : ' . $path);
			}
			$prev = substr($prev, 0, $p2);
			$next = substr($path, $pos + 3);
			if ($next && $next[0] !== '/')
			{
				log_message('error', 'MY_Upload ERROR : Path tampering : ' . $path);
			}
			$path = $prev . $next;
		}

		// Be sure we have one last '/'
		$path = rtrim($path, '/').'/';

		$path = $lead . $path;

		return $path;
	}


	// --------------------------------------------------------------------


	/**
	 * Get the full upload path
	 * The user wished dir (sent by $_POST) can be passed
	 *
	 * @param string $user_dir
	 *
	 * @return string
	 *
	 */
	public function get_full_upload_path($user_dir = '')
	{
		if ($user_dir != '')
			$user_dir = $this->normalize($user_dir);

		$user_path = $this->upload_path . $user_dir;

		return $this->normalize($user_path);
	}


	// --------------------------------------------------------------------


	/**
	 * Returns the file mime
	 *
	 * @param $file
	 *
	 * @return string
	 *
	 */
	public function get_file_type($file)
	{
		$file_type = preg_replace("/^(.+?);.*$/", "\\1", $file['type']);
		$file_type = strtolower(trim(stripslashes($file_type), '"'));

		return $file_type;
	}


	// --------------------------------------------------------------------


	/**
	 * Each file sent via DropZone has one key (number starting from 0)
	 * This key must be sent again to DropZone after upload
	 *
	 * @return int
	 */
	public function get_file_key()
	{
		// Send by DropZone. Usualy 'file_';
		$file_input_prefix = $_POST['file_input_prefix'];

		reset($_FILES);
		$k = key($_FILES);

		return (int)substr($k, strpos($k, $file_input_prefix) + strlen($file_input_prefix));
	}


	// --------------------------------------------------------------------


	/**
	 * Set one error and throw one exception if asked to.
	 *
	 * @param      		$msg
	 * @param bool 		$throw_exception
	 * @param string 	$optional_info
	 *
	 * @throws Exception
	 */
	public function set_error($msg, $throw_exception = FALSE, $optional_info = NULL)
	{
		$CI =& get_instance();
		$CI->lang->load('upload');

		if (is_array($msg))
		{
			foreach ($msg as $val)
			{
				$msg = ($CI->lang->line($val) == FALSE) ? $val : $CI->lang->line($val);
				if ( ! is_null($optional_info))
					$msg .= ' : ' . $optional_info;
				$this->error_msg[] = $msg;
				log_message('error', $msg);
			}
		}
		else
		{
			$msg = ($CI->lang->line($msg) == FALSE) ? $msg : $CI->lang->line($msg);
			if ( ! is_null($optional_info))
				$msg .= ' : ' . $optional_info;
			$this->error_msg[] = $msg;
			log_message('error', $msg);
		}

		if ($throw_exception)
			throw new Exception($msg);
	}


	// --------------------------------------------------------------------


	/**
	 * Resize one image
	 * Check if the resize values are properly set.
	 *
	 * @param $image_path
	 */
	public function resize_image($image_path)
	{
		if ($this->resize_width == 0 && $this->resize_height == 0)
			return;

		$CI =& get_instance();

		$config['source_image'] = $image_path;
		$config['maintain_ratio'] = TRUE;

		if ($this->resize_width > 0)
			$config['width'] = $this->resize_width;

		if ($this->resize_height > 0)
			$config['height'] = $this->resize_height;

		$CI->load->library('image_lib', $config);

		$CI->image_lib->resize();
	}


	// --------------------------------------------------------------------


	/**
	 * @param $file
	 *
	 * @return null|string
	 *
	 */
	public function get_mime_from_extension($file)
	{
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		$mime = NULL;

		if ((empty($mime) || $mime === 'application/octet-stream') && strlen($ext) > 0)
		{
			$ext2mimetype_arr = $this->get_mime_type_definitions();

			if (array_key_exists($ext, $ext2mimetype_arr))
				$mime = $ext2mimetype_arr[$ext];
		}

		if (empty($mime))
		{
			$mime = 'application/octet-stream';
		}

		return $mime;
	}


	// --------------------------------------------------------------------


	/**
	 * @return array
	 */
	public function get_mime_type_definitions()
	{
		$mimes = array();
		include(APPPATH.'config/mimes_ionize.php');

		$pref_ext = array();
		$mimes_result = array();

		if (is_array($mimes))
		{
			foreach($mimes as $values)
			{
				foreach($values as $k => $v)
				{
					if ( ! is_array($v)) $v = array($v);

					$mimes_result[$k] = $v[0];
					$p = NULL;
					if ( ! empty($v[1]))
					{
						$p = trim($v[1]);
					}
					// is this the preferred extension for this mime type? Or is this the first known extension for the given mime type?
					if ($p === '*' || !array_key_exists($v[0], $pref_ext))
					{
						$pref_ext[$v[0]] = $k;
					}
				}
			}
			// stick the mime-to-extension map into an 'illegal' index:
			$mimes_result['.'] = $pref_ext;
		}

		return $mimes_result;
	}


	// --------------------------------------------------------------------


	public function get_max_upload_size()
	{
		$max_upload = $this->convert_size(ini_get('upload_max_filesize'));
		$max_post = $this->convert_size(ini_get('post_max_size'));
		$memory_limit = $this->convert_size(ini_get('memory_limit'));
		$limit = min($max_upload, $max_post, $memory_limit);

		return $limit;
	}


	// --------------------------------------------------------------------


	/**
	 *
	 * Convert to bytes a information scale
	 *
	 * @param	string	Information scale
	 * @return	integer	Size in bytes
	 *
	 */
	protected function convert_size($val)
	{
		$val = trim($val);
		$last = strtolower($val[strlen($val) - 1]);

		switch ($last) {
			case 'g': $val *= 1024;

			case 'm': $val *= 1024;

			case 'k': $val *= 1024;
		}

		return $val;
	}


	// --------------------------------------------------------------------


	/**
	 * Returns the HTTP headers
	 * Useful for the HTML5 mode
	 *
	 * @return array
	 */
	public function get_http_headers()
	{
		if (is_null($this->_http_headers))
		{
			// GetAllHeaders doesn't work with PHP-CGI
			if (function_exists('getallheaders'))
			{
				$headers = getallheaders();
			}
			else
			{
				$headers = array(
					'Content-Length' => $_SERVER['CONTENT_LENGTH'],
					'X-File-Id' 	=> $_SERVER['HTTP_X_FILE_ID'],
					'X-File-Name' 	=> $_SERVER['HTTP_X_FILE_NAME'],
					'X-File-Resume' => $_SERVER['HTTP_X_FILE_RESUME'],
					'X-File-Size' 	=> $_SERVER['HTTP_X_FILE_SIZE'],
					'X-Directory' 	=> $_SERVER['X-Directory'],
					'X-Filter' 		=> $_SERVER['X-Filter'],
					'X-Resize' 		=> $_SERVER['X-Resize'],
				);

				foreach($_SERVER as $key => $val)
				{
					if ( substr(0, 6) == 'HTTP_X')
					{
						$new_key = strtolower($key);
						$new_key = array_slice(explode('_', $new_key), 1);
						array_walk ( $new_key , function(&$n) {$n = ucfirst($n); } );
						$new_key = implode('-', $new_key);
						$headers[$new_key] = $val;
					}
				}
			}

			$this->_http_headers = $headers;
		}

		return $this->_http_headers;
	}
}
