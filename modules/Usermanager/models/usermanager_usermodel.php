<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('CI_Model')) { class CI_Model extends Model {} }

class Usermanager_usermodel extends CI_Model 
{
	public function get_custom_fields($user)
	{
		if ($user['id_user'] == '0')
			return false;
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		if( ! is_array($user))
		{
			$user = array('id_user' => $user);
		}

		// Get all fields from config, that aren't in the table users.
		// "Field" => "Table"
		$arr = array();
		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($val['save'] != 'users' && $val['save'] != false && $key != "user_id" && $key != "id_user" && $key != "id")
				$arr[$key] = $val['save'];
		}

		$tables = array_values(array_unique($arr));
		$qry = array();

		foreach ($tables as $key)
		{
			$qry[$key] = array_keys($arr, $key);
		}

		$qTables = "";
		$qWhere = "";
		$qFields = "";
		foreach ($qry as $key => $val)
		{
			if ($qTables)
				$qTables .= ", ";
			$qTables .= $key;
			if ($qWhere)
				$qWhere .= " AND ";
			$qWhere .= $key.".id_user = ".$ci->db->escape($user['id_user']);
			foreach ($val as $field)
			{
				if ($qFields)
					$qFields .= ",";
				$qFields .= $key.".".$field;
			}
		}
		$q = $ci->db->query("SELECT ".$qFields." FROM ".$qTables." WHERE ".$qWhere.";");
		$usr = array();
		if ($q && $q->num_rows() > 0)
		{
			$row = $q->row_array(); 
			foreach ($qry as $key => $val)
			{
				foreach ($val as $field)
				{
					$usr[$field] = $row[$field];
				}
			}
		}
		else
		{
			foreach ($qry as $key => $val)
			{
				foreach ($val as $field)
				{
					$usr[$field] = "";
				}
			}
		}

		return $usr;
	}

	public function set_custom_fields($id)
	{
		if ($id == '0')
			return false;
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		// Get all fields from config, that aren't in the table users.
		// "Field" => "Table"
		$arr = array();
		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($val['save'] != 'users' && $val['save'] != false && $key != "id")
				$arr[$key] = $val['save'];
		}

		// Just if it wasn't set before
		$_POST['id_user'] = $id;

		$tables = array_values(array_unique($arr));
		$qry = array();

		foreach ($tables as $key)
		{
			$qry[$key] = array_keys($arr, $key);
		}

		foreach ($qry as $key => $val)
		{
			$qFields = "";
			$qTables = $key;
			foreach ($val as $field)
			{
				if (isset($_POST[$field]))
				{
					if ($qFields)
						$qFields .= ", ";
					$qFields .= $key.".".$field." = ".$ci->db->escape($_POST[$field]);
				}
			}
			if (!$ci->db->query("insert into ".$qTables." SET ".$qFields.";"))
				return false;
		}
		return true;
	}

	public function update_custom_fields($id)
	{
		if ($id == '0')
			return false;
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		// Get all fields from config, that aren't in the table users.
		// "Field" => "Table"
		$arr = array();
		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($val['save'] != 'users' && $val['save'] != false && $key != "user_id" && $key != "id_user" && $key != "id")
				$arr[$key] = $val['save'];
		}

		$tables = array_values(array_unique($arr));
		$qry = array();

		foreach ($tables as $key)
		{
			$qry[$key] = array_keys($arr, $key);
		}

		$qTables = "";
		$qFields = "";
		$qWhere = "";
		foreach ($qry as $key => $val)
		{
			if ($qTables)
				$qTables .= ", ";
			$qTables .= $key;
			if ($qWhere)
				$qWhere .= " AND ";
			$qWhere .= $key.".id_user = ".$ci->db->escape($id);
			foreach ($val as $field)
			{
				if (isset($_POST[$field]))
				{
					if ($qFields)
						$qFields .= ", ";
					$qFields .= $key.".".$field." = ".$ci->db->escape($_POST[$field]);
				}
			}
		}

		if ($ci->db->query("update ".$qTables." SET ".$qFields." WHERE ".$qWhere.";"))
			return true;
		else
			return false;
	}

	public function update_all_fields($id)
	{
		if ($id == '0')
			return false;
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		// Get all fields from config, that aren't in the table users.
		// "Field" => "Table"
		$arr = array();
		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($val['save'] != false && $key != "user_id" && $key != "id_user" && $key != "id")
				$arr[$key] = $val['save'];
		}

		$tables = array_values(array_unique($arr));
		$qry = array();

		foreach ($tables as $key)
		{
			$qry[$key] = array_keys($arr, $key);
		}

		$qTables = "";
		$qFields = "";
		$qWhere = "";
		foreach ($qry as $key => $val)
		{
			if ($qTables)
				$qTables .= ", ";
			$qTables .= $key;
			if ($qWhere)
				$qWhere .= " AND ";
			$qWhere .= $key.".id_user = ".$ci->db->escape($id);
			foreach ($val as $field)
			{
				if (isset($_POST[$field]))
				{
					if ($qFields)
						$qFields .= ", ";
					$qFields .= $key.".".$field." = ".$ci->db->escape($_POST[$field]);
				}
			}
		}

		if ($ci->db->query("update ".$qTables." SET ".$qFields." WHERE ".$qWhere.";"))
			return true;
		else
			return false;
	}

	public function update_field($id, $key, $val = false)
	{
		if ($id == '0' || !$id)
			return false;
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		// Get all fields from config, that aren't in the table users.
		// "Field" => "Table"
		$arr = array();
		$cond = false;
		if (isset($config['usermanager_user_model'][$key]))
			$cond = $config['usermanager_user_model'][$key];
		if (!$cond)
			return false;

		$table = $cond['save'];
		if (!$table)
			return false;

		if ($val === false)
			$val = $ci->input->post($key);

		if ($val === false)
			return false;

		if ($ci->db->query("update ".$table." SET ".$key." = ".$ci->db->escape($val)." WHERE id_user = ".$id.";"))
			return true;
		else
			return false;
	}

	public function delete_user($id)
	{
		if ($id == '0')
			return false;
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		// Get all fields from config, that aren't in the table users.
		// "Field" => "Table"
		$arr = array();
		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($val['save'] != 'users' && $val['save'] != false && $key != "user_id" && $key != "id_user" && $key != "id")
				$arr[$key] = $val['save'];
		}

		$tables = array_values(array_unique($arr));
		$qTables = "";

		foreach ($tables as $key)
		{
			$ci->db->query("delete from ".$key." WHERE id_user = ".$ci->db->escape($id).";");
		}
		return true;
	}

	public function check_for_missing_tables($id)
	{
		if ($id == '0')
			return false;
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		// Get all fields from config, that aren't in the table users.
		// "Field" => "Table"
		$arr = array();
		foreach ($config['usermanager_user_model'] as $key => $val)
		{
			if ($val['save'] != 'users' && $val['save'] != false && $key != "user_id" && $key != "id_user" && $key != "id")
				$arr[$key] = $val['save'];
		}

		$tables = array_values(array_unique($arr));

		foreach ($tables as $key)
		{
			$q = $ci->db->query("SELECT * FROM ".$key." WHERE id_user = ".$ci->db->escape($id).";");
			if (!$q || $q->num_rows() < 1)
			{
				$ci->db->query("INSERT INTO ".$key." SET id_user = ".$ci->db->escape($id).";");
			}
		}
		return true;
	}
}
