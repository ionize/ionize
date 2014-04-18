<?php

class Ajaxform_Tags extends TagManager_Form
{
	/**
	 * Module's config
	 * @var array
	 */
	private static $config = array();

	/**
	 * Tags declaration
	 *
	 */
	public static $tag_definitions = array
	(
		'form' => 	'tag_form',
	);

	/**
	 * Displays the form
	 * Inherits from TagManager_Form the form displaying methods
	 * so the view can be slightly the same than the one used for one classical form
	 *
	 * @param FTL_Binding $tag
	 *
	 * @return string
	 */
	public static function tag_form(FTL_Binding $tag)
	{
		$ajax = $tag->getAttribute('ajax');

		// Ajax form
		if ($ajax == TRUE)
		{
			// Get form string
			$str = parent::tag_form($tag);

			// No JS  The user will add the JS part in his own JS script
			$nojs = $tag->getAttribute('nojs');
			$form_name = $tag->getAttribute('name');
			$form_submit_id = $tag->getAttribute('submit');
			// $error_tag = $tag->getAttribute('error_tag');
			// $error_tag_class = $tag->getAttribute('error_tag_class');

			// Module settings
			self::$config = Modules()->get_module_config('Ajaxform');

			if ( ! $nojs)
			{
				// Add the JS part of the module
				if ($form_name && $form_submit_id)
				{
					$data = array(
						'form_name' => $form_name,
						'form_submit_id' => $form_submit_id,
						'url' => base_url() . Settings::get_lang() . '/' . self::$config['uri'] . '/post'
					);

					$str .= self::$ci->load->view('ajaxform_js', $data, TRUE);
				}
				else
				{
					log_message('error', 'Ajaxform ERROR : Set the name & submit attributes of the <ion:form name="formName" submit="submitButtonID"> tag');
				}
			}

			return $str;
		}
		// Standard form handling
		else
		{
			return parent::tag_form($tag);
		}
	}

}