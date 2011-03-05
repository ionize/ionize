<?php
/**
 * Ionize Search module tags
 *
 * This class define the Demo module tags
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.6
 *
 *
 */


/**
 * Demo Module's TagManager 
 *
 */
class Demo_Tags
{
	
	/**
	 * Base search module tag
	 * The index function of this class refers to the <ion:search /> tag
	 * In other words, this function makes the <ion:search /> tag available as main module parent tag
	 * for all other tags defined in this class.
	 *
	 * @usage	<ion:demo >
	 *			...
	 *			</ion:demo>
	 *
	 */
	public static function index(FTL_Binding $tag)
	{
		$str = $tag->expand();
		
		return $str;
	}


	// ------------------------------------------------------------------------

	public static function name(FTL_Binding $tag)
	{
		return MODPATH;

	}




}
