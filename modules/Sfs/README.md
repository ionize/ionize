Stop Forum Spam
=======================

Version : 1.0

Released on july 2013

### About Stop Form Spam module

Stop Form Spam helps fighting against form spam.
It uses the spam database from : http://www.stopforumspam.com/


### Authors

[Michel-Ange Kuntz](http://www.partikule.net)


### Installation

* Copy the folder "Sfs" into the "/modules" folder of your Ionize installation.
* In the ionize backend, go to : Modules > Administration
* Click on "install"
* Reload the backend panel
* Setup the module


### Setup

* In the setup field "Event", set one event, for example "Myform.register.check"
* Create one custom registration form, by editing /themes/your_theme/config/forms.php

```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['forms'] = array
(
	// Register Form
	'register' => array
	(
		// This will be the lib /themes/your_theme/libraries/Tagmanager/Register.php :
		'process' => 'TagManager_Register::process_data',
		/*
		 * ... 
		 * See documentation for form declaration details
		 *
		 */
		'fields' => array
		(
			'firstname' => array(
				'rules' => 'trim|required|xss_clean',
				'label' => 'form_label_firstname',
			),
			'email' => array(
				'rules' => 'trim|required|min_length[5]|valid_email|xss_clean',
				'label' => 'form_label_email',
			),
			'password' => array(
				'rules' => 'trim|required|min_length[4]|xss_clean',
				// 'rules' => 'trim|required|min_length[4]|matches[password2]|xss_clean',
				'label' => 'form_label_password',
			),
			'password2' => array(
				'rules' => 'trim|required|min_length[4]|xss_clean',
				'label' => 'form_label_password_confirmation',
				// If set to FALSE, ths field will not be saved to DB
				'save' => FALSE,
			),
		),
	),
);
```

* In the lib which processes your form data, fire the event you previously setup 
(in this case "Myform.register.check") :
To do that, create the file /themes/your_theme/libraries/Tagmanager/Register.php :

```php
<?php

class TagManager_Register extends TagManager
{
	public static function process_data(FTL_Binding $tag)
	{
		if (TagManager_Form::validate('register'))
		{
			$post = self::$ci->input->post();
			
			// Event::fire() returns an array (more than one method can be registered to the same event)
			$results = Event::fire('Myform.register.check', $post);

			$return = TRUE;

			if (is_array($results))
			{
				foreach($results as $result)
					if ( ! $result)
						$return = FALSE;
			}

			// The user can register
			if ( $return == TRUE)
			{
				// ... Register
			}
			else
			{
				// ... No registration
				// ... But don't tell him... :-)
			}
		}
	}
}
```

