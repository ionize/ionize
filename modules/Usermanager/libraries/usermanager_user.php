<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usermanager_User {

	function __construct()
    {
		$ci =  &get_instance();
		if (!isset($ci->usermanager_usermodel))
			$ci->load->model('usermanager_usermodel');
    }

	/*
	 * Functions, that are used by the module itself
	 */

	public function get_current_user($id_user = false)
	{
		$ci = &get_instance();
		$user = "";
		if ($id_user === false)
		{
			$user = $ci->connect->get_current_user();
		}
		else
		{
			$ci->load->model('connect_model');
			$user = $ci->connect_model->find_user(array($ci->connect_model->users_pk => $id_user));
		}

		if ($user)
			return array_merge($user, $ci->usermanager_usermodel->get_custom_fields($user));
		else
			return false;
	}

	public function get_custom_fields($id_user = false)
	{
		$ci = &get_instance();
		$user = "";
		if ($id_user === false)
			$user = $ci->connect->get_current_user();
		else
			$user = $ci->access->find_user(array($user_pk => $this->session->userdata($id_user)));

		if ($user)
			return $ci->usermanager_usermodel->get_custom_fields($user);
		else
			return false;
	}

	public function set_custom_fields($id)
	{
		$ci = &get_instance();
		return $ci->usermanager_usermodel->set_custom_fields($id);
	}

	// Used for registration and Profile editing
	// Only sets present fields
	public function update_custom_fields($id)
	{
		$ci = &get_instance();
		$ret = true;
		// First check for pictures and change them, then set the fields
		// The picture fields are also set in update_custom_fields
		$ret = $this->upload_pictures($id);
		$ret2 = $ci->usermanager_usermodel->update_custom_fields($id);
		return $ret && $ret2;
	}

	// Also users-table
	// Only sets present fields
	public function update_all_fields($id)
	{
		$ci = &get_instance();
		$ret = true;
		// First check for pictures and change them, then set the fields
		// The picture fields are also set in update_custom_fields
		$ret = $this->upload_pictures($id);
		$ret2 = $ci->usermanager_usermodel->update_all_fields($id);
		return $ret && $ret2;
	}

	public function upload_pictures($id)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci = &get_instance();
		if (!isset($ci->usermanager_picture))
			$ci->load->library('usermanager_picture');
		if (!isset($ci->usermanager_functions))
			$ci->load->model('usermanager_functions');

		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($val['special_field'] === "picture")
			{
				if ($val['save'] != 'users' && $val['save'] != false && $this->_upload_present($key))
				{
					$r = $ci->usermanager_picture->upload_picture($key, $id);
					if (!($r === true))
					{
						$ci->usermanager_functions->additional_err['upload'] = $r;
						$_POST[$key] = "0";
						return false;
					}
					else
					{
						$_POST[$key] = "1";
					}
				}
				elseif ($val['save'] != 'users' && $val['save'] != false && $this->_upload_to_delete($key))
				{
					$ci->usermanager_picture->delete_picture($key, $user);
					$_POST[$key] = "0";
				}
				else
				{
					$g = glob($config['usermanager_picture'][$key]['upload_path'] . "/".$id."/" . $id."_".$key."_"."original".".*");
					if (!$g || empty($g))
						$_POST[$key] = "0";
					else
						$_POST[$key] = "1";
				}
			}
		}
		return true;
	}

	public function update_field($id, $key, $val = false)
	{
		$ci = &get_instance();
		return $ci->usermanager_usermodel->update_field($id, $key, $val);
	}

	public function delete_user($id)
	{
		$ci = &get_instance();
		$d1 = $ci->usermanager_usermodel->delete_user($id);
		$d2 = $ci->users_model->delete($id);
		if ($d1 && $d2)
			return true;
		return false;
	}

	public function check_for_missing_tables($id)
	{
		$ci = &get_instance();
		$ci->usermanager_usermodel->check_for_missing_tables($id);
	}

	public function get_field($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci = &get_instance();
		$user = $this->get_current_user(isset($tag->attr['id_user']) ? $tag->attr['id_user'] : false);
		//if (!$user)
		//	return "";


		$ret = "";
		if ($tag->attr['attr'] === "username" && $config['usermanager_email_as_username'])
			$tag->attr['attr'] = "email";
		
		if (isset($config['usermanager_user_model'][$tag->attr['attr']]) && $config['usermanager_user_model'][$tag->attr['attr']]['special_field'] != "restricted")
		{
			// Don't use default value if it's a checkbox and we're in validation process:
			if (!($config['usermanager_user_model'][$tag->attr['attr']]['special_field'] === 'checkbox' && isset($tag->attr["from_post_data"]) && $tag->attr["from_post_data"] === $ci->input->post('form_name')))
				if (isset($tag->attr["from_default_value"]) && $tag->attr["from_default_value"] === "1")
					$ret = $config['usermanager_user_model'][$tag->attr['attr']]['default_value'] === false ? "" : $config['usermanager_user_model'][$tag->attr['attr']]['default_value'];

			if (isset($tag->attr["from_user_field"]) && $tag->attr["from_user_field"] === "1" && $user)
				$ret = isset($user[$tag->attr['attr']]) && $user[$tag->attr['attr']] != null ? $user[$tag->attr['attr']] : $ret;

			if (isset($tag->attr["from_post_data"]) && $ci->input->post('form_name') === $tag->attr["from_post_data"])
				$ret = !($ci->input->post($tag->attr['attr']) === false) ? $ci->input->post($tag->attr['attr']) : $ret;

			//$ret = $ci->input->post($tag->attr['attr']) === false && ($config['usermanager_user_model'][$tag->attr['attr']]['default_value'] === "1" || $config['usermanager_user_model'][$tag->attr['attr']]['default_value'] === 1) && !($ci->input->post("register") === "1" || $ci->input->post("login") === "1" || $ci->input->post("minilogin") === "1" || $ci->input->post("editdo") === "1") ? "1" : $ci->input->post($tag->attr['attr']); // Checkboxes
		}

		// If only the post data is requested
		if (!isset($tag->attr['is_like']))
			return (!isset($tag->attr['html_encode']) || $tag->attr['html_encode'] == "1") ? htmlentities(utf8_decode($ret)) : $ret;
		
		// If the post data is compared to is_like
		else
			return $tag->attr['is_like'] === $ret ? $tag->expand() : "";
	}

	/*
	 * Functions, that are used by tags
	 */

	public function is($tag)
	{
		$ci = &get_instance();
		$user = $this->get_current_user(isset($tag->attr['id_user']) ? $tag->attr['id_user'] : false);
		if (!$user)
			return "";

		if (isset($tag->attr['is']))
		{
			if ($ci->connect->is($tag->attr['is']))
			{
				return $tag->expand();
			}
		}
		return "";
	}

	public function is_editor($tag)
	{
		$ci = &get_instance();
		$user = $this->get_current_user(isset($tag->attr['id_user']) ? $tag->attr['id_user'] : false);
		if (!$user)
			return "";

		if (isset($tag->attr['is_like']))
		{
			if ($user['group']['id_group'] < 4 && $tag->attr['is_like'] == '1')
				return $tag->expand();
			if ($user['group']['id_group'] >= 4 && $tag->attr['is_like'] == '1')
				return "";
			if ($user['group']['id_group'] < 4 && $tag->attr['is_like'] == '0')
				return "";
			if ($user['group']['id_group'] >= 4 && $tag->attr['is_like'] == '0')
				return $tag->expand();
		}
		else
		{
			if($user['group']['id_group'] < 4)
				return $tag->expand();
			return "";
		}
		return "";
	}

	public function is_logged_in($tag)
	{
		$ci = &get_instance();
		$user = $this->get_current_user(isset($tag->attr['id_user']) ? $tag->attr['id_user'] : false);

		if (isset($tag->attr['is_like']))
		{
			if ($user && $tag->attr['is_like'] == '1')
				return $tag->expand();
			if (!$user && $tag->attr['is_like'] == '1')
				return "";
			if ($user && $tag->attr['is_like'] == '0')
				return "";
			if (!$user && $tag->attr['is_like'] == '0')
				return $tag->expand();
		}
		else
		{
			if (!$user)
				return "";
			return $tag->expand();
		}
		return "";
	}

	public function get_picture($tag)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci = &get_instance();

		if (!isset($ci->usermanager_picture))
			$ci->load->library('usermanager_picture');

		$user = $this->get_current_user(isset($tag->attr['id_user']) ? $tag->attr['id_user'] : false);
		if (!$user || !$tag->attr['field'])
			return "";

		$dimensions = $tag->attr['dimensions'] ? $tag->attr['dimensions'] : "original";

		if ($user[$tag->attr['field']] == "1")
		{
			$g = glob($config['usermanager_picture'][$tag->attr['field']]['upload_path'] . "/".$user['id_user']."/" . $user['id_user']."_".$tag->attr['field']."_".$dimensions.".*");
			if (!$g || empty($g))
				return $config['usermanager_picture'][$tag->attr['field']]['default'][$dimensions];
			else
				return $config['usermanager_picture'][$tag->attr['field']]['view_path'] . "/".$user['id_user']."/" . $user['id_user']."_".$tag->attr['field']."_".$dimensions.".".$ci->usermanager_picture->get_extention($g[0]);
		}

		return $config['usermanager_picture'][$tag->attr['field']]['default'][$dimensions];
	}

	/**
	 * Return the activation key stored in locals vars.
	 * The activation key should be set in locals before calling this function
	 * 
	 */
	public function get_activation_key($tag)
	{
		if ( ! empty($tag->locals->vars['activation_key']))
		{
			return $tag->locals->vars['activation_key'];
		}
		
		return '';
	}

	public function activate($tag)
	{
		$ci =  &get_instance();
		if (!isset($ci->usermanager_functions))
			$ci->load->library('usermanager_functions');

		if (isset($tag->attr['has_success']))
		{
			if (!empty($ci->usermanager_functions->additional_success) && $tag->attr['has_success'] == '1')
				return $tag->expand();
			
			if (empty($ci->usermanager_functions->additional_success) && $tag->attr['has_success'] == '0')
				return $tag->expand();
		}
		
		return '';
	}
	


	/*
	 * Private functions
	 */

	private function _upload_present($key)
	{
		$ci = &get_instance();
		if (!($ci->input->post($key) === false) &&
					isset($_FILES[$key]) &&
					isset($_FILES[$key]['name']) &&
					isset($_FILES[$key]['size']) &&
					$_FILES[$key]['name'] &&
					$_FILES[$key]['size'] &&
					!($ci->input->post($key) === "delete"))
			return true;
		else
			return false;
	}

	private function _upload_to_delete($key)
	{
		$ci = &get_instance();
		if (!($ci->input->post($key) === false) &&
					($ci->input->post($key) === "delete"))
			return true;
		else
			return false;
	}
}
