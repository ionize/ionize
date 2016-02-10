<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Memory
{

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
	}

	public function get_usage()
	{
		return $this->_convert(memory_get_usage(true));
	}


	private function _convert($size)
	{
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
}