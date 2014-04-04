<?php 
/*
 * Created on 2009 Jan 02
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

require 'context.php';
require 'binding.php';
require 'varstack.php';


/**
 * XML template parser.
 * 
 * @package FTL_Parser
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @copyright Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class FTL_Parser{
	
	/**
	 * The tag prefix.
	 * 
	 * @var string
	 */
	public $tag_prefix = 't';
	
	/**
	 * The context used to render the data tree.
	 * 
	 * @var XT_Context
	 */
	public $context;
	
	/**
	 * The parser stack.
	 * 
	 * @var array
	 */
	protected $stack;
	
	/**
	 * The current scope of the parsing, also the result of the parsing.
	 * 
	 * @var array
	 */
	protected $current;
	
	/**
	 * PHP array, that will be extracted before parsing.
	 * If the string, that will be parsed contains PHP,
	 * this array will be available.
	 * 
	 * @var array
	 */
	protected $php_data;


	/**
	 * @param  FTL_Context	The context to use
	 * @param  array 		The other options
	 *
	 */
	function __construct($context = FALSE, $options = array())
	{
		if(is_array($context) && empty($options))
		{
			$options = $context;
			$context = isset($options['context']) ? $options['context'] : NULL;
		}
		
		$this->context = $context instanceof FTL_Context ? $context : new FTL_Context();
		$this->tag_prefix = isset($options['tag_prefix']) ? $options['tag_prefix'] : 't';
		$this->php_data = isset($options['php_data']) ? $options['php_data'] : FALSE;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * The parsing initializer.
	 * 
	 * @param  string  The string to parse
	 * @return string
	 */
	public function parse($text)
	{
		$tree = $this->generate_tree($text);

		return $this->render($tree);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Generates a tree containing a parsed block structure.
	 * 
	 * @param  string
	 * @return array
	 */
	public function generate_tree($string)
	{
		unset($this->current);
		unset($this->stack);
		
		$this->current = array();
		$this->stack = array(array('content' => &$this->current));
		
		$this->pre_parse($string);
		
		return $this->current;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Renders the block tree to a single string.
	 * 
	 * @param  array The block tree structure
	 * @return string
	 */
	public function render($tree)
	{
		$this->context->parser =& $this;
		
		$result = $this->compile($tree);
		
		unset($this->context->parser);
		
		$result = $this->parse_php($result);
		
		if(config_item('compress_html_output') == 1)
		    return $this->compress($result);

		return $result;
	}
	
	// --------------------------------------------------------------------
		
	/**
     * Compress HTML output
     *
     * To remove useless whitespace from generated HTML.
     *
     * @param $output
     * @return mixed
     */
    public function compress($output)
    {
        $buffer = $output;

        $search = array(
            '/\>[^\S ]+/s', //strip whitespaces after tags, except space
            '/[^\S ]+\</s', //strip whitespaces before tags, except space
            '/(\s)+/s', // shorten multiple whitespace sequences
            '/&lt;!--(.|\s)*?--&gt;/', // strip HTML comments
            '#(?://)?<!\[CDATA\[(.*?)(?://)?\]\]>#s' // leave CDATA alone
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            '',
            "//&lt;![CDATA[\n".'\1'."\n//]]>"
        );

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }
	
	// --------------------------------------------------------------------
		
	/**
	 * Compiles the data tree, calling the context for all the tags.
	 *
	 * @param  array  The stack to parse
	 * @return string
	 */
	public function compile($stack)
	{
		if(empty($stack))
		{
			return '';
		}
		elseif(is_array($stack) && isset($stack['name']))
		{
			$stack = array($stack);
		}

		$str = '';
		foreach((Array) $stack as $key => $element)
		{
			if(is_string($element))
			{
				$str .= $element;
			}
			elseif( ! empty($element))
			{
				$str .= $this->context->render_tag($element['name'], $element['args'], $element['content']);
			}
		}
		return $str;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Parses all blocks into $this->current.
	 * 
	 * @param  string  The string to parse
	 * @return void
	 */
	protected function pre_parse($string)
	{
		// The Correct Regex for proper XML: '%([\w\W]*?)(<' . $this->tag_prefix . ':([\w:]+?)(\s+(?:\w+\s*=\s*(?:"[^"]*?"|\'[^\']*?\')\s*)*|)>|</' . $this->tag_prefix . ':([\w:]+?)\s*>)([\w\W]*)%'
		while(preg_match('%([\w\W]*?)<(/)?' . $this->tag_prefix . ':([\w-:]+)([^>]*?)(/)?>([\w\W]*)%', $string, $matches))
		{
			// Reset so we won't overwrite the stuff
			unset($tmp);
			unset($parent);
			unset($data);
			
			list(, $pre_match, $is_end_tag, $tag, $args, $is_individual, $string) = $matches;

			$this->current[] = $pre_match;

			// Is it an individual tag?
			if( ! empty($is_individual))
			{
				// Yes
				$data = array(
					'name' => $tag,
					'args' => $this->parse_args($args),
					'content' => array()
				);
				
				$this->current[] =& $data;
				
				// Done, go to next
				continue;
			}
			
			// Is it a block
			if(empty($is_end_tag))
			{			
				// create new block
				$data = array(
					'name' => $tag,
					'args' => $this->parse_args($args),
					'content' => array()
				);
				
				// add it to the tree
				$this->current[] =& $data;
				$this->stack[] =& $data;
				
				// move deeper
				$this->current =& $data['content'];
			}
			else
			{
				// close the current block
				unset($this->current);
				$tmp =& array_pop($this->stack);
				
				if($tag == $tmp['name'])
				{
					// move up in the tree
					$parent =& $this->stack[count($this->stack) - 1];
					$this->current =& $parent['content'];
				}
				else
				{
					show_error('Missing End tag for "'.$tag.'"');
				}
			}
		}
		
		$this->current[] = $string;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Parses all arguments for a tag.
	 * 
	 * @param  string  The argument string
	 * @return array
	 */
	protected function parse_args($string)
	{
		$arguments = array();
		
		preg_match_all('@([\w-]+?)\s*=\s*(\'|")(.*?)\2@', $string, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)
		{
			$arguments[$match[1]] = $match[3];
		}
		return $arguments;
	}
	
	
	// --------------------------------------------------------------------
	
	
	protected function parse_php($string)
	{
		ob_start();

		echo $this->get_eval($string);

		$string = ob_get_contents();

		ob_end_clean(); 

		return $string;
	}
	
	protected function get_eval($string)
	{
		// Extract PHP data before eval.
		// It contains an array, that will be available for the PHP code.
		if ($this->php_data)
			extract($this->php_data);
		return eval('?>'.$string.'<?php ');
	}
}

/* End of file parser.php */
/* Location: ./application/libraries/ftl/parser.php */
