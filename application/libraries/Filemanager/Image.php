<?php
/**
 * Styx::Image - Provides an Interface to the GD-Library for image manipulation
 *
 * @package Styx
 * @subpackage Utility
 *
 * @license MIT-style License
 * @author Christoph Pojer <christoph.pojer@gmail.com>
 *
 * @link http://www.bin-co.com/php/scripts/classes/gd_image/ Based on work by "Binny V A"
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
		$file = realpath($file);
		if(!file_exists($file))
			return;
		
		$this->file = $file;
		$img = getimagesize($file);

		$this->meta = array(
			'width' => $img[0],
			'height' => $img[1],
			'mime' => $img['mime'],
			'ext' => end(explode('/', $img['mime'])),
		);
		if($this->meta['ext']=='jpg')
			$this->meta['ext'] = 'jpeg';
		
		if(!in_array($this->meta['ext'], array('gif', 'png', 'jpeg')))
			return;
		
		if(in_array($this->meta['ext'], array('gif', 'png'))){
			$this->image = $this->create();
			
			$fn = 'imagecreatefrom'.$this->meta['ext'];
			$original = $fn($file);
			imagecopyresampled($this->image, $original, 0, 0, 0, 0, $this->meta['width'], $this->meta['height'], $this->meta['width'], $this->meta['height']);
		}else{
			$this->image = imagecreatefromjpeg($file);
		}
	}
	
	public function __destruct(){
		if(!empty($this->image)) imagedestroy($this->image);
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
		
		$image = imagecreatetruecolor($x, $y);
		if(!$ext) $ext = $this->meta['ext'];
		if($ext=='png'){
			imagealphablending($image, false);
			imagefilledrectangle($image, 0, 0, $x, $y, imagecolorallocatealpha($image, 0, 0, 0, 127));
		}
		
		return $image;
	}
	
	/**
	 * Replaces the image resource with the given parameter
	 *
	 * @param resource $new
	 */
	private function set($new){
		imagedestroy($this->image);
		$this->image = $new;
		
		$this->meta['width'] = imagesx($this->image);
		$this->meta['height'] = imagesy($this->image);
	}
	
	/**
	 * Returns the path to the image file
	 *
	 * @return string
	 */
	public function getPathname(){
		return $this->file;
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
		
		$this->set(imagerotate($this->image, $angle, is_array($bgcolor) ? imagecolorallocatealpha($this->image, $bgcolor[0], $bgcolor[1], $bgcolor[2], !empty($bgcolor[3]) ? $bgcolor[3] : null) : $bgcolor));

		return $this;
	}
	
	/**
	 * Resizes the image to the given size, automatically calculates
	 * the new ratio if parameter {@link $ratio} is set to true
	 *
	 * @param int $x
	 * @param int $y
	 * @param bool $ratio
	 * @return Image
	 */
	public function resize($x = null, $y = null, $ratio = true){
		if(empty($this->image) || (!$x && !$y)) return $this;
		
		if(!$y) $y = $ratio ? $this->meta['height']*$x/$this->meta['width'] : $this->meta['height'];
		if(!$x) $x = $ratio ? $this->meta['width']*$y/$this->meta['height'] : $this->meta['width'];
		
		$new = $this->create($x, $y);
		imagecopyresampled($new, $this->image, 0, 0, 0, 0, $x, $y, $this->meta['width'], $this->meta['height']);
		$this->set($new);
		
		return $this;
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
		imagecopy($new, $this->image, 0, 0, $left, $top, $x, $y);
		$this->set($new);
		
		return $this;
	}
	
	/**
	 * Flips the image horizontally or vertically. To Flip both just use ->rotate(180)
	 *
	 * @see Image::rotate()
	 * @param string $type Either horizontal or vertical
	 * @return Image
	 */
	public function flip($type){
		if(empty($this->image) || !in_array($type, array('horizontal', 'vertical'))) return $this;
		
		$new = $this->create();
		
		if($type=='horizontal')
			for($x=0;$x<$this->meta['width'];$x++)
				imagecopy($new, $this->image, $this->meta['width']-$x-1, 0, $x, 0, 1, $this->meta['height']);
		elseif($type=='vertical')
			for($y=0;$y<$this->meta['height'];$y++)
				imagecopy($new, $this->image, 0, $this->meta['height']-$y-1, 0, $y, $this->meta['width'], 1);
		
		$this->set($new);
		
		return $this;
	}
	
	/**
	 * Stores the image in the desired directory or outputs it
	 *
	 * @param string $ext
	 * @param string $file
	 */
	private function process($ext = null, $file = null){
		if(!$ext) $ext = $this->meta['ext'];
		
		if($ext=='png') imagesavealpha($this->image, true);
		$fn = 'image'.$ext;
		$fn($this->image, $file);
		
		// If there is a new filename change the internal name too
		if($file) $this->file = $file;
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
		
		$this->process($ext, $file);
		
		return $this;
	}

	/**
	 * Outputs the manipulated image
	 *
	 * @return Image
	 */
	public function show(){
		if(empty($this->image)) return $this;
		
		header('Content-type: '.$this->meta['mime']);
		$this->process();
		
		return $this;
	}
	
}