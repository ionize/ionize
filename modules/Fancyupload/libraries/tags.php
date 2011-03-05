<?php
/**
 * Ionize
 * FancyUpload tags
 *
 * @package		Ionize
 * @author		
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.93
 *
 * 
 *
 *
 */


/**
 * FancyUpload TagManager 
 *
 */
class Fancyupload_Tags
{
	/**
	 * Define the enclosing tag.
	 * Make the <ion:fancyupload /> tag available as parent tag
	 *
	 * @usage	<ion:fancyupload type="photoqueue" />
	 *			type : 	"photoqueue" : 	Simple file queue uploader
	 *					"complete" : 		Complete Fancyupload
	 *
	 */
	public static function index(FTL_Binding $tag)
	{
		// Get the module URI
		include APPPATH . 'config/modules.php';

		$uri = array_search('Fancyupload', $modules);
		
		$tag->expand();
		
		return $tag->parse_as_nested(file_get_contents(MODPATH.'Fancyupload/views/fancyupload_'.config_item('fancyupload_type').EXT));
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the module users URI from the config/modules.php file
	 *
	 */
	public static function uri(FTL_Binding $tag)
	{
		// Get the module URI
		include APPPATH . 'config/modules.php';

		$uri = array_search('Fancyupload', $modules);
		
		return ( ! empty($uri)) ? $uri : '';
		
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the max upload size for JS format
	 * 
	 */
	public static function post_max_size(FTL_Binding $tag)
	{

		if (config_item('fancyupload_max_upload') != 0)
		{
			$post_max_size = config_item('fancyupload_max_upload').'m';
		}
		else
		{
			$post_max_size = ini_get('post_max_size');
		}
	
		
	    $val = trim($post_max_size);
	    $last = strtolower($val[strlen($val)-1]);
	    
	    switch($last) 
	    {
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }
	
	    return $val;
	}
	
	
	// ------------------------------------------------------------------------

	/**
	 * Returns url_encoded user data.
	 * Used by the fancyupload form to send encrypted info about the current connected user 
	 * to the upload method (controller/fancyupload()->upload())
	 *
	 * @usage		<ion:userdata item="<user_attribute>" [url_encode="true"] />
	 *
	 *				item 			mandatory.
	 *								Can takes values : "username", "screen_name", "email"
	 *				url_encode : 	optional.
	 *								if set to true, encodes the returned encrypted string using the
	 *								PHP rawurlencode function.
	 *
	 */
	public static function userdata(FTL_Binding $tag)
	{
		$item = (isset($tag->attr['item']) &&  $tag->attr['item'] != '') ? $tag->attr['item'] : false;
		$url_encode = (isset($tag->attr['url_encode']) &&  $tag->attr['url_encode'] == 'true') ? true : false;
		
		// If no item, return an empty string
		if ( ! $item) return '';
		
		$ci =  &get_instance();
		
		// Encryption library
		if ( empty($ci->encrypt) )
			$ci->load->library('encrypt');
			
		$user = Connect()->get_current_user();

		if ( ! empty($user->$item))
		{
			if ($url_encode === true)
			{
				return rawurlencode($ci->encrypt->encode($user->$item));
			}
			else
			{
				return $ci->encrypt->encode($user->$item);			
			}
		}
		
		return '';		
	}

}
