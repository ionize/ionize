<?php
/**
 * Image - Provides an Interface to the GD-Library for image manipulation
 *
 *
 * @license MIT-style License
 * @author Christoph Pojer <christoph.pojer@gmail.com>
 * @author Additions: Fabian Vogelsteller <fabian.vogelsteller@gmail.com>
 *
 * @link http://www.bin-co.com/php/scripts/classes/gd_image/ Based on work by "Binny V A"
 *
 * @version 1.11
 * Changlog<br>
 *    - 1.11 fixed $ratio in resize when both values are given
 *    - 1.1 add real resizing, with comparison of ratio
 *    - 1.01 small fixes in process method and add set memory limit to a higher value
 */



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
	 * @param string $file The path to the image file
	 */
	public function __construct($file){

		$finfo = self::guestimateRequiredMemorySpace($file);
		$file = $finfo['path'];

		// is it a valid file existing on disk?
		if (!isset($finfo['imageinfo']))
			throw new Exception('no_imageinfo');

		// only set the new memory limit of 64MB when the configured one is smaller:
		if ($finfo['memory_limit'] < 64 * 1024 * 1024)
		{
			ini_set('memory_limit', '64M'); //  handle large images
		}

		$this->file = $file;
		$img = $finfo['imageinfo'];

		// and will it fit in available memory if we go and load the bugger?
		if (!$finfo['will_fit'])
			throw new Exception('img_will_not_fit:' . round(($finfo['usage_min_advised'] + 9.9E5) / 1E6) . ' MByte');

		$explarr = explode('/', $img['mime']); // make sure the end() call doesn't throw an error next in E_STRICT mode:
		$ext_from_mime = end($explarr);
		$this->meta = array(
			'width' => $img[0],
			'height' => $img[1],
			'mime' => $img['mime'],
			'ext' => $ext_from_mime,
		);

		if($this->meta['ext']=='jpg')
			$this->meta['ext'] = 'jpeg';
		if(!in_array($this->meta['ext'], array('gif', 'png', 'jpeg')))
			throw new Exception('unsupported_imgfmt:' . $this->meta['ext']);

		if(in_array($this->meta['ext'], array('gif', 'png'))){
			$this->image = $this->create();

			$fn = 'imagecreatefrom'.$this->meta['ext'];
			$original = @$fn($file);
			if (!$original) throw new Exception('imagecreate_failed');

			if (!@imagecopyresampled($this->image, $original, 0, 0, 0, 0, $this->meta['width'], $this->meta['height'], $this->meta['width'], $this->meta['height']))
				throw new Exception('cvt2truecolor_failed:' . $this->meta['width'] . ' x ' . $this->meta['height']);
			imagedestroy($original);
			unset($original);
		} else {
			$this->image = @imagecreatefromjpeg($file);
			if (!$this->image) throw new Exception('imagecreate_failed');
		}
	}

	public function __destruct(){
		if(!empty($this->image)) imagedestroy($this->image);
		unset($this->image);
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


		$file = str_replace('\\','/',$file);
		$file = preg_replace('#/+#','/',$file);
		$file = str_replace($_SERVER['DOCUMENT_ROOT'],'',$file);
		$file = $_SERVER['DOCUMENT_ROOT'].$file;
		$file = str_replace('\\','/',$file);
		$file = preg_replace('#/+#','/',$file);
		$file = realpath($file);
		$file = str_replace('\\','/',$file);

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
				$rv['imageinfo_extra'] = $info_ex;

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
	 * Creates a new, empty image with the desired size
	 *
	 * @param int $x
	 * @param int $y
	 * @param string $ext
	 * @return resource
	 */
	private function create($x = null, $y = null, $ext = null){
		if(!$x) $x = $this->meta['width'];
		if(!$y) $y = $this->meta['height'];

		$image = @imagecreatetruecolor($x, $y);
		if (!$image) throw new Exception('imagecreatetruecolor_failed');
		if(!$ext) $ext = $this->meta['ext'];
		if($ext=='png'){
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
	 * @return Image
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
	public function resize($x = null, $y = null, $ratio = true, $resizeWhenSmaller = true){
		if(empty($this->image) || (empty($x) && empty($y))) return false;

		$xStart = $x;
	$yStart = $y;
	$ratioX = $this->meta['width'] / $this->meta['height'];
	$ratioY = $this->meta['height'] / $this->meta['width'];
	//echo 'ALLOWED: <br>'.$xStart.'x'."<br>".$yStart.'y'."<br>---------------<br>";
	// ->> keep the RATIO
	if($ratio) {
	  //echo 'BEGINN: <br>'.$this->meta['width'].'x'."<br>".$this->meta['height'].'y'."<br><br>";
		// -> align to WIDTH
		if(!empty($x) && ($x < $this->meta['width'] || $resizeWhenSmaller))
		  $y = $x / $ratioX;
		// -> align to HEIGHT
		elseif(!empty($y) && ($y < $this->meta['height'] || $resizeWhenSmaller))
		  $x = $y / $ratioY;
		else {
		  $y = $this->meta['height'];
		  $x = $this->meta['width'];
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
	// ->> DONT keep the RATIO (but keep ration when only width OR height is set)
	} else {
	  // RATIO X
	  if(!empty($y) && empty($x) && ($y < $this->meta['height'] || $resizeWhenSmaller))
		$x = $y / $ratioX;
	  // RATIO Y
	  elseif(empty($y) && !empty($x) && ($x < $this->meta['width'] || $resizeWhenSmaller))
		$y = $x / $ratioY;
	}
	$x = round($x);
	$y = round($y);

		//echo 'END: <br>'.$x.'x'."<br>".$y.'y'."<br><br>";

		// speedup? only do the resize operation when it must happen:
		if ($x != $this->meta['width'] || $y != $this->meta['height'])
		{
			$new = $this->create($x, $y);
			if(@imagecopyresampled($new, $this->image, 0, 0, 0, 0, $x, $y, $this->meta['width'], $this->meta['height'])) {
				$this->set($new);
				unset($new);
			}
			return $this;
		}
		else
		{
			throw new Exception('imagecopyresampled_failed');
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
	 * @return Image object
	 */
	public function process($ext = null, $file = null, $quality = 100){
		if(empty($this->image)) return $this;

		if(!$ext) $ext = $this->meta['ext'];
		if($ext=='jpg') $ext = 'jpeg';
		if($ext=='png') imagesavealpha($this->image, true);

		if($file == null)
		  $file = $this->file;
		if(!$file) throw new Exception('process_nofile');

		$fn = 'image'.$ext;
		if($ext == 'jpeg')
		  $rv = @$fn($this->image, $file, $quality);
		elseif($ext == 'png')
		  $rv = @$fn($this->image, $file, 9); // PNG is lossless: always maximum compression!
		else
		  $rv = @$fn($this->image, $file);
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
	 * @return Image
	 */
	public function save($file = null){
		if(empty($this->image)) return $this;

		if(!$file) $file = $this->file;

		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if(!$ext){
			$file .= '.'.$this->meta['ext'];
			$ext = $this->meta['ext'];
		}

		if($ext=='jpg') $ext = 'jpeg';

		if(!in_array($ext, array('png', 'jpeg', 'gif')))
			return $this;

		return $this->process($ext, $file);
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
