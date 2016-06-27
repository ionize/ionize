<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Text Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/text_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Word Limiter
 *
 * Limits a string to X number of words.
 *
 * @access	public
 * @param	string
 * @param	integer
 * @param	string	the end character. Usually an ellipsis
 * @return	string
 */
if ( ! function_exists('word_limiter'))
{
	function word_limiter($str, $limit = 100, $end_char = '&#8230;')
	{
		if (trim($str) == '')
		{
			return $str;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

		if (strlen($str) == strlen($matches[0]))
		{
			$end_char = '';
		}

		return rtrim($matches[0]).$end_char;
	}
}

// ------------------------------------------------------------------------

/**
 * Character Limiter
 *
 * Limits the string based on the character count.  Preserves complete words
 * so the character count may not be exactly as specified.
 *
 * @access	public
 * @param	string	$str
 * @param	integer	$n
 * @param	string	$end_char	the end character. Usually an ellipsis
 * @return	string
 */
if ( ! function_exists('character_limiter'))
{
	function character_limiter($str, $n = 500, $end_char = '&#8230;')
	{
		return strlen($str) < $n
			? $str
			: truncateHtml($str, $n, $end_char, false, true);
	}
}

// ------------------------------------------------------------------------

/**
 * High ASCII to Entities
 *
 * Converts High ascii text and MS Word special characters to character entities
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('ascii_to_entities'))
{
	function ascii_to_entities($str)
	{
		$count	= 1;
		$out	= '';
		$temp	= array();

		for ($i = 0, $s = strlen($str); $i < $s; $i++)
		{
			$ordinal = ord($str[$i]);

			if ($ordinal < 128)
			{
				/*
					If the $temp array has a value but we have moved on, then it seems only
					fair that we output that entity and restart $temp before continuing. -Paul
				*/
				if (count($temp) == 1)
				{
					$out  .= '&#'.array_shift($temp).';';
					$count = 1;
				}

				$out .= $str[$i];
			}
			else
			{
				if (count($temp) == 0)
				{
					$count = ($ordinal < 224) ? 2 : 3;
				}

				$temp[] = $ordinal;

				if (count($temp) == $count)
				{
					$number = ($count == 3) ? (($temp['0'] % 16) * 4096) + (($temp['1'] % 64) * 64) + ($temp['2'] % 64) : (($temp['0'] % 32) * 64) + ($temp['1'] % 64);

					$out .= '&#'.$number.';';
					$count = 1;
					$temp = array();
				}
			}
		}

		return $out;
	}
}

// ------------------------------------------------------------------------

/**
 * Entities to ASCII
 *
 * Converts character entities back to ASCII
 *
 * @access	public
 * @param	string
 * @param	bool
 * @return	string
 */
if ( ! function_exists('entities_to_ascii'))
{
	function entities_to_ascii($str, $all = TRUE)
	{
		if (preg_match_all('/\&#(\d+)\;/', $str, $matches))
		{
			for ($i = 0, $s = count($matches['0']); $i < $s; $i++)
			{
				$digits = $matches['1'][$i];

				$out = '';

				if ($digits < 128)
				{
					$out .= chr($digits);

				}
				elseif ($digits < 2048)
				{
					$out .= chr(192 + (($digits - ($digits % 64)) / 64));
					$out .= chr(128 + ($digits % 64));
				}
				else
				{
					$out .= chr(224 + (($digits - ($digits % 4096)) / 4096));
					$out .= chr(128 + ((($digits % 4096) - ($digits % 64)) / 64));
					$out .= chr(128 + ($digits % 64));
				}

				$str = str_replace($matches['0'][$i], $out, $str);
			}
		}

		if ($all)
		{
			$str = str_replace(array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;", "&#45;"),
								array("&","<",">","\"", "'", "-"),
								$str);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * Word Censoring Function
 *
 * Supply a string and an array of disallowed words and any
 * matched words will be converted to #### or to the replacement
 * word you've submitted.
 *
 * @access	public
 * @param	string	the text string
 * @param	string	the array of censoered words
 * @param	string	the optional replacement value
 * @return	string
 */
if ( ! function_exists('word_censor'))
{
	function word_censor($str, $censored, $replacement = '')
	{
		if ( ! is_array($censored))
		{
			return $str;
		}

		$str = ' '.$str.' ';

		// \w, \b and a few others do not match on a unicode character
		// set for performance reasons. As a result words like über
		// will not match on a word boundary. Instead, we'll assume that
		// a bad word will be bookeneded by any of these characters.
		$delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

		foreach ($censored as $badword)
		{
			if ($replacement != '')
			{
				$str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/i", "\\1{$replacement}\\3", $str);
			}
			else
			{
				$str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'", $str);
			}
		}

		return trim($str);
	}
}

// ------------------------------------------------------------------------

/**
 * Code Highlighter
 *
 * Colorizes code strings
 *
 * @access	public
 * @param	string	the text string
 * @return	string
 */
if ( ! function_exists('highlight_code'))
{
	function highlight_code($str)
	{
		// The highlight string function encodes and highlights
		// brackets so we need them to start raw
		$str = str_replace(array('&lt;', '&gt;'), array('<', '>'), $str);

		// Replace any existing PHP tags to temporary markers so they don't accidentally
		// break the string out of PHP, and thus, thwart the highlighting.

		$str = str_replace(array('<?', '?>', '<%', '%>', '\\', '</script>'),
							array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'), $str);

		// The highlight_string function requires that the text be surrounded
		// by PHP tags, which we will remove later
		$str = '<?php '.$str.' ?>'; // <?

		// All the magic happens here, baby!
		$str = highlight_string($str, TRUE);

		// Prior to PHP 5, the highligh function used icky <font> tags
		// so we'll replace them with <span> tags.

		if (abs(PHP_VERSION) < 5)
		{
			$str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
			$str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
		}

		// Remove our artificially added PHP, and the syntax highlighting that came with it
		$str = preg_replace('/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i', '<span style="color: #$1">', $str);
		$str = preg_replace('/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is', "$1</span>\n</span>\n</code>", $str);
		$str = preg_replace('/<span style="color: #[A-Z0-9]+"\><\/span>/i', '', $str);

		// Replace our markers back to PHP tags.
		$str = str_replace(array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
							array('&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;'), $str);

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * Phrase Highlighter
 *
 * Highlights a phrase within a text string
 *
 * @access	public
 * @param	string	the text string
 * @param	string	the phrase you'd like to highlight
 * @param	string	the openging tag to precede the phrase with
 * @param	string	the closing tag to end the phrase with
 * @return	string
 */
if ( ! function_exists('highlight_phrase'))
{
	function highlight_phrase($str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>')
	{
		if ($str == '')
		{
			return '';
		}

		if ($phrase != '')
		{
			return preg_replace('/('.preg_quote($phrase, '/').')/i', $tag_open."\\1".$tag_close, $str);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * Convert Accented Foreign Characters to ASCII
 *
 * @access	public
 * @param	string	the text string
 * @return	string
 */
if ( ! function_exists('convert_accented_characters'))
{
	function convert_accented_characters($str)
	{
		static $array_from, $array_to;

		if ( ! is_array($array_from))
		{
			if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars'.EXT))
			{
				include(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars'.EXT);
			}
			elseif (is_file(APPPATH.'config/foreign_chars'.EXT))
			{
				include(APPPATH.'config/foreign_chars'.EXT);
			}

			if ( ! isset($foreign_characters))
			{
				return $str;
			}
			$array_from = array_keys($foreign_characters);
			$array_to = array_values($foreign_characters);
		}

		return preg_replace($array_from, $array_to, $str);
	}
}

// ------------------------------------------------------------------------

/**
 * Word Wrap
 *
 * Wraps text at the specified character.  Maintains the integrity of words.
 * Anything placed between {unwrap}{/unwrap} will not be word wrapped, nor
 * will URLs.
 *
 * @access	public
 * @param	string	the text string
 * @param	integer	the number of characters to wrap at
 * @return	string
 */
if ( ! function_exists('word_wrap'))
{
	function word_wrap($str, $charlim = '76')
	{
		// Se the character limit
		if ( ! is_numeric($charlim))
			$charlim = 76;

		// Reduce multiple spaces
		$str = preg_replace("| +|", " ", $str);

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// If the current word is surrounded by {unwrap} tags we'll
		// strip the entire chunk and replace it with a marker.
		$unwrap = array();
		if (preg_match_all("|(\{unwrap\}.+?\{/unwrap\})|s", $str, $matches))
		{
			for ($i = 0; $i < count($matches['0']); $i++)
			{
				$unwrap[] = $matches['1'][$i];
				$str = str_replace($matches['1'][$i], "{{unwrapped".$i."}}", $str);
			}
		}

		// Use PHP's native function to do the initial wordwrap.
		// We set the cut flag to FALSE so that any individual words that are
		// too long get left alone.  In the next step we'll deal with them.
		$str = wordwrap($str, $charlim, "\n", FALSE);

		// Split the string into individual lines of text and cycle through them
		$output = "";
		foreach (explode("\n", $str) as $line)
		{
			// Is the line within the allowed character count?
			// If so we'll join it to the output and continue
			if (strlen($line) <= $charlim)
			{
				$output .= $line."\n";
				continue;
			}

			$temp = '';
			while ((strlen($line)) > $charlim)
			{
				// If the over-length word is a URL we won't wrap it
				if (preg_match("!\[url.+\]|://|wwww.!", $line))
				{
					break;
				}

				// Trim the word down
				$temp .= substr($line, 0, $charlim-1);
				$line = substr($line, $charlim-1);
			}

			// If $temp contains data it means we had to split up an over-length
			// word into smaller chunks so we'll add it back to our current line
			if ($temp != '')
			{
				$output .= $temp."\n".$line;
			}
			else
			{
				$output .= $line;
			}

			$output .= "\n";
		}

		// Put our markers back
		if (count($unwrap) > 0)
		{
			foreach ($unwrap as $key => $val)
			{
				$output = str_replace('{{unwrapped'.$key.'}}', $val, $output);
			}
		}

		// Remove the unwrap tags
		$output = str_replace(array('{unwrap}', '{/unwrap}'), '', $output);

		return $output;
	}
}

// ------------------------------------------------------------------------

/**
 * Ellipsize String
 *
 * This function will strip tags from a string, split it at its max_length and ellipsize
 *
 * @param	string		string to ellipsize
 * @param	integer		max length of string
 * @param	mixed		int (1|0) or float, .5, .2, etc for position to split
 * @param	string		ellipsis ; Default '...'
 * @return	string		ellipsized string
 */
if ( ! function_exists('ellipsize'))
{
	function ellipsize($str, $max_length, $position = 1, $ellipsis = '&hellip;')
	{
		// Strip tags
		$str = trim(strip_tags($str));

		// Is the string long enough to ellipsize?
		if (strlen($str) <= $max_length)
		{
			return $str;
		}

		$beg = substr($str, 0, floor($max_length * $position));

		$position = ($position > 1) ? 1 : $position;

		if ($position === 1)
		{
			$end = substr($str, 0, -($max_length - strlen($beg)));
		}
		else
		{
			$end = substr($str, -($max_length - strlen($beg)));
		}

		return $beg.$ellipsis.$end;
	}
}


// ------------------------------------------------------------------------

if ( ! function_exists('truncateHtml')) {
	/**
	 * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
	 *
	 * @param string  $text         String to truncate.
	 * @param integer $length       Length of returned string, including ellipsis.
	 * @param string  $ending       Ending to be appended to the trimmed string.
	 * @param boolean $exact        If false, $text will not be cut mid-word
	 * @param boolean $considerHtml If true, HTML tags would be handled correctly
	 *
	 * @return string Trimmed string.
	 */
	function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
	{
		if( $considerHtml ) {
			// if the plain text is shorter than the maximum length, return the whole text
			if( strlen(preg_replace('/<.*?>/', '', $text)) <= $length ) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach($lines as $line_matches) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if( ! empty($line_matches[ 1 ]) ) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if( preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matches[ 1 ]) ) {
						// do nothing
						// if tag is a closing tag
					}
					else if( preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matches[ 1 ], $tag_matches) ) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matches[ 1 ], $open_tags);
						if( $pos !== false ) {
							unset($open_tags[ $pos ]);
						}
						// if tag is an opening tag
					}
					else if( preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matches[ 1 ], $tag_matches) ) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matches[ 1 ]));
					}
					// add html-tag to $truncated text
					$truncate .= $line_matches[ 1 ];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matches[ 2 ]));
				if( $total_length + $content_length > $length ) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if( preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matches[ 2 ], $entities, PREG_OFFSET_CAPTURE) ) {
						// calculate the real length of all entities in the legal range
						foreach($entities[ 0 ] as $entity) {
							if( $entity[ 1 ] + 1 - $entities_length <= $left ) {
								$left--;
								$entities_length += strlen($entity[ 0 ]);
							}
							else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matches[ 2 ], 0, $left + $entities_length);
					// maximum length is reached, so get off the loop
					break;
				}
				else {
					$truncate .= $line_matches[ 2 ];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if( $total_length >= $length ) {
					break;
				}
			}
		}
		else {
			if( strlen($text) <= $length ) {
				return $text;
			}
			else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if( ! $exact ) {
			// ...search the last occurance of a space...
			$spacePos = strrpos($truncate, ' ');
			if( isset($spacePos) ) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacePos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if( $considerHtml ) {
			// close all unclosed html-tags
			foreach($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}
}

/* End of file text_helper.php */
/* Location: ./system/helpers/text_helper.php */
