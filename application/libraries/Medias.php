<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.8
 *
 */

/**
 * Ionize Media Class
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	Libraries
 *
 */
class Medias
{
	protected static $_inited = FALSE;

	public static $ci;

	/*
	 * System default "No picture" source file.
	 * Must be in the admin theme folder :
	 * /themes/admin/images/
	 *
	 */
	public static $no_picture_source = "no_picture_source_smiley.png";


	protected static $allowed_resize_method = array
	(
		'square', 		// Square picture
		'adaptive', 	// Adapted to the size
		'border', 		// Adds borders to picture, doesn't crop
		'width', 		// Master size : width
		'height',		// Master size : height
		'wider_side', 	// Default value, not method needs to be set to use this one.
	);

	/**
	 * Default resize method
	 * Allowed methods :
	 *
	 * @var string
	 *
	 */
	public static $default_resize_method = 'wider_side';


	/**
	 * Default border color for 'border' resize method
	 *
	 * @var string
	 *
	 */
	public static $default_border_color = '#555';

	// ------------------------------------------------------------------------


	function __construct()
	{
		self::$ci =& get_instance();
	}


	// ------------------------------------------------------------------------


	/**
	 * @param array
	 * @param array
	 * @param string
	 *
	 * @return string
	 *
	 */
	public function get_src($media, $settings, $fail_picture_path=NULL)
	{

		if ( ! file_exists(DOCPATH.$media['path']))
		{
			$media = $this->_get_no_source_picture($fail_picture_path);
		}

		$thumb_file_path = self::get_thumb_file_path($media, $settings);

		$settings['refresh'] = isset($settings['refresh']) ? $settings['refresh'] : FALSE;

		// Create the thumb if it doesn't exists
		if ( ! file_exists($thumb_file_path) OR $settings['refresh'] == TRUE)
		{
			// Resize method
			if (
				empty($settings['method']) OR
				! in_array($settings['method'], self::$allowed_resize_method)
			)
				$settings['method'] = self::$default_resize_method;

			// Other options
			$settings['watermark'] = ( ! empty($settings['watermark'])) ? $settings['watermark'] : '';
			$settings['unsharp'] = ( ! empty($settings['unsharp'])) ? $settings['unsharp'] : FALSE;

			$size_error = FALSE;
			$background_error = FALSE;
			$thumb_folder = $this->get_thumb_folder($settings);

			// User asked size check
			$size = explode(',', $settings['size']);

			switch($settings['method'])
			{
				case 'square':

					$settings['width'] = $size[0];
					$settings['height'] = $size[0];
					$settings['square_crop'] = (empty($settings['start'])) ? $media['square_crop'] : $settings['start'];

					// check size attribut
					if(!preg_match('/^([0-9]){1,4}x([0-9]){1,4}$/', $thumb_folder))
						$size_error = TRUE;

					break;

				case 'width':

					$settings['width'] = $size[0];

					// check size attribut
					if(!preg_match('/^([0-9]){1,4}x$/', $thumb_folder))
						$size_error = TRUE;

					break;

				case 'height':

					$settings['height'] = $size[0];

					// check size attribut
					if(!preg_match('/^x([0-9]){1,4}$/', $thumb_folder))
						$size_error = TRUE;

					break;

				// Default resize method
				case 'wider_side';

					$settings['size'] = $size[0];

					// check size attribut
					if(!preg_match('/^([0-9]){1,4}$/', $thumb_folder))
						$size_error = TRUE;

					break;

				case 'adaptive':

					$settings['width'] = $size[0];
					$settings['height'] = ( ! empty($size[1])) ? $size[1] : $size[0];
					$settings['square_crop'] = (empty($settings['start'])) ? $media['square_crop'] : $settings['start'];

					// check size attribut
					if(!preg_match('/^([0-9]){1,4}x([0-9]){1,4}a$/', $thumb_folder))
						$size_error = TRUE;

					break;

				case 'border':

					$settings['width'] = $size[0];
					$settings['height'] = ( ! empty($size[1])) ? $size[1] : $size[0];
					$settings['color'] = empty($settings['color']) ? self::$default_border_color : $settings['color'];

					// check background color
					if(
						!preg_match('/^([A-Fa-f0-9]){6}$/', $settings['color'])
						&& !preg_match('/^([A-Fa-f0-9]){3}$/', $settings['color'])
						&& !preg_match('/^#([A-Fa-f0-9]){6}$/', $settings['color'])
						&& !preg_match('/^#([A-Fa-f0-9]){3}$/', $settings['color'])
					)
						$background_error = TRUE;

					// check size attribute
					if(!preg_match('/^([0-9]){1,4}x([0-9]){1,4}e$/', $thumb_folder))
						$size_error = TRUE;

					break;

			}

			// On invalid size attribute or invalid background attribute
			// don't create thumb
			if( ! $size_error && ! $background_error)
				self::create_thumb_onthefly($media['path'], $thumb_file_path, $settings);

		}

		// If no thumbs exists here, that means
		// 1. There is size or background error
		// 2. There was a problem when creating the folder / thumb
		if ( ! file_exists($thumb_file_path))
		{
			if ( ! is_null($fail_picture_path))
			{
				return base_url() . Settings::get('files_path') . '/' . $fail_picture_path;
			}
			return self::_get_picture_url($media, $settings);
		}

		return self::_get_thumb_url($media, $settings);
	}


	// ------------------------------------------------------------------------


	public function create_thumb($source_path, $dest_path, $settings = array())
	{
		self::$ci->load->library('image_lib');

		self::create_thumb_folder($dest_path);
		
		// Settings for square array
		$settings_square = array();
		
		// Source picture size
		if ( $dim = self::get_image_dimensions($source_path) )
		{
			$settings['master_dim'] = ($dim['width'] > $dim['height']) ? 'width' : 'height';
			$settings['source_image'] =	$source_path;
			$settings['new_image'] =	$dest_path;
			$settings['quality'] =		'90';
			$settings['maintain_ratio'] = TRUE;
	
			if (isset($settings['square']) && $settings['square'] == TRUE )
			{
				if ($dim['width'] >= $dim['height']) 
					$settings['master_dim'] =	$settings_square['master_dim'] = 'height';
				else 
					$settings['master_dim'] =	$settings_square['master_dim'] = 'width';
			}
	
			if ($dim[$settings['master_dim']] >= $settings['size'])
			{
				$settings['width'] = $settings['height'] = $settings['size']; 		// Resize on master_dim. Used to keep ratio.

				self::$ci->image_lib->clear();
				self::$ci->image_lib->initialize($settings);
	
				// Thumbnail creation
				if ( self::$ci->image_lib->resize() )
				{
					if( isset($settings['square']) && $settings['square'] == TRUE )
					{
						$settings_square['source_image'] =	self::$ci->image_lib->full_dst_path;
						
						// Calculate x and y axis
						$settings_square['x_axis'] = $settings_square['y_axis'] = '0';
						
						// Get image dimension before crop
						$dim = self::get_image_dimensions(self::$ci->image_lib->full_dst_path);
		
						// Center the scare
						if ($dim['width'] > $dim['height'])
						{
							$settings_square['x_axis'] = ($dim['width'] - $settings['width']) / 2;
						}
						else
						{
							$settings_square['y_axis'] = ($dim['height'] - $settings['height']) / 2;
						}
		
						$settings_square['new_image'] =		'';
						$settings_square['unsharpmask'] =	FALSE;
						$settings_square['maintain_ratio'] = FALSE;
						$settings_square['height'] =		$settings['size'];
						$settings_square['width'] =			$settings['size'];
						self::$ci->image_lib->clear();
						self::$ci->image_lib->initialize($settings_square);

						self::$ci->image_lib->crop();
					}		
				}
			}
			else
			{
				return $source_path;
			}
			return $dest_path;
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	public function create_thumb_onthefly($source_path, $dest_path, $settings = array())
	{
		// Result of the process
		$result = FALSE;

		if (!file_exists(FCPATH.$source_path))
			return $result;

		self::$ci->load->library('image_lib');

		$dim = self::get_image_dimensions($source_path);

		$create_func = '';
		$save_func = '';
		$quality = 0;
		$type = '';

		switch($dim['type'])
		{
			case 1:
				
				$create_func = 'imagecreatefromgif';
				$save_func = 'imagegif';
				$type = 'gif';
				
				break;
			
			case 2:
				
				$create_func = 'imagecreatefromjpeg';
				$save_func = 'imagejpeg';
				$quality = 90;
				$type = 'jpeg';
				
				break;
			
			case 3:
			
				$create_func = 'imagecreatefrompng';
				$save_func = 'imagepng';
				$quality = 9;
				$type = 'png';
				
				break;
		}
		
		// if image is not jpg, png or gif don't create thumb
		//if(empty ($type)) return;
		
		self::create_thumb_folder($dest_path);
		
		$ci_settings = array();
		$ci_settings['source_image'] = $source_path;
		$ci_settings['new_image'] = $dest_path;
		$ci_settings['maintain_ratio'] = TRUE;
		$ci_settings['quality'] = $quality;	
		$ci_settings['unsharpmask'] = $settings['unsharp'];

		switch($settings['method'])
		{
			case 'square':
				
				$ci_settings['width'] = $settings['width'];
				$ci_settings['height'] = $settings['height'];
				
				$ci_settings['master_dim'] = ($dim['width'] < $dim['height']) ? 'width' : 'height';

				self::$ci->image_lib->clear();
				self::$ci->image_lib->initialize($ci_settings);
				
				if ( self::$ci->image_lib->resize() )
				{
					$ci_settings['source_image'] =	self::$ci->image_lib->full_dst_path;
					
					$ci_settings['x_axis'] = $ci_settings['y_axis'] = '0';
					
					// Get image dimension before crop
					$dim = self::get_image_dimensions(self::$ci->image_lib->full_dst_path);
					
					// Center the square
					if ($dim['width'] > $dim['height'])
						$ci_settings['x_axis'] = ($dim['width'] - $settings['width']) / 2;
					else
						$ci_settings['y_axis'] = ($dim['height'] - $settings['height']) / 2;

					switch ($settings['square_crop'])
					{
						// crop top-left area
						case 'tl':
							$ci_settings['x_axis'] = '0';
							$ci_settings['y_axis'] = '0';
							break;
						
						// crop bottom-right area
						case 'br':
							$ci_settings['x_axis'] = $dim['width'] - $settings['width'];
							$ci_settings['y_axis'] = $dim['height'] - $settings['height'];
							break;
					}
					
					$ci_settings['master_dim'] = 'auto';
					
					$ci_settings['maintain_ratio'] = FALSE;

					self::$ci->image_lib->clear();
					self::$ci->image_lib->initialize($ci_settings);

					$result = self::$ci->image_lib->crop();
				}
				
				break;
			
			case 'width':
				
				$ci_settings['width'] = $settings['width'];
				$ci_settings['height'] = intval($settings['width']  * $dim['height'] / $dim['width']);
				
				$ci_settings['maintain_ratio'] = FALSE;

				self::$ci->image_lib->clear();
				self::$ci->image_lib->initialize($ci_settings);

				$result = self::$ci->image_lib->resize();
				
				break;
			
			case 'height':
				
				$ci_settings['height'] = $settings['height'];
				$ci_settings['width'] = intval($settings['height']  * $dim['width'] / $dim['height']);
				
				$ci_settings['maintain_ratio'] = FALSE;

				self::$ci->image_lib->clear();
				self::$ci->image_lib->initialize($ci_settings);

				$result = self::$ci->image_lib->resize();
				
				break;
			
			case 'wider_side':
				
				if ($dim['width'] < $dim['height']) 
					$ci_settings['master_dim'] = 'height';
				else 
					$ci_settings['master_dim'] = 'width';
				
				$ci_settings['width'] = $settings['size'];
				$ci_settings['height'] = $settings['size'];

				self::$ci->image_lib->clear();
				self::$ci->image_lib->initialize($ci_settings);

				$result = self::$ci->image_lib->resize();

				break;
				
			case 'adaptive':

				$params = self::calculate_crop_params($dim['width'], $dim['height'], $settings['width'], $settings['height'], 'crop');

				$ci_settings['width'] = $params['resize_width'];
				$ci_settings['height'] = $params['resize_height'];
				
				$ci_settings['maintain_ratio'] = FALSE;

				self::$ci->image_lib->clear();
				self::$ci->image_lib->initialize($ci_settings);
				
				if ( self::$ci->image_lib->resize() )
				{
					$ci_settings['source_image'] =	self::$ci->image_lib->full_dst_path;
					
					$ci_settings['width'] = $settings['width'];
					$ci_settings['height'] = $settings['height'];
					$ci_settings['x_axis'] = $params['x_axis'];
					$ci_settings['y_axis'] = $params['y_axis'];

					switch ($settings['square_crop'])
					{
						// crop top-left area
						case 'tl':
							$ci_settings['x_axis'] = '0';
							$ci_settings['y_axis'] = '0';
							break;

						// crop bottom-right area
						case 'br':
							$dim = self::get_image_dimensions(self::$ci->image_lib->full_dst_path);
							$ci_settings['x_axis'] = $dim['width'] - $settings['width'];
							$ci_settings['y_axis'] = $dim['height'] - $settings['height'];
							break;
					}

					self::$ci->image_lib->clear();
					self::$ci->image_lib->initialize($ci_settings);
					
					$result = self::$ci->image_lib->crop();
				}
				
				break;
				
			case 'border':
					
				if(!empty($create_func))
				{
					$params = self::calculate_crop_params($dim['width'], $dim['height'], $settings['width'], $settings['height'], 'expand');

					$ci_settings['width'] = $params['resize_width'];
					$ci_settings['height'] = $params['resize_height'];

					$ci_settings['maintain_ratio'] = FALSE;

					self::$ci->image_lib->clear();
					self::$ci->image_lib->initialize($ci_settings);

					if ( self::$ci->image_lib->resize() )
					{
						$imagefile = self::$ci->image_lib->full_dst_path;

						$color = self::get_color_from_html_color($settings['color']);

						$imcanvas = imagecreatetruecolor($settings['width'], $settings['height']);
						imagefill($imcanvas, 0, 0, $color);

						$image = $create_func($imagefile);

						$src_w = imagesx($image);
						$src_h = imagesy($image);

						imagecopyresampled(
							$imcanvas,
							$image,
							$params['x_axis'],
							$params['y_axis'],
							0,
							0,
							$src_w,
							$src_h,
							$src_w,
							$src_h
						);

						if($save_func == 'imagegif')
							$save_func($imcanvas, $imagefile);
						else
							$save_func($imcanvas, $imagefile, $quality);

						imagedestroy($imcanvas);
						imagedestroy($image);
					}
				}

				break;
		}
		
		if($type == 'jpeg' && ! empty ($settings['watermark']) && file_exists($dest_path))
			$this->embed_watermark($dest_path, $settings['watermark']);

		return $result;
	}


	// ------------------------------------------------------------------------


    /**
     * Create Thumb Folders for Attached Pictures
     *
     * @param $thumb_path
     */
	public function create_thumb_folder($thumb_path)
	{
		// Create directory is not exists
		if( ! is_dir($thumb_path) )
		{
            $doc_path = rtrim(DOCPATH, '/');
            $thumb_path = str_replace(DOCPATH, '', $thumb_path);

			$path_segments = explode('/', ltrim($thumb_path, '/'));
			array_pop($path_segments);
			
			$next_folder = '';
			
			// Check if server os is windows,
			// If server os is windows, first separator has to be empty string
			// $separator = ((strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') !== FALSE) ? '' : '/';
            $separator = '/';

			foreach($path_segments as $folder)
			{
				$next_folder .= $separator . $folder;
				$separator = '/';

                if ( ! @is_dir($doc_path . $next_folder))
				{
                    @mkdir($doc_path . $next_folder, 0777);
				}
			}
		}
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Get the dimensions of a picture
	 *
	 * @param	string	Complete path to the image file
	 * @return	array	Array of dimension.
	 *					'width' : contains the width
	 *					'height' : contains the height
	 *
	 */
	public static function get_image_dimensions($path)
	{
		$dim = array();
		
		if (function_exists('getimagesize'))
		{
			$d = @getimagesize($path);
			
			if ($d !== FALSE)
			{
				$dim['width']	= $d['0'];
				$dim['height']	= $d['1'];
				$dim['type']	= $d['2'];
				return $dim;
			}
		}
		return FALSE;
	}


	//-------------------------------------------------------------------------


	/**
	 * 
	 * Calculate crop area
	 * 
	 *	Usage of $data :
	 *	1. Resize picture to $data['resize_width'], $data['resize_height'] 
	 *	2. If crop picture: copy area ($dest_width, $dest_height) with offset $data['x_axis'], $data['y_axis']
	 *	3. If add borders: create empty image ($dest_width, $dest_height), 
	 *		Copy resized image to position (offset)  $data['x_axis'], $data['y_axis']
	 */
	
	public static function calculate_crop_params($src_width, $src_height, $dest_width, $dest_height, $type = 'crop')
	{
		$data = array();

		if ($src_width > 0 && $src_height > 0)
		{
			$k1 = $dest_width / $src_width;
			$k = $dest_height / $src_height;

			$w1 = round ( $k1 * $src_width );
			$h1 = round ( $k1 * $src_height );

			if($w1 >= $dest_width && $h1 >= $dest_height && $type == 'crop')
				$k = $k1;

			if($w1 <= $dest_width && $h1 <= $dest_height && $type == 'expand')
				$k = $k1;

			// data for picture resizing
			$data['resize_width'] = round($src_width * $k);
			$data['resize_height'] = round($src_height * $k);

			// crop start position
			$data['x_axis'] = round(($data['resize_width'] - $dest_width) / 2);
			$data['y_axis'] = round(($data['resize_height'] - $dest_height) / 2);

			if($type == 'expand')
			{
				$data['x_axis'] = - $data['x_axis'] ;
				$data['y_axis'] = - $data['y_axis'];
			}
		}
		return $data;
	}


	//-------------------------------------------------------------------------


	/**
	 * 
	 * Calculate int color value from html hex color code (rgb) (3 or 6 digits)
	 *
	 * @param string $html_color
	 * @return type int
	 * 
	 */
	
	public static function get_color_from_html_color($html_color)
	{
		$color = $html_color;
		
		if ($color[0] == '#')
			$color = substr($color, 1);
		
		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
		
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		
		else
			return FALSE;
		
		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		
		$color = $r * 65536 + $g * 256 + $b;
		
		return $color;
	}


	// ------------------------------------------------------------------------


	/**
	 * Embed watermark
	 *
	 * watermark is file inside 'files' folder, filename: 'watermark.png'
	 *
	 */
	public function embed_watermark($filepath, $watermark_positions)
	{
		self::$ci->load->library('image_lib');
		
		$watermark_path = DOCPATH . Settings::get('files_path') . '/watermark.png';
		
		if( !file_exists($watermark_path))
			return;
		
		$w = explode(',', $watermark_positions);
		
		foreach($w as $p)
		{
			$p = trim($p);
			if(strlen($p) == 2)
			{
				// vertical position
				$v = substr($p, 0, 1);
				
				// horizontal position
				$h = substr($p, 1, 1);
				
				$vert = 'middle';
				$hor = 'center';
				
				switch($v)
				{
					case 't': $vert = 'top'; break;
					case 'b': $vert = 'bottom'; break;	
				}
				
				switch($h)
				{
					case 'l': $hor = 'left'; break;
					case 'r': $hor = 'right'; break;	
				}
				
				$wconf = array(
					'wm_type' => 'overlay',
					'source_image' => $filepath,
					'quality' => 90,
					'wm_vrt_alignment' => $vert,
					'wm_hor_alignment' => $hor,
					'wm_overlay_path' => $watermark_path
				);

				self::$ci->image_lib->clear();
				self::$ci->image_lib->initialize($wconf);

				self::$ci->image_lib->watermark();
			}
		}	
	}


	// ------------------------------------------------------------------------


	/**
	 * Deletes all thumbs from one media
	 *
	 * @param $media
	 */
	public function delete_thumbs($media)
	{
		self::$ci->load->helper('file');

		$thumb_folder = (Settings::get('thumb_folder')) ? Settings::get('thumb_folder') : '.thumbs';
		$thumb_path_segment = str_replace(Settings::get('files_path') . '/', '', $media['base_path'] );
		$thumb_base_path = DOCPATH . Settings::get('files_path') . '/' . $thumb_folder . '/';
		$thumb_file_path = $thumb_base_path . $thumb_path_segment . $media['file_name'];

		$thumbs = glob_recursive($thumb_file_path);

		foreach($thumbs as $thumb)
			@unlink($thumb);
	}


	// ------------------------------------------------------------------------


	/**
	 * Return a pseudo media data array based on the no source picture
	 *
	 * @param 	null|string
	 *
	 * @return 	array
	 *
	 */
	private function _get_no_source_picture($no_source_picture_file = NULL)
	{
		// No "no source picture"
		if (
			! $no_source_picture_file OR
			! file_exists(DOCPATH . Settings::get('files_path') .'/' . $no_source_picture_file)
		)
		{
			$no_source_picture_file = self::$no_picture_source;

			$system_source = DOCPATH .'themes/' . Settings::get('theme_admin') . '/images/' . $no_source_picture_file;

			if (file_exists($system_source ))
			{
				$dest_file = DOCPATH . Settings::get('files_path') .'/' . $no_source_picture_file;

				if (copy($system_source, $dest_file))
				{
					self::$ci->load->model('settings_model');

					self::$ci->settings_model->save_setting(array(
						'name' => 'no_source_picture',
						'content' => $no_source_picture_file
					));
				}
			}
		}

		$mimes = array();
		include(APPPATH.'/config/mimes_ionize.php');

		$extension = substr(strrchr($no_source_picture_file,'.'),1);

		$media = array(
			'type' => 'picture',
			'file_name' => $no_source_picture_file,
			'path' => Settings::get('files_path') .'/' . $no_source_picture_file,
			'base_path' => Settings::get('files_path') .'/',
			'extension' => $extension,
			'mime' => $mimes['picture'][$extension],
			'square_crop' => 'm',
			'title' => '',
			'alt' => '',
			'description' => '',
			'copyright' => '',
			'id_media' => 0,
		);

		return $media;
	}


	// ------------------------------------------------------------------------


	public function get_thumb_file_path($media, $settings)
	{
		$thumb_folder = (Settings::get('thumb_folder')) ? Settings::get('thumb_folder') : '.thumbs';

		$size_folder = $this->get_thumb_folder($settings);

		$thumb_path_segment = str_replace(Settings::get('files_path') . '/', '', $media['base_path'] );
		$thumb_base_path = DOCPATH . Settings::get('files_path') . '/' . $thumb_folder . '/';
		$thumb_path = $thumb_base_path . $thumb_path_segment;
		$thumb_file_path = $thumb_path . $size_folder . '/' . $media['file_name'];

		return $thumb_file_path;
	}


	// ------------------------------------------------------------------------


	public function get_thumb_folder($settings)
	{
		if (empty($settings['method']))
			$settings['method'] = self::$default_resize_method;

		// Some check regarding size
		$size = explode(',', $settings['size']);
		if (empty($size))
				return '';

		if ($settings['method'] == 'square')
		{
			return $size[0] . 'x' . $size[0];
		}

		// Master width method
		if ($settings['method'] == 'width' OR $settings['method'] == 'height')
		{
			// width is fixed
			if($settings['method'] == 'width')
				return $size[0] . 'x';

			// height is fixed
			if($settings['method'] == 'height')
				return 'x' . $size[0];
		}

		if ($settings['method'] == 'adaptive')
		{
			if (empty($size[1])) $size[1] = $size[0];

			return trim($size[0]) . 'x' . trim($size[1]) . 'a';
		}

		if ($settings['method'] == 'border')
		{
			if (empty($size[1])) $size[1] = $size[0];

			return trim($size[0]) . 'x' . trim($size[1]) . 'e';
		}

		// Default
		return $size[0];
	}


	// ------------------------------------------------------------------------


	private function _get_thumb_url($media, $settings)
	{
		$thumb_folder = (Settings::get('thumb_folder')) ? Settings::get('thumb_folder') : '.thumbs';

		$size_folder = $this->get_thumb_folder($settings);

		$thumb_path_segment = str_replace(Settings::get('files_path') . '/', '', $media['base_path'] );

		return base_url() . Settings::get('files_path') . '/' . $thumb_folder . '/' . $thumb_path_segment . $size_folder . '/' . $media['file_name'];
	}


	// ------------------------------------------------------------------------


	private function _get_picture_url($media, $settings)
	{
		return base_url() . $media['path'];
	}


	// ------------------------------------------------------------------------


	private function _get_clean_picture_filename($filename)
	{
		$chars = array('(', ')', '+', '<', '>', '!', '?');
		$filename = str_replace($chars, '', $filename);
		$remove=array(' ');
		$filename = str_replace($remove, '_', $filename);

		return $filename;
	}
}

