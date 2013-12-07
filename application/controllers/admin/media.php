<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Media Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
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
        $this->load->model(
            array(
                'media_model',
                'extend_field_model'
            ), '', TRUE);

		// Librairies
		$this->load->library('medias');
		$this->load->library('image_lib');

		// Remove protection if the filemanager is called on upload
		// Purpose : Allow upload.
		// Security check is done in the method.
		if ($this->uri->segment(3) == 'filemanager' && $this->uri->segment(4) == 'upload')
		{
			User()->disable_folder_protection();
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Display the filemanager
	 * Used for standalone display in the "Content->Medias" panel of Ionize
	 *
	 * @param null $mode
	 */
	public function get_media_manager($mode = NULL)
	{
		$this->output('filemanager/filemanager');
	}


	// ------------------------------------------------------------------------


	/**
	 * Mootools FileManager loader
	 * Equiv. to the "manager.php" file in mootools-filemanager Demo folder.
	 *
	 * @param null $event
	 * @param bool $resize
	 * @param bool $uploadAuthData
	 */
	public function filemanager($event = NULL, $resize = FALSE, $uploadAuthData = FALSE)
	{
		// Get allowed mimes
		$allowed_mimes = implode(',', Settings::get_allowed_mimes());

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
			// 'DestroyIsAuthorized_cb' => array($this, 'can_filemanager_destroy')
			// 'hashMethod' => config_item('files_path_hash_method'),
		);

		$this->load->library('Filemanager', $params);

		// Fires the Event called by FileManager.js
		// The answer of this called id a JSON object
		// If no event is givven, it will call the "display" event
		if ($event != 'upload')
		{
			$this->Filemanager->fireEvent( ! is_null($event) ? $event : NULL);
		}
		else
		{
			$this->Filemanager->fireEvent($event);



			// Flash mode (Multiple files) : PHPSESSID is send
			/*
			if ( ! empty($_POST['PHPSESSID']))
				$session_data = $this->session->switchSession($_POST['PHPSESSID']);

			// Get the original session tokken
			$tokken = $this->session->userdata('uploadTokken');

			// Get the sent tokken & compare
			$sent_tokken = ( ! empty($_POST['uploadTokken'])) ? $_POST['uploadTokken'] : '';

			// Only upload if tokkens match
			if ($tokken == $sent_tokken)
			{
				$this->Filemanager->fireEvent($event);
			}
			else
			{
				$this->xhr_output(array(
					'status' => 0,
					'error' => lang('ionize_session_expired')
				));
			}
			*/
		}
		
		die();
	}


	// ------------------------------------------------------------------------


	/**
	 * Filemanager Destroy callback
	 *
	 * @param      $fm
	 * @param      $action
	 * @param null $path
	 *
	 * @return bool
	 */
	public static function can_filemanager_destroy($fm, $action, $path=NULL)
	{
		return TRUE;
		// return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns a tokken based on the current session ID + the encryption key :
	 * md5($this->session->userdata('session_id') . config_item('encryption_key'))
	 * If the user isn't connected, returns an empty string
	 *
	 * Called through XHR when the filemanager is opened.
	 * The tokken is send with the uploaded data and checked before anything is uploaded
	 *
	 */
	public function get_tokken()
	{
		if (User()->is('editors'))
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
				$view = $this->load->view('media/picture/list', $data, TRUE);
			else
				$view = $this->load->view('media/list', $data, TRUE);
			
			// Addon data to the answer			
			$output_data = array('type' => $type, 'content' => $view);
			
			// Answer send
			$this->success(NULL, $output_data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_media
	 *
	 */
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
		
		$this->output('media/picture/crop');
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 */
	public function crop()
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
	 * @param $type			Media type. Can be 'picture', 'music', 'video', 'file'
	 * @param $parent		parent. Example : 'article', 'page'
	 * @param $id_parent	Parent ID
	 */
	public function add_media($type, $parent, $id_parent)
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
		$media = $this->media_model->get($id);
		$event_data = array(
			'element' => $parent,
			'id_element' => $id_parent,
			'media' => $media,
		);

        if($media['type'] == 'picture')
        {
            $thumb_path = DOCPATH . Settings::get('files_path'). str_replace(Settings::get('files_path'), '/.thumbs', $media['base_path']);

            // If no thumb, try to create it
            if ( ! file_exists($thumb_path.$media['file_name']))
            {
                $settings = array(
                    'size' => (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : 120,
                    'unsharp' => '0'
                );

                $this->medias->create_thumb(DOCPATH . $media['path'], $thumb_path.$media['file_name'], $settings);

            }
        }

		// Parent linking
		if (!$this->media_model->attach_media($type, $parent, $id_parent, $id))
		{
			// Event
			Event::fire('Media.link.error', $event_data);

			$this->error(lang('ionize_message_media_already_attached'));
		}
		else 
		{
			// Event
			Event::fire('Media.link.success', $event_data);

			// Addon answer data
			$output_data = array('type' => $type);

			// Delete thumbs
			if($type == 'picture')
			{
				$this->medias->delete_thumbs($media);
			}

			$this->success(lang('ionize_message_media_attached'), $output_data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 */
	public function add_external_media()
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
			// Get the array of information concerning this service
			$provider = '';
			$info = $this->get_service_info($path);
			if (!is_null($info))
			{
				$path = $info['path'];
				$provider = $info['provider'];
			}

			// DB Insert
			$id = $this->media_model->insert_media($type, $path, $provider);
			$media = $this->media_model->get($id);

			// Event data
			$event_data = array(
				'element' => $parent,
				'id_element' => $id_parent,
				'media' => $media
			);

			// Parent linking
			if (!$this->media_model->attach_media($type, $parent, $id_parent, $id)) 
			{
				// Event
				Event::fire('Media.link.external.error', $event_data);

				$this->error(lang('ionize_message_media_already_attached'));
			}
			else 
			{
				// Event
				Event::fire('Media.link.external.success', $event_data);

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
	 *
	 * @param	string	parent type
	 * @param	string	parent ID
	 *
	 * @TODO : Improve the errors management
	 *
	 */
	public function init_thumbs_for_parent($parent, $id_parent)
	{
		$pictures =	$this->media_model->get_list($parent, $id_parent, 'picture');

		foreach($pictures as $picture)
		{
			try
			{
				$this->medias->delete_thumbs($picture);
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
	public function init_thumbs($id)
	{
		try
		{
			// Thumbs init
			$picture = $this->media_model->get($id);
			$this->medias->delete_thumbs($picture);

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
	 * Empty the .thumbs folder
	 *
	 */
	public function delete_all_thumbs()
	{
		$thumb_path = DOCPATH . Settings::get('files_path'). '/.thumbs/';
		delete_files($thumb_path, TRUE);

		$result = array(
			'title' => lang('ionize_title_delete_thumbs'),
			'status' => 'success',
			'message' => lang('ionize_message_thumbs_deleted'),
		);

		$this->xhr_output($result);

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
	public function detach_media($type, $parent, $id_parent, $id_media)
	{
		if ($parent !== FALSE && $id_parent !== FALSE && $id_media !== FALSE)
		{			
			// Clear the cache
			Cache()->clear_cache();

			// Event data
			$media = $this->media_model->get($id_media);
			$event_data = array(
				'element' => $parent,
				'id_element' => $id_parent,
				'media' => $media,
			);

			// Delete succeed : Message to user
			if ($this->media_model->delete_joined_key('media', $id_media, $parent, $id_parent) > 0)
			{
				// Event
				Event::fire('Media.unlink.success', $event_data);

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
				// Event
				Event::fire('Media.unlink.error', $event_data);

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
	public function detach_media_by_type($parent, $id_parent, $type = FALSE)
	{
		if ($parent !== FALSE && $id_parent !== FALSE && $type !== FALSE)
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
	public function save_ordering($parent, $id_parent)
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
	 * @param int		Media ID
	 * @param string	parent type ('page', 'article')
	 * @param int		Parent ID (context in which the media is linked)
	 *
	 */
	public function edit($type, $id, $parent, $id_parent)
	{
		$this->media_model->feed_template($id, $this->template);
		$this->media_model->feed_lang_template($id, $this->template);

		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
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
		/*
		$location = array_shift(explode('/', $this->template['path']));
		if ($location == 'http:')
			$this->template['is_external'] = TRUE;
		else
			$this->template['is_external'] = FALSE;
		*/
		// $this->template['location'] = $location;
		
		// context data
		$this->template['context_data'] = $this->media_model->get_context_data($id, $parent, $id_parent);

		// Modules addons
		$this->load_modules_addons($this->template);

		$this->output('media/edit');
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Saves one media metadata
	 *
	 */
	public function save()
	{
		// Clear the cache
		Cache()->clear_cache();
		
		// Get old values
		$id_media = $this->input->post('id_media'); 
		$old_media_data = $this->media_model->get($id_media);

		// Standard data;
		$data = array();
		
		// Standard fields
		$fields = $this->db->list_fields('media');

		foreach ($fields as $field)
		{
			if ( $this->input->post($field) !== FALSE)
			{
				$data[$field] = htmlentities($this->input->post($field), ENT_QUOTES, 'utf-8');
			}
		}

		// Lang data
		$lang_data = array();

		$fields = $this->db->list_fields('media_lang');
		
		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				if ( $this->input->post($field.'_'.$language['lang']) !== FALSE)
					$lang_data[$language['lang']][$field] = htmlentities($this->input->post($field.'_'.$language['lang']), ENT_QUOTES, 'utf-8');
			}
		}

		// Event
		$event_data = array(
			'base' => $data,
			'lang' => $lang_data,
		);
		$event_received = Event::fire('Media.save.before', $event_data);
		$event_received = array_pop($event_received);
		if ( ! empty($event_received['base']) && !empty($event_received['lang']))
		{
			$data = $event_received['base'];
			$lang_data = $event_received['lang'];
		}

		// Database save
		$this->id = $this->media_model->save($data, $lang_data);

		// Event
		$event_data = array(
			'base' => $data,
			'lang' => $lang_data,
		);
		Event::fire('Media.save.success', $event_data);

		// Save extend fields data
		$this->extend_field_model->save_data('media', $this->id, $_POST);

		// Save parent context data
		$this->media_model->save_context_data($_POST);

		$media = $this->media_model->get($this->id, Settings::get_lang('default'));

		// Save ID3 to file if MP3
		if ( $this->is($media['path'], 'mp3') )
		{
			$tags = array(
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
		
		// Delete picture thumbnails
		if($media['type'] == 'picture')
			$this->medias->delete_thumbs($media);

		if ( $this->id !== FALSE )
		{
			// Success Message
			$this->callback = array(
				array(
					'fn' => 'mediaManager.loadMediaList',
					'args' => $media['type']
				),
				array(
					'fn' => 'ION.notification',
					'args' => array('success', lang('ionize_message_media_data_saved'))
				)
			);
		}
		else
		{
			Event::fire('Media.save.error');

			// Error Message
			$this->callback[] = array
			(
				'fn' => 'ION.notification',
				'args' => array('error', lang('ionize_message_media_data_not_saved'))
			);
		}
		$this->response();
	}


	// ------------------------------------------------------------------------


	public function get_thumb($id)
	{
		// Pictures data from database
		$picture = $id ? $this->media_model->get($id) : FALSE;

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
					$return_thumb_path = FCPATH.'themes/'.Settings::get('theme_admin').'/styles/'.Settings::get('backend_ui_style').'/images/icon_48_no_folder_rights.png';
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
			$content = read_file(FCPATH.'themes/'.Settings::get('theme_admin').'/styles/'.Settings::get('backend_ui_style').'/images/icon_48_no_source_picture.png');
			self::push_thumb($content, $mime, 0);
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


	// ------------------------------------------------------------------------


	/**
	 * Return service info array about the external service
	 *
	 * Detects :
	 * - Youtube
	 * - Vimeo
	 * - Dailymotion
	 *
	 * @param $path
	 *
	 * @return array|null
	 */
	private function get_service_info($path)
	{
		$file_name = substr( strrchr($path, '/') ,1 );
		$base_path = str_replace($file_name, '', $path);

		// Youtube
		if (
			substr($file_name, 0, 6) == 'watch?'
			OR substr($base_path,0, 22) == 'http://www.youtube.com'
		)
		{
			$file_name = str_replace('watch?', '', $file_name);
			$segments = explode('&', $file_name);

			foreach($segments as $seg)
			{
				if (substr($seg,0,2) == 'v=')
				{
					$file_name = substr($seg, 2);
					break;
				}
			}

			$service = array(
				'path' => 'http://www.youtube.com/embed/' . $file_name,
				'provider' => 'youtube'
			);
			return $service;
		}

		// Vimeo
		if ($base_path == 'http://vimeo.com/')
		{
			$service = array(
				'path' => 'http://player.vimeo.com/video/' . $file_name,
				'provider' => 'vimeo'
			);
			return $service;
		}

		// Dailymotion
		if ($base_path == 'http://www.dailymotion.com/video/')
		{
			$file_name = substr($file_name,0, strpos($file_name, '_'));
			$service = array(
				'path' => 'http://www.dailymotion.com/embed/video/' .$file_name,
				'provider' => 'dailymotion'
			);
			return $service;
		}

		return NULL;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the dimensions of a picture
	 *
	 * @param	string	Complete path to the image file
	 * @return	array	Array of dimension.
	 *					'width' : contains the width
	 *					'height' : contains the height
	 * @throws Exception
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


	/**
	 * @param $path
	 *
	 * @return array
	 */
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


	// ------------------------------------------------------------------------


	/**
	 * @param $data
	 * @param $tags
	 *
	 */
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


	// ------------------------------------------------------------------------

	/**
	 * @param $path
	 * @param $tags
	 *
	 * @return bool
	 */
	private function write_ID3($path, $tags)
	{
		if ( is_file(DOCPATH.$path) )
		{
			require_once(APPPATH.'libraries/getid3/getid3.php');

			$getID3 = new getID3;
			$getID3->setOption(array('encoding'=>'UTF-8'));
			getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.php', __FILE__, TRUE);


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

	// ------------------------------------------------------------------------


	/**
	 * @param $path
	 * @param $ext
	 *
	 * @return bool
	 */
	private function is($path, $ext)
	{
		if (pathinfo(DOCPATH.$path, PATHINFO_EXTENSION) == $ext)
			return TRUE;
			
		return FALSE;
	}
}
