<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.92
 *
 */

/**
 * Ionize Tagmanager Form Class
 *
 * Gives a controller tags to manage forms through FTL
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	TagManager Libraries
 *
 */
class TagManager_Form extends TagManager
{

	public function __construct($controller)
	{
		$this->ci = $controller;
		
		$this->tag_definitions = array_merge($this->tag_definitions, array(
			'form_error' => 				'tag_form_error',
			'form_error_class' => 			'tag_form_error_class',
			'validation_errors' => 			'tag_validation_errors',
			'validation_errors_message' => 	'tag_validation_errors_message',
			'set_value' => 					'tag_set_value',
			'set_select' => 				'tag_set_select',
			'set_checkbox' => 				'tag_set_checkbox',
			'set_radio' => 					'tag_set_radio'
		));
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds global values to the context.
	 * 
	 * @param  FTL_Context
	 * @return void
	 */
	public function add_globals(FTL_Context $con)
	{
		parent::add_globals($con);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns an individual error HTML element message
	 * CI equivalent to : Form Validation->form_error('username')
	 *
	 * @usage	<ion:form_error input="username" tag="p" class="errorMessage" />
	 *
	 * @return 	String	The HTML error element, surrounded by the defined delimiter
	 *
	 */
	public function tag_form_error($tag)
	{
		$delimiter = (isset($tag->attr['tag'] )) ? $tag->attr['tag'] : false ;
		$class = (isset($tag->attr['class'] )) ? ' class="' . $tag->attr['class'] . '"' : '' ;
		$id = (isset($tag->attr['id'] )) ? ' id="' . $tag->attr['id'] . '"'  : '' ;

		$prefix = $suffix = '';
		
		if ($delimiter !== false)
		{
			$prefix = '<' . $delimiter . $id . $class . '>';
			$suffix = '</' . $delimiter .'>';
		}

		if (isset($tag->attr['input'] ))
		{
			// The form_validation object is available : the form controller sends the output directly
			// This is valid when data are send by XHR
			if (isset($this->ci->form_validation))
			{
				if ($this->ci->form_validation->error($tag->attr['input']) != '')
				{
					return $prefix . $this->ci->form_validation->error($tag->attr['input']) . $suffix;
				}
				else
					return;
			}
			// This could be called in case of form redirect to the form page (on fail)
			else if ($this->ci->session->flashdata('field_data'))
			{
				// field_data[$field]['error'] : Error message
				$data = $this->ci->session->flashdata('field_data');
				
				if (isset($data[$tag->attr['input']]['error']))
				{
					return $prefix . $data[$tag->attr['input']]['error'] . $suffix;
				}
			}
			
			// Return nothing if no form_validation object found
			return '';
		}
		
		// return nothing if no "input" attribute set.
		return '';
	}


	// ------------------------------------------------------------------------

		
	/**
	 * Returns a individual field error class in case of error
	 * 
	 * @usage	<label for="username" class="<ion:form_error_class input="username" class="errorlabel"/>">
	 *			In this case, the HTML class attribute will contains the CSS class "errorLabel" in case of error on the field "username"
	 *
	 * @return 	String	The CSS defined errorclass, or nothing if not defined
	 *
	 */
	public function tag_form_error_class($tag)
	{
		$class = (isset($tag->attr['class'] )) ? $tag->attr['class']  : '' ;
	
		if (isset($tag->attr['input'] ))
		{
			// The form_validation object is available : the form controller sends the output directly
			if (isset($this->ci->form_validation))
			{
				if ($this->ci->form_validation->error($tag->attr['input']) != '')
				{
					return $class;
				}
			}
			// This could be called in case of form redirect to the form page (on fail)
			else if ($this->ci->session->flashdata('field_data'))
			{
				// field_data[$field]['error'] : Error message
				$data = $this->ci->session->flashdata('field_data');
				
				if (isset($data[$tag->attr['input']]['error']) && $data[$tag->attr['input']]['error'] != '')
				{
					return $class;
				}
			}
			
			// Return nothing if no form_validation object found
			return '';
		}
		
		// return nothing if no "input" attribute set.
		return '';
	}

		
	// ------------------------------------------------------------------------


	/**
	 * Returns the all errors list, surrounded by the defined tag
	 * CI equivalent to : form_validation->error_string()
	 * 
	 * @usage	
	 * @return 
	 */
	public function tag_validation_errors($tag)
	{
		// Set message prefix and suffix. See form_validation or form_helper->validation_errors() for more info. 
		$delimiter = (isset($tag->attr['tag'] )) ? $tag->attr['tag'] : false ;
		$class = (isset($tag->attr['class'] )) ? ' class="' . $tag->attr['class'] . '"' : '' ;
		$id = (isset($tag->attr['id'] )) ? ' id="' . $tag->attr['id'] . '"'  : '' ;
	
		$prefix = $suffix = '';
		
		if ($delimiter !== false)
		{
			$prefix = '<' . $delimiter . $id . $class . '>';
			$suffix = '</' . $delimiter .'>';
		}
		
		// Set when the form controller directly output the new form
		if (isset($this->ci->form_validation))
		{
			return $prefix . $this->ci->form_validation->error_string() . $suffix;
		}
		// This could be called in case of form redirect to the form page (on fail)
		else if ($this->ci->session->flashdata('validation_errors'))
		{
			return $prefix . $this->ci->session->flashdata('validation_errors'). $suffix;
		}
		
		return '';
	}

				
	// ------------------------------------------------------------------------


	/**
	 * Returns a user defined error message if form_validation->error_string() is not empty
	 * Important : Does not return the error_string() content 
	 * CI equivalent to : form_validation->validation_errors()
	 *
	 * @usage	<ion:validation_errors_message tag="div" class="formErrorMessage formMessage" term="form_contact_error" />
	 *			In this case, the HTML tag will contains the tranlated term value "form_contact_error"
	 *			and will be showed only if some errors occur.
	 *
	 * @return 	The user defined error message, surrounded by the defined tag
	 *
	 */
	public function tag_validation_errors_message($tag)
	{
		$term = (isset($tag->attr['term'] )) ? $tag->attr['term']  : false ;
		$delimiter = (isset($tag->attr['tag'] )) ? $tag->attr['tag'] : false ;
		$class = (isset($tag->attr['class'] )) ? ' class="' . $tag->attr['class'] . '"' : '' ;
		$id = (isset($tag->attr['id'] )) ? ' id="' . $tag->attr['id'] . '"'  : '' ;
		
		$prefix = $suffix = '';
		
		if ($delimiter !== false)
		{
			$prefix = '<' . $delimiter . $id . $class . '>';
			$suffix = '</' . $delimiter .'>';
		}
		
		// Set when the form controller directly output the new form
		if (isset($this->ci->form_validation))
		{
			if ($this->ci->form_validation->error_string() != '')
				return $prefix . lang($term) . $suffix;
		}
		// This could be called in case of form redirect to the form page (on fail)
		else if ($this->ci->session->flashdata('validation_errors'))
		{
			if ($term != '')
			{
				return $prefix . lang($term) . $suffix;
			}
			else
			{
				return $prefix . $this->ci->session->flashdata('validation_errors') . $suffix;
			}
		}
		
		return '';
	}

		
	// ------------------------------------------------------------------------


	/**
	 * Returns the repopulated form field value 
	 * CI equivalent to : Form_Validation->set_value('field name')
	 *
	 * @return 	String	The field value
	 *
	 */
	public function tag_set_value($tag)
	{
		if (isset($tag->attr['input'] ))
		{
			if (isset($this->ci->form_validation))
			{
				$this->ci->session->unset_userdata('field_data');
				return set_value($tag->attr['input']);
			}
			else if ($this->ci->session->flashdata('field_data'))
			{
				$data = $this->ci->session->flashdata('field_data');

				if (isset($data[$tag->attr['input']]['postdata']))
				{
					return $data[$tag->attr['input']]['postdata'];
				}
			}
			else
			{
				return '';
			}
		}
		
		// return nothing if no "input" attribute set.
		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the repopulated form select  
	 * CI equivalent to : Form_Validation->set_select('field name')
	 *
	 * @return 	String	The field value
	 *
	 */
	public function tag_set_select($tag)
	{
		if (isset($tag->attr['select'] ))
		{
			if (isset($this->ci->form_validation))
			{
				$this->ci->session->unset_userdata('field_data');
				return set_select($tag->attr['select'], $tag->attr['value']);
			}
			else if ($this->ci->session->flashdata('field_data'))
			{
				$data = $this->ci->session->flashdata('field_data');
				
				if (isset($data[$tag->attr['select']]['postdata']))
				{
					$posted = $data[$tag->attr['select']]['postdata'];
					
					if (is_array($posted))
					{
						if (in_array($tag->attr['value'], $posted))
						{
							return ' selected="selected"';
						}
					}
					else
					{
						if($tag->attr['value'] == $posted)
						{
							return ' selected="selected"';
						}
					}
				}
				return '';
			}
			else
			{
				return '';
			}
		}
		
		// return nothing if no "input" attribute set.
		return '';
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the repopulated form checkbox 
	 * CI equivalent to : Form_Validation->set_checkbox('field name')
	 *
	 * @return 	String	The field value
	 *
	 */
	public function tag_set_checkbox($tag)
	{
		$attr = false;
		
		if (isset($tag->attr['checkbox']))
			$attr = $tag->attr['checkbox'];
		else if (isset($tag->attr['radio']))
			$attr = $tag->attr['radio'];
		
		if ($attr !== false)
		{
			if (isset($this->ci->form_validation))
			{
				$this->ci->session->unset_userdata('field_data');
				return set_select($attr, $tag->attr['value']);
			}
			else if ($this->ci->session->flashdata('field_data'))
			{
				$data = $this->ci->session->flashdata('field_data');
				
				if (isset($data[$attr]['postdata']))
				{
					$posted = $data[$attr]['postdata'];
					
					if (is_array($posted))
					{
						if (in_array($tag->attr['value'], $posted))
						{
							return ' checked="checked"';
						}
					}
					else
					{
						if($tag->attr['value'] == $posted)
						{
							return ' checked="checked"';
						}
					}
				}
				return '';
			}
			else
			{
				return '';
			}
		}
		
		// return nothing if no "input" attribute set.
		return '';
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Returns the repopulated form radio 
	 * CI equivalent to : Form_Validation->set_radio('field name')
	 *
	 * @return 	String	The field value
	 *
	 */
	public function tag_set_radio($tag)
	{
		return self::tag_set_checkbox($tag);
	}
	
}

/* End of file Form.php */
/* Location: /application/libraries/Tagmanager/Form.php */