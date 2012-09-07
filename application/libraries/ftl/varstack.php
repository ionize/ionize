<?php 
/*
 * Created on 2009 Jan 03
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

/**
 * An object with simulated properties, if property doesn't exists, it calls
 * the enclosed object.
 * 
 * @package FTL_Parser
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @copyright Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class FTL_VarStack
{
	/**
	 * The data stored in this object.
	 * 
	 * @var array
	 */
	public $hash;
	
	/**
	 * Parent object.
	 * 
	 * @var FTL_VarStack|null
	 */
	protected $object;
	
	function __construct($object = null)
	{
		$this->object = $object;
		$this->hash = array();
	}
	
	function &__get($property)
	{
		if(array_key_exists($property, $this->hash))
		{
			return $this->hash[$property];
		}
		
		if($this->object == null)
		{
			$null = null;
			return $null;
		}
		
		return $this->object->__get($property);
	}
	
	function __set($property, $value)
	{
		$this->hash[$property] = $value;
	}
	
	function __isset($property)
	{
		return array_key_exists($property, $this->hash) ? true : isset($this->object->$property);
	}
	
	function __unset($property)
	{
		unset($this->hash[$property]);
	}
	
	function count_struct()
	{
		if(isset($this->object) &&
					$this->object instanceof FTL_VarStack){
			return $this->object->count_struct() + 1;
		}
		
		return 1;
	}
}

/* End of file varstack.php */
/* Location: ./application/libraries/ftl/varstack.php */