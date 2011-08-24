<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Rss admin controller
*
* The controller that handles actions 
* related to Rss module admin.
*
* @author	Ionize Dev Team
*/
class Rss extends Module_Admin 
{
	/**
	* Constructor
	*
	* @access	public
	* @return	void
	*/
	function construct()
	{
		// Article Model : Needed by Rss model to extend Article_model
		$this->load->model('article_model', '', TRUE);
		$this->load->model('rss_model');
	}

	/**
	* Admin panel
	* Called from the modules list.
	*
	* @access	public
	* @return	parsed view
	*/
	function index()
	{
		// Get the modules config file
		include APPPATH . 'config/modules.php';

		// Get the module URI segment
		$this->template['uri'] = 'rss';
		
		foreach($modules as $segment => $f)
		{
			if($f == 'Rss')
				$this->template['uri'] = $segment; 
		}

		$this->output('admin/rss');
	}

	/**
	* Saves the config.php settings
	*
	* @access	public
	* @return	mixed
	*/
	function save_config()
	{
		// Model used to write config.php
		$this->load->model('config_model', '', TRUE);
		
		// Settings to save
		$settings = array('module_rss_feed_title', 'module_rss_feed_description', 'module_rss_feed_author');

		foreach($settings as $field)
		{
			$setting = '';
			
			// Set settings only if not FALSE
			if($this->input->post($field))
			{
				$setting = $this->input->post($field);
			}
			
			// Try to change the setting
			if($this->config_model->change('config.php', $field, addslashes($setting), 'Rss') == FALSE)
			{
				// Error message
				$this->error(lang('module_rss_error_writing_config_file'));
				
				die();
			}
		}
		
		// Answer
		$this->success(lang('ionize_message_operation_ok'));				
	}
	
	/**
	* Returns pages used for Rss
	* Used to display pages list.
	*
	* @access	public
	* @return	parsed view
	*/
	function get_pages()
	{
		// Page model
		$this->load->model('page_model', '', TRUE);

		// Rss pages ID
		$pages_id = explode(',', config_item('module_rss_pages'));
		
		// All pages
		$pages = $this->page_model->get_lang_list(FALSE, Settings::get_lang('default'));
		
		// Used Rss pages (empty array)
		$rss_pages = array();
		
		// Get the real used pages
		foreach($pages_id as $id)
		{
			foreach($pages as $page)
			{
				if($id == $page['id_page'])
				{
					$rss_pages[] = $page;
				}
			}
		}
		
		// Send Rss pages to template
		$this->template['pages'] = $rss_pages;		
		
		$this->output('admin/rss_pages');
	}

	/**
	* Add one page to the Rss Feed
	* Called when the Admin drags a page to the Rss pages list.
	*
	* @access	public
	* @return	mixed
	*/
	function add_page()
	{
		// Model used to write config.php
		$this->load->model('config_model', '', TRUE);

		// Already linked Rss Pages ID
		$pages_id = array();
		
		if(config_item('module_rss_pages') !== '')
			$pages_id = explode(',', config_item('module_rss_pages'));
		
		$id_page = $this->input->post('id_page');

		if( ! in_array($id_page, $pages_id))
		{
			$pages_id[] = $id_page;

			$pages_id = (count($pages_id) > 1) ? implode(',', $pages_id) : array_shift($pages_id);
						
			if($this->config_model->change('config.php', 'module_rss_pages', $pages_id, 'Rss') == FALSE)
			{
				// Error message
				$this->error(lang('module_rss_error_writing_config_file'));
				
				die();
			}
			else
			{
				$this->callback = array(
					array(
						'fn' => 'ION.HTML',
						'args' => array(admin_url() . 'module/rss/rss/get_pages', '', array('update' => 'rssPagesContainer'))
					),
					array(
						'fn' => 'ION.notification',
						'args' => array('success', lang('ionize_message_operation_ok'))
					)
				);

				$this->response();			
			}
		}
	}

	/**
	* Removes one page from the Rss Feed
	* Called when the Admin unlinks a page from the Rss pages list.
	*
	* @access	public
	* @return	mixed
	*
	*/
	function remove_page()
	{
		// Model used to write config.php
		$this->load->model('config_model', '', TRUE);
		
		// Already linked Rss Pages ID
		$pages_id = explode(',', config_item('module_rss_pages'));
		
		// The page ID to remove
		$id_page = $this->input->post('id_page');
	
		if(in_array($id_page, $pages_id))
		{
			$new_array = array();
			
			foreach($pages_id as $id)
			{
				if($id !== $id_page)
					$new_array[] = $id;
			}
			
			$pages_id = (count($new_array) > 1) ? implode(',', $new_array) : array_shift($new_array);
			
			if($pages_id == FALSE) $pages_id = '';
			
			if($this->config_model->change('config.php', 'module_rss_pages', $pages_id, 'Rss') == FALSE)
			{
				// Error message
				$this->error(lang('module_rss_error_writing_config_file'));
				
				die();
			}
			else
			{
				$this->callback = array(
					array(
						'fn' => 'ION.HTML',
						'args' => array(admin_url() . 'module/rss/rss/get_pages', '', array('update' => 'rssPagesContainer'))
					),
					array(
						'fn' => 'ION.notification',
						'args' => array('success', lang('ionize_message_operation_ok'))
					)
				);

				$this->response();			
			}
		}
	}
}

/* End of file rss.php */
/* Location: /modules/Rss/controllers/admin/rss.php */
