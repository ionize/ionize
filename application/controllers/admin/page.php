<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Page Controller
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
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
	 * Data array representing one article
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Lang Data array of the article
	 *
	 * @var array
	 */
	protected $lang_data = array();

	/**
	 * Boolean options (checkboxes)
	 *
	 * @var array
	 */
	protected $boolean_options = array('used_by_module', 'appears', 'has_url', 'home');


	/**
	 * Frontend / Backend Authority actions
	 * @var array
	 */
	protected static $_AUTHORITY_BACKEND_ACTIONS = array('edit','delete','status','add_page','add_article');
	protected static $_AUTHORITY_FRONTEND_ACTIONS = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Models
        $this->load->model(
            array(
                'menu_model',
                'page_model',
                'article_model',
//                'structure_model', // Not using by controller
                'extend_field_model',
                'system_check_model',
                'url_model',
                'type_model',
                'resource_model',
                'rule_model'
            ), '', TRUE);

		// Libraries
		$this->load->library('structure');

		$this->load->helper('string_helper');
		$this->load->helper('text_helper');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Create one page
	 *
	 * @param	int		Menu ID
	 * @param	int		Parent page ID
	 *
	 */
	public function create($id_menu, $id_parent=NULL)
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
			$datas = $this->_get_views_dropdown_data($datas, 'Page');
			$datas = array('0' => lang('ionize_select_default_view')) + $datas; 
			$this->template['views'] = $this->template['single_views'] = form_dropdown('view', $datas, FALSE, 'class="select w160"');
		}

		// Dropdown Article views
		$datas = isset($views['article']) ? $views['article'] : array() ;
		if(count($datas) > 0)
		{
			$datas = $this->_get_views_dropdown_data($datas, 'Article');
			$datas = array('0' => lang('ionize_select_default_view')) + $datas;
			$this->template['article_views'] = form_dropdown('article_view', $datas, FALSE, 'class="select w160"');
			$this->template['article_list_views'] = form_dropdown('article_list_view', $datas, FALSE, 'class="select w160"');
		}

		$this->template['priority'] = '5';
		$this->template['has_url'] = '1';
		$this->template['id_parent'] = $id_parent;

		//  Extend fields
		$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('page');

		$this->output('page/page');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Edit one page
	 *
	 * @param	string	Page ID
	 *
	 */
	public function edit($id)
	{
		$resource = $this->_get_resource_name('backend', 'page', $id);

		if (Authority::can('edit', 'admin/page') && Authority::can('edit', $resource, null, true))
		{
			// Datas
			$page = $this->page_model->get_by_id($id);

			if( ! empty($page) )
			{
				$this->load_modules_addons($page);

				// Correct the menu ID (for phantom pages)
				if ($page['id_menu'] == '0') $page['id_menu'] = '1';

				// Data & Lang Data
				$this->template = array_merge($this->template, $page);
				$this->page_model->feed_lang_template($id, $this->template);

				// Array of path to the element. Gives the complete URL to the element.
				$this->template['parent_array'] = $this->page_model->get_parent_array($id);

				// Breadcrumbs
				$pages = $this->page_model->get_parent_array($id, array(), Settings::get_lang('default'));

				$breadcrump = array();
				foreach($pages as $page)
				{
					$breadcrump[] = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
				}
				$this->template['breadcrump'] = implode(' > ', $breadcrump);

				// Extend fields
				$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('page', $id);

				// URLs
				$this->template['urls'] = $this->url_model->get_entity_urls('page', $id);

				// Output
				$this->output('page/page');
			}
			else
			{
				$this->error(lang('ionize_message_page_not_exist'));
			}
		}
		else
		{
			$this->output(self::$_DENY_MAIN_VIEW);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays the options side panel
	 *
	 * @param   int     Page ID
	 *
	 */
	public function get_options($id)
	{
		$resource = $this->_get_resource_name('backend', 'page', $id);

		if (Authority::can('edit', $resource, null, true))
		{
			// Datas
			$page = $this->page_model->get_by_id($id);

			if( ! empty($page) )
			{
				// Correct the menu ID (for phantom pages)
				if ($page['id_menu'] == '0') $page['id_menu'] = '1';

				// Page data
				$this->template = array_merge($this->template, $page);
				$this->page_model->feed_lang_template($id, $this->template);

				// Load the module's addons
				$this->load_modules_addons($page);

				// Dropdown menus
				$datas = $this->menu_model->get_select();
				$this->template['menus'] = form_dropdown('id_menu', $datas, $this->template['id_menu'], 'id="id_menu" class="select"');

				// Subnav menu
				$subnav_page = $this->page_model->get_by_id($page['id_subnav']);
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
					$datas = $this->_get_views_dropdown_data($datas, 'Page');
					$datas = array('' => lang('ionize_select_default_view')) + $datas;
					$this->template['views'] = form_dropdown('view', $datas, $this->template['view'], 'class="select"');
					$this->template['single_views'] = form_dropdown('view_single', $datas, $this->template['view_single'], 'class="select"');
				}

				// Dropdown article list views (templates)
				$datas = isset($views['article']) ? $views['article'] : array() ;
				if(count($datas) > 0)
				{
					$datas = $this->_get_views_dropdown_data($datas, 'Article');
					$datas = array('' => lang('ionize_select_default_view')) + $datas;
					$this->template['article_list_views'] = form_dropdown('article_list_view', $datas, $this->template['article_list_view'], 'class="select"');
					$this->template['article_views'] = form_dropdown('article_view', $datas, $this->template['article_view'], 'class="select"');
				}

				// Roles & Rules
				$frontend_roles_resources = $this->resource_model->get_element_roles_resources(
					'page',
					$id,
					self::$_AUTHORITY_FRONTEND_ACTIONS,
					'frontend'
				);
				$this->template['frontend_roles_resources'] = $frontend_roles_resources;

				$backend_roles_resources = $this->resource_model->get_element_roles_resources(
					'page',
					$id,
					self::$_AUTHORITY_BACKEND_ACTIONS,
					'backend'
				);
				$this->template['backend_roles_resources'] = $backend_roles_resources;

				// Roles which have permission set for this page
				$this->template['frontend_role_ids'] = $this->rule_model->get_element_role_ids('page', $id);
				$this->template['backend_role_ids'] = $this->rule_model->get_element_role_ids('page', $id, 'backend');

				// Default Deny Action
				if (empty($page['deny_code']))
					$this->template['deny_code'] = '404';

				// Types
				$types = $this->type_model->get_select('page', lang('ionize_select_no_type'));
				if (count($types) > 1)
				{
					$this->template['types'] = form_dropdown(
						'id_type',
						$types,
						$this->template['id_type'],
						'class="select"'
					);
				}

				// Output
				$this->output('page/options');
			}
			else
			{
				$this->error(lang('ionize_message_page_not_exist'));
			}
		}
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Saves a page
	 *
	 */
	public function save()
	{
		/* Check if the default lang URL or the default lang title are set
		 * One of these need to be set to save the page
		 *
		 */
		if ($this->_check_before_save() == TRUE)
		{
			$id = $this->input->post('id_page');
			
			// Clear the cache
			Cache()->clear_cache();
			
			// Prepare data before save
			$this->_prepare_data();

			// Event : On before save
			$event_data = array(
				'base' => $this->data,
				'lang' => $this->lang_data,
                'post' => $this->input->post()
			);
			$event_received = Event::fire('Page.save.before', $event_data);
			$event_received = array_pop($event_received);
			if ( ! empty($event_received['base']) && !empty($event_received['lang']))
			{
				$this->data = $event_received['base'];
				$this->lang_data = $event_received['lang'];
			}

			// Save Page
			$saved_id = $this->page_model->save($this->data, $this->lang_data);

			// Correct Pages levels
			// TODO : Move this into the model.
			if ( ! empty($id) )
			{
				// Correct pages levels regarding parents.
				$this->system_check_model->check_page_level(TRUE);
			}

			// Save extends fields data
			$this->extend_field_model->save_data('page', $saved_id, $_POST);
					
			// Save linked access groups authorizations
			// $this->base_model->join_items_keys_to('user_groups', $this->input->post('groups'), 'page', $this->id);

			// Save the Sitemap
			$this->structure->build_sitemap();

			// Prepare the Json answer
			$page = array_merge($this->lang_data[Settings::get_lang('default')], $this->page_model->get_by_id($saved_id));
			$page['menu'] = $this->menu_model->get(array('id_menu' => $page['id_menu']));

			// Remove HTML tags from returned array
			strip_html($page);
			$this->callback = array();

            // Event : On after save
			$event_data = array(
				'base' => $this->data,
				'lang' => $this->lang_data,
                'post' => $this->input->post()
			);
			Event::fire('Page.save.success', $event_data);

			// New page : Simply reloads the panel
			if ( empty($id))
			{
				// Save the Urls
				$this->page_model->save_urls($saved_id);

				// Used by JS Tree to detect if page in inserted in tree or not
				$page['inserted'] = TRUE;
				
				$this->callback[] = array(
					'fn' => $page['menu']['name'].'Tree.insertElement',
					'args' => array($page, 'page')
				);

				// Reload the panel
				$this->_reload_panel($saved_id);

				$this->success(lang('ionize_message_page_saved'));
			}
			// Existing page : Saves options
			else
			{
				// Save options : as callback
				$this->callback[] = array(
					'fn' => 'ION.sendForm',
					'args' => array(
						'page/save_options',
						'pageOptionsForm'
					)
				);
				$this->response();
			}
		}
		else
		{
			Event::fire('Page.save.error');

			$this->error(lang('ionize_message_page_needs_url_or_title'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves the page's options.
	 * If no page ID is given by $_POST
	 * @param int	Page ID
	 *
	 *
	 */
	public function save_options()
	{
		$id = $this->input->post('id_page');

		// Do stuff
		if ($id)
		{
			// Prepare data before save
			$this->_prepare_options_data();

            // Event Data
            $event_data = array(
                'base' => $this->data,
                'lang' => $this->lang_data,
                'post' => $this->input->post()
            );

            Event::fire('Page.options.save.before', $event_data);

			// Save Page
			$this->page_model->save($this->data, $this->lang_data);

			// Save the Urls
			$this->page_model->save_urls($id);

			// Save Home page
			if ($this->data['home'] == '1')
				$this->page_model->update_home_page($id);

			$page = array_merge($this->lang_data[Settings::get_lang('default')], $this->page_model->get_by_id($id));
			$page['menu'] = $this->menu_model->get($page['id_menu']);

			// Rules
			if (Authority::can('access', 'admin/page/permissions/backend'))
			{
				$resource = $this->_get_resource_name('backend', 'page', $id);
				$this->rule_model->save_element_roles_rules($resource, $this->input->post('backend_rule'));
			}

			if (Authority::can('access', 'admin/page/permissions/frontend'))
			{
				$resource = $this->_get_resource_name('frontend', 'page', $id);
				$this->rule_model->save_element_roles_rules($resource, $this->input->post('frontend_rule'));
			}

            Event::fire('Page.options.save.success', $event_data);

			// Remove HTML tags from returned array
			strip_html($page);
		}

		// Reloads the page edition panel
		$this->_reload_panel($id);

		// Answer
		$this->success(lang('ionize_message_page_saved'));
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Gets the parent list list for the parent select dropdown
	 *
	 * Receives by $_POST :
	 * - id_menu : Menu ID
	 * - id_current : Current page ID
	 * - id_parent : Parent page ID
	 *
	 * @returns	string	HTML string of options items
	 *
	 */
	public function get_parents_select()
	{
		$id_menu = $this->input->post('id_menu');
		$id_current = $this->input->post('id_current');
		$id_parent = $this->input->post('id_parent');
		$element_id = $this->input->post('element_id');
		$check_add_page = $this->input->post('check_add_page');

		$data = $this->page_model->get_lang_list(array('id_menu' => $id_menu), Settings::get_lang('default'));

		$parents = array('0' => '/');
		($parents_array = $this->structure->get_parent_select($data, $id_current) ) ? $parents += $parents_array : '';

		if ($check_add_page)
		{
			foreach($parents as $id_page => $str)
			{
				if (Authority::cannot('add_page', 'backend/page/' . $id_page, NULL, TRUE))
					unset($parents[$id_page]);
			}
		}

		$this->template['pages'] = $parents;
		$this->template['id_selected'] = $id_parent;
		$this->template['element_id'] = $element_id;

		$this->output('page/parent_select');
	}



	// ------------------------------------------------------------------------


	/**
	 * Set an item online / offline depending on its current status
	 *
	 * @param	int		item ID
	 *
	 */
	public function switch_online($id)
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
	public function save_ordering()
	{
        if (!Authority::can('edit', 'admin/page')) {
            $this->error(lang('permission_denied'));
        }
	  
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


	public function reorder_articles()
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


	// ------------------------------------------------------------------------

	/**
	 * Updates the page name
	 *
	 */
	public function update_name()
	{
		$id_page = $this->input->post('id');
		$value = $this->input->post('value');

		if ($id_page && !empty($value))
		{
			$value = $this->page_model->get_unique_name($value, $id_page);

			$result = $this->page_model->update(array('id_page' => $id_page), array('name' => $value));

			if ($result)
			{
				$this->success(lang('ionize_message_operation_ok'));
			}
		}
		$this->response();
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates one page's field
	 * Called through XHR
	 *
	 * Data are get by $_POST :
	 * - field : DB field name
	 * - id_page : Page ID`
	 * - value : Value to set
	 *
	 */
	public function update_field()
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
				$page = $this->page_model->get_by_id($id_page, Settings::get_lang('default'));
				$menu = $this->menu_model->get($page['id_menu']);
				
				$page['title'] = htmlspecialchars_decode($page['title'], ENT_QUOTES);
				$page['element'] = 'page';
				$page['menu'] = $menu;

				$this->callback[] = array(
					'fn' => 'ION.notification',
					'args' => array('success', lang('ionize_message_page_saved'))
				);

				$this->callback[] = array(
					'fn' => $page['menu']['name'].'Tree.updateElement',
					'args' => array($page, 'page')
				);

				$this->response();
			}
		}
	}


	// ------------------------------------------------------------------------


	public function get_link()
	{
		// Get the link
		$id_page = $this->input->post('id_page');
		
		$page = $this->page_model->get_by_id($id_page);
		$link_type = $page['link_type'];

		$title = NULL;
		$breadcrumb = '';

		if (in_array($link_type, array('page', 'article')))
		{
			if ($link_type == 'article')
			{
				$link_rel = explode('.', $page['link_id']);
				$link_id = isset($link_rel[1]) ? $link_rel[1] : NULL;
				$breadcrumb = $this->page_model->get_breadcrumb_string($link_rel[0]);
			}
			else
			{
				$link_id = $page['link_id'];
				$breadcrumb = $this->page_model->get_breadcrumb_string($page['link_id']);
			}

			$link = $this->{$link_type.'_model'}->get_by_id($link_id, Settings::get_lang('default'));

			if ( ! empty($link))
			{
				$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];

				if ($link_type == 'article')
					$breadcrumb .= ' > ' . $title;
			}
			// The destination doesn't exists anymore : remove the link
			else
			{
				$this->_remove_link($id_page);
			}
		}
		// External link
		else
		{
			$title = $page['link'];
		}

		$this->template = array('parent' => 'page');

		if ( ! is_null($title))
		{
			$this->template = array(
				'parent' => 'page',
				'rel' => $id_page,
				'link_id' => $page['link_id'],
				'link_type' => $page['link_type'],
				'link' => $title,
				'breadcrumb' => $breadcrumb
			);
		}

		$this->output('shared/link');
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
	public function add_link()
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
				$link = $this->page_model->get_by_id($link_id, Settings::get_lang('default'));
				$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
				break;
			
			case 'article' :
				$link_rel = explode('.', $link_id);
				$link = $this->article_model->get_by_id($link_rel[1], Settings::get_lang('default'));
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


	public function remove_link()
	{
		$id_page = $this->input->post('rel');
		
		if ($id_page)
		{
			// Clear the cache
			Cache()->clear_cache();

			$this->_remove_link($id_page);

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
	public function delete($id)
	{
		$affected_rows = $this->page_model->delete($id);
		
		// Delete was successfull
		if ($affected_rows > 0)
		{
			// Clean URL table
			$this->url_model->clean_table();

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


	protected function _get_resource_name($type, $element, $id)
	{
		return $type . '/' . $element . '/' . $id;
	}


	// ------------------------------------------------------------------------


	protected function _get_views_dropdown_data($data, $nogroup_title='Page')
	{
		$optgroup = array();
		$nogroup = array();

		foreach($data as $file => $label)
		{
			$arr = explode('/', $file);
			if (isset($arr[1]))
			{
				$group_name = ucwords(str_replace('_', ' ', $arr[0]));
				if ( ! isset($optgroup[$group_name])) $optgroup[$group_name] = array();

				$optgroup[$group_name][$file] = $label;
			}
			else
			{
				$nogroup[$file] = $label;
			}
		}
		if ( ! empty($optgroup))
		{
			if (empty($nogroup) && count(array_keys($optgroup)) == 1)
			{
				$result = array_pop($optgroup);
			}
			else
			{
				$result = array($nogroup_title => $nogroup);
				$result = array_merge($result, $optgroup);
			}

			return $result;
		}
		else
			return $nogroup;
	}


	// ------------------------------------------------------------------------


	protected function _remove_link($id_page)
	{
		$context = array(
			'link_type' => '',
			'link_id' => '',
			'link' => ''
		);

		// Save the context
		$this->page_model->update(array('id_page' => $id_page), $context);
	}


	// ------------------------------------------------------------------------


	protected function _prepare_options_data()
	{
		// Standard fields
		$fields = $this->db->list_fields('page');

		// Set the data to the posted value.
		foreach ($fields as $field)
		{
			if ($this->input->post($field) !== FALSE OR in_array($field, $this->boolean_options))
				$this->data[$field] = $this->input->post($field);
		}

		// Lang data
		$fields = $this->db->list_fields('page_lang');

		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				if ($this->input->post($field.'_'.$language['lang']) !== FALSE)
				{
					$content = $this->input->post($field.'_'.$language['lang']);
					$this->lang_data[$language['lang']][$field] = $content;
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Prepare page data before saving
	 *
	 * 
	 */
	protected function _prepare_data()
	{
		// Standard fields
		$fields = $this->db->list_fields('page');
		
		// Set the data to the posted value.
		foreach ($fields as $field)
		{
			if ($this->input->post($field) !== FALSE)
			{
//			if ( ! in_array($field, $this->no_htmlspecialchars))
//				$this->data[$field] = htmlspecialchars($this->input->post($field), ENT_QUOTES, 'utf-8');
//			else
				$this->data[$field] = $this->input->post($field);
			}
		}

		// level ?
		$parent = $this->page_model->get_by_id($this->input->post('id_parent'));

		if ( ! empty($parent) )
			$this->data['level'] = $parent['level'] + 1;	
		else 
			$this->data['level'] = 0;

		// Author & updater
		if ($this->input->post('id_page'))
			$this->data['updater'] = User()->get('username');
		else
		{
			$this->data['author'] =  User()->get('username');
			$this->data['appears'] =  $this->input->post('appears');
		}

		// URLs : Feed the other languages URL with the default one if the URL is missing
		$urls = $this->_get_urls(TRUE);

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

			// Create the page name : Only done for new page
			$this->data['name'] = $urls[Settings::get_lang('default')];
			$this->data['name'] = $this->page_model->get_unique_name($this->data['name'], $this->input->post('id_page'));
		}
		else
		{
			unset($this->data['ordering']);
		}


		// Lang data
		$fields = $this->db->list_fields('page_lang');

		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				if ( $field != 'url' && $this->input->post($field.'_'.$language['lang']) !== FALSE)
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
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Checks if the element save process can be done.
	 *
	 * @returns		Boolean		True if the save can be done, false if not
	 *
	 */
	protected function _check_before_save()
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
	 * @return		Array		Multidimensional array of URLs
	 *							ex : $url['en'] = 'my-element-url'
	 *
	 */
	protected function _get_urls($fill_empty_lang = FALSE)
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
			if ($url == '')	$urls[$lang] = $default_lang_url;
		}

		return $urls;
	}


	// ------------------------------------------------------------------------


	/**
	 * When called, relaods the Page Edition panel
	 *
	 * @param	Page ID
	 *
	 */
	protected function _reload_panel($id_page)
	{
		$page = $this->page_model->get_by_id($id_page, Settings::get_lang('default'));
		$page['menu'] = $this->menu_model->get($page['id_menu']);

		$title = empty($page['title']) ? $page['name'] : $page['title'];

		$this->callback[] =	array(
			'fn' => 'ION.splitPanel',
			'args' => array(
				'urlMain'=> admin_url(TRUE) . 'page/edit/'.$id_page,
				'urlOptions'=> admin_url(TRUE) . 'page/get_options/'.$id_page,
				'title'=> lang('ionize_title_edit_page') . ' : ' . $title
			)
		);

		$this->callback[] = array(
			'fn' => $page['menu']['name'].'Tree.updateElement',
			'args' => array($page, 'page')
		);
	}
}
