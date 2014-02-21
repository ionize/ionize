<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Text Helper
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 *
 */


// ------------------------------------------------------------------------


/** Get the x tags content from start
 * 	@param	$string 	HTML string
 * 	@param	$tag		Tag you wish to delimit
 * 	@param	$nb			Number of tag occurrence you want to return
 * 	@param	$start		Tag start (0 will begin at the first tag)
 *  
 *  For example : 	$string = "<p>Hello</p><p>My name is Toto</p>";
 * 					
 *					tag_limiter($string, 'p', $nb=1, $start=0)
 *					
 *					returns : "<p>Hello</p>"
 *
 */
if ( ! function_exists('tag_limiter'))
{
	function tag_limiter($string, $tag, $nb=1, $start=0) {
		
		$rString = '';
		
		if ($string != '')
		{
			$startPos = 0;
			$endPos = strlen($string);
			
			$tag = "</".$tag.">";
			$wString = $string;
			$nbTag = substr_count($string, $tag);
			$cPos = 0;
			$nb = $start + $nb;
		
			for($i=0; $i<$nbTag; $i++) {
		
				if($i== $start) 
				{
					$startPos = $cPos ;
				}
				if($i== $nb) 
				{
					$endPos = $cPos ;
				}
				$pos = strpos($wString, $tag) + strlen($tag);
				$wString = substr($wString, $pos, strlen($wString));
				$cPos += $pos;
			}
			if (!isset($endPos)) $endPos = strlen($string);
		
			$rString = substr($string, $startPos, $endPos-$startPos);
		}
		
		return $rString;
	}
}


// ------------------------------------------------------------------------


if ( ! function_exists('to_entities'))
{
	function to_entities($str)
	{
		$str = htmlspecialchars($str, ENT_COMPAT, 'UTF-8', false);

		return $str;
	}
}



