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
 * Contact TagManager
 *
 * This tag manager is one example of user defined tag managers.
 * It belongs to the user theme
 *
 */
class TagManager_Contact extends TagManager
{
	/**
	 * Processes the form POST data.
	 *
	 * @param FTL_Binding		'init' tag (not the user one because this method is run before any tag parsing)
	 *							This tag is supposed to be only used to send Emails.
	 * 							With this tag, Emails views have access to the global tags, but not to any other
	 * 							object tag.
	 *
	 * @return void
	 *
	 */
	public static function process_data(FTL_Binding $tag)
	{
		// Name of the form : Must be send to identify the form.
		$form_name = self::$ci->input->post('form');

		// Because Form are processed before any tag rendering, we have to run the validation
		if (TagManager_Form::validate($form_name))
		{
			//
			// ... Here you do what you want with the data ...
			//
			// For the example, we will send one email to the address the user gave in the form
			//

			// Posted data
			// To see the posted array, uncomment trace($posted)
			// If you prefer to see these data through one log file,
			// uncomment log_message(...) and be sure /application/config/config.php contains :
			// $config['log_threshold'] = 1;
			// The log files are located in : /application/logs/log-YYYY-MM-DD.php
			// We prefer to log our 'dev' data as 'error' to not see the all CodeIgniter 'debug' messages.

			$post = self::$ci->input->post();
			// trace($posted);
			// log_message('error', print_r($posted, TRUE));

			// SFS : Fires the event declared in Stop Form Spam module config
			// Do we go further in the form processing ? Yes by default.
			$go_further = TRUE;
			$results = Event::fire('Form.contact.check', $post);

			if (is_array($results))
			{
				foreach($results as $result)
					if ( ! $result)
						$go_further = FALSE;
			}

			if ($go_further)
			{
				// Send the posted data to the Email library and send the Email
				// as defined in /themes/your_theme/config/forms.php
				TagManager_Email::send_form_emails($tag, $form_name, $post);

				// Add one custom Success message
				// Get the messages key defined in : /themes/your_theme/config/forms.php
				// You can also set directly one lang translated key
				$message = TagManager_Form::get_form_message('success');
				TagManager_Form::set_additional_success($form_name, $message);

				// Alternative : Set the message by using directly one lang translated key :
				// TagManager_Form::set_additional_success($form_name, lang('form_message_success'));
			}

			// Use of the 'redirect' option of the form config.
			// If no redirect after processing, the form data can be send again if the user refreshes the page
			// To avoid that, we use the redirection directive as set in the config file:
			// /themes/your_theme/config/forms.php
			$redirect = TagManager_Form::get_form_redirect();
			if ($redirect !== FALSE) redirect($redirect);
		}
		/*
		// Normally, nothing should be done here, because the validation process refill the form
		// and doesn't redirect, so the user's filled in data can be used to fill the form again.
		// Remember : If you redirect here, the form refill will not be done, as the data are lost
		// (no access to the posted data anymore after redirection)
		else
		{
			// ... Do something here ...
		}
		*/
	}
}