<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Stop Form Spam module : Stop Forum Spam Class
 *
 * @package 	SFS module
 * @author 		Partikule
 *
 */
class Sfs_Sfs
{
	protected static $ci;

	protected static $config;

	public function __construct($config = array())
	{
		// If the CI object is needed :
		self::$ci =& get_instance();

		self::$config = Modules()->get_module_config('Sfs');

		self::$config = array(
			'api_server' => ( ! empty(self::$config['api_server'])) ? self::$config['api_server'] : NULL,
			'api_key' => ( ! empty(self::$config['api_key'])) ? self::$config['api_key'] : NULL,
			'track' => ( ! empty(self::$config['track'])) ? self::$config['track'] : FALSE,
			'evidence_input' => ( ! empty(self::$config['evidence_input'])) ? self::$config['evidence_input'] : NULL,
			'username_input' => ( ! empty(self::$config['username_input'])) ? self::$config['username_input'] : NULL,
		);

		self::$ci->load->library('rest', array('server' => self::$config['api_server']));
	}


	/**
	 * Return TRUE if the check passed.
	 * FALSE if the user looks like a spammer (record in Stop Forum Spam DB)
	 *
	 * Doc : http://www.stopforumspam.com/usage
	 *
	 * @param $post
	 *
	 * @return string
	 *
	 */
	public function on_post_check_before($post)
	{
		$trusted = TRUE;

		$post['ip'] = self::$ci->input->ip_address();

		$params = 'email='.$post['email'].'&ip='.$post['ip'].'&f=serial';

		self::$ci->rest->initialize(array('server' => self::$config['api_server']));
		$response = self::$ci->rest->get('api', $params);

		if (is_string($response))
		{
			$response = unserialize($response);

			if ( ! empty($response['ip']['appears']) && intval($response['ip']['appears']) > 0)
				$trusted = FALSE;

			if ( ! empty($response['email']['appears']) && intval($response['email']['appears']) > 0)
				$trusted = FALSE;

			if ( ! $trusted && self::$config['track'] == TRUE && ! empty(self::$config['api_key']))
				self::submit($post);
		}

		return $trusted;
	}


	/**
	 * Submits one spammer data to Stop Forum Spam DB
	 *
	 * @param $post
	 *
	 */
	public function submit($post)
	{
		$username = $email = $ip = $evidence = '';

		if ( ! is_null(self::$config['username_input']))
		{
			$fields = explode(',', self::$config['username_input']);
			{
				foreach ($fields as $field)
				{
					$field = trim($field);
					if ( ! empty($post[$field]))
					{
						if ( ! empty($username)) $username .= ' ';
						$username .= urlencode($post[$field]);
					}
				}
			}
			$username = urlencode($username);
		}

		if ( ! is_null(self::$config['evidence_input']) && !empty($post[self::$config['evidence_input']]))
			$evidence = urlencode($post[self::$config['evidence_input']]);
		else
			$evidence = '';

		if ( ! empty($post['ip']))
			$ip = $post['ip'];

		if ( ! empty($post['email']))
			$email = $post['email'];

		if (
			empty($ip)
			OR empty($username)
			OR empty($email)
		)
		{
			log_message('error', 'Stop Forum Spam Module : Cannot submit, $ip, $username, $email & $evidence must be set');
		}
		else
		{
			$params = 'api_key='.self::$config['api_key'];
			$params .= '&ip_addr='.$ip;
			$params .= '&email='.$email;
			$params .= '&username='.$username;
			$params .= '&evidence='.$evidence;

			self::$ci->rest->initialize(array('server' => self::$config['api_server']));
			self::$ci->rest->get('add.php', $params);
		}
	}


	public function test_api($config)
	{
		$result = array(
			'api_server' => TRUE,
			'called_url' => '',
			// Needed only to submit data
			'api_key' => TRUE,
			'evidence_input' => TRUE,
			'server_response' => FALSE,
		);

		if (empty(self::$config['api_server'])) $result['api_server'] = FALSE;
		if (empty($config['api_key'])) $result['api_key'] = FALSE;
		if (empty($config['evidence_input'])) $result['evidence_input'] = FALSE;

		if ( ! empty(self::$config['api_server']))
		{
			$email = Settings::get('website_email');

			$post['ip'] = self::$ci->input->ip_address();

			$params = 'email=' . Settings::get('site_email');
			$params .= '&ip=' . self::$ci->input->ip_address();
			$params .= '&f=serial';

			$result['called_url'] = self::$config['api_server'] . '/?' . $params;

			self::$ci->rest->initialize(array('server' => self::$config['api_server']));
			$response = self::$ci->rest->get('api', $params);

			if (is_string($response))
			{
				$response = unserialize($response);
				$result['server_response'] = $response;
			}
		}

		return $result;
	}

}
