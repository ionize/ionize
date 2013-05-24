<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Partikule Studio
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.92
 */

/**
 * Ionize URI Context Class
 *
 * Manage the URI context 
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	URI Context Libraries
 *
 */

/*

One context has :
- name
- URL
- URI segment position
- URI segmeent value
- active : true / false


Exemple of use:

Called URL : 	www.domain.tdl/bla/category/parent/2

$contexts = array (
	
	[0] => array(
		
	)

)



*/


class Uri_context
{
	
	/* Array of URI context
	 *
	 */
	protected static $contexts = array();

	

	public $ci;
	

	public static function init_context($config)
	{
		self::$contexts = $config;
	}
	
	


}