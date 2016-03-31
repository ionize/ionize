<?php

$config['module']['sfs'] = array
(
	'name' => "Stop Form Spam",
	'description' => "Protect your forms against spam",
	'author' => "Ionize Dev Team",
	'version' => "1.1",
	"release_date" => "2016-03-29",

	'has_admin' => TRUE,

	'js_main_class' => 'SFS_MODULE',

	// Module's config items
	'api_server' => 'http://www.stopforumspam.com',

	'api_key' => 'if4yrgwoqkd9ja',

	// Send the email and IP of one spambot again to stopforumspam.com
	'track' => '1',

	// Input / Textarea which contains the user's send message
	'evidence_input' => 'message',

	// Input which contains the username (needed for stopforumspam submission)
	'username_input' => 'name',

	// Registered events
	'events' => 'Form.contact.check,Form.newsletter.check',
);

return $config['module']['sfs'];

