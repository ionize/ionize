<?php
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
 * Archive TagManager
 *
 */
class TagManager_Authority extends TagManager
{
	/**
	 * @var array
	 */
	public static $tag_definitions = array
	(
		'authority' =>				'tag_authority',
		'authority:can' =>			'tag_authority_can',
	);


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_authority(FTL_Binding $tag)
	{
		return $tag->expand();
	}


	/**
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_authority_can(FTL_Binding $tag)
	{
		$action = $tag->getAttribute('action');
		$resource = $tag->getAttribute('resource');

		if ( empty($action) && empty($resource))
		return self::show_tag_error($tag, 'Feed the "action" and "resource" attributes');


		if (Authority::can($action, $resource))
		{
			return $tag->expand();
		}
		else
		{
			// Else
			self::$trigger_else++;
		}
		return '';
	}



}


