<?php

class Demo_Tags extends TagManager
{
	/**
	 * Tag declaration
	 * These declaration will overwrite the autoload of tags
	 *
	 * @var array
	 *
	 * @usage	"<scope>" => "<method_in_this_class>"
	 * 			Examples :
	 * 			"articles:hello" => "my_hello_method"
	 * 			"demo:authors" => "my_authors_method"
	 */
	public static $tag_definitions = array
	(
		"articles:authors" => 			"core_articles_authors",
		"articles:authors:author" => 	"tag_author",
		"demo:authors:author" =>		"tag_author"
	);


	/**
	 * Base module tag
	 * The index function of this class refers to the <ion:#module_name /> tag
	 * In other words, this function makes the <ion:#module_name /> tag
	 * available as main module parent tag for all other tags defined
	 * in this class.
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


	/**
	 * Loops through authors
	 *
	 * @param FTL_Binding $tag
	 * @return string
	 *
	 * @usage	<ion:demo:authors >
	 *				...
	 *			</ion:demo:authors>
	 *
	 */
	public static function tag_authors(FTL_Binding $tag)
	{
		// Returned string
		$str = '';

		// Model load
		self::load_model('demo_author_model', 'author_model');

		// Authors array
		$authors = self::$ci->author_model->get_lang_list();

		foreach($authors as $author)
		{
			// Set the local tag var "author"
			$tag->set('author', $author);

			// Tag expand : Process of the children tags
			$str .= $tag->expand();
		}

		return $str;
	}


	/**
	 * Author tag
	 *
	 * @param		FTL_Binding		Tag object
	 * @return		String			Tag attribute or ''
	 *
	 * @usage		<ion:demo:authors>
	 *					<ion:author field="name" />
	 * 				</ion:demo:authors>
	 *
	 */
	public static function tag_author(FTL_Binding $tag)
	{
		// Returns the field value or NULL if the attribute is not set
		$field = $tag->getAttribute('field');

		if ( ! is_null($field))
		{
			// Get the local tag var "author"
			$author = $tag->get('author');

			if ( ! empty($author[$field]))
				return self::wrap($tag, $author[$field]);
		}

		// Here we have the choice :
		// - Ether return nothing if the field attribute isn't set or doesn't exist
		// - Ether silently return ''
		return self::show_tag_error(
			'author',
			'The attribute <b>"field"</b> is not set or the field doesn\'t exists.'
		);

		// return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Article Core Tag Extend : Authors List tag
	 *
	 * @param		FTL_Binding		Tag object
	 * @return		String			List of Authors
	 *
	 * @usage		<ion:articles>
	 * 					<ion:authors>
	 *					...
	 * 					</ion:authors>
	 *				<ion:articles>
	 *
	 */
	public static function core_articles_authors(FTL_Binding $tag)
	{
		$str = '';

		// Model load
		self::load_model('demo_author_model', 'author_model');

		// Get the article from local tag var :
		// The 'articles' tag is a parent of this tag and has set the 'article' var.
		$article = $tag->get('article');

		$authors = self::$ci->author_model->get_linked_author('article', $article['id_article']);

		foreach($authors as $author)
		{
			// Set the local tag var "author"
			$tag->set('author', $author);

			// Tag expand : Process of the children tags
			$str .= $tag->expand();
		}

		return $str;
	}
}