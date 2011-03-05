<?php 
/*
 * Created on  Jan 03
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

/**
 * A variant of the default context which fallbacks on the locals/globals.
 * 
 * If a tag isn't found, it tries to find a tag in the local
 * (or global, depending on setting) var scope.
 * If a matching key is found, it prints it (scalar) / repeats it (array)
 * otherwise it calls the parent::tag_missing().
 * 
 * @package FTL_Parser
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @copyright Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class FTL_ArrayContext extends FTL_Context
{
	/**
	 * Sets the data to be used by this array context.
	 * 
	 * <code>
	 * // example array, with all the different array structures and their impact on compiling the stack
	 * array(
	 *		'tag_name' => 'text which will replace it',
	 * 		'repeat' => array(
	 *			array('tag' => 'repeat 1'),
	 *			array('tag' => 'repeat 2'),
	 * 			array('tag' => 'repeat 3')
	 *		),
	 * 		'nested' => array(
	 *			'tag1' => 'replace 1',
	 *			'tag2' => 'replace 2'
	 *		)
	 *	);
	 * 
	 * // example template:
	 * <t:tag_name />
	 * 
	 * <t:repeat>
	 *     <t:tag />
	 * </t:repeat>
	 * 
	 * <t:nested>
	 *     <t:tag1 />
	 *     <t:tag2 />
	 * </t:nested>
	 * </code>
	 * 
	 * @param  array  An associative array, can contain nested arrays
	 * @return void
	 */
	public function set_data($data = array())
	{
		$this->globals->hash = array_merge($data, $this->globals->hash);
	}
	
	/**
	 * This searches the vars for the tag and renders it if it is present.
	 * 
	 * Parameters:
	 *	Single tags:
	 * 		manip - A list of callables separated with "|" to run the var through
	 *	Block tags:
	 *		use_globals - If to use the globals instead of the local vars
	 */
	public function tag_missing($name, $args = array(), $block = null)
	{
		if(isset($args['use_globals']) && strtolower($args['use_globals']) == 'yes')
		{
			// do we have a matching global key?
			if(isset($this->globals->$name))
			{
				$to_render = $this->globals->$name;
			}
			else
			{
				// nope
				return parent::tag_missing($name, $args, $block);
			}
		}
		else
		{
			// get previous locals
			$previous = end($this->tag_binding_stack);
			$previous_locals = $previous == null ? $this->globals : $previous->locals;
			
			// do we have a matching key?
			if(isset($previous_locals->$name))
			{
				$to_render = $previous_locals->$name;
			}
			else
			{
				// nope
				return parent::tag_missing($name, $args, $block);
			}
		}
		
		if(is_array($to_render))
		{
			// is the array empty?
			if(empty($to_render))
			{
				return '';
			}
			
			// create new binding and stack it
			$locals = new FTL_VarStack(isset($previous_locals) ? $previous_locals : $this->globals);
			$binding = new FTL_Binding($this, $locals, $name, $args, $block);
			$this->tag_binding_stack[$name] = $binding;
			$str = '';
			
			// don't make a repeat if we don't have numeric indexes
			if( ! isset($to_render[0]))
			{
				$to_render = array($to_render);
			}
			
			// loop the array and expand for every iteration
			foreach($to_render as $data)
			{
				// set the local vars
				$locals->hash = $data;
				$str .= $this->parser->compile($block);
			}
			
			// jump out
			array_pop($this->tag_binding_stack);
			
			return $str;
		}
		else
		{
			// single tag
			
			// shall we manipulate it?
			if(isset($args['manip']))
			{
				// iterate callables
				foreach(explode('|', $args['manip']) as $call)
				{
					$param = array($to_render);
					
					// get parameter (zero isn't acceptable)
					if(($start = strpos($call, '[')) != false && ($end = strpos($call, ']')) != false)
					{
						// we've got a parameter, extract it
						$param[] = substr($call, $start + 1, $end - $start - 1);
						$call = substr($call, 0, $start);
					}
					
					// TODO: Let it have a list of valid callbacks to validate against
					// to prevent calls to potentially harmful methods
					
					if(is_callable($call))
					{
						$to_render = call_user_func_array($call, $param);
					}
					// just ignore if it doesn't exist
				}
			}
			
			return $to_render;
		}
	}
}

/* End of file array_context.php */
/* Location: ./application/libraries/xt_parser/array_context.php */