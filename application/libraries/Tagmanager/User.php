<?php
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
 * User TagManager
 *
 */
class TagManager_User extends TagManager
{
	public static $tag_definitions = array
	(
		'user' => 				'tag_user',
		'user:name' => 			'tag_user_name',
		'user:join_date' =>		'tag_simple_date',
		'user:last_visit' =>	'tag_simple_date',
		'user:email' => 		'tag_simple_value',
		'user:firstname' => 	'tag_simple_value',
		'user:lastname' => 		'tag_simple_value',
		'user:gender' => 		'tag_simple_value',
		'user:birth_date' => 	'tag_simple_value',
	);


	/**
	 * Returns one article's author
	 *
	 * @param 	FTL_Binding
	 *
	 * @return 	string
	 *
	 * @usage	<ion:article:author [who='updater']>
	 * 				<ion:name />
	 *				<ion:email />
	 *				<ion:join_date />
	 * 				...
	 * 			</ion:article:author>
	 *
	 */
	public static function tag_user(FTL_Binding $tag)
	{
		self::load_model('users_model');

		$parent_tag_name = $tag->getParentName();

		$element = $tag->get($parent_tag_name);

		$user_key = $tag->getAttribute('who', 'author');

		if ( ! is_null($element) && isset($element[$user_key]))
		{
			$user = self::$ci->users_model->get(array('username' => $element[$user_key]));
			$tag->set('user', $user);
		}

		return self::wrap($tag, $tag->expand());
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the screen name of the user (complete name)
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_user_name(FTL_Binding $tag)
	{
		return self::wrap($tag, $tag->getValue('screen_name'));
	}
}