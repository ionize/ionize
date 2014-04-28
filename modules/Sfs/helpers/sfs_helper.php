<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('get_sfs_declare_spam_url'))
{
	function get_sfs_declare_spam_url()
	{
		$url = NULL;

		$config = Modules()->get_module_config('Sfs');

		if ( ! empty($config))
		{
			$ci =& get_instance();
			$ui = $config['username_input'];

			// Username
			$username = '';

			if ( ! empty($ui))
			{
				$username = array();
				$arr = explode(',', $ui);

				foreach($arr as $key)
					if ($ci->input->post($key)) $username[] = $ci->input->post($key);

				$username = '&username=' . urlencode(implode(' ', $username));
			}

			// Email
			$email = '';
			if ($ci->input->post('email'))
				$email = '&email=' . $ci->input->post('email');

			// Evidence
			$evidence = '';
			if ($ci->input->post($config['evidence_input']))
				$evidence = '&evidence=' . urlencode($ci->input->post($config['evidence_input']));

			// IP
			$ip = '&ip_addr=' . $ci->input->ip_address();

			$base_href = $config['api_server'] . '/add?api_key=' . $config['api_key'] . $username . $email . $ip ;

			$complete_href = $base_href . $evidence;

			$url = '<a href="' . $complete_href . '">' .$base_href. '</a>';
		}

		return $url;
	}
}