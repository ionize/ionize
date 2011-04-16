<?php
/**
 * Ionize #module_name module tags
 *
 * This class define the #module_name module tags
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
 * #module_name Module's TagManager 
 *
 */
class Comments_Tags
{
	/**
	 * Routing current modules tags to extends Core Tags (like articles)
	 * Usage : Add an entry for earch tag to be routed
	 * ie : "current_module_tag" => "core_tag",
	 *      "my_tag" => "articles" 
	 **/
	 
	public static $tag_extension_map = array(												
	
		"comments_admin"	=>	"articles:comments_admin", 						// Display admin options & save changes
		"comments"			=>	"articles:comments", 							// Comments loop
		"comment_form"		=>	"articles:comment_form", 						// Display "comment" form
		"comment_save"		=>	"articles:comment_save", 						// Save new comment
		"comments_count"	=>	"articles:comments_count", 						// Return number of comments for an article
		"content"			=>	"articles:comments:content", 					// Return comment content
		"author"			=>	"articles:comments:author", 					// Return comment author
		"email"				=>	"articles:comments:email", 						// Return comment email
		"date"				=>	"articles:comments:date",						// Display comment date
		"gravatar"			=>	"articles:comments:gravatar",					// Display avatar, using gravatar site
		"comments_allowed"	=>  "articles:comments_allowed",					// Display nested content if comments allowed
		"message"			=> 	"articles:comments_admin", 						// Display admin flash message 
		"success_message"	=> 	"articles:comments_allowed:success_message",	// Display success flash message
		"error_message"		=> 	"articles:comments_allowed:error_message"		// Display error flash message  								
	);
	
	
	/*************************************************************************
	 * Base #module_name module tag
	 * The index function of this class refers to the <ion:#module_name /> tag
	 * In other words, this function makes the <ion:#module_name /> tag available as main module parent tag
	 * for all other tags defined in this class.
	 *
	 * @usage	<ion:#module_name >
	 *			...
	 *			</ion:#module_name>
	 *
	 */
	public static function index(FTL_Binding $tag)
	{
		$str = $tag->expand();
		return $str;
	}

	

	// ------------------------------------------------------------------------


	/***********************************************************************
	 * Display the form for new blog comment entry
	 * Might not be used, use a partial view instead
	 */
	public static function comment_form(FTL_Binding $tag)
	{
		
		// the tag returns the content of this view :
		return $tag->parse_as_nested(file_get_contents(MODPATH.'Comments/views/comment_form'.EXT));
	}
	
	/***********************************************************************
	 * Save the new entry, if "POST" detected
	 *
	 */
	public static function comment_save(FTL_Binding $tag)
	{
		// get CodeIgniter instance
		$CI =& get_instance();
	
		// Comment was posted, saving it
		if ($content = $CI->input->post('content'))
		{
			// Loads the comments module model
			if (!isset($CI->comments_model)) 
				$CI->load->model('comments_model', '', true);
						
			// Save comment 
			if ($CI->comments_model->insert_comment( $tag->locals->article['id_article'] )) 
				$CI->locals->showSuccessFlashMessage=true;
			else
				$CI->locals->showErrorFlashMessage=true;		
			
		}
		
	
		return;
	}
	
	/***********************************************************************
	 * Displaying comments
	 * Loops through the list of existing comments
	 *
	 */
	public static function comments(FTL_Binding $tag)
	{
		// get CodeIgniter instance
		$CI =& get_instance();
		// Loads the comments module model
		if (!isset($CI->comments_model)) $CI->load->model('comments_model', '', true);
			
		// Load comments
		$comments = $CI->comments_model->get_comments($tag->locals->article['id_article']);
		
		
		// Make comment count available to child tags
		$tag->locals->comment_count = sizeof( $comment );
		
		$output = ""; // Var used to store the built display
		
		// Loop through comments
		foreach ($comments as $comment)
		{
			// Make comment available to child tags
			$tag->locals->comment = $comment;
			
			// Get "comments" tag content & execute child tags
			$output .= $tag->expand();
		}
		
		// Return output, for display
		return $output;
	}
	
	/***********************************************************************
	 * Display number of comments attached to the post
	 *
	 */
	public static function comments_count(FTL_Binding $tag)
	{
		// get CodeIgniter instance
		$CI =& get_instance();
		// Loads the comments module model
		if (!isset($CI->comments_model)) $CI->load->model('comments_model', '', true);
			
		// Load comments
		$comments = $CI->comments_model->get_comments($tag->locals->article['id_article']);
		
		
		return sizeof( $comments );

	}

	
	/***********************************************************************
	 * Display comment's content
	 *
	 */
	public static function content(FTL_Binding $tag)
	{
		return $tag->locals->comment["content"];
	}
	
	/***********************************************************************
	 * Display comment's author
	 *
	 */
	public static function author(FTL_Binding $tag)
	{
		return $tag->locals->comment["author"];
	}
	
	/***********************************************************************
	 * Display comment's email
	 *
	 */
	public static function email(FTL_Binding $tag)
	{
		return $tag->locals->comment["email"];
	}
	
	/***********************************************************************
	 * Display comment's creation date
	 *
	 */
	public static function date(FTL_Binding $tag)
	{
		if (!isDate($tag->locals->comment["created"])) return;
		return $tag->locals->comment["created"];
		
	}

	/************************************************************************
	 * Display comment's author's gravatar
	 *
	 * Attributes :
	 * default : 	can be "mm" (people shadow), "identicon" (default), 
	 				"monsterid", "wavatar", "retro"
	 * 				or link to a public accessible default image 
	 * TODO :
	 * - Allow to define the size
	 */
	public static function gravatar(FTL_Binding $tag)
	{
		// Using "identicon" if no other default avatar is specified 
		$default_avatar = isset($tag->attr['default']) ? $tag->attr['default'] : 'identicon';
		
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $tag->locals->comment["email"]) ) ) . "?s=80&d=".$default_avatar;
		return $grav_url;	
	}


	/*************************************************************************
	 * Display toolbar for admin : allow to configure comments for an article
	 *
	 */
	public static function comments_admin(FTL_Binding $tag)
	{
		$CI =& get_instance();
				
		$allowed = $CI->connect->restrict( array('group' => 'admins'), true );
		
		//return $tag->locals->article['comment_allow'];
		$result = "nothing";
		
		if ($allowed) 
		{
			
			if (!isset($CI->comments_model)) $CI->load->model('comments_model', '', true);
			
			// Checking if a modification (POST) should be done
			if ($CI->input->post( "comments_article_update" )=="1" )
			{
				$tag->locals->article['comment_allow'] = $CI->comments_model->update_article( $tag->locals->article['id_article'] );				$tag->locals->showFlashMessage=true;	
			}
			
			return $tag->expand();
		}
		
	}
	
	/***************************************************************************
	 * Display a flash message to inform admin that action was completed
	 *
	 */
	public static function message(FTL_Binding $tag)
	{
		
		if ($tag->locals->showFlashMessage==true)
		{
			$class = isset($tag->attr['class']) ? ' class="'.$tag->attr['class'].'"' : '';
			$id = isset($tag->attr['id']) ? ' id="'.$tag->attr['id'].'"' : '';
			$tag_open = isset($tag->attr['tag']) ? "<".$tag->attr['tag'].$id.$class.">" : '';
			$tag_close = isset($tag->attr['tag']) ? "</".$tag->attr['tag'].">" : '';
			
			return $tag_open.$tag->expand().$tag_close;
		} 	
	}


	/***************************************************************************
	 * Display a flash message to inform user that action was completed
	 *
	 */
	public static function success_message(FTL_Binding $tag)
	{
		$CI =& get_instance();	
		
		// Build flash message "success"
		if ($CI->locals->showSuccessFlashMessage==true)
		{
			$class = isset($tag->attr['class']) ? ' class="'.$tag->attr['class'].'"' : '';
			$id = isset($tag->attr['id']) ? ' id="'.$tag->attr['id'].'"' : '';
			$tag_open = isset($tag->attr['tag']) ? "<".$tag->attr['tag'].$id.$class.">" : '';
			$tag_close = isset($tag->attr['tag']) ? "</".$tag->attr['tag'].">" : '';
			
			return $tag_open.$tag->expand().$tag_close;
		} 	
	}
	
	/***************************************************************************
	 * Display a flash error message to inform user that action wasn't completed
	 *
	 */
	public static function error_message(FTL_Binding $tag)
	{
		$CI =& get_instance();	
		
		// Build flash message "success"
		if ($CI->locals->showErrorFlashMessage==true)
		{
			$class = isset($tag->attr['class']) ? ' class="'.$tag->attr['class'].'"' : '';
			$id = isset($tag->attr['id']) ? ' id="'.$tag->attr['id'].'"' : '';
			$tag_open = isset($tag->attr['tag']) ? "<".$tag->attr['tag'].$id.$class.">" : '';
			$tag_close = isset($tag->attr['tag']) ? "</".$tag->attr['tag'].">" : '';
			
			return $tag_open.$tag->expand().$tag_close;
		} 	
	}
	
	/***************************************************************************
	 * Return "checked" if comments are allowed
	 *
	 */
	public static function comments_allowed(FTL_Binding $tag)
	{
		
		$result = $tag->locals->article['comment_allow']=="1" ? $result = $tag->expand() : $result ="";
		return $result;	
	}


}
