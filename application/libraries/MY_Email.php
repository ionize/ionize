<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Email extends CI_Email
{
	public static $ci;

	/**
	 * Constructor - Sets Email Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		self::$ci =& get_instance();
	}

	/**
	 * Sends multiple emails from form
	 *
	 *
	 * @param       $form		Form settings array
	 * @param array $data		Posted data
	 */
	public function send_form_emails($form, $data = array())
	{
		$emails = ! empty($form['emails']) ? $form['emails'] : array();

		// Get the 'sender' email : Must be set in Ionize : Settings > Advanced settings > Email
		$website_email = Settings::get('site_email') ? Settings::get('site_email') : NULL;

		foreach($emails as $email_setting)
		{
			$email = $asked_email = $email_setting['email'];
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
				// Subject, adds the website title as swap text : diplayed in title if the %s key is used.
				$subject = lang($email_setting['subject'], Settings::get('site_title'));
				$data['subject'] = $subject;

				$this->clear();

				// Subject / From / To
				$this->subject($subject);
				$this->from($website_email, Settings::get("site_title"));
				$this->to($email);

				if ( ! is_null($reply_to))
					$this->reply_to($reply_to);

				// View & Message content
				$view_content = self::$ci->load->view($email_setting['view'], $data, TRUE);

				$this->message($view_content);

				// Send silently
				$result = @$this->send();

				if ( ! $result)
				{
					log_message('error', 'Error : MY_Email::send_form_emails() : Email was not sent. Possible reason : Email settings not complete, check your website sending email.');
				}
			}
			else
			{
				log_message('error', 'Error : MY_Email::send_form_emails() : Email not found : ' . $asked_email . '. Set it in the ionize backend !');
			}
		}
	}
}
