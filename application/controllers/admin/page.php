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
		$this->load->model('system_check_model', '', true);
		$this->load->model('url_model', '', true);
		
		// Libraries
		$this->load->library('structure');

		$this->load->helper('string_helper');
		$this->load->helper('text_helper');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Create one page
	 * @param	string		Menu ID
	 *
	 */
	function create($id_menu) 
	{	
		$this->load_modules_addons();
	
		// Current menu : Needs to be improved.
		// TODO : Create a menu table or see how to manage menus.
		$this->template['id_menu'] = $id_menu;

		// Create blank data for this page
		$this->page_model->feed_blank_template($this->template);
		$this->page_model->feed_blank_lang_template($this->template);

		// Dropdown menus
		$datas = $this->menu_model->get_select();
		$this->template['menus'] =	form_dropdown('id_menu', $datas, $id_menu, 'id="id_menu" class="select"');
		

		// Dropdowns Views : Get $view var from my_theme/config/views.php
		$views = array();
		if (is_file(FCPATH.'themes/'.Settings::get('theme').'/config/views.php'))
			require_once(FCPATH.'themes/'.Settings::get('theme').'/config/views.php');
		
		// Dropdown Page views
		$datas = isset($views['page']) ? $views['page'] : array() ;
		if(count($datas) > 0)
		{
			$datas = array('0' => lang('ionize_select_default_view')) + $datas; 
			$this->template['views'] = $this->template['single_views'] = form_dropdown('view', $datas, false, 'class="select w160"');
		}

		// Dropdown Article views
		$datas = isset($views['article']) ? $views['article'] : array() ;
		if(count($datas) > 0)
		{
			$datas = array('0' => lang('ionize_select_default_view')) + $datas; 
			$this->template['article_views'] = form_dropdown('article_view', $datas, false, 'class="select w160"');
			$this->template['article_list_views'] = form_dropdown('article_list_view', $datas, false, 'class="select w160"');
		}

		// Access groups : authorizations
		$groups = $this->page_model->get_groups_select();
//		$this->template['groups'] =	form_dropdown('groups[]', $groups, false, 'class="select" multiple="multiple"');
		$this->template['groups'] =	form_dropdown('id_group', $groups, false, 'class="select"');
		
		$this->template['priority'] = '5';
		$this->template['has_url'] = '1';
		
		/*
		 * Extend fields
		 *
		 */
		$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('page');

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
		// Datas
		$page = $this->page_model->get($id);

		if( !empty($page) )
		{
			$this->load_modules_addons($page);

			// Correct the menu ID (for phantom pages)
			if ($page['id_menu'] == '0') $page['id_menu'] = '1'; 
			
			$this->template = array_merge($this->template, $page);
			
			// Array of path to the element. Gives the complete URL to the element.
//			$this->template['parent_array'] = $this->page_model->get_parent_array($id);
			
			// Dropdown menus
			$datas = $this->menu_model->get_select();
			$this->template['menus'] = form_dropdown('id_menu', $datas, $this->template['id_menu'], 'id="id_menu" class="select"');
			
			// Subnav menu
			$subnav_page = $this->page_model->get($page['id_subnav']);
			$selected_subnav = ( ! empty($subnav_page['id_menu'])) ? $subnav_page['id_menu'] : '-1';
			$this->template['subnav_menu'] = form_dropdown('id_subnav_menu', $datas, $selected_subnav, 'id="id_subnav_menu" class="select"');


			// Dropdowns Views
			$views = array();
			if (is_file(FCPATH.'themes/'.Settings::get('theme').'/config/views.php'))
				require_once(FCPATH.'themes/'.Settings::get('theme').'/config/views.php');
			
			// Dropdown Page views
			$datas = isset($views['page']) ? $views['page'] : array() ;
			if(count($datas) > 0)
			{
				$datas = array('' => lang('ionize_select_default_view')) + $datas; 
				$this->template['views'] = form_dropdown('view', $datas, $this->template['view'], 'class="select w140"');
				$this->template['single_views'] = form_dropdown('view_single', $datas, $this->template['view_single'], 'class="select"');
			}
			
			// Dropdown article list views (templates)
			$datas = isset($views['article']) ? $views['article'] : array() ;
			if(count($datas) > 0)
			{
				$datas = array('' => lang('ionize_select_default_view')) + $datas; 
				$this->template['article_list_views'] = form_dropdown('article_list_view', $datas, $this->template['article_list_view'], 'class="select"');
			}
			
			// Dropdown article views (templates)
			$datas = isset($views['article']) ? $views['article'] : array() ;
			$datas = array('' => lang('ionize_select_default_view')) + $datas; 
			if(count($datas) > 0)
			{
				$this->template['article_views'] = form_dropdown('article_view', $datas, $this->template['article_view'], 'class="select"');
			}
			
			// Breadcrumbs
			$pages = $this->page_model->get_parent_array(array('id_page' => $id), array(), Settings::get_lang('default'));
			
			$breadcrump = array();
			foreach($pages as $page)
			{
				$breadcrump[] = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
			}
			$this->template['breadcrump'] = implode(' > ', $breadcrump);

			
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
			$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('page', $id);
			
			/*
			 * Lang data
			 */
			$this->page_model->feed_lang_template($id, $this->template);
			
			
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
			
			// Clear the cache
			Cache()->clear_cache();
			
			// Prepare data before save
			$this->_prepare_data();

			// Save Page
			$saved_id = $this->page_model->save($this->data, $this->lang_data);

			// Correct DB integrity : links URL and names, childrens pages menus
			if ( ! empty($id) )
			{
				$this->page_model->correct_integrity($this->data, $this->lang_data);
				
				// Correct pages levels regarding parents.
				$this->system_check_model->check_page_level(TRUE);
			}

			// Save extends fields data
			$this->extend_field_model->save_data('page', $saved_id, $_POST);
					
			// Save linked access groups authorizations
			// $this->base_model->join_items_keys_to('user_groups', $this->input->post('groups'), 'page', $this->id);

			// Save Home page
			if ($this->data['home'] == '1')
			{
				$this->page_model->update_home_page($saved_id);
			}
			
			// Save the Urls
//			$this->url_model->save_page_urls($saved_id);
			
			$this->page_model->save_urls($saved_id);
			
			
			// Set the Path (breadcrumb)
			// Breadcrumbs
/*
// One breadcrumb / lang
// Take care to the path !!!

			$pages = $this->page_model->get_parent_array(array('id_page' => $saved_id), array(), Settings::get_lang('default'));
			
			$breadcrump = array();
			foreach($pages as $page)
			{
				$breadcrump[] = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
			}
			$this->template['breadcrump'] = implode(' > ', $breadcrump);
*/			
			
			// Save the Sitemap
			$this->structure->build_sitemap();

			// Prepare the Json answer
			$page = array_merge($this->lang_data[Settings::get_lang('default')], $this->page_model->get($saved_id));

			$page['menu'] = $this->menu_model->get($page['id_menu']);

			// Remove HTML tags from returned array
			strip_html($page);

			if ( empty($id))
			{
				// Used by JS Tree to detect if page in inserted in tree or not
				$page['inserted'] = TRUE;
				
				$this->callback = array(
					'fn' => $page['menu']['name'].'Tree.insertElement',
					'args' => array($page, 'page')
				);
			}
			else
			{
				$this->callback = array(
					'fn' => 'ION.updateTreePage',
					'args' => $page
				);
			}				

			$this->update[] = array(
				'element' => 'mainPanel',
				'url' => admin_url() . 'page/edit/'.$saved_id,
				'title' => lang('ionize_title_edit_page')
			);

			// Answer
			$this->success(lang('ionize_message_page_saved'));
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
		// Clear the cache
		Cache()->clear_cache();

		$status = $this->page_model->switch_online($id);

		$this->callback = array(
			array(
				'fn' => 'ION.switchOnlineStatus',
				'args' => array(
					'status' => $status,
					'selector' => '.page'.$id
				)
			)
		);
		
		// Answer send
		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves page ordering
	 * 
	 */
	function save_ordering()
	{
		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Clear the cache
			Cache()->clear_cache();

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

	function reorder_articles()
	{
		$id_page = $this->input->post('id_page');
		$direction = $this->input->post('direction');
		
		if ($direction && $id_page)
		{
			// Clear the cache
			Cache()->clear_cache();

			$articles = $this->article_model->get_lang_list(array('id_page'=>$id_page), Settings::get_lang('default'));
			
			$kdate = array();
			foreach($articles as $key => $article)
			{
				$kdate[$key] = strtotime($article['date']);
			}

			$sort_direction = 'SORT_'.$direction;
			
			// Sort the results by realm occurences DESC first, by date DESC second.			
			array_multisort($kdate, constant($sort_direction), $articles);
			
			$ids = array();
			foreach($articles as $idx => $article)
			{
				$this->page_model->update(array('id_page'=>$id_page, 'id_article' => $article['id_article']), array('ordering' => $idx + 1), 'page_article');
				$ids[] = $article['id_article'];
			}

			$this->callback = array(
				array(
					'fn' => 'ION.HTML',
					'args' => array('article/get_list', array('id_page' => $id_page), array('update' => 'articleListContainer'))
				),
				array(
					'fn' => 'ION.notification',
					'args' => array('success', lang('ionize_message_articles_ordered'))
				),
				array(
					'fn' => 'ION.updateArticleOrder',
					'args' => array(
						'id_page' => $id_page,
						'order' => implode(',', $ids)
					)
				)				
			);

			$this->response();
		}
	}


	function update_field()
	{
		$field = $this->input->post('field');
		$id_page = $this->input->post('id_page');
		$type = $this->input->post('type');
		
		if ($id_page && $field)
		{
			$value = $this->input->post('value');
			
			// Check the type of data, for special process
			if ($type == 'date')
			{
				$value = ($value) ? getMysqlDatetime($value) : '0000-00-00 00:00:00';
			}

			// Update
			$result = $this->page_model->update(array('id_page' => $id_page), array($field => $value));

			if ($result)
			{
				// Datas
				$page = $this->page_model->get($id_page, Settings::get_lang('default'));
				$menu = $this->menu_model->get($page['id_menu']);
				
//				$page = array_merge($this->page_model->get_lang(Settings::get_lang('default')), $page);
				$page['title'] = htmlspecialchars_decode($page['title'], ENT_QUOTES);
				$page['element'] = 'page';
				$page['menu'] = $menu;

				$this->callback[] = array(
					'fn' => 'ION.notification',
					'args' => array('success', lang('ionize_message_page_saved'))
				);

				$this->callback[] = array(
					'fn' => 'ION.updateTreePage',
					'args' => $page
				);

				$this->response();
			}
		}
	}


	// ------------------------------------------------------------------------


	function get_link()
	{
		// Get the link
		$id_page = $this->input->post('id_page');
		
		$page = $this->page_model->get(array('id_page' => $id_page));
		$link_type = $page['link_type'];

		$this->template = array('parent' => 'page');

		if ( ! empty($page['link']) )		
		{
			$title = NULL;
			
			// Prep the Link
			switch($page['link_type'])
			{
				case 'page' :
					$link = $this->page_model->get(array('id_page' => $page['link_id']), Settings::get_lang('default'));
					$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
					break;
					
				case 'article' :
					$link_rel = explode('.', $page['link_id']);
					$link = $this->article_model->get(array('id_article' => $link_rel[1]), Settings::get_lang('default'));
					$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
					break;
				
				case 'external' :
					$link_rel = '';
					$title = $page['link'];
					break;
			}

			$this->template = array(
				'parent' => 'page',
				'rel' => $id_page,
				'link_id' => $page['link_id'],
				'link_type' => $page['link_type'],
				'link' => $title
			);
		}

		$this->output('link');
	}
	

	// ------------------------------------------------------------------------


	/**
	 * Adds a link to a page
	 *
	 * Receives : 
	 * $_POST['link_type'] : 	type of the link
	 * $_POST['link_rel'] : 	REL to the link (can be a page or an article)
	 * $_POST['receiver_rel'] : REL of the receiver's article
	 * $_POST['url'] : 			URL of the external link
	 *
	 */
	function add_link()
	{
		// Clear the cache
		Cache()->clear_cache();

		// Sent by ION.dropElementAsLink() or ION.addExternalLink()
		$id_page = $this->input->post('receiver_rel');
		$link_type = $this->input->post('link_type');
		$link_id = $this->input->post('link_rel');

		// Link name (default lang title, for display)
		$title = NULL;
		
		switch($link_type)
		{
			case 'page' :
				$link = $this->page_model->get(array('id_page' => $link_id), Settings::get_lang('default'));
				$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
				break;
			
			case 'article' :
				$link_rel = explode('.', $link_id);
				$link = $this->article_model->get(array('id_article' => $link_rel[1]), Settings::get_lang('default'));
				$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
				break;

			case 'external' :
				$link_rel = '';
				if ($this->input->post('url') != lang('ionize_label_drop_link_here')) 
				{
					$title = prep_url($this->input->post('url'));
				}
				else
				{
					$title = $link_type = '';
				}
				break;
		}

		$data = array(
			'link_id' => $link_id,
			'link_type' => $link_type,
			'link' => $title
		);


		// Save the link
		$this->page_model->update(array('id_page' => $id_page), $data);

		// Test the external link
		/*
		if ($link_type == 'external')
		{
			$check = check_url($title);
			
			if($check === FALSE)
			{
				$this->callback[] = array
				(
					'fn' => 'ION.notification',
					'args' => array	(
						'error',
						lang('ionize_message_url_not_found')
					)
				);
				$this->response();

			}
			elseif($check == 404)
			{
				$this->callback[] = array
				(
					'fn' => 'ION.notification',
					'args' => array	(
						'error',
						lang('ionize_message_url_got_404')
					)
				);
				$this->response();
			}
		}
		*/
		
		if ($title != '')
		{
			$this->callback = array(
				array(
					'fn' => 'ION.HTML',
					'args' => array('page/get_link', array('id_page' => $id_page), array('update' => 'linkContainer'))
				),
				array(
					'fn' => 'ION.notification',
					'args' => array('success', lang('ionize_message_link_added'))
				)
			);
		}

		$this->response();
	}


	// ------------------------------------------------------------------------


	function remove_link()
	{
		$id_page = $this->input->post('rel');
		
		if ($id_page)
		{
			// Clear the cache
			Cache()->clear_cache();

			$context = array(
				'link_type' => '',
				'link_id' => '',
				'link' => ''
			);

			// Save the context		
			$this->page_model->update(array('id_page' => $id_page), $context);

			$this->callback = array(
				array(
					'fn' => 'ION.HTML',
					'args' => array('page/get_link', array('id_page' => $id_page), array('update' => 'linkContainer'))
				)
			);

			$this->response();
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
			// Clear the cache
			Cache()->clear_cache();
			
			// Remove deleted article from DOM
			$this->callback[] = array(
				'fn' => 'ION.deleteDomElements',
				'args' => array('.page' . $id)
			);
			
			// If the current edited article is deleted
			if ($this->input->post('redirect'))
			{
				$this->callback[] = array(
					'fn' => 'ION.updateElement',
					'args' => array(
						'element' => 'mainPanel',
						'url' => 'dashboard'
					)
				);
			}

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
//			if ( ! in_array($field, $this->no_htmlspecialchars))
//				$this->data[$field] = htmlspecialchars($this->input->post($field), ENT_QUOTES, 'utf-8');
//			else
				$this->data[$field] = $this->input->post($field);
		}

		// level ?
		$parent = $this->page_model->get($this->input->post('id_parent'));

		if ( ! empty($parent) )
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

		
		// Update the page name (not used anymore in the frontend)
		$this->data['name'] = $urls[Settings::get_lang('default')];


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
//					if ( ! in_array($field, $this->no_htmlspecialchars))
//						$content = htmlspecialchars($content, ENT_QUOTES, 'utf-8');
						
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

		unset($this->data['link']);
		unset($this->data['link_type']);
		unset($this->data['link_id']);
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
				$urls[$l['lang']] = url_title(convert_accented_characters($this->input->post('url_'.$l['lang'])));
			}
			else
			{
				// Try to use the lang title
				if ( $this->input->post('title_'.$l['lang']) !== '' )
				{
					$urls[$l['lang']] = url_title(convert_accented_characters($this->input->post('title_'.$l['lang'])));
				}
				// Fill with empty value if needed 
				else if ($fill_empty_lang == TRUE)
				{
					$urls[$l['lang']] = '';
				}
			}
		}

		$default_lang_url = $urls[Settings::get_lang('default')];
		
		foreach($urls as $lang => $url)
		{
			if ($url == '')
				$urls[$lang] = $default_lang_url;
		}

		
		return $urls;
	}

}


/* End of file page.php */
/* Location: ./application/controllers/admin/page.php */