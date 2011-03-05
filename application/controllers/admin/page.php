<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Page Controller
 * Display Page administration panel
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Page management
 * @author		Ionize Dev Team
 *
 */


class Page extends MY_admin
{

	/**
	 * Fields on wich the htmlspecialchars function will not be used before saving
	 * 
	 * @var array
	 */
	protected $no_htmlspecialchars = array('subtitle');


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model('menu_model', '', true);
		$this->load->model('page_model', '', true);
		$this->load->model('article_model', '', true);
		$this->load->model('structure_model', '', true);
		$this->load->model('extend_field_model', '', true);
		
		// Libraries
		$this->load->library('structure');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Create one page
	 * @param	string		Menu ID
	 *
	 */
	function create($id_menu) 
	{	
		// Current menu : Needs to be improved.
		// TODO : Create a menu table or see how to manage menus.
		$this->template['id_menu'] = $id_menu;

		// Create blank data for this page
		$this->page_model->feed_blank_template($this->template);
		$this->page_model->feed_blank_lang_template($this->template);

		// Dropdown menus
		$datas = $this->menu_model->get_select();
		$this->template['menus'] =	form_dropdown('id_menu', $datas, $id_menu, 'id="id_menu" class="select"');

		
		// Dropdown parents
//		$datas = $this->page_model->get_lang_list(array('id_menu' => '1'), Settings::get_lang('default'));
//		$parents = array('0' => '/');
//		($parents_array = $this->structure->get_parent_select($datas) ) ? $parents += $parents_array : '';
//		$this->template['parents'] =	form_dropdown('id_parent', $parents, false, 'class="select" id="id_parent"');

		
		// Dropdowns Views : Get $view var from my_theme/config/views.php
		if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
			require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');
		
		// Dropdown Page views
		$datas = isset($views['page']) ? $views['page'] : array() ;
		if(count($datas) > 0)
		{
			$datas = array('0' => lang('ionize_select_default_view')) + $datas; 
			$this->template['views'] = form_dropdown('view', $datas, false, 'class="select w160"');
		}

		// Dropdown Article views
		$datas = isset($views['article']) ? $views['article'] : array() ;
		if(count($datas) > 0)
		{
			$datas = array('0' => lang('ionize_select_default_view')) + $datas; 
			$this->template['article_views'] = form_dropdown('article_views', $datas, false, 'class="select w160"');
			$this->template['article_list_views'] = form_dropdown('article_list_views', $datas, false, 'class="select w160"');
		}

		// Access groups : authorizations
		$groups = $this->page_model->get_groups_select();
//		$this->template['groups'] =	form_dropdown('groups[]', $groups, false, 'class="select" multiple="multiple"');
		$this->template['groups'] =	form_dropdown('id_group', $groups, false, 'class="select"');

		/*
		 * Extend fields
		 *
		 */
		$this->template['extend_fields'] = array();
		if (Settings::get('use_extend_fields') == '1')
		{
			$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('page');
		}

		$this->output('page');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Edit one page
	 *
	 * @param	string	Page ID
	 *
	 */
	function edit($id)
	{
		$this->load->model('article_type_model', '', true);
		
	
		// Datas
		$page = $this->page_model->get($id);

		if( !empty($page) )
		{
			// Correct the menu ID (for phantom pages)
			if ($page['id_menu'] == '0') $page['id_menu'] = '1'; 
			
			$this->template = array_merge($this->template, $page);
			
			// Array of path to the element. Gives the complete URL to the element.
//			$this->template['parent_array'] = $this->page_model->get_parent_array($id);
			
			// Dropdown menus
			$datas = $this->menu_model->get_select();
			$this->template['menus'] =	form_dropdown('id_menu', $datas, $this->template['id_menu'], 'id="id_menu" class="select"');
			
			// Dropdowns Views
			if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
				require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');
			
			// Dropdown Page views
			$datas = isset($views['page']) ? $views['page'] : array() ;
			if(count($datas) > 0)
			{
				$datas = array('' => lang('ionize_select_default_view')) + $datas; 
				$this->template['views'] = form_dropdown('view', $datas, $this->template['view'], 'class="select w160"');
			}
			
			// Dropdown article list views (templates)
			$datas = isset($views['article']) ? $views['article'] : array() ;
			if(count($datas) > 0)
			{
				$datas = array('' => lang('ionize_select_default_view')) + $datas; 
				$this->template['article_list_views'] = form_dropdown('article_list_view', $datas, $this->template['article_list_view'], 'class="select w160"');
			}
			
			// Dropdown article views (templates)
			$datas = isset($views['article']) ? $views['article'] : array() ;
			$datas = array('' => lang('ionize_select_default_view')) + $datas; 
			if(count($datas) > 0)
			{
				$this->template['article_views'] = form_dropdown('article_view', $datas, $this->template['article_view'], 'class="select w160"');
			}
			
			// All articles views to template
			$this->template['all_article_views'] = $datas;
			
			// All articles type to template
			$datas = $this->article_type_model->get_types_select();
			$datas = array('' => lang('ionize_select_no_type')) + $datas; 
			$this->template['all_article_types'] = $datas;

			
			/*
			 * Groups access
			 *
			 * Formely multiple groups for one page. Not used anymore
			
			$groups = $this->page_model->get_groups_select();
			
			// Current groups
			$current_groups = $this->page_model->get_current_groups($id);
			*/
			
			// All groups
			$groups = $this->page_model->get_groups_select();
			
			// Form dropdown to template
			$this->template['groups'] =	form_dropdown('id_group', $groups, $page['id_group'], 'class="select"');
			
			
			/*
			 * Extend fields
			 *
			 */
			$this->template['extend_fields'] = array();
			if (Settings::get('use_extend_fields') == '1')
			{
				$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('page', $id);
			}
			
			/*
			 * Lang data
			 */
			$this->page_model->feed_lang_template($id, $this->template);
			
			/*
			 * Linked articles
			 */
			$articles = $this->article_model->get_lang_list(array('id_page'=>$id), Settings::get_lang('default'));
			
			// Add lang content to each article
			$this->article_model->add_lang_data($articles);
			
			// Add view logical name to article list (from theme/config/views.php file)
//			$this->article_model->add_view_name($articles, $views);

			$this->template['articles'] = $articles;
			
			/*
			 * Output the view
			 */
			$this->output('page');
		}
		else
		{
			$this->error(lang('ionize_message_page_not_exist'));
		}
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Saves a page
	 *
	 */
	function save() 
	{
		/* Check if the default lang URL or the default lang title are set
		 * One of these need to be set to save the page
		 *
		 */
		if ($this->_check_before_save() == TRUE)
		{

			$id = $this->input->post('id_page');
			
			// try to get the page with one of the form provided URL
			$urls = array_values($this->_get_urls());
			
			$pages = $this->page_model->get_from_urls($urls, $exclude = $id);

			// If no article ID (means new one) and this article URL already exists in DB : No save 
			if ( !empty($pages) )
			{
				$this->error(lang('ionize_message_page_url_exists'));
			}
			else
			{
				// Prepare data before save
				$this->_prepare_data();
	
				// Save base datas
				$this->id = $this->page_model->save($this->data, $this->lang_data);

				// Correct DB integrity : links URL and names, childrens pages menus
				if ( ! empty($id) )
					$this->page_model->correct_integrity($this->data, $this->lang_data);

				// Save extends fields data
				if (Settings::get('use_extend_fields') == '1')
					$this->extend_field_model->save_data('page', $this->id, $_POST);
						
				// Save linked access groups authorizations
				// $this->base_model->join_items_keys_to('user_groups', $this->input->post('groups'), 'page', $this->id);

				// Save Home page
				if ($this->data['home'] == '1')
				{
					$this->page_model->update_home_page($this->id);
				}


				// Prepare the Json answer
				$menu = $this->menu_model->get($this->data['id_menu']);
				
				$this->data = array_merge($this->lang_data[Settings::get_lang('default')], $this->data);
				$this->data['title'] = htmlspecialchars_decode($this->data['title'], ENT_QUOTES);
				$this->data['id_page'] = $this->id;
				$this->data['element'] = 'page';
				$this->data['menu'] = $menu;
				$this->data['ordering'] = $this->input->post('ordering');
				
				if ( empty($id))
				{
					$this->callback = array(
						'fn' => $menu['name'].'Tree.insertTreePage',
						'args' => $this->data
					);
				}
				else
				{
					$this->callback = array(
						'fn' => 'ION.updateTreePage',
						'args' => $this->data
					);
				}				

				$this->update[] = array(
					'element' => 'mainPanel',
					'url' => admin_url() . 'page/edit/'.$this->id,
					'title' => lang('ionize_title_edit_page')
				);

				// Answer
				$this->success(lang('ionize_message_page_saved'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_page_needs_url_or_title'));
		}
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Gets the parent list list for the parent select dropdown
	 * @param	int		Menu ID
	 * @param	int		Current page ID (will not be included in the list)
	 * @param	int		Parent page ID	 
	 *
	 * @returns	string	HTML string of options items
	 *
	 */
	function get_parents_select($id_menu, $id_current=0, $id_parent=0)
	{
		$datas = $this->page_model->get_lang_list(array('id_menu' => $id_menu), Settings::get_lang('default'));

		$parents = array('0' => '/');
		($parents_array = $this->structure->get_parent_select($datas, $id_current) ) ? $parents += $parents_array : '';
		
		$this->template['pages'] = $parents;
		$this->template['id_selected'] = $id_parent;
		
		$this->output('page_parent_select');
	}


	// ------------------------------------------------------------------------


	/**
	 * Set an item online / offline depending on its current status
	 *
	 * @param	int		item ID
	 *
	 */
	function switch_online($id)
	{
		$status = $this->page_model->switch_online($id);

		$this->id = $id;

		// Output array
		$output_data = array('status' => $status);
		
		// Answer send
		$this->success(lang('ionize_message_operation_ok'), $output_data);
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves page ordering
	 * 
	 */
	function save_ordering()
	{
		if( $order = $this->input->post('order') )
		{
			// Saves the new ordering
			$this->page_model->save_ordering($order);

			// Answer send
			$this->success(lang('ionize_message_page_ordered'));
		}		
		else 
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Deletes one page
	 * @note	For the moment, this method doesn't delete the linked articles, wich will stay in database as phantom
	 *
	 * @param	int		Page ID
	 *
	 */
	function delete($id)
	{
		$affected_rows = $this->page_model->delete($id);
		
		// Delete was successfull
		if ($affected_rows > 0)
		{
			// Remember the deleted page ID
			$this->id = $id;
			
			$this->success(lang('ionize_message_operation_ok'));
		}
		else
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	/** 
	 * Prepare page data before saving
	 *
	 * 
	 */
	function _prepare_data() 
	{
		// Standard fields
		$fields = $this->db->list_fields('page');
		
		// Set the data to the posted value.
		foreach ($fields as $field)
		{
			if ( ! in_array($field, $this->no_htmlspecialchars))
				$this->data[$field] = htmlspecialchars($this->input->post($field), ENT_QUOTES, 'utf-8');
			else
				$this->data[$field] = $this->input->post($field);
		}

		// level ?
		if ($parent = $this->page_model->get($this->input->post('id_parent')) )
			$this->data['level'] = $parent['level'] + 1;	
		else 
			$this->data['level'] = 0;

		// Author & updater
		$current_user = $this->connect->get_current_user();
		if ($this->input->post('id_page'))
			$this->data['updater'] = $current_user['username'];
		else
			$this->data['author'] =  $current_user['username'];

		// Unset Ordering if no new page : Saved by page ordering in tree view
		if (!$this->input->post('id_page') || $this->input->post('id_page') == '')
		{
			$this->db->select_max('ordering', 'ordering');
			$this->db->where('id_menu', $this->input->post('id_menu'));
			$query = $this->db->get('page');

			if ($query->num_rows() > 0)
			{
				$row = $query->row(); 
				$this->data['ordering'] = $row->ordering + 1;
			}
		}
		else
		{
			unset($this->data['ordering']);
		}


		// URLs : Feed the other languages URL with the default one if the URL is missing
		$urls = $this->_get_urls(TRUE);

		$default_lang_url = $urls[Settings::get_lang('default')];
		
		foreach($urls as $lang => $url)
		{
			if ($url == '')
				$urls[$lang] = $default_lang_url;
		}
		
		// Update the page name (not used anymore in the frontend)
		$this->data['name'] = $default_lang_url;


		// Lang data
		$this->lang_data = array();

		$fields = $this->db->list_fields('page_lang');

		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				if ( $field != 'url' && $this->input->post($field.'_'.$language['lang']) !== false)
				{
					$content = $this->input->post($field.'_'.$language['lang']);
					
					// Convert HTML special char only on other fields than these defined in $no_htmlspecialchars
					if ( ! in_array($field, $this->no_htmlspecialchars))
						$content = htmlspecialchars($content, ENT_QUOTES, 'utf-8');
						
					$this->lang_data[$language['lang']][$field] = $content;
				}
				// URL : Fill with the correct URLs array data
				else if ($field == 'url')
				{
					$this->lang_data[$language['lang']]['url'] = $urls[$language['lang']];
				}
			}
			
			// Online value
			$this->lang_data[$language['lang']]['online'] = $this->input->post('online_'.$language['lang']);
		}

		/*
		 * Links
		 *
		 */
		if ($this->data['link'] != lang('ionize_label_drop_link_here'))
		{
			// External Link cleaning : We assume an external link has a "." in its URL
			if (strpos($this->data['link'], '.') !== FALSE OR $this->data['link_type'] == '')
			{
				$this->data['link_id'] = '';
				$this->data['link_type'] = 'external';
				
				if ( ! empty($this->data['link']))
					$this->data['link'] = prep_url($this->data['link']);
				
				// This link is unique : All languages data need to have the same
				foreach(Settings::get_languages() as $language)
				{
					$this->lang_data[$language['lang']]['link'] = $this->data['link'];
				}
				
			}
			// Internal link : Get link urls for each language
			else if ($this->data['link_type'] != '' && $this->data['link_type'] != '0')
			{
				$elements = $this->{$this->data['link_type'].'_model'}->get_lang($this->data['link_id']);
				
				foreach ($elements as $element)
				{
					$this->lang_data[$element['lang']]['link'] = $element['url'];
				}
			}
		}
		// Clean languages link
		else
		{
			$this->data['link'] = '';
			
			foreach(Settings::get_languages() as $language)
			{
				$this->lang_data[$language['lang']]['link'] = '';
			}
		}
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Checks if the element save process can be done.
	 *
	 * @returns		Boolean		True if the save can be done, false if not
	 *
	 */
	function _check_before_save()
	{
		$default_lang = Settings::get_lang('default');
		$default_lang_url = $this->input->post('url_'.$default_lang);
		$default_lang_title = $this->input->post('title_'.$default_lang);
		
		if ($default_lang_url == FALSE && $default_lang_title == FALSE)
		{
			return FALSE;
		}
		
		return TRUE;
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Returns all the URLs sent for this element
	 *
	 * @param		Boolean		Should the empty lang index be filled with '' ?
	 *
	 * @returns		Array		Multidimensional array of URLs
	 *							ex : $url['en'] = 'my-element-url'
	 *
	 */
	function _get_urls($fill_empty_lang = FALSE)
	{
		$urls = array();
		
		foreach(Settings::get_languages() as $l)
		{
			// If lang URL exists, use it
			if ( $this->input->post('url_'.$l['lang']) !== '' )
			{
				$urls[$l['lang']] = url_title($this->input->post('url_'.$l['lang']));
			}
			else
			{
				// Try to use the lang title
				if ( $this->input->post('title_'.$l['lang']) !== '' )
				{
					$urls[$l['lang']] = url_title($this->input->post('title_'.$l['lang']));
				}
				// Fill with empty value if needed 
				else if ($fill_empty_lang == TRUE)
				{
					$urls[$l['lang']] = '';
				}
			}
		}
		
		return $urls;
	}

}


/* End of file page.php */
/* Location: ./application/controllers/admin/page.php */