<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ionize CMS
 *
 * Image Library
 * 
 * Extends the CodeIgniter CI_Image_lib by adding : 
 * - Some memory check before picture creation, to avoid fatal errors
 * - An unsharp library
 *
 * @package		Ionize
 * @category	Libraries
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */

class MY_Image_lib extends CI_Image_lib {

	// Unsharp properties
	var $unsharpmask = '';

    function MY_Image_lib($props = array())
    {
        parent::__construct($props);
    }

	/**
	 * Image Process Using GD/GD2
	 *
	 * This function will resize or crop
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */		
	function image_process_gd($action = 'resize')
	{
		$v2_override = FALSE;

		// If the target width/height match the source, AND if the new file name is not equal to the old file name
		// we'll simply make a copy of the original with the new name... assuming dynamic rendering is off.
		if ($this->dynamic_output === FALSE)
		{
			if ($this->orig_width == $this->width AND $this->orig_height == $this->height)
			{
				if ($this->source_image != $this->new_image)
				{
					if (@copy($this->full_src_path, $this->full_dst_path))
					{
						@chmod($this->full_dst_path, FILE_WRITE_MODE);
					}
				}

				return TRUE;
			}
		}

		// Let's set up our values based on the action
		if ($action == 'crop')
		{
			//  Reassign the source width/height if cropping
			$this->orig_width  = $this->width;
			$this->orig_height = $this->height;

			// GD 2.0 has a cropping bug so we'll test for it
			if ($this->gd_version() !== FALSE)
			{
				$gd_version = str_replace('0', '', $this->gd_version());
				$v2_override = ($gd_version == 2) ? TRUE : FALSE;
			}
		}
		else
		{
			// If resizing the x/y axis must be zero
			$this->x_axis = 0;
			$this->y_axis = 0;
		}

		// Check available memory
		if (false === $this->_checkMemoryBeforeImageCreate($this->full_src_path, $this->width, $this->height) )
		{
			$this->set_error('Not enough memory to handle this picture');

			return false;
		}

		//  Create the image handle
		if ( ! ($src_img = $this->image_create_gd()))
		{
			// $this->set_error('imglib_image_process_failed');
			return FALSE;
		}


		//  Create The Image
		//
		//  old conditional which users report cause problems with shared GD libs who report themselves as "2.0 or greater"
		//  it appears that this is no longer the issue that it was in 2004, so we've removed it, retaining it in the comment
		//  below should that ever prove inaccurate.
		//
		//  if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor') AND $v2_override == FALSE)
		if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor'))
		{
			$create	= 'imagecreatetruecolor';
			$copy	= 'imagecopyresampled';
		}
		else
		{
			$create	= 'imagecreate';
			$copy	= 'imagecopyresized';
		}

		$dst_img = $create($this->width, $this->height);

		if ($this->image_type == 3) // png we can actually preserve transparency
		{
			imagealphablending($dst_img, FALSE);
			imagesavealpha($dst_img, TRUE);
		}

		$copy($dst_img, $src_img, 0, 0, $this->x_axis, $this->y_axis, $this->width, $this->height, $this->orig_width, $this->orig_height);

		// Hack : Ajout de  unsharpMask
		if ($this->image_type != 3 && $this->unsharpmask !== '' && $this->unsharpmask != false)
		{
			$dst_img = $this->unsharpMask($dst_img, 50, 0.5, 3);
		}

		//  Show the image
		if ($this->dynamic_output == TRUE)
		{
			$this->image_display_gd($dst_img);
		}
		else
		{
			// Or save it
			if ( ! $this->image_save_gd($dst_img))
			{
				return FALSE;
			}
		}

		//  Kill the file handles
		imagedestroy($dst_img);
		imagedestroy($src_img);

		// Set the file to 777
		@chmod($this->full_dst_path, FILE_WRITE_MODE);

		return TRUE;
	}


	/**
	 * Unsharp Mask for PHP - version 2.1.1
	 * Unsharp mask algorithm by Torstein Hønsi 2003-07.
	 * thoensi_at_netcom_dot_no.
	 * Please leave this notice.
	 *
	 * WARNING! Due to a known bug in PHP 4.3.2 this script is not working well in this version. The sharpened images get too dark. The bug is fixed in version 4.3.3.
	 *
	 * From version 2 (July 17 2006) the script uses the imageconvolution function in PHP version >= 5.1, which improves the performance considerably.
	 *
	 * Unsharp masking is a traditional darkroom technique that has proven very suitable for
	 * digital imaging. The principle of unsharp masking is to create a blurred copy of the image
	 * and compare it to the underlying original. The difference in colour values
	 * between the two images is greatest for the pixels near sharp edges. When this
	 * difference is subtracted from the original image, the edges will be
	 * accentuated.
	 *
	 * The Amount parameter simply says how much of the effect you want. 100 is 'normal'.
	 * Radius is the radius of the blurring circle of the mask. 'Threshold' is the least
	 * difference in colour values that is allowed between the original and the mask. In practice
	 * this means that low-contrast areas of the picture are left unrendered whereas edges
	 * are treated normally. This is good for pictures of e.g. skin or blue skies.
	 *
	 * Any suggenstions for improvement of the algorithm, expecially regarding the speed
	 * and the roundoff errors in the Gaussian blur process, are welcome.
	 *
	 * http://vikjavev.no/computing/ump.php?id=350
	 *
	 *
	 *
	 *
	 *
	 */
	function unsharpMask($img, $amount, $radius, $threshold)
	{
	    // $img is an image that is already created within php using
	    // imgcreatetruecolor. No url! $img must be a truecolor image. 
	
	    // Attempt to calibrate the parameters to Photoshop: 
	    if ($amount > 500)    $amount = 500; 
	    $amount = $amount * 0.016; 
	    if ($radius > 50)    $radius = 50; 
	    $radius = $radius * 2; 
	    if ($threshold > 255)    $threshold = 255; 
	     
	    $radius = abs(round($radius));     // Only integers make sense. 
	    if ($radius == 0) {
	        return $img;
			// imagedestroy($img);
			// break;
		}
	    $w = imagesx($img); $h = imagesy($img); 
	    $imgCanvas = imagecreatetruecolor($w, $h); 
	    $imgBlur = imagecreatetruecolor($w, $h); 
	     
	    // Gaussian blur matrix: 
	    //                         
	    //    1    2    1         
	    //    2    4    2         
	    //    1    2    1         
	    //                         
	    ////////////////////////////////////////////////// 
	    if (function_exists('imageconvolution')) { // PHP >= 5.1  
	            $matrix = array(  
	            array( 1, 2, 1 ),  
	            array( 2, 4, 2 ),  
	            array( 1, 2, 1 )  
	        );  
	        imagecopy ($imgBlur, $img, 0, 0, 0, 0, $w, $h); 
	        imageconvolution($imgBlur, $matrix, 16, 0);  
	    }  
	    else {  
	
	    // Move copies of the image around one pixel at the time and merge them with weight 
	    // according to the matrix. The same matrix is simply repeated for higher radii. 
	        for ($i = 0; $i < $radius; $i++)    { 
	            imagecopy ($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left 
	            imagecopymerge ($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right 
	            imagecopymerge ($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center 
	            imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h); 
	
	            imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up 
	            imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down 
	        } 
	    } 
	
	    if($threshold>0){ 
	        // Calculate the difference between the blurred pixels and the original 
	        // and set the pixels 
	        for ($x = 0; $x < $w-1; $x++)    { // each row
	            for ($y = 0; $y < $h; $y++)    { // each pixel 
	                     
	                $rgbOrig = ImageColorAt($img, $x, $y); 
	                $rOrig = (($rgbOrig >> 16) & 0xFF); 
	                $gOrig = (($rgbOrig >> 8) & 0xFF); 
	                $bOrig = ($rgbOrig & 0xFF); 
	                 
	                $rgbBlur = ImageColorAt($imgBlur, $x, $y); 
	                 
	                $rBlur = (($rgbBlur >> 16) & 0xFF); 
	                $gBlur = (($rgbBlur >> 8) & 0xFF); 
	                $bBlur = ($rgbBlur & 0xFF); 
	                 
	                // When the masked pixels differ less from the original 
	                // than the threshold specifies, they are set to their original value. 
	                $rNew = (abs($rOrig - $rBlur) >= $threshold)  
	                    ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))  
	                    : $rOrig; 
	                $gNew = (abs($gOrig - $gBlur) >= $threshold)  
	                    ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))  
	                    : $gOrig; 
	                $bNew = (abs($bOrig - $bBlur) >= $threshold)  
	                    ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))  
	                    : $bOrig; 
	                 
	                if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) { 
	                        $pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew); 
	                        ImageSetPixel($img, $x, $y, $pixCol); 
	                    } 
	            } 
	        } 
	    } 
	    else{ 
	        for ($x = 0; $x < $w; $x++)    { // each row 
	            for ($y = 0; $y < $h; $y++)    { // each pixel 
	                $rgbOrig = ImageColorAt($img, $x, $y); 
	                $rOrig = (($rgbOrig >> 16) & 0xFF); 
	                $gOrig = (($rgbOrig >> 8) & 0xFF); 
	                $bOrig = ($rgbOrig & 0xFF); 
	                 
	                $rgbBlur = ImageColorAt($imgBlur, $x, $y); 
	                 
	                $rBlur = (($rgbBlur >> 16) & 0xFF); 
	                $gBlur = (($rgbBlur >> 8) & 0xFF); 
	                $bBlur = ($rgbBlur & 0xFF); 
	                 
	                $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig; 
	                    if($rNew>255){$rNew=255;} 
	                    elseif($rNew<0){$rNew=0;} 
	                $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig; 
	                    if($gNew>255){$gNew=255;} 
	                    elseif($gNew<0){$gNew=0;} 
	                $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig; 
	                    if($bNew>255){$bNew=255;} 
	                    elseif($bNew<0){$bNew=0;} 
	                $rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew; 
	                    ImageSetPixel($img, $x, $y, $rgbNew); 
	            } 
	        } 
	    } 
	    imagedestroy($imgCanvas); 
	    imagedestroy($imgBlur); 
	     
	    return $img; 
	}


	/**
	 * Check if enough memory will be available to process an Image create
	 * If imagecreatetruecolor function exists, it will be takken in account for the memory size
	 *
	 * @param $source_path
	 * @param $dest_width
	 * @param $dest_height
	 *
	 * @return bool
	 */
	function _checkMemoryBeforeImageCreate($source_path, $dest_width, $dest_height)
	{
		if (empty($this->full_src_path))
			return FALSE;

		// check if imagecreatetruecolor available
		$truecolor = 0;
		if (function_exists('imagecreatetruecolor'))
			$truecolor = 1;

		// Memory limit from php.ini config file in bytes
		$ini_memory_limit = intval(substr(ini_get('memory_limit'), 0, -1)) * 1024 * 1024;

		// First, check the source
		$size = getimagesize($this->full_src_path);
		$picture_need = $size[0]*$size[1]*(2.2+($truecolor*3)) + memory_get_usage();

		if ($picture_need > $ini_memory_limit)
		{
			log_message('error', 'Memory need : ' . $picture_need . '. Src : ' . $source_path);
			return false;
		}

		// Then, check the destination memory need, including the current used memory, in bytes
		$picture_need = $dest_width*$dest_height*(2.2+($truecolor*3)) + memory_get_usage();

		if ($picture_need > $ini_memory_limit)
		{
			log_message('error', 'Memory need : ' . $picture_need . '. Src : ' . $source_path);
			return false;
		}

		return true;
	}
}
