<?php
/**
 * Image - Provides an Interface to the GD-Library for image manipulation
 *
 *
 * @license MIT-style License
 * @author Christoph Pojer <christoph.pojer@gmail.com>
 * @author Additions: Fabian Vogelsteller <fabian.vogelsteller@gmail.com>
 * @author Additions: Ger Hobbelt <ger@hobbelt.com>
 *
 * @link http://www.bin-co.com/php/scripts/classes/gd_image/ Based on work by "Binny V A"
 *
 * @version 1.12
 * Changelog
 *    - 1.12 added memory usage guestimator to warn you when attempting to process overlarge images which will silently but fataly crash PHP
 *    - 1.11 fixed $ratio in resize when both values are given
 *    - 1.1 add real resizing, with comparison of ratio
 *    - 1.01 small fixes in process method and add set memory limit to a higher value
 */

// memory_limit setting, in Megabytes; increase when Image class reports too often the images don't fit in memory.
// Origin : 64
define('IMAGE_PROCESSING_MEMORY_MAX_USAGE', 256);

class Image {
	/**
	 * The path to the image file
	 *
	 * @var string
	 */
	private $file;
	/**
	 * The image resource
	 *
	 * @var resource
	 */
	private $image;
	/**
	 * Metadata regarding the image
	 *
	 * @var array
	 */
	private $meta;
	/**
	 * Flags whether the image has been manipulated by this instance in any way and has not yet been saved to disk.
	 */
	private $dirty;

	/**
	 * @param string $file The path to the image file
	 */
	public function __construct($file)
	{
		$this->dirty = false;

		$this->meta = self::checkFileForProcessing($file);

		// only set the new memory limit of IMAGE_PROCESSING_MEMORY_MAX_USAGE MB when the configured one is smaller:
		if ($this->meta['fileinfo']['memory_limit'] < IMAGE_PROCESSING_MEMORY_MAX_USAGE * 1024 * 1024)
		{
			ini_set('memory_limit', IMAGE_PROCESSING_MEMORY_MAX_USAGE . 'M'); //  handle large images
		}

		$this->file = $file;

		if($this->meta['ext'] != 'jpeg')
		{
			$this->image = $this->create();

			$fn = 'imagecreatefrom'.$this->meta['ext'];
			$original = false;
			if (function_exists($fn))
			{
				$original = @$fn($file);
			}
			if (!$original) throw new Exception('imagecreate_failed:' . $fn);

			if (!@imagecopyresampled($this->image, $original, 0, 0, 0, 0, $this->meta['width'], $this->meta['height'], $this->meta['width'], $this->meta['height']))
				throw new Exception('cvt2truecolor_failed:' . $this->meta['width'] . ' x ' . $this->meta['height']);
			imagedestroy($original);
			unset($original);
		}
		else
		{
			ini_set('gd.jpeg_ignore_warning', 1);
			$this->image = @imagecreatefromjpeg($file);
			if (!$this->image) throw new Exception('imagecreate_failed:imagecreatefromjpeg');
		}
	}

	public function __destruct(){
		if(!empty($this->image)) imagedestroy($this->image);
		unset($this->image);
	}

	/**
	 * Return an array of supported extensions (rather: the second parts of the mime types!)
	 *
	 * A type is listed as 'supported' when it can be READ.
	 */
	public static function getSupportedTypes()
	{
		static $supported_types;

		if (empty($supported_types))
		{
			$gdi = gd_info();

			$supported_types = array();
			if (!empty($gdi['GIF Read Support']))
				$supported_types[] = 'gif';
			if (!empty($gdi['PNG Support']))
				$supported_types[] = 'png';
			if (!empty($gdi['JPEG Support']) || !empty($gdi['JPG Support']) /* pre 5.3.0 */ )
				$supported_types[] = 'jpeg';
			if (!empty($gdi['WBMP Support']))
				$supported_types[] = 'wbmp';
			if (!empty($gdi['XPM Support']))
				$supported_types[] = 'xpm';
			if (!empty($gdi['XBM Support']))
				$supported_types[] = 'xbm';
			$supported_types[] = 'bmp';
		}
		return $supported_types;
	}

	/**
	 * Guestimates how much RAM memory must be available to be able to process the given image file.
	 *
	 * @return an array with key,value pairs: 'needed' specifies the guestimated minimum amount of free
	 *         memory required for the given file, 'memory_limit' is an integer value representing the total
	 *         amount of bytes reserved for PHP script, while 'will_fit' is a boolean which indicates whether
	 *         the given image file could potentially be loaded and processed without causing fatal out-of-memory
	 *         errors.
	 *         The directory separator and path-corrected filespec is returned in the 'path' value.
	 *
	 * @note The given image file must exist on disk; if it does not, 'needed' and 'will_fit' keys will not
	 *       be present in the returned array.
	 */
	public static function guestimateRequiredMemorySpace($file)
	{
		$val = trim(ini_get('memory_limit'));

		$last = strtoupper(substr($val, -1, 1));
		$val = floatval($val); // discards the KMG postfix, allow $val to carry values beyond 2..4G
		switch($last)
		{
		// The 'G' modifier is available since PHP 5.1.0
		case 'G':
			$val *= 1024.0;
		case 'M':
			$val *= 1024.0;
		case 'K':
			$val *= 1024.0;
			break;
		}
		$limit = $val;

		$in_use = (function_exists('memory_get_usage') ? memory_get_usage() : 1000000 /* take a wild guess, er, excuse me, 'apply a heuristic' */ );

		$rv = array(
			'memory_limit' => $limit,
			'mem_in_use' => $in_use,
			'path' => $file
			);
		if(file_exists($file))
		{
			$raw_size = @filesize($file);
			$rv['filesize'] = $raw_size;

			$img = @getimagesize($file, $info_ex);
			if ($img)
			{
				$width = $img[0];
				$height = $img[1];
				$rv['imageinfo'] = $img;
				// $rv['imageinfo_extra'] = $info_ex;

				// assume RGBA8, i.e. 4 bytes per pixel
				// ... having had a good look with memory_get_usage() and memory_get_peak_usage(), it turns out we need
				// a 'fudge factor' a.k.a. heuristic as the '4 bytes per pixel' estimate is off by quite a bit (if we have
				// to believe the numbers following a single GD image load operation):
				$needed = 4.0 * $width * $height;
				$needed *= 34.0 / 27.0;
				$rv['needed'] = $needed;

				// since many operations require a source and destination buffer, that'll be 2 of them buffers, thank you very much:
				// ... however, having had a good look with memory_get_usage() and memory_get_peak_usage(), it turns out
				// we need about triple!
				$will_eat = $needed * 2.8;
				// ^^^ factor = 2.8 : for very large images the estimation error is now ~ +1..8% too pessimistic. Soit!
				//     (tested with PNG images which consumed up to 475MB RAM to have their thumbnail created. This took a few
				//      seconds per image, so you might ask yourself if being able to serve such memory megalodons would be,
				//      ah, desirable, when considered from this perspective.)

				// and 'worst case' (ahem) we've got the file itself loaded in memory as well (on initial load and later save):
				// ... but this is more than covered by the 'triple charge' already, so we ditch this one from the heuristics.
				if (0) $will_eat += $raw_size;

				// interestingly, JPEG files only appear to require about half that space required by PNG resize processes...
				if (!empty($img['mime']) && $img['mime'] == 'image/jpeg')
				{
					$will_eat /= 2.0;
				}

				$rv['usage_guestimate'] = $will_eat;

				// now we know what we about need for this bugger, see if we got enough:
				$does_fit = ($limit - $in_use > $will_eat);
				$rv['usage_min_advised'] = $will_eat + $in_use;
				$rv['will_fit'] = $does_fit;
			}
			else
			{
				// else: this is not a valid image file!
				$rv['not_an_image_file'] = true;
			}
		}
		else
		{
			// else: this file does not exist!
			$rv['not_an_image_file'] = true;
		}

		return $rv;
	}

	/**
	 * Check whether the given file is really an image file and whether it can be processed by our Image class, given the PHP
	 * memory restrictions.
	 *
	 * Return the meta data array when the expectation is that the given file can be processed; throw an exception otherwise.
	 */
	public static function checkFileForProcessing($file)
	{
		$finfo = self::guestimateRequiredMemorySpace($file);

		if (!empty($finfo['not_an_image_file']))
			throw new Exception('no_imageinfo');

		// is it a valid file existing on disk?
		if (!isset($finfo['imageinfo']))
			throw new Exception('no_imageinfo');

		// only set the new memory limit of IMAGE_PROCESSING_MEMORY_MAX_USAGE MB when the configured one is smaller:
		if ($finfo['memory_limit'] < IMAGE_PROCESSING_MEMORY_MAX_USAGE * 1024 * 1024)
		{
			// recalc the 'will_fit' indicator now:
			$finfo['will_fit'] = ($finfo['usage_min_advised'] < IMAGE_PROCESSING_MEMORY_MAX_USAGE * 1024 * 1024);
		}

		$img = $finfo['imageinfo'];

		// and will it fit in available memory if we go and load the bugger?
		if (!$finfo['will_fit'])
			throw new Exception('img_will_not_fit:' . ceil($finfo['usage_min_advised'] / 1E6) . ' MByte');

		$explarr = explode('/', $img['mime']); // make sure the end() call doesn't throw an error next in E_STRICT mode:
		$ext_from_mime = end($explarr);
		$meta = array(
			'width' => $img[0],
			'height' => $img[1],
			'mime' => $img['mime'],
			'ext' => $ext_from_mime,
			'fileinfo' => $finfo
		);

		if($meta['ext'] == 'jpg')
			$meta['ext'] = 'jpeg';
		else if($meta['ext'] == 'bmp')
			$meta['ext'] = 'bmp';
		else if($meta['ext'] == 'x-ms-bmp')
			$meta['ext'] = 'bmp';
		if(!in_array($meta['ext'], self::getSupportedTypes()))
			throw new Exception('unsupported_imgfmt:' . $meta['ext']);

		return $meta;
	}

	/**
	 * Calculate the resize dimensions of an image, given the original dimensions and size limits
	 *
	 * @param int $orig_x the original's width
	 * @param int $orig_y the original's height
	 * @param int $x the maximum width after resizing has been done
	 * @param int $y the maximum height after resizing has been done
	 * @param bool $ratio set to FALSE if the image ratio is solely to be determined
	 *                    by the $x and $y parameter values; when TRUE (the default)
	 *                    the resize operation will keep the image aspect ratio intact
	 * @param bool $resizeWhenSmaller if FALSE the images will not be resized when
	 *                    already smaller, if TRUE the images will always be resized
	 * @return array with 'width', 'height' and 'must_resize' component values on success; FALSE on error
	 */
	public static function calculate_resize_dimensions($orig_x, $orig_y, $x = null, $y = null, $ratio = true, $resizeWhenSmaller = false)
	{
		if(empty($orig_x) || empty($orig_y) || (empty($x) && empty($y))) return false;

		$xStart = $x;
		$yStart = $y;
		$ratioX = $orig_x / $orig_y;
		$ratioY = $orig_y / $orig_x;
		$ratio |= (empty($y) || empty($x)); // keep ratio when only width OR height is set
		//echo 'ALLOWED: <br>'.$xStart.'x'."<br>".$yStart.'y'."<br>---------------<br>";
		// ->> keep the RATIO
		if($ratio) {
			//echo 'BEGINN: <br>'.$orig_x.'x'."<br>".$orig_y.'y'."<br><br>";
			// -> align to WIDTH
			if(!empty($x) && ($x < $orig_x || $resizeWhenSmaller))
				$y = $x / $ratioX;
			// -> align to HEIGHT
			elseif(!empty($y) && ($y < $orig_y || $resizeWhenSmaller))
				$x = $y / $ratioY;
			else {
				$y = $orig_y;
				$x = $orig_x;
			}
			//echo 'BET: <br>'.$x.'x'."<br>".$y.'y'."<br><br>";
			// ->> align to WIDTH AND HEIGHT
			if((!empty($yStart) && $y > $yStart) || (!empty($xStart) && $x > $xStart)) {
				if($y > $yStart) {
					$y = $yStart;
					$x = $y / $ratioY;
				} elseif($x > $xStart) {
					$x = $xStart;
					$y = $x / $ratioX;
				}
			}
		}
		// else: ->> DONT keep the RATIO

		$x = round($x);
		$y = round($y);

		//echo 'END: <br>'.$x.'x'."<br>".$y.'y'."<br><br>";
		$rv = array(
			'width' => $x,
			'height' => $y,
			'must_resize' => false
		);
		// speedup? only do the resize operation when it must happen:
		if ($x != $orig_x || $y != $orig_y)
		{
			$rv['must_resize'] = true;
		}
		return $rv;
	}

	/**
	 * Returns the size of the image
	 *
	 * @return array
	 */
	public function getSize(){
		return array(
			'width' => $this->meta['width'],
			'height' => $this->meta['height'],
		);
	}


	/**
	 * Returns a copy of the meta information of the image
	 *
	 * @return array
	 */
	public function getMetaInfo(){
		return array_merge(array(), (is_array($this->meta) ? $this->meta : array()));
	}


	/**
	 * Returns TRUE when the image data have been altered by this instance's operations, FALSE when the content has not (yet) been touched.
	 *
	 * @return boolean
	 */
	public function isDirty(){
		return $this->dirty;
	}


	/**
	 * Creates a new, empty image with the desired size
	 *
	 * @param int $x
	 * @param int $y
	 * @param string $ext
	 * @return resource GD image handle on success; throws an exception on failure
	 */
	private function create($x = null, $y = null, $ext = null){
		if(!$x) $x = $this->meta['width'];
		if(!$y) $y = $this->meta['height'];

		$image = @imagecreatetruecolor($x, $y);
		if (!$image) throw new Exception('imagecreatetruecolor_failed');
		if(!$ext) $ext = $this->meta['ext'];
		if($ext == 'png'){
			if (!@imagealphablending($image, false))
				throw new Exception('imagealphablending_failed');
			$alpha = @imagecolorallocatealpha($image, 0, 0, 0, 127);
			if (!$alpha) throw new Exception('imageallocalpha50pctgrey_failed');
			imagefilledrectangle($image, 0, 0, $x, $y, $alpha);
		}

		return $image;
	}

	/**
	 * Replaces the image resource with the given parameter
	 *
	 * @param resource $new
	 */
	private function set($new){
		if(!empty($this->image)) imagedestroy($this->image);
			$this->dirty = true;
			$this->image = $new;

			$this->meta['width'] = imagesx($this->image);
			$this->meta['height'] = imagesy($this->image);
	}

	/**
	 * Returns the path to the image file
	 *
	 * @return string
	 */
	public function getImagePath(){
		return $this->file;
	}

	/**
	 * Returns the resource of the image file
	 *
	 * @return resource
	 */
	public function getResource(){
		return $this->image;
	}

	/**
	 * Rotates the image by the given angle
	 *
	 * @param int $angle
	 * @param array $bgcolor An indexed array with red/green/blue/alpha values
	 * @return resource Image resource on success; throws an exception on failure
	 */
	public function rotate($angle, $bgcolor = null){
		if(empty($this->image) || !$angle || $angle>=360) return $this;

		$alpha = (is_array($bgcolor) ? @imagecolorallocatealpha($this->image, $bgcolor[0], $bgcolor[1], $bgcolor[2], !empty($bgcolor[3]) ? $bgcolor[3] : null) : $bgcolor);
		if (!$alpha) throw new Exception('imagecolorallocatealpha_failed');
		$img = @imagerotate($this->image, $angle, $alpha);
		if (!$img) throw new Exception('imagerotate_failed');
		$this->set($img);
		unset($img);

		return $this;
	}

	/**
	 * Resizes the image to the given size, automatically calculates
	 * the new ratio if parameter {@link $ratio} is set to true
	 *
	 * @param int $x the maximum width after resizing has been done
	 * @param int $y the maximum height after resizing has been done
	 * @param bool $ratio set to FALSE if the image ratio is solely to be determined
	 *                    by the $x and $y parameter values; when TRUE (the default)
	 *                    the resize operation will keep the image aspect ratio intact
	 * @param bool $resizeWhenSmaller if FALSE the images will not be resized when
	 *                    already smaller, if TRUE the images will always be resized
	 * @return resource Image resource on success; throws an exception on failure
	 */
	public function resize($x = null, $y = null, $ratio = true, $resizeWhenSmaller = false)
	{
		if(empty($this->image) || (empty($x) && empty($y)))
		{
			throw new Exception('resize_inerr');
		}

		$dims = Image::calculate_resize_dimensions($this->meta['width'], $this->meta['height'], $x, $y, $ratio, $resizeWhenSmaller);
		if ($dims === false)
		{
			throw new Exception('resize_inerr:' . $this->meta['width'] . ' x ' . $this->meta['height']);
		}

		// speedup? only do the resize operation when it must happen:
		if ($dims['must_resize'])
		{
			$new = $this->create($dims['width'], $dims['height']);
			if(@imagecopyresampled($new, $this->image, 0, 0, 0, 0, $dims['width'], $dims['height'], $this->meta['width'], $this->meta['height'])) {
				$this->set($new);
				unset($new);
				return $this;
			}
			unset($new);
			throw new Exception('imagecopyresampled_failed:' . $this->meta['width'] . ' x ' . $this->meta['height']);
		}
		else
		{
			return $this;
		}
	}

	/**
	 * Crops the image. The values are given like margin/padding values in css
	 *
	 * <b>Example</b>
	 * <ul>
	 * <li>crop(10) - Crops by 10px on all sides</li>
	 * <li>crop(10, 5) - Crops by 10px on top and bottom and by 5px on left and right sides</li>
	 * <li>crop(10, 5, 5) - Crops by 10px on top and by 5px on left, right and bottom sides</li>
	 * <li>crop(10, 5, 3, 2) - Crops by 10px on top, 5px by right, 3px by bottom and 2px by left sides</li>
	 * </ul>
	 *
	 * @param int $top
	 * @param int $right
	 * @param int $bottom
	 * @param int $left
	 * @return Image
	 */
	public function crop($top, $right = null, $bottom = null, $left = null){
		if(empty($this->image)) return $this;

		if(!is_numeric($right) && !is_numeric($bottom) && !is_numeric($left))
			$right = $bottom = $left = $top;

		if(!is_numeric($bottom) && !is_numeric($left)){
			$bottom = $top;
			$left = $right;
		}

		if(!is_numeric($left))
			$left = $right;

		$x = $this->meta['width']-$left-$right;
		$y = $this->meta['height']-$top-$bottom;

		if($x<0 || $y<0) return $this;

		$new = $this->create($x, $y);
		if (!@imagecopy($new, $this->image, 0, 0, $left, $top, $x, $y))
			throw new Exception('imagecopy_failed');

		$this->set($new);
		unset($new);

		return $this;
	}

	/**
	 * Flips the image horizontally or vertically. To Flip both copy multiple single pixel strips around instead
	 * of just using ->rotate(180): no image distortion this way.
	 *
	 * @see Image::rotate()
	 * @param string $type Either horizontal or vertical
	 * @return Image
	 */
	public function flip($type){
		if(empty($this->image) || !in_array($type, array('horizontal', 'vertical'))) return $this;

		$new = $this->create();

		if($type=='horizontal')
		{
			for($x=0;$x<$this->meta['width'];$x++)
			{
				if (!@imagecopy($new, $this->image, $this->meta['width']-$x-1, 0, $x, 0, 1, $this->meta['height']))
					throw new Exception('imageflip_failed');
			}
		}
		elseif($type=='vertical')
		{
			for($y=0;$y<$this->meta['height'];$y++)
			{
				if (!@imagecopy($new, $this->image, 0, $this->meta['height']-$y-1, 0, $y, $this->meta['width'], 1))
					throw new Exception('imageflip_failed');
			}
		}

		$this->set($new);
		unset($new);

		return $this;
	}

	/**
	 * Stores the image in the desired directory or overwrite the old one
	 *
	 * @param string $ext
	 * @param string $file
	 * @param int $quality the amount of lossy compression to apply to the saved file
	 * @param boolean $store_original_if_unaltered (default: FALSE) set to TRUE if you want to copy the
	 *                                             original instead of saving the loaded copy when no
	 *                                             edits to the image have occurred. (set to TRUE when
	 *                                             you like to keep animated GIFs intact when they have
	 *                                             not been cropped, rescaled, etc., for instance)
	 *
	 * @return Image object
	 */
	public function process($ext = null, $file = null, $quality = 100, $store_original_if_unaltered = false){
		if(empty($this->image)) return $this;

		if(!$ext) $ext = $this->meta['ext'];
		$ext = strtolower($ext);

		if($ext=='jpg') $ext = 'jpeg';
		else if($ext=='png') imagesavealpha($this->image, true);

		if($file == null)
			$file = $this->file;
		if(!$file) throw new Exception('process_nofile');
		if(!is_dir(dirname($file))) throw new Exception('process_nodir');
		if ($store_original_if_unaltered && !$this->isDirty() && $ext == $this->meta['ext'])
		{
			// copy original instead of saving the internal representation:
			$rv = true;
			if ($file != $this->file)
			{
				$rv = @copy($this->file, $file);
			}
		}
		else
		{
			$rv = false;
			$fn = 'image'.$ext;
			if (function_exists($fn))
			{
				if($ext == 'jpeg')
					$rv = @$fn($this->image, $file, $quality);
				elseif($ext == 'png')
					$rv = @$fn($this->image, $file, 9); // PNG is lossless: always maximum compression!
				else
					$rv = @$fn($this->image, $file);
			}
		}
		if (!$rv)
			throw new Exception($fn . '_failed');

		// If there is a new filename change the internal name too
		$this->file = $file;

		return $this;
	}

	/**
	 * Saves the image to the given path
	 *
	 * @param string $file Leave empty to replace the original file
	 * @param int $quality the amount of lossy compression to apply to the saved file
	 * @param boolean $store_original_if_unaltered (default: FALSE) set to TRUE if you want to copy the
	 *                                             original instead of saving the loaded copy when no
	 *                                             edits to the image have occurred. (set to TRUE when
	 *                                             you like to keep animated GIFs intact when they have
	 *                                             not been cropped, rescaled, etc., for instance)
	 *
	 * @return Image
	 */
	public function save($file = null, $quality = 100, $store_original_if_unaltered = false){
		if(empty($this->image)) return $this;

		if(!$file) $file = $this->file;

		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if(!$ext){
			$file .= '.'.$this->meta['ext'];
			$ext = $this->meta['ext'];
		}

		if($ext=='jpg') $ext = 'jpeg';

		return $this->process($ext, $file, $quality, $store_original_if_unaltered);
	}

	/**
	 * Outputs the manipulated image. Implicitly overwrites the old one on disk.
	 *
	 * @return Image
	 */
	public function show(){
		if(empty($this->image)) return $this;

		header('Content-type: '.$this->meta['mime']);
		return $this->process();
	}
}






if (!function_exists('imagecreatefrombmp'))
{
	/**
	 * http://nl3.php.net/manual/en/function.imagecreatefromwbmp.php#86214
	 */
	function imagecreatefrombmp($filepath)
	{
		// Load the image into a string
		$filesize = @filesize($filepath);
		if ($filesize < 108 + 4)
			return false;

		$read = file_get_contents($filepath);
		if ($file === false)
			return false;

		$temp = unpack("H*",$read);
		unset($read);               // reduce memory consumption
		$hex = $temp[1];
		unset($temp);
		$header = substr($hex, 0, 108);

		// Process the header
		// Structure: http://www.fastgraph.com/help/bmp_header_format.html
		if (substr($header, 0, 4) == '424d')
		{
			// Cut it in parts of 2 bytes
			$header_parts = str_split($header, 2);

			// Get the width        4 bytes
			$width = hexdec($header_parts[19] . $header_parts[18]);

			// Get the height        4 bytes
			$height = hexdec($header_parts[23] . $header_parts[22]);

			// Unset the header params
			unset($header_parts);
		}

		// Define starting X and Y
		$x = 0;
		$y = 1;

		// Create newimage
		$image = imagecreatetruecolor($width, $height);
		if ($image === false)
			return $image;

		// Grab the body from the image
		$body = substr($hex, 108);
		unset($hex);

		// Calculate if padding at the end-line is needed
		// Divided by two to keep overview.
		// 1 byte = 2 HEX-chars
		$body_size = strlen($body) / 2;
		$header_size = $width * $height;

		// Use end-line padding? Only when needed
		$usePadding = ($body_size > $header_size * 3 + 4);

		// Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
		// Calculate the next DWORD-position in the body
		for ($i = 0; $i < $body_size; $i += 3)
		{
			// Calculate line-ending and padding
			if ($x >= $width)
			{
				// If padding needed, ignore image-padding
				// Shift i to the ending of the current 32-bit-block
				if ($usePadding)
					$i += $width % 4;

				// Reset horizontal position
				$x = 0;

				// Raise the height-position (bottom-up)
				$y++;

				// Reached the image-height? Break the for-loop
				if ($y > $height)
					break;
			}

			// Calculation of the RGB-pixel (defined as BGR in image-data)
			// Define $i_pos as absolute position in the body
			$i_pos = $i * 2;
			$r = hexdec($body[$i_pos+4] . $body[$i_pos+5]);
			$g = hexdec($body[$i_pos+2] . $body[$i_pos+3]);
			$b = hexdec($body[$i_pos] . $body[$i_pos+1]);

			// Calculate and draw the pixel
			$color = imagecolorallocate($image, $r, $g, $b);
			if ($color === false)
			{
				imagedestroy($image);
				return false;
			}
			imagesetpixel($image, $x, $height - $y, $color);

			// Raise the horizontal position
			$x++;
		}

		// Unset the body / free the memory
		unset($body);

		// Return image-object
		return $image;
	}
}
