<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usermanager_Action
{
	function __construct()
    {
		$ci =  &get_instance();
		if (!isset($ci->form_validation))
			$ci->load->library('form_validation');
		if (!isset($ci->users_model))
			$ci->load->model('users_model', '', true);
		if (!isset($ci->usermanager_user))
			$ci->load->library('usermanager_user');
		if (!isset($ci->usermanager_functions))
			$ci->load->library('usermanager_functions');
    }

	/*
	 * We need to invoke process_data once this class is created.
	 * But we can't use the constructor, as the tag won't be available there.
	 * So we need this pseudo_construct in order not to leak memory.
	 */
	private $pseudo_constructed = false;
	
	public function pseudo_construct($tag)
	{
		if ($this->pseudo_constructed)
			return;
		$this->pseudo_constructed = true;

		$this->process_data($tag);
	}

	/*
	 * Tags
	 */

	public function help($tag)
	{
		$ci =  &get_instance();
		$output = $tag->parse_as_nested(file_get_contents(MODPATH.'Usermanager/views/tag_help'.EXT));
		return $output;
	}

	public function process_data($tag)
	{
		/*
		 * This function is called every time, Usermanager is used. It checks whether there is something to save or something to do
		 * All this is done at the first place in order to make every tag render the right thing.
		 * If this wasn't used like that, some tags would display wrong data, cause things would be displayed, before they're saved.
		 */
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		/*
		 * LOGOUT
		 */
		if ($ci->input->post('form_name')  === "logout")
		{
			if ($ci->connect->logged_in())
			{
				$ci->connect->logout(base_url());
			}
		}

		/*
		 * LOGIN
		 */
		if (($ci->input->post('form_name') === "minilogin") || ($ci->input->post('form_name') === "login"))
		{
			if (!$ci->connect->logged_in())
			{
				$tmp_form_name = $_POST['form_name'];
				unset($_POST['submit']);
				unset($_POST['submit_form']);
				unset($_POST['form_name']);
				if($ci->usermanager_functions->check_login_input())
				{
					if ($ci->connect->login($_POST))
					{
						$_POST['form_name'] = $tmp_form_name;
						$ci->usermanager_functions->additional_success['login'] = lang("module_usermanager_text_logged_in");
					}
					else
					{
						$_POST['form_name'] = $tmp_form_name;
						$ci->usermanager_functions->additional_err['login'] = lang("module_usermanager_error_bad_login_information");
					}
				}
				else
				{
					$_POST['form_name'] = $tmp_form_name;
				}
			}
		}

		/*
		 * REGISTER
		 */
		if ($ci->input->post('form_name') === "register")
		{
			if ($ci->usermanager_functions->check_register_input())
			{
				try
				{
					$usr = array(
						"username" => $config['usermanager_email_as_username'] == true ? $ci->input->post('email') : $ci->input->post('username'),
						"screen_name" => $ci->input->post('screen_name'),
						"email" => $ci->input->post('email'),
						"password" => $ci->input->post('password'),
						"id_group" => $ci->input->post('id_group'),
						'join_date' => date('Y-m-d H:i:s')
					);
					// Save new user only if it doesn't exist
					if ( ! $ci->base_model->exists(array('username' => $usr['username']), "users"))
					{
						
						if ( ! $ci->base_model->exists(array('email' => $usr['email']), "users"))
						{
							if (!$ci->connect->register($usr))
							{
								$ci->usermanager_functions->additional_err['register'] = $ci->connect->error;
							}
							else
							{
								// Save the user	
								$id = $ci->db->insert_id();
								$ci->usermanager_user->set_custom_fields($id);
								$ci->users_model->save_meta($id, $_POST);

								// Store the activation key for email usage
								$usr = $ci->connect->get_user($usr['username']);
								$activation_key = $ci->connect->calc_activation_key($usr);
								$tag->locals->vars['activation_key'] = $activation_key;
								
								// Send a mail
								if (!isset($ci->email))
									$ci->load->library('email');
								$ci->email->from(Settings::get("site_email"), Settings::get("site_title"));
								$ci->email->to($ci->input->post('email')); 
								$ci->email->subject(Settings::get("site_title")." - ".lang("module_usermanager_email_registration_title"));
								$ci->email->message($tag->parse_as_nested(file_get_contents(MODPATH.'Usermanager/views/mail_register'.EXT)));
								$ci->email->send();


								// Auto Login the user
								// uncomment if you want the user to be logged after registration
								// unset($usr['id_group']);
								// unset($usr['join_date']);
								// unset($usr['screen_name']);
								// $ci->connect->login($usr);
															
								// Success message
								$ci->usermanager_functions->additional_success['profile'] = lang("module_usermanager_text_registered") . " <a href='".base_url().$config['usermanager_login_url']."'>".lang("module_usermanager_text_registered_here")."</a>.";
							}
						}
						else
						{
							$ci->usermanager_functions->additional_err['register'] = lang("module_usermanager_error_email_exists");
						}
					}
					else
					{
						$ci->usermanager_functions->additional_err['register'] = $config['usermanager_email_as_username'] == true ? lang("module_usermanager_error_email_exists") : lang("module_usermanager_error_username_exists");
					}
					
					
				}
				catch(Exception $e)
				{
					$ci->usermanager_functions->additional_err['register'] = $e;
				}
			}
		}

		/*
		 * PROFILE
		 */

		if ($ci->input->post('form_name') === "profile_save")
		{
			if ($ci->connect->logged_in())
			{
				if ($ci->usermanager_functions->check_profile_input())
				{
					try
					{
						if ($ci->input->post("delete") === "1")
						{
							$user = $ci->usermanager_user->get_current_user();
							$pwd = $ci->connect->decrypt($user['password'], $user);
							
							if ($user != false && $ci->input->post('password') === $pwd && $pwd)
							{
								$ci->connect->logout();
								$ci->usermanager_user->delete_user($user['id_user']);
								// Don't use $output, as we want to use a completely different view file
								$ci->usermanager_functions->additional_success['profile'] = lang("module_usermanager_text_user_deleted");
							}
							else
							{
								$ci->usermanager_functions->additional_err['register'] = lang("module_usermanager_error_password_for_delete");
							}
						}
						else
						{
							$user = $ci->usermanager_user->get_current_user();
							$usr = array(
								"username" => $ci->input->post('username') ? $ci->input->post('username') : $user['username'],
								"screen_name" => $ci->input->post('screen_name'),
								"email" => $ci->input->post('email')
							);
							if ( $usr['username'] == $user['username'] || !$ci->base_model->exists(array('username' => $usr['username']), "users"))
							{
								if ( $usr['email'] == $user['email'] || !$ci->base_model->exists(array('email' => $usr['email']), "users"))
								{
									if ($ci->input->post('password') && $ci->input->post('password2'))
									{
										$usr['salt'] = $ci->connect->get_salt();
										$usr['password'] = $ci->connect->encrypt($ci->input->post('password'), $usr);
									}
									$ci->db->where("id_user", $user['id_user']);
									$ci->db->update("users", $usr);
									$ci->users_model->save_meta($user['id_user'], $_POST);
									$ci->usermanager_user->update_custom_fields($user['id_user']);
									$ci->usermanager_functions->additional_success['profile'] = lang("module_usermanager_text_profile_saved");
								}
								else
								{
									$ci->usermanager_functions->additional_err['profile'] = lang("module_usermanager_error_email_exists");
								}
							}
							else
							{
								$ci->usermanager_functions->additional_err['profile'] = $config['usermanager_email_as_username'] == true ? lang("module_usermanager_error_email_exists") : lang("module_usermanager_error_username_exists");
							}
						}
					}
					catch(Exception $e)
					{
						$ci->usermanager_functions->additional_err['profile'] = $e;
					}
				}
			}
			else
			{
				$ci->usermanager_functions->additional_notices['profile'] = lang("module_usermanager_text_not_logged_in");
			}
		}
		elseif ($ci->input->post('form_name') === "profile_edit" && $ci->connect->logged_in())
		{
			$user = $ci->usermanager_user->get_current_user();
			$ci->usermanager_user->check_for_missing_tables($user['id_user']);
		}


		/*
		 * PASSWORD BACK
		 */
		if ($ci->input->post('form_name') === "restore_password")
		{
			// Check if user exists
			if ( $ci->base_model->exists(array('email' => $ci->input->post('email')), "users"))
			{
				// Get the user
				$ci->load->model('connect_model');
				$user = $ci->connect_model->find_user(array('email' => $ci->input->post('email')), FALSE);
				
				// New password
				$random_password = $ci->connect->get_random_password(8);
				
				// Save the user with this new password
				$user['salt'] = $ci->connect->get_salt();
				$user['password'] = $ci->connect->encrypt($random_password, $user);

				$ci->db->where("id_user", $user['id_user']);
				$ci->db->update("users", $user);
				$activation_key = $ci->connect->calc_activation_key($user);
			
				// Send Mail
				$tag->locals->vars['password'] = $random_password;
				$tag->locals->vars['username'] = $user['username'];
				$tag->locals->vars['screen_name'] = $user['screen_name'];

				if (!isset($ci->email))
					$ci->load->library('email');
				$ci->email->from(Settings::get("site_email"), Settings::get("site_title"));
				$ci->email->to($ci->input->post('email')); 
				$ci->email->subject(Settings::get("site_title")." - ".lang("module_usermanager_email_restore_password_title_new_login"));
				$ci->email->message($tag->parse_as_nested(file_get_contents(MODPATH.'Usermanager/views/mail_restore_password'.EXT)));
				$ci->email->send();

				$ci->usermanager_functions->additional_success['restore_password'] = "ok";
			}
			else
			{
				$ci->usermanager_functions->additional_err['restore'] = lang('module_usermanager_error_bad_login_information');
		
			}
		}

		/*
		 * ACTIVATION
		 */
		$uris = explode('/', uri_string());
		if (in_array($config['usermanager_activation_url'], $uris))
		{
			$activation_code = array_pop($uris);
			$username = array_pop($uris);

			if ( ! $ci->connect->activate($username, $activation_code))
			{
				$ci->usermanager_functions->additional_err['activated'] = $ci->connect->error;
			}
			else
			{
				// Get the user and log him in.
				$user = $ci->connect->get_user($username);
				
				$ci->connect->login($user, $ci->connect->decrypt($user['password'], $user));
				
				// This text is not displayed for the moment. Should add a query tag...
				$ci->usermanager_functions->additional_success['activated'] = lang('module_usermanager_text_user_activated');
			}
		}

		/*
		 * RANDOM FIELDS
		 */
		if ($ci->input->post('form_name') === "random_fields_form")
		{
			if ($ci->usermanager_functions->check_random_fields_input())
			{
				$user = $ci->usermanager_user->get_current_user();
				$ci->usermanager_user->update_all_fields($user['id_user']);
				$ci->usermanager_functions->additional_success['success'] = lang("module_usermanager_text_data_saved");
			}
		}
		$ci->usermanager_functions->check_additional_errors();
	}

	public function minilogin($tag)
	{
		return $tag->parse_as_nested(file_get_contents(MODPATH.'Usermanager/views/tag_minilogin'.EXT));
	}

	public function login($tag)
	{
		return $tag->parse_as_nested(file_get_contents(MODPATH.'Usermanager/views/tag_login'.EXT));
	}


	public function register($tag)
	{
		return $tag->parse_as_nested(file_get_contents(MODPATH.'Usermanager/views/tag_register'.EXT)/*, $data*/);
	}


	public function profile($tag)
	{
		return $tag->parse_as_nested(file_get_contents(MODPATH.'Usermanager/views/tag_profile'.EXT)/*, $data*/);
	}

/*
	public function activate($tag)
	{
	
		
	
		
	}
*/

	public function user($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_user))
			$ci->load->library('usermanager_user');

		switch ($tag->attr['attr'])
		{
			case "activation_key":
				return $ci->usermanager_user->get_activation_key($tag);
				break;
			case "is_editor":
				return $ci->usermanager_user->is_editor($tag);
				break;
			case "is":
				return $ci->usermanager_user->is($tag);
				break;
			case "is_logged_in":
				return $ci->usermanager_user->is_logged_in($tag);
				break;
			case "get_picture":
				return $ci->usermanager_user->get_picture($tag);
				break;
			case "activate":
				return $ci->usermanager_user->activate($tag);
				break;
			default:
				return $ci->usermanager_user->get_field($tag);
		}
	}

	public function globals($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_global))
			$ci->load->library('usermanager_global');

		switch ($tag->attr['attr'])
		{
			case "admin_url":
				return $ci->usermanager_global->admin_url($tag);
				break;
			case "profile_url":
				return $ci->usermanager_global->profile_url($tag);
				break;
			case "register_url":
				return $ci->usermanager_global->register_url($tag);
				break;
			case "login_url":
				return $ci->usermanager_global->login_url($tag);
				break;
			case "activation_url":
				return $ci->usermanager_global->activation_url($tag);
				break;
			case "url":
				return $ci->usermanager_global->url($tag);
				break;
			case "login_field_name":
				return $ci->usermanager_global->login_field_name($tag);
				break;
			case "login_field_label":
				return $ci->usermanager_global->login_field_label($tag);
				break;
			case "email_as_username":
				return $ci->usermanager_global->email_as_username($tag);
				break;
			case "not_email_as_username":
				return $ci->usermanager_global->not_email_as_username($tag);
				break;
			default:
				return $tag->expand();
		}
	}

	public function form($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_form))
			$ci->load->library('usermanager_form');

		switch ($tag->attr['attr'])
		{
			case "has_errors":
				return $ci->usermanager_form->has_errors($tag);
				break;
			case "error_string":
				return $ci->usermanager_form->error_string($tag);
				break;
			case "has_notices":
				return $ci->usermanager_form->has_noticess($tag);
				break;
			case "notice_string":
				return $ci->usermanager_form->notice_string($tag);
				break;
			case "has_success":
				return $ci->usermanager_form->has_success($tag);
				break;
			case "success_string":
				return $ci->usermanager_form->success_string($tag);
				break;
			default:
				return $tag->expand();
		}
	}

	public function load($tag)
	{
		// This function is invoced by views, that make use of usermanager
		// and parent part of usermanager itself. It's the only way to load FTL-Binding.
		return "";
	}

	/*public function post($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_post))
			$ci->load->library('usermanager_post');

		switch ($tag->attr['attr'])
		{
			case "":
				return "";
				break;
			default:
				return $ci->usermanager_post->get_field($tag);
		}
	}
	*/
}
