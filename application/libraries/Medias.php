<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
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
	

	function __construct(){}
	

	public function create_thumb($source_path, $dest_path, $settings = array())
	{
		$CI =& get_instance();
		$CI->load->library('image_lib');

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
			$settings['maintain_ratio'] = true;	
	
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
	
				$CI->image_lib->clear();
				$CI->image_lib->initialize($settings);
	
				// Thumbnail creation
				if ( $CI->image_lib->resize() )
				{
					if( isset($settings['square']) && $settings['square'] == TRUE ) 
					{
						$settings_square['source_image'] =	$CI->image_lib->full_dst_path;
						
						// Calculate x and y axis
						$settings_square['x_axis'] = $settings_square['y_axis'] = '0';
						
						// Get image dimension before crop
						$dim = self::get_image_dimensions($CI->image_lib->full_dst_path);
		
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
						$settings_square['unsharpmask'] =	false;
						$settings_square['maintain_ratio'] = false;
						$settings_square['height'] =		$settings['size'];
						$settings_square['width'] =			$settings['size'];
						$CI->image_lib->clear();
						$CI->image_lib->initialize($settings_square);
						
						$CI->image_lib->crop();
					}		
				}
			}
			return $dest_path;
		}
		return FALSE;	
	}
	
	
	public function create_thumb_folder($thumb_path)
	{
		// Create directory is not exists
		if( ! is_dir($thumb_path) )
		{
			$path_segments = explode('/', ltrim($thumb_path, '/'));
			array_pop($path_segments);
			
			$next_folder = '';
			
			// Check if server os is windows,
			// If server os is windows, first separator has to be empty string
			$separator = ((strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') !== false) ? '' : '/';

			foreach($path_segments as $folder)
			{
				$next_folder .= $separator . $folder;
				$separator = '/';

				if ( ! @is_dir($next_folder))
				{
					@mkdir($next_folder, 0777);
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
				return $dim;
			}
		}
		return FALSE;
	}
}


