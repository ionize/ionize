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
 * Writer TagManager
 *
 */
class TagManager_Writer extends TagManager
{
	public static $tag_definitions = array
	(
		'writer' => 			'tag_writer',
		'writer:name' => 		'tag_writer_name',
		'writer:join_date' =>	'tag_simple_date',
		'writer:last_visit' =>	'tag_simple_date',
		'writer:email' => 		'tag_simple_value',
		'writer:firstname' => 	'tag_simple_value',
		'writer:lastname' => 	'tag_simple_value',
		'writer:gender' => 		'tag_simple_value',
		'writer:birth_date' => 	'tag_simple_value',
	);


	/**
	 * Returns one article's author
	 *
	 * @param 	FTL_Binding
	 *
	 * @return 	string
	 *
	 * @usage	<ion:article:user [who='updater']>
	 * 				<ion:name />
	 *				<ion:email />
	 *				<ion:join_date />
	 * 				...
	 * 			</ion:article:user>
	 *
	 */
	public static function tag_writer(FTL_Binding $tag)
	{
		self::load_model('user_model');

		$parent_tag_name = $tag->getParentName();

		$element = $tag->get($parent_tag_name);

		$user_key = $tag->getAttribute('who', 'author');

		if ( ! is_null($element) && isset($element[$user_key]))
		{
			$user = self::$ci->user_model->get(array('username' => $element[$user_key]));
			$tag->set('writer', $user);
		}

		return self::wrap($tag, $tag->expand());
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the screen name of the writer (complete name)
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 *
	 */
	public static function tag_writer_name(FTL_Binding $tag)
	{
		return self::output_value($tag, $tag->getValue('screen_name'));
	}
}