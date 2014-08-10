<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.9
 *
 */


/**
 * Email TagManager
 *
 */
class TagManager_Email extends TagManager
{
	public static $tag_definitions = array();

	/**
	 * Sends Emails as defined in the forms.php config file.
	 * Important : This method receives the "form" tag
	 *
	 * @param FTL_Binding	Form tag
	 * @param string
	 * @param array       	Array of data send to the Email view.
	 * 						Each key of this array will be available in the view by :
	 * 						<ion:data:key />
	 *
	 * 						The passed array should look like this :
	 * 						array(
	 * 							'email' => 'user email',		// Email of the user (POST, DB...)
	 * 							'key1' => 'value1,				// Makes <ion:data:key1 /> available in the Email view
	 * 						);
	 *
	 */
	public static function send_form_emails(FTL_Binding $tag, $form_name, $data = array())
	{
		// Set the 'data' tag from the received data array
		self::$context->define_tag('data', array(__CLASS__, 'tag_expand'));

		foreach($data as $key=>$value)
			if ( ! is_array($value) && ! is_object($value))
				self::$context->define_tag('data:'.$key, array(__CLASS__, 'tag_simple_value'));

		// Get all declared emails configuration data from forms.php config file
		$emails = TagManager_Form::get_form_emails($form_name);

		// Get the 'sender' email : Must be set in Ionize : Settings > Advanced settings > Email
		$website_email = Settings::get('site_email') ? Settings::get('site_email') : NULL;

		// Send all defined emails
		foreach($emails as $email_setting)
		{
			$email = $email_setting['email'];
			$reply_to = isset($email_setting['reply_to']) ? $email_setting['reply_to'] : NULL;

			// Get potential website / form email
			switch($email)
			{
				case 'site':
					$email = (Settings::get('site_email') != '') ? Settings::get('site_email') : NULL;
					break;

				case 'form':
					$email = isset($data['email']) ? $data['email'] : self::$ci->input->post('email');
					break;

                case ($email == 'contact' || $email == 'technical' || $email == 'info'):
                    $email = (Settings::get('email_'.$email) != '') ? Settings::get('email_'.$email) : NULL;
                    break;

				default:
                    $email = $email;
                    $_email = explode('::', $email);
                    if( ! empty($_email[1]) )
                        $email = self::$ci->input->post($_email[1]);
					break;
			}

			if ( ! is_null($reply_to))
			{
				switch($reply_to)
				{
					case 'site':
						$reply_to = (Settings::get('site_email') != '') ? Settings::get('site_email') : NULL;
						break;

					case 'form':
						$reply_to = isset($data['email']) ? $data['email'] : self::$ci->input->post('email');
						break;

					default:
						$reply_to = (Settings::get('email_'.$email) != '') ? Settings::get('email_'.$email) : NULL;
						break;
				}
			}

			// Send the email
			if ( $email )
			{
				// Subject, adds the website title as swap text : displayed in title if the %s key is used.
				$subject = lang($email_setting['subject'], Settings::get('site_title'));
				$data['subject'] = $subject;

				// Set the "data tag" array of data.
				$tag->set('data', $data);

				// Email Lib
				if ( ! isset(self::$ci->email)) self::$ci->load->library('email');
				self::$ci->email->clear();

				// Subject / From / To
				self::$ci->email->subject($subject);
				self::$ci->email->from($website_email, Settings::get("site_title"));
				self::$ci->email->to($email);

				if ( ! is_null($reply_to))
					self::$ci->email->reply_to($reply_to);

				// View & Message content
				$view_content = $tag->parse_as_standalone(self::$tag_prefix, Theme::load($email_setting['view']));

				self::$ci->email->message($view_content);

				// Send silently
				$result = @self::$ci->email->send();

				if ( ! $result)
				{
					log_message('error', 'Error : Tagmanager/Email->send_form_emails() : Email was not sent.');
				}
			}
			else
			{
				log_message('error', 'Error : Tagmanager/Email->send_form_emails() : Email not found');
			}
		}
	}
}