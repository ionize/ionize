<?php

class Demo_Tags extends TagManager
{
	/**
	 * Tags declaration
	 * To be available, each tag must be declared in this static array.
	 *
	 * @var array
	 *
	 * @usage	"<tag scope>" => "<method_in_this_class>"
	 * 			Examples :
	 * 			"articles:hello" => "my_hello_method" : The tag "hello" will be usable as child of "articles"
	 * 			"demo:authors" => "my_authors_method"
	 */
	public static $tag_definitions = array
	(
		// <ion:article:authors /> calls the method “core_articles_authors”
		"demo:authors" =>				"tag_authors",
		"demo:authors:author" =>		"tag_author",
		"article:authors" => 			"core_article_authors",
		"article:authors:author" => 	"tag_author",

		"demo:main" =>					"tag_demo_main",
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
	 * Loads the main Front module's view
	 * Because the parent tag (index) is expanded, the result of this method will be displayed
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return mixed
	 */
	public static function tag_demo_main(FTL_Binding $tag)
	{
		$view = self::$ci->load->view('index', '', TRUE);

		return $view;
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
			{
				return self::output_value($tag, $author[$field]);
			}
		}

		// Here we have the choice :
		// - Ether return nothing if the field attribute isn't set or doesn't exist
		// - Ether silently return ''
		return self::show_tag_error(
			$tag,
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
	public static function core_article_authors(FTL_Binding $tag)
	{
		$str = '';

		// Model load
		self::load_model('demo_author_model', 'author_model');

		// Get the article from local tag var :
		// The 'article' tag is a parent of this tag and has the 'article' data array set.
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