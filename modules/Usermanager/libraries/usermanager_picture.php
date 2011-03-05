<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Usermanager_Picture {

	function __construct()
    {
		$ci =  &get_instance();
		if (!isset($ci->usermanager_usermodel))
			$ci->load->model('usermanager_usermodel');
	}

	public function upload_picture($field, $id)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$ci =  &get_instance();

		$upload_path = $config['usermanager_picture'][$field]['upload_path'];
		
		if ($id && $upload_path != false && $upload_path !='')
		{
			// Do we get a file ?
			if (!isset($_FILES[$field]) || !is_uploaded_file($_FILES[$field]['tmp_name']))
			{
				return lang("module_usermanager_error_upload_something");
			}
			else
			{
				if (!$this->_allowed_file($_FILES[$field]['name'], $_FILES[$field]['type'], $field))
					return lang("module_usermanager_error_upload_file_type");

				if ($_FILES[$field]['size']*1 > $config['usermanager_picture'][$field]['max_size']*1)
					return lang("module_usermanager_error_upload_file_size");

				$ext = $this->get_extention($_FILES[$field]['name']);

				// move pic
				if (!is_dir($upload_path))
					mkdir($upload_path);
				if (!is_dir($upload_path . "/".$id))
					mkdir($upload_path . "/".$id);


				$g = glob($upload_path . "/".$id."/" . $id."_".$field."_"."*"."."."*");
				if (!$g || empty($g))
				{
					// ...
				}
				else
				{
					foreach ($g as $f)
						unlink($f);
				}
				// Try to delete it again, just to make sure
				if (file_exists($upload_path . "/".$id."/" . $id."_".$field."_"."original".".".$ext))
					unlink($upload_path . "/".$id."/" . $id."_".$field."_"."original".".".$ext);
				if (! @move_uploaded_file($_FILES[$field]['tmp_name'], $upload_path . "/".$id."/" . $id."_".$field."_"."original".".".$ext ))
					return lang("module_usermanager_error_upload_something");

				// duplicate n times
				foreach ($config['usermanager_picture'][$field]['dimensions'] as $key => $val)
				{
					//if (!copy($upload_path . "/".$id."/" . $id."_".$field."_"."original".".".$ext, $upload_path . "/".$id."/" . $id."_".$field."_".$key.".".$ext))
					//	return lang("module_usermanager_error_upload_something");
					// Try to delete it again, just to make sure
					if (file_exists($upload_path . "/".$id."/" . $id."_".$field."_".$key.".".$ext))
						unlink($upload_path . "/".$id."/" . $id."_".$field."_".$key.".".$ext);

					// New Dimensions
					$from_size = getimagesize($upload_path . "/".$id."/" . $id."_".$field."_"."original".".".$ext);
					$to_w = $val[0];
					$to_h = $val[1];
					$from_w = $from_size[0];
					$from_h = $from_size[1];
					$new_w = 0;
					$new_h = 0;
					if ($from_w >= $from_h && $from_w > $to_w)
					{
						$new_w = $to_w;
						$new_h = intval($from_h * $new_w / $from_w);
					}
					else if ($from_h > $from_w && $from_h > $to_h)
					{
						$new_h = $to_h;
						$new_w = intval($from_w * $new_h / $from_h);
					}
					else
					{
						$new_h = $from_h;
						$new_w = $from_w;
					}

					$new_img = "";
					if($_FILES[$field]['type'] == "image/pjpeg" || $_FILES[$field]['type'] == "image/jpeg"){
						$new_img = imagecreatefromjpeg($upload_path . "/".$id."/" . $id."_".$field."_"."original".".".$ext);
					}elseif($_FILES[$field]['type'] == "image/x-png" || $_FILES[$field]['type'] == "image/png"){
						$new_img = imagecreatefrompng($upload_path . "/".$id."/" . $id."_".$field."_"."original".".".$ext);
					}elseif($_FILES[$field]['type'] == "image/gif"){
						$new_img = imagecreatefromgif($upload_path . "/".$id."/" . $id."_".$field."_"."original".".".$ext);
					}

					if (!$new_img)
						return lang("module_usermanager_error_upload_something");

					$resized_img = imagecreatetruecolor($new_w, $new_h);
					imagecopyresampled($resized_img, $new_img, 0, 0, 0, 0, $new_w, $new_h, $from_w, $from_h);

					if($_FILES[$field]['type'] == "image/pjpeg" || $_FILES[$field]['type'] == "image/jpeg"){
						ImageJpeg ($resized_img, $upload_path . "/".$id."/" . $id."_".$field."_".$key.".".$ext, 100);
					}elseif($_FILES[$field]['type'] == "image/x-png" || $_FILES[$field]['type'] == "image/png"){
						ImagePng ($resized_img, $upload_path . "/".$id."/" . $id."_".$field."_".$key.".".$ext);
					}elseif($_FILES[$field]['type'] == "image/gif"){
						ImageGif ($resized_img, $upload_path . "/".$id."/" . $id."_".$field."_".$key.".".$ext);
					}

					ImageDestroy ($resized_img);
					ImageDestroy ($new_img);
					return true;
				}
			}
		}
		else
		{
			return lang("module_usermanager_error_upload_something");
		}
		
		return lang("module_usermanager_error_upload_something");
	}

	private function _allowed_file($filename, $browser_mime, $field)
	{
		if (strpos($filename, '.') === FALSE)
			return false;

		$parts		= explode('.', $filename);
		$ext		= array_pop($parts);
		$filename	= array_shift($parts);

		if ($this->_get_mime($ext, $field) === false)
			return false;

		if ($this->_check_mime($browser_mime, $field) === false)
			return false;

		return true;
	}

	public function get_extention($filename)
	{
		if (strpos($filename, '.') === FALSE)
		{
			return $filename;
		}

		$parts		= explode('.', $filename);
		$ext		= array_pop($parts);
		$filename	= array_shift($parts);

		return $ext;
	}

	// Checks whether mime is allowed and returns mime-string
	private function _get_mime($mime, $field)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		return (!isset($config['usermanager_picture'][$field]['allowed_mimes'][$mime])) ? FALSE : $config['usermanager_picture'][$field]['allowed_mimes'][$mime];
	}

	private function _check_mime($mime, $field)
	{
		include APPPATH . '../modules/Usermanager/config/config.php';
		$match = false;
		foreach ($config['usermanager_picture'][$field]['allowed_mimes'] as $val)
		{
			if (gettype($val) === "string")
			{
				if ($val === strtolower($mime))
					$match = true;
			}
			else
			{
				if (in_array(strtolower($mime), $val))
					$match = true;
			}
		}
		return $match;
	}
}
