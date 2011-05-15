<?php
/**
 * Ionize Search module tags
 *
 * This class define the Search module tags
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 *
 *
 */


/**
 * Search TagManager 
 *
 */
class Search_Tags extends TagManager
{
	
	/**
	 * Base search module tag
	 * The index function of this class refers to the <ion:search /> tag
	 * In other words, this function makes the <ion:search /> tag available as main module parent tag
	 * for all other tags defined in this class.
	 *
	 * @usage	<ion:search >
	 *			...
	 *			</ion:search>
	 *
	 */
	public static function index(FTL_Binding $tag)
	{
		$str = $tag->expand();
		
		return $str;
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Search form tag
	 * 
	 * Returns the search form view
	 *
	 * @usage	<ion:searchform />
	 *
	 */
	public static function searchform(FTL_Binding $tag)
	{
		$CI =& get_instance();
	
		$searchForm_action = (isset($tag->attr['result_page']) ) ? $tag->attr['result_page'] : '';
		$tag->locals->result_page = $searchForm_action;
		
		
		// If the realm data was posted, the form will not be displayed
		// Useful when results should be displayed on the same page as the search form
		if ($realm = $CI->input->post('realm'))
		{
			return '';
		}
		else
		{
			$tag->expand();
			
			// the tag returns the content of this view :
			return $tag->parse_as_nested(file_get_contents(MODPATH.'Search/views/search_form'.EXT));
		}
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Form status message tag
	 * Useful if the form is sent again after an unsuccessful search
	 *
	 */
	public static function status_message(FTL_Binding $tag)
	{
		// Local results are set : Means the search process was done
		// Because the form will only be displayed again after unsuccesful search, if the var is set, it means no results were found.
		if ( isset($tag->locals->results))
		{
			return lang('module_search_message_no_results');
		}
		else
		{
			return lang('module_search_fill_the_field');
		}
	}
	
	/**
	 * Form action tag
	 * Used to display the results on a new page
	 *
	 */
	public static function result_page(FTL_Binding $tag)
	{
		// Local results are set : Means the search process was done
		if ( isset($tag->locals->result_page))
		{
			return $tag->locals->result_page;
		}
		else
		{
			return "";
		}
	}
	
	

	// ------------------------------------------------------------------------


	/**
	 * Search results tag
	 * Parent tag for results
	 *
	 *
	 */
	public static function searchresults(FTL_Binding $tag)
	{
		$CI =& get_instance();
		
		$str = '';
		
		// Add the realm to the local var, so it can be retrieved
		$tag->locals->realm = $CI->input->post('realm');
		
		// Did we got the POST realm ?
		if ($realm = $CI->input->post('realm'))
		{
			// Loads the serach module model
			$CI->load->model('search_model', '', true);
			
			// Get the results
			$articles = $CI->search_model->get_articles($realm);
			
	

			// If result, expand the tag
			if ( ! empty($articles))
			{
				// arrays of keys, for multisorting
				$knum = $kdate = array();
				
				foreach($articles as $key => &$article)
				{
					// set number of found words
					preg_match_all('#'.$tag->locals->realm.'#i', $article['title'].' '.$article['content'], $match);
					$num = count($match[0]);

					$article['nb_found'] = $knum[$key] = $num;
					$kdate[$key] = strtotime($article['date']);
				}
				
				array_multisort($knum, SORT_DESC, SORT_NUMERIC, $kdate, SORT_DESC, SORT_NUMERIC, $articles);
				
				
				// Put the results to the local results var
				$tag->locals->results = $articles;
				$tag->locals->nb_results = count($articles);

				
				// $tag->locals->results = $articles;
				$str .= $tag->expand();
			}
			// If no results, display a message or returns another view, the form view, whatever you want in fact...
			else
			{
				$tag->locals->results = array();
				// Example of message return. This message is stored in /modules/Search/language/xx/search_lang.php
//				$str .= lang('module_search_message_no_results');
			
				// Example with returning the form view
				// return $tag->parse_as_nested(file_get_contents(MODPATH.'Search/views/search_form'.EXT));

			}
			$str .= $tag->expand();

		}
		return $str;
	}


	// ------------------------------------------------------------------------


	/**
	 * Nested in <ion:searchresults> tag
	 * Feeds each result
	 *
	 * @usage : <ion:searchresults>
	 *				<ion:results>
	 *					<ion:title/>
	 *					...
	 *				</ion:results>
	 *			</ion:searchresults>
	 *
	 */
	public static function results(FTL_Binding $tag)
	{
		$str = '';
		
		foreach($tag->locals->results as $article)
		{
			// Add each article as the current local result
			$tag->locals->result = $article;
			
			// Expand the tag : Means the children tags of this tag will get the above defined "result" 
			$str .= $tag->expand();
		}	
	
		return $str;
	}

	public static function no_results(FTL_Binding $tag)
	{
		if( empty($tag->locals->results))
		{
			return $tag->expand();
		}
	
	}
	// ------------------------------------------------------------------------

	public static function realm(FTL_Binding $tag)
	{
		$str = '';
		
		if( ! empty($tag->locals->realm))
		{
			$str = $tag->locals->realm;
		}
		
		return $str;
	}
	
	public static function highlight(FTL_Binding $tag)
	{
		$search = '';
		
		
		if( ! empty($tag->locals->realm))
		{
			$search = $tag->locals->realm;
		}
		
		$str = '<script type="text/javascript"> highlightSearchTerms(\''.$search.'\');</script>'; 		
		
		return $str;
	}
	
	/**
	 * Returns one asked field of the current result.
	 * If you query result contains the field 'title', you can retrieve it with this tag.
	 *
	 * @usage		<ion:result field="title" />
	 *
	 *
	 */
	public static function result(FTL_Binding $tag)
	{
		$field = (isset($tag->attr['field']) ) ? $tag->attr['field'] : false;
		
		if ($field && ( ! empty($tag->locals->result[$field])))
		{
			return self::wrap($tag, $tag->locals->result[$field]);
		}
		return '';
	}


	/**
	 * Return one result title
	 * Nested in <ion:results>
	 *
	 * @usage : <ion:results>
	 *				<ion:title>
	 *				...
	 *			</ion:results>
	 *
	 */
	public static function title(FTL_Binding $tag)
	{
		return $tag->locals->result['title'];
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the result URL
	 *
	 */
	public static function url(FTL_Binding $tag)
	{
		return $tag->locals->result['page_url'].'/'.$tag->locals->result['url'];
	}

	public static function content(FTL_Binding $tag)
	{
		// paragraph limit ?
		$paragraph = (isset($tag->attr['paragraph'] )) ? (int)self::get_attribute($tag, 'paragraph') : FALSE ;

		$content = $tag->locals->result['content'];

		// Limit to x paragraph if the attribute is set
		if ($paragraph !== FALSE)
			$content = tag_limiter($content, 'p', $paragraph);

		return self::wrap($tag, $content);
	}

	// ------------------------------------------------------------------------

	public static function date($tag) { return self::wrap($tag, self::format_date($tag, $tag->locals->result['date'])); }
	
	public static function nb_results($tag) { return self::wrap($tag, $tag->locals->nb_results); }

	public static function nb_found($tag) { return self::wrap($tag, $tag->locals->result['nb_found']); }


}
