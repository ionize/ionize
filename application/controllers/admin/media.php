<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Media Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Media management
 * @author		Ionize Dev Team
 *
 */

class Media extends MY_admin 
{

	protected static $DEFAULT_EXPIRE = 604800;
	protected static $DEFAULT_TYPE = 'text/html';
	
	protected static $MP3_ID3 = array('album', 'artist', 'title', 'year');


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model('media_model');
		$this->load->model('extend_field_model', '', true);

		// Librairies
		$this->load->library('medias');
		$this->load->library('image_lib');

		
		// Remove protection if the filemanager is called on upload
		// Purpose : Allow upload.
		// Check is done in the method.
		if ($this->uri->segment(3) == 'filemanager' && $this->uri->segment(4) == 'upload')
		{
			$this->connect->folder_protection = array();	
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Display the filemanager
	 * Used for standalone display in the "Content->Medias" panel of Ionize
	 *
	 */
	function get_media_manager($mode = NULL)
	{
		// Open the file manager regarding to settings
		switch(Settings::get('filemanager'))
		{
			// TinyMCE FileManager
			case 'filemanager' :
				
				// Filemanager view
				$this->output('filemanager/filemanager');
				break;
				
			case 'tinybrowser' :
				
				$this->template['mode'] = (is_null($mode)) ? 'file' : 'image';
				
				$this->output('filemanager/tinybrowser');
				break;
				
			case 'kcfinder' :
				
				$this->output('filemanager/kcfinder');
				break;
			
			case 'mootools-filemanager' :

				$this->output('filemanager/mootools_filemanager');
				break;
			
				
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Mootools FileManager loader
	 * Equiv. to the "manager.php" file in mootools-filemanager Demo folder.
	 *
	 * @param	String		Event to call. Calls Filemanager->on[$event](). Exemple : The URL /media/filemanager/detail calls Filemanager->onDetail()
	 * @param	String		The directory to upload. Only used if $event is "upload"
	 * @param	Boolean		Should the picture be resized ? 1 for yes.
	 * @param	String		Tokken used to check if the user is connected.
	 *
	 */
	function filemanager($event = NULL, $resize = FALSE, $uploadAuthData = FALSE)
	{
		// Get allowed mimes
		$allowed_mimes = implode(',', Settings::get_allowed_mimes());

		$params = array (
			'URLpath4FileManagedDirTree' => Settings::get('files_path') . '/',
//			'URLpath4FileManagedDirTree' => '/' . trim(Settings::get('files_path'), '/') . '/',
//			'FileSystemPath4SiteDocumentRoot' => DOCPATH,
			'URLpath4assets' => Theme::get_theme_path().'javascript/mootools-filemanager/Assets',
//			'URLpath4thumbnails' => '/' . trim(Settings::get('files_path'), '/') . '/.thumbs',
			'URLpath4thumbnails' => Settings::get('files_path') . '/.thumbs',
			
			
			'upload' => TRUE,
			'destroy' => TRUE,
			'create' => TRUE,
			'move' => TRUE,
			'download' => FALSE,
			'thumbSmallSize' => (Settings::get('media_thumb_size') !='') ? Settings::get('media_thumb_size') : 120,
			'thumbBigSize' => 500,
			'maxImageDimension' => array(
				'width' => (Settings::get('picture_max_width') !='') ? Settings::get('picture_max_width') : 2000,
				'height' => (Settings::get('picture_max_height') !='') ? Settings::get('picture_max_height') : 2000
			),
			'maxUploadSize' => intval(substr(ini_get('upload_max_filesize'), 0, -1)) * 1024 * 1024,
			'filter' => $allowed_mimes
		);

//		$this->load->library('Filemanager', $params);
		$this->load->library('Filemanagerwithaliassupport', $params);

		// Fires the Event called by FileManager.js
		// The answer of this called id a JSON object
		// If no event is givven, it will call the "display" event
		if ($event != 'upload')
		{
//			$this->Filemanager->fireEvent(!is_null($event) ? $event : null);
			$this->Filemanagerwithaliassupport->fireEvent(!is_null($event) ? $event : null);
		}
		else
		{
			if ($event == 'upload')
			{
				// Flash mode (Multiple files) : PHPSESSID is send
				if ( ! empty($_POST['PHPSESSID']))
					$session_data = $this->session->switchSession($_POST['PHPSESSID']);
				
				// Get the original session tokken
				$tokken = $this->session->userdata('uploadTokken');
				
				// Get the sent tokken & compare
				$sent_tokken = ( ! empty($_POST['uploadTokken'])) ? $_POST['uploadTokken'] : '';

				// Only upload if tokkens match
				if ($tokken == $sent_tokken)
				{
//					$this->Filemanager->fireEvent($event);
					$this->Filemanagerwithaliassupport->fireEvent($event);
				}
				else
				{
					$this->xhr_output(array(
						'status' => 0,
						'error' => lang('ionize_session_expired')
					));
				}
			}
		}
		
		die();
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Called by ckEditor
	 * CKEDITOR.config.filebrowserBrowseUrl
	 *
	 */
	function ckfilemanager()
	{
		$data['CKEditorFuncNum'] = $_REQUEST['CKEditorFuncNum'];
	
		$this->output('filemanager/mootools_filemanager_ck', $data);	
	}

	// ------------------------------------------------------------------------


	/**
	 * Returns a tokken based on the current session ID + the encryption key : md5($this->session->userdata('session_id') . config_item('encryption_key'))
	 * If the user isn't connected, returns an empty string
	 *
	 * Called through XHR when the filemanager is opened.
	 * The tokken is send with the uploaded data and checked before anything is uploaded
	 *
	 */
	function get_tokken()
	{
		if (Connect()->is('editors'))
		{
			$tokken = md5(session_id() . config_item('encryption_key'));
			
			$this->session->set_userdata('uploadTokken', $tokken);

			echo json_encode(array(
				'tokken' => $tokken 
			));
		}
		else
		{
			echo json_encode(array(
				'tokken' => ''
			));
		}
		die();
	}

	// ------------------------------------------------------------------------


	/**
	 * Returns the media list depending on the media type
	 *
	 * @param	string	Media type. Can be 'picture', 'music', 'video', 'file'
	 * @param	string	parent. Example : 'article', 'page'
	 * @param	string	Parent ID
	 *
	 */
	function get_media_list($type, $parent, $id_parent)
	{
		$data['items'] = $this->media_model->get_list($parent, $id_parent, $type);
		
		// To set data relative to the parent
		$data['parent'] = $parent;
		$data['id_parent'] = $id_parent;

		$data['type'] = $type;
		
		if (empty($data['items']))
		{
			// Addon data to the answer
			$output_data = array('type' => $type);

			// Answer send
			$this->notice(lang('ionize_message_no_'.$type), $output_data);
		}
		else
		{
			// Media List view
			if ($type == 'picture')
				$view = $this->load->view('media_picture_list', $data, true);
			else
				$view = $this->load->view('media_list', $data, true);
			
			// Addon data to the answer			
			$output_data = array('type' => $type, 'content' => $view);
			
			// Answer send
			$this->success(null, $output_data);
		}
	}


	function get_crop($id_media)
	{
		$picture = $this->media_model->get($id_media);
		
		$path = $picture['path'];

		$size = @getimagesize(DOCPATH.$path);
		$size = array
		(
			'width' => $size[0],
			'height' => $size[1]
		);
			
		$this->template['id_media'] = $id_media;
		$this->template['path'] = $path;
		$this->template['size'] = $size;
		
		$this->output('media_picture_crop');	
	}

	
	function crop()
	{
		$coords = $this->input->post('coords');
		$id_media = $this->input->post('id_media');
		$path = DOCPATH.$this->input->post('path');
		
		// Get image dimension before crop
		$dim = $this->get_image_dimensions($path);
			
		// CI Image_lib config array
		$config = array
		(
			'source_image' => $path,
			'new_image' => '',
			'x_axis' => $coords['x'],
			'y_axis' => $coords['y'],
			'unsharpmask' => FALSE,
			'maintain_ratio' => FALSE,
			'width' => $coords['w'],
			'height' => $coords['h']
		);
		
		$this->image_lib->clear();
		$this->image_lib->initialize($config);
				
		if ( TRUE !== $this->image_lib->crop() )
		{
			// Error Message
			$this->callback[] = array(
				'fn' => 'ION.notification',
				'args' => array('error', lang('ionize_exception_image_crop'))
			);
		}
		else
		{
			// Success Message
			$this->callback[] = array(
				'fn' => 'ION.notification',
				'args' => array('success', lang('ionize_message_operation_ok'))
			);

			$this->callback[] = array(
				'fn' => 'ION.updateElement',
				'args' => array(
					'element'=> 'wImageCrop'.$id_media.'_content',
					'url' => 'media/get_crop/' . $id_media
				)
			);

		}
		
		$this->response();
		
	}

	// ------------------------------------------------------------------------


	/**
	 * Add one media to a parent
	 * Creates also thumbnails for picture if type = 'picture'
	 *
	 * @param	string	Media type. Can be 'picture', 'music', 'video', 'file'
	 * @param	string	parent. Example : 'article', 'page'
	 * @param	string	Parent ID
	 * @param	string	Deprecated.
	 *					The path is send through post
	 *					Complete path, including media file name, to the medium
	 *
	 */
	function add_media($type, $parent, $id_parent) 
	{
		// Clear the cache
		Cache()->clear_cache();

		/*
		 * Some path cleaning
		 * The media path should start at the root media dir.
		 * Adding base_url() to the media path gives the complete media path
		 * Example : files/pictures/my_picture.jpg
		 */
		$path = ltrim($this->input->post('path'), '/');
		 
		
		// If not protocol prefix, the base URL has to be cut
		$host = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
		$host .= "://".$_SERVER['HTTP_HOST'];

		// Get the base URL as /ionize_install_path
		$base_url = str_replace($host, '', base_url());

		// Clean the first '/'
		$base_url = preg_replace('/^[\/]/', '', $base_url);

		$path = str_replace($base_url, '', $path);
		
		// Clean the first '/'
		$path = preg_replace('/^[\/]/', '', $path);

		// DB Insert
		$id = $this->media_model->insert_media($type, $path);

		// Thumbnail creation for picture media type (if the picture isn't in a thumb folder)
		if ($type == 'picture' && (strpos($path, '/thumb_') == FALSE))
		{
			try 
			{
				$this->_init_thumbs($id);
			}
			catch (Exception $e)
			{
				$this->error($e->getMessage());
				return;
			}
		}

		// Tag ID3 if MP3
		if ($type == 'music' && $this->is($path, 'mp3'))
		{
			$data = array();
			$this->media_model->feed_template($id, $data);
			$this->media_model->feed_lang_template($id, $data);

			$this->set_ID3($data, $this->get_ID3($path));

			$this->media_model->save($data, $data);
		}
		
		// Parent linking
		$data = '';		

		if (!$this->media_model->attach_media($type, $parent, $id_parent, $id)) 
		{
			$this->error(lang('ionize_message_media_already_attached'));
		}
		else 
		{
			// Addon answer data
			$output_data = array('type' => $type);
		
			$this->success(lang('ionize_message_media_attached'), $output_data);
		}
	}

	
	
	function add_external_media()
	{
		// Clear the cache
		Cache()->clear_cache();

		$path = $this->input->post('path');
		$type = $this->input->post('type');
		$parent = $this->input->post('parent');
		$id_parent = $this->input->post('id_parent');

		$pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
		preg_match($pattern, $path, $link);
		if (empty($link[0])) $path = FALSE;
		else $path = $link[0];			

		if ($path != FALSE)
		{
			// DB Insert
			$id = $this->media_model->insert_media($type, $path);
		
			// Parent linking
			if (!$this->media_model->attach_media($type, $parent, $id_parent, $id)) 
			{
				$this->error(lang('ionize_message_media_already_attached'));
			}
			else 
			{
				// Addon answer data
				$output_data = array('type' => $type);
			
				// Error Message
				$this->callback = array(
					array(
						'fn' => 'mediaManager.loadMediaList',
						'args' => 'video'
					),
					array(
						'fn' => 'ION.emptyElement',
						'args' => 'addVideo'
					)					
					
				/*
					array(
						'fn' => 'ION.notification',
						'args' => array('success', lang('ionize_message_media_attached'), $output_data)
					),
					array(
						'fn' => 'ION.JSON',
						'args' => array	(
							'media/get_media_list/' . $type . '/' .  $parent . '/' . $id_parent
						)
					)
				*/
				);
	
				$this->response();
			}
		}
		else
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	/** 
	 * Init all the thumbs fo a given parent
	 * @param	string	parent type
	 * @param	string	parent ID
	 *
	 * @TODO : Improve the errors management
	 *
	 */
	function init_thumbs_for_parent($parent, $id_parent)
	{
		$pictures =	$this->media_model->get_list($parent, $id_parent, 'picture');

		$return = true;

		foreach($pictures as $picture)
		{
			try
			{
				$this->_init_thumbs($picture['id_media']);
			}
			catch(Exception $e)
			{
				// Fail message
				$this->error($e->getMessage());

				return;
			}
		}
		
		// Everything's OK
		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	/** 
	 * Init the thumbs for one picture
	 *
	 * @param	string	Picture ID
	 *
	 */
	function init_thumbs($id)
	{
		try
		{
			// Thumbs init
			$this->_init_thumbs($id);
			
			// Confirmation message
			$this->success(lang('ionize_message_operation_ok'));
		}
		catch(Exception $e)
		{
			// Fail message
			$this->error($e->getMessage());
		}
	}


	// ------------------------------------------------------------------------


	/** 
	 * Detach media from a parent element
	 *
	 * @param	string		Media type. Transmitted to send it back to the javascript onSuccess (disposeMedia)
	 * @param	string		parent type. Ex : 'page', 'article'
	 * @param	string		parent ID
	 * @param	string		medium ID
	 *
	 */
	function detach_media($type, $parent, $id_parent, $id_media) 
	{
		if ($parent !== false && $id_parent !== false && $id_media !== false)
		{			
			// Clear the cache
			Cache()->clear_cache();

			// Delete succeed : Message to user
			if ($this->media_model->delete_joined_key('media', $id_media, $parent, $id_parent) > 0)
			{
				// Used by answer callback to delete HtmlDomElement item
				$this->id = $id_media;
				
				// Addon data
				$output_data = array('type' => $type);
				
				// Answer
				$this->success(lang('ionize_message_media_detached'), $output_data);
			}
			// Error Message
			else
			{
				$this->error(lang('ionize_message_media_not_detached'));
			}
		}
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Detach all media depending on the type for a given parent
	 *
	 * @param 	string	parent type
	 * @param	string	Parent ID
	 * @param	string 	parent ID
	 *
	 */
	function detach_media_by_type($parent, $id_parent, $type = false)
	{
		if ($parent !== false && $id_parent !== false && $type !== false)
		{
			// Clear the cache
			Cache()->clear_cache();

			// Delete succeed : Message to user
			if ($this->media_model->detach_media_by_type($parent, $id_parent, $type) > 0)
			{
				$this->success(lang('ionize_message_operation_ok'));
			}
			// Notice message : No media to detach
			else
			{
				$this->error(lang('ionize_message_no_media_to_detach'));
			}
		}	
	}
	

	// ------------------------------------------------------------------------


	/** 
	 * Saves media order for one parent
	 * 
	 * @param	string	parent type. Can be 'page', 'article'
	 * @param	string	parent ID
	 *
	 */
	function save_ordering($parent, $id_parent)
	{
		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Clear the cache
			Cache()->clear_cache();

			$this->media_model->save_ordering($order, $parent, $id_parent);
			
			// Answer
			$this->success(lang('ionize_message_operation_ok'));
		}
		else 
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}
	
	
	// ------------------------------------------------------------------------

	
	/** 
	 * Shows one media meta data
	 *
	 * @param string	Media type
	 * @param string	Media ID
	 *
	 */
	function edit($type, $id)
	{
		$this->media_model->feed_template($id, $this->template);

		$this->media_model->feed_lang_template($id, $this->template);
		
		// Get the mp3 tags
		if ( $this->is($this->template['path'], 'mp3') )
		{
			$this->set_ID3($this->template, $this->get_ID3($this->template['path']));
		}

		// Get the thumbs to check each thumb status
		$this->template['thumbs'] = $this->settings_model->get_list(array('name like' => 'thumb_%'));

		/*
		 * extend fields
		 *
		 */
		$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('media', $id);
		
		// Location : 'http:' / 'files'
		$location = array_shift(explode('/', $this->template['path']));
		if ($location == 'http:')
			$this->template['is_external'] = TRUE;
		else
			$this->template['is_external'] = FALSE;
		
		$this->template['location'] = $location;
		
		// Modules addons
		$this->load_modules_addons($this->template);

		$this->output('media_edit');	
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Saves one media metadata
	 *
	 */
	function save()
	{
		// Clear the cache
		Cache()->clear_cache();

		// Standard data;
		$data = array();
		
		// Standard fields
		$fields = $this->db->list_fields('media');

		foreach ($fields as $field)
		{
			if ( $this->input->post($field) !== false)
			{
				$data[$field] = $this->input->post($field);
			}
		}

		// Lang data
		$lang_data = array();

		$fields = $this->db->list_fields('media_lang');
		
		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				if ( $this->input->post($field.'_'.$language['lang']) !== false)
					$lang_data[$language['lang']][$field] = $this->input->post($field.'_'.$language['lang']);
			}
		}

		// Database save
		$this->id = $this->media_model->save($data, $lang_data);

		// Save extend fields data
		$this->extend_field_model->save_data('media', $this->id, $_POST);

		$media = $this->media_model->get($this->id, Settings::get_lang('default'));

		// Save ID3 to file if MP3
		if ( $this->is($media['path'], 'mp3') )
		{
			$tags = array
			(
				'artist' => array($media['copyright']),
				'title' => array($media['title']),
				'album' => array($media['container'])
			);
			
			$date = strtotime($media['date']);
			
			if ($date !== FALSE)
			{
				$tags['year'][] = (String) date('Y', $date);
			}

			$this->write_ID3($media['path'], $tags);
		}	
		
		if ( $this->id !== false )
		{
			$this->success(lang('ionize_message_media_data_saved'));
		}
		else
		{
			$this->success(lang('ionize_message_media_data_not_saved'));
		}
	}


	function get_thumb($id)
	{
		// Header data
		$mime = self::$DEFAULT_TYPE;

		// Pictures data from database
		$picture = $this->media_model->get($id);

		// Path to the picture
		if ($picture && file_exists($picture_path = DOCPATH.$picture['path']))
		{
			$thumb_path = DOCPATH . Settings::get('files_path'). str_replace(Settings::get('files_path').'/', '/.thumbs/', $picture['base_path']);
			
			$return_thumb_path = $thumb_path.$picture['file_name'];

			// If no thumb, try to create it
			if ( ! file_exists($thumb_path.$picture['file_name']))
			{
				$settings = array(
					'size' => (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : 120,
					'unsharp' => '0'
				);
				
				try
				{
					$return_thumb_path = $this->medias->create_thumb(DOCPATH . $picture['path'], $thumb_path.$picture['file_name'], $settings);
				}
				catch(Exception $e)
				{
					$return_thumb_path = FCPATH.'themes/'.Settings::get('theme_admin').'/images/icon_48_no_folder_rights.png';
				}
			}
			
			$mime = get_mime_by_extension($return_thumb_path);
			$content = read_file($return_thumb_path);
			
			self::push_thumb($content, $mime, 0);
		}
		// No source file
		else
		{
			$mime = 'image/png';
			$content = read_file(FCPATH.'themes/'.Settings::get('theme_admin').'/images/icon_48_no_source_picture.png');
			self::push_thumb($content, $mime, 0);
		}
	}


	function push_thumb($content, $mime = NULL, $expire = NULL)
	{
        if ($expire === NULL) $expire = self::$DEFAULT_EXPIRE;
        $expires = gmdate("D, d M Y H:i:s", time() + $expire) . " GMT";
        $size = strlen($content);

        header("Content-Type: $mime");
        header("Expires: $expires");
        header("Cache-Control: max-age=$expire");
/*
        header("Pragma: !invalid");
*/
        header("Content-Length: $size");

        echo $content;
        
        die();
	}

	// ------------------------------------------------------------------------


	/** 
	 * Init the thumbs for one picture
	 * @access	private
	 *
	 * @param	string	Picture ID
	 *
	 * Thumb settings : Array(
	 *						max_width : max width
	 *						square : 	is the thumbs cropped to square (true, false)
	 *						unsharp : 	unsharp filter on thumb (true, false)
	 *					 )
	 */
	function _init_thumbs($id)
	{
		// Pictures data from database
		$picture = $this->media_model->get($id);

		// Thumbs settings
		$this->base_model->set_table('setting');
		$thumbs = $this->base_model->get_list(array('name like' => 'thumb_%'));

		// Create other thumbs
		if ( ! empty($thumbs))
		{
			$picture_path = DOCPATH . $picture['path'];
			// Check if source file exists
			if ( ! is_file($picture_path) )
			{
				throw new Exception( lang('ionize_exception_no_source_file').' : '. $picture['file_name'] );						
			}
	
			// Create thumbs for each thumbs
			foreach($thumbs as $thumb)
			{
				// Thumb settings : from DB.
				$settings = explode(",", $thumb['content']);
				$setting = array(
								'dir' =>		$thumb['name'],
//								'sizeref' => 	$settings[0],
								'size' => 		$settings[1],
								'square' => 	$settings[2],
								'unsharp' => 	$settings[3]
							);
				
				// Thumbnail creation
				$thumb_path = DOCPATH . $picture['base_path'].$setting['dir']."/".$picture['file_name'];
	
				try
				{
					$this->medias->create_thumb($picture_path, $thumb_path, $setting);
				}
				catch(Exception $e)
				{
					throw new Exception($e->getMessage());
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Create one thumbnail
	 *
	 * @param	string	Full path to the source image, including image file name
	 * @param	string	Full path to the destination image, including image file name
	 * @param	array	Thumb settings array
	 *
	 */
/*
	function _create_thumbnail($source_image, $new_image, $settings)
	{
		// Get images data : sizes
		$imgData = array();
		
		// Max memory needed
		$max_size = 0;
		
		// Get the image dimensions
		$dim = $this->get_image_dimensions($source_image);
		$imgData['image_width'] = $dim['width'];
		$imgData['image_height'] = $dim['height'];
		
		// CI Image_lib config array
		$config['source_image'] =	$source_image;
		$config['new_image'] =		$new_image;
		$config['quality'] =		'90';
		$config['maintain_ratio'] = true;
		$config['unsharpmask'] =	$settings['unsharp'];
		
		$config2 = array();

		// Non square picture
		if( empty($settings['square']) || $settings['square'] != 'true' )
		{
			// Master dim as choosen size ref
			$config['master_dim'] =	$settings['sizeref'];
		}
		// Square picture
		else 
		{
			if ($imgData['image_width'] >= $imgData['image_height']) 
				$config['master_dim'] =	$config2['master_dim'] = 'height';
			else 
				$config['master_dim'] =	$config2['master_dim'] = 'width';
				
		}
		
		// Delete existing thumb
		if (is_file($new_image))
		{
			// Change the file rights
			if ( ! @chmod($new_image, 0777))
			{
	//			throw new Exception(lang('ionize_exception_chmod') );
			}
			
			// Delete the old thumb file
			if ( ! @unlink($new_image))
			{
	//			throw new Exception(lang('ionize_exception_unlink') );
			}
		}
		
		// Resize only if image size greather than thumb wished size
		// If greather, copy the source to the thumb destination folder
		if ($imgData['image_'.$config['master_dim']] >= $settings['size'])
		{
			$config['width'] =	$config['height'] =	$settings['size']; 		// Resize on master_dim. Used to keep ratio.
		
			$this->image_lib->clear();
			$this->image_lib->initialize($config);

			// Thumbnail creation
			if ( ! $this->image_lib->resize() )
			{
				throw new Exception(lang('ionize_exception_image_resize') );
			} 
			
			// Crop to square if necessary
			if(!empty($settings['square']) && $settings['square'] == 'true') 
			{
				// CI Image_lib config array
				$config2['source_image'] =	$this->image_lib->full_dst_path;
				
				// Calculate x and y axis
				$config2['x_axis'] = $config2['y_axis'] = '0';
				
				// Get image dimension before crop
				$dim = $this->get_image_dimensions($this->image_lib->full_dst_path);

				// Center the scare
				if ($dim['width'] > $dim['height'])
				{
					$config2['x_axis'] = ($dim['width'] - $config['width']) / 2;
				}
				else
				{
					$config2['y_axis'] = ($dim['height'] - $config['height']) / 2;
				}

				$config2['new_image'] =		'';
				$config2['unsharpmask'] =	false;
				$config2['maintain_ratio'] = false;
				$config2['height'] =		$settings['size'];
				$config2['width'] =			$settings['size'];
				$this->image_lib->clear();
				$this->image_lib->initialize($config2);
				
				if ( true !== $this->image_lib->crop() )
				{
					throw new Exception(lang('ionize_exception_image_crop') );
				}
			}
			
			// Change the mod of the generated file
			if ( ! @chmod($new_image, 0777))
			{
//				throw new Exception(lang('ionize_exception_chmod') . ' : ' . $new_image);
			}
		}
		else 
		{
			if ( ! @copy($source_image, $new_image) )
			{
				throw new Exception(lang('ionize_exception_copy') . ' : ' . $source_image);
			}
		}
	}
*/


	// ------------------------------------------------------------------------


	/**
	 * Get the dimensions of a picture
	 *
	 * @param	string	Complete path to the image file
	 * @return	array	Array of dimension.
	 *					'width' : contains the width
	 *					'height' : contains the height
	 *
	 */
	private function get_image_dimensions($path)
	{
		$dim = array();
		
		if (function_exists('getimagesize'))
		{
			$d = @getimagesize($path);
			
			if ($d !== FALSE)
			{
				$dim['width']	= $d['0'];
				$dim['height']	= $d['1'];
				return $dim;
			}
			else
			{
				throw new Exception(lang('ionize_exception_getimagesize_get'). ' : '.$path);
			}
		}
		else
		{
			throw new Exception(lang('ionize_exception_getimagesize'));
		}
	}
	

	// ------------------------------------------------------------------------

	private function get_ID3($path)
	{
		$tags = array_fill_keys(self::$MP3_ID3, '');
	
		if ( is_file(DOCPATH.$path) )
		{
			require_once(APPPATH.'libraries/getid3/getid3.php');

			// Initialize getID3 engine
			$getID3 = new getID3;

			// Analyze file and store returned data in $ThisFileInfo
			$id3 = $getID3->analyze(DOCPATH.$path);

			foreach(self::$MP3_ID3 as $index)
			{
				$tags[$index] = ( ! empty($id3['tags_html']['id3v2'][$index][0])) ? $id3['tags_html']['id3v2'][$index][0] : '';
			}
		}
		
		return $tags;
	}
	
	private function set_ID3(&$data, $tags)
	{
		// Displayed datas
		$data['copyright'] = $tags['artist'];
		$data['date'] = date('Y.m.d H:m:s', strtotime($tags['year']));

		$data['container'] = $tags['album'];
		
		// Title
		foreach(Settings::get_languages() as $lang)
		{
			$data[$lang['lang']]['title'] = $tags['title'];
			$data[$lang['lang']]['alt'] = $data[$lang['lang']]['description'] = $tags['artist'] . ' - ' . $tags['album'] . ' : ' . $tags['title'];
		}
	}
	
	private function write_ID3($path, $tags)
	{
		if ( is_file(DOCPATH.$path) )
		{
			require_once(APPPATH.'libraries/getid3/getid3.php');

			$getID3 = new getID3;
			$getID3->setOption(array('encoding'=>'UTF-8'));
			getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, true);


			$tagwriter = new getid3_writetags;
			$tagwriter->filename = $path;
			$tagwriter->tag_encoding = 'UTF-8';
			$tagwriter->tagformats = array('id3v1', 'id3v2.3');
			$tagwriter->overwrite_tags = TRUE;
			$tagwriter->tag_data = $tags;
			
			$tagwriter->WriteTags();

			if (!empty($tagwriter->warnings))
			{
				return FALSE;
			}
			return TRUE;
		}
		return FALSE;		
	}
	
	
	private function is($path, $ext)
	{
		if (pathinfo(DOCPATH.$path, PATHINFO_EXTENSION) == $ext)
			return TRUE;
			
		return FALSE;
	}
	
}


/* End of file media.php */
/* Location: ./application/controllers/admin/media.php */
