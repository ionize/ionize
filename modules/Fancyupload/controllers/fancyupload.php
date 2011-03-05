<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.93
 */

// ------------------------------------------------------------------------

/**
 * FancyUpload Module Controller
 *
 * @package		Ionize
 * @subpackage	Modules
 * @category	Upload module
 * @author		Ionize Dev Team
 *
 */


class Fancyupload extends Base_Controller 
{
	var $mimes			= array();


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

	}


	// ------------------------------------------------------------------------

	/**
	 * No access to index()
	 *
	 */
	function index()
	{
		echo('FancyUpload');
	}
	

	// ------------------------------------------------------------------------


	function upload()
	{
		// Upload folder
		$upload_path = config_item('fancyupload_folder');
		
		// Upload result
		$return = array();

		/**
		 * Get the connected users data
		 * These data are encrypted through the CI Encryption library
		 *
		 */
		if ( empty($this->encrypt))
			$this->load->library('encrypt');

		$username = $this->encrypt->decode(rawurldecode($_POST['usrn']));
		$email = $this->encrypt->decode(rawurldecode($_POST['usre']));
		
		// Try to get the user
		$user = Connect()->get_user($username);
		
		// If we have an user and an upload path
		if ($user && $upload_path != false && $upload_path !='')
		{
			// Users group
			$usergroup = Connect()->get_group($user['id_group']);
			
			// Fancy upload upload allowed group
			$fancygroup = Connect()->get_group(config_item('fancyupload_group'));
			
			/**
			 * If the users email and the users group has the right to upload,
			 * we can start uploading
			 *
			 */
			if ($user['email'] == $email && $usergroup['level'] >= $fancygroup['level'])
			{
				// Do we get a file ?
				if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']))
				{
					$this->error(lang('module_fancyupload_invalid_upload'));
				}
				else
				{
					// Before move : Clean the file name
					// and add user email to file name if defined

					$new_file_name = $this->_prep_filename($this->clean_file_name($_FILES['Filedata']['name']));
									
					if (config_item('fancyupload_file_prefix') == '1')
					{
						$new_file_name = $email . '_' . $new_file_name;
					}				
				
					if ( ! @move_uploaded_file($_FILES['Filedata']['tmp_name'], config_item('fancyupload_folder') . $new_file_name))
					{
						$return['status'] = '0';
					}
					else
					{
 						$return['status'] = '1';
 						
 						// Send an alert mail to the admin if the option is set.
 						if (config_item('fancyupload_send_alert') == '1' && config_item('fancyupload_email') != '')
 						{
 							$to = config_item('fancyupload_email') ;
							
							$subject_admin = lang('fancyupload_alert_mail_subject') . ' : ' . $user['screen_name'];

							// Email preparation
							$data = array(
								'username' => 		$user['username'],
								'screen_name' =>	$user['screen_name'],
								'email' =>			$user['email'],
								'filename' =>		$new_file_name,
								'upload_date' =>	date('d.m.Y H:i:s'),
								'upload_folder' =>	config_item('fancyupload_folder')
							);
							
							// Email to Admin
							$message = $this->load->view('emails/fancyupload_upload_admin_alert', $data, true);
							
							$this->send_mail($to, $message, $subject_admin);
 						}
					}
					$return['src'] = config_item('fancyupload_folder') . $new_file_name;
				
				}
			}
			// The user mail is not corresponding to the saved mail or the user group level < authorized group : 
			// Not allowed to upload
			else
			{
				$this->error(lang('module_fancyupload_no_right'));
				
			}
		}
		
		echo json_encode($return);
	}


	/**
	 * Return a JSON error message and stop the script
	 * 
	 * @param	String		Error message
	 *
	 */ 
	function error($message)
	{
		$return = array(
			'status' => '0',
			'error' => $error
		);
		echo $return;
		
		die();
	}


	// ------------------------------------------------------------------------


	/**
	 * Clean the file name for security
	 * 
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function clean_file_name($filename)
	{
		$bad = array(
						"<!--",
						"-->",
						"'",
						"<",
						">",
						'"',
						'&',
						'$',
						'=',
						';',
						'?',
						'/',
						"%20",
						"%22",
						"%3c",		// <
						"%253c", 	// <
						"%3e", 		// >
						"%0e", 		// >
						"%28", 		// (
						"%29", 		// )
						"%2528", 	// (
						"%26", 		// &
						"%24", 		// $
						"%3f", 		// ?
						"%3b", 		// ;
						"%3d"		// =
					);
					
		$filename = str_replace($bad, '', $filename);


		return stripslashes($filename);
	}
	
	
	// --------------------------------------------------------------------

	
	/**
	 * Prep Filename
	 * Copied from CI Upload lib as this lib is a Upload lib private one.
	 *
	 * Prevents possible script execution from Apache's handling of files multiple extensions
	 * http://httpd.apache.org/docs/1.3/mod/mod_mime.html#multipleext
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 *
	 */
	function _prep_filename($filename)
	{
		if (strpos($filename, '.') === FALSE)
		{
			return $filename;
		}

		$parts		= explode('.', $filename);
		$ext		= array_pop($parts);
		$filename	= array_shift($parts);

		foreach ($parts as $part)
		{
			if ($this->mimes_types(strtolower($part)) === FALSE)
			{
				$filename .= '.'.$part.'_';
			}
			else
			{
				$filename .= '.'.$part;
			}
		}

		// file name override, since the exact name is provided, no need to
		// run it through a $this->mimes check.
		/*
		if ($this->file_name != '')
		{
			$filename = $this->file_name;
		}
		*/

		$filename .= '.'.$ext;
		
		return $filename;
	}


	// --------------------------------------------------------------------

	
	/**
	 * List of Mime Types
	 * Copied from CI
	 *
	 * This is a list of mime types.  We use it to validate
	 * the "allowed types" set by the developer
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function mimes_types($mime)
	{
		global $mimes;
	
		if (count($this->mimes) == 0)
		{
			if (@require_once(APPPATH.'config/mimes'.EXT))
			{
				$this->mimes = $mimes;
				unset($mimes);
			}
		}
	
		return ( ! isset($this->mimes[$mime])) ? FALSE : $this->mimes[$mime];
	}


	// ------------------------------------------------------------------------


	/**
	 * Prepare and sends the mails.
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return bool	
	 */
	 private function send_mail($to, $message, $subject = 'Upload')
	 {
        $CI =& get_instance();
        
        if ( ! isset($CI->email))
			$CI->load->library('email');

        $CI->email->from(Settings::get('site_email'), Settings::get('site_title'));
		$CI->email->to($to);
        $CI->email->subject($subject);

		$CI->email->message($message);

		return $CI->email->send();
	 }

	
}

/* End of file fancyupload.php */
/* Location: ./modules/Fancyupload/controllers/fancyupload.php */