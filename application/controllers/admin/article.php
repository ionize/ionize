<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Article Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Article extends MY_admin 
{

	/**
	 * Fields on wich the htmlspecialchars function will not be used before saving
	 * 
	 * @var array
	 */
	protected $no_htmlspecialchars = array('content', 'title', 'subtitle');

	protected $htmlspecialchars = array('meta_title');

	/**
	 * Fields on wich no XSS filtering is done
	 * 
	 * @var array
	 */
	protected $no_xss_filter = array('content');

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
	 * Boolean data (checkboxes)
	 *
	 * @var array
	 */
	protected $boolean_data = array();

	/**
	 * Boolean options (checkboxes)
	 *
	 * @var array
	 */
	protected $boolean_options = array();


	/**
	 * Frontend / Backend Authority actions
	 * @var array
	 */
	protected static $_AUTHORITY_BACKEND_ACTIONS = array('edit','delete','status','unlink');
	protected static $_AUTHORITY_FRONTEND_ACTIONS = NULL;


	/**
	 * Nb of articles by pagination page
	 * @var int
	 */
	protected static $_NB_ARTICLES_PAGINATION = 20;


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
                'category_model',
                'article_type_model',
                'tag_model',
                'extend_field_model',
                'url_model',
                'resource_model',
                'rule_model'
            ), '', TRUE);

		$this->load->library('structure');
		
		$this->load->helper('string_helper');
		$this->load->helper('text_helper');
	}


	// ------------------------------------------------------------------------


	/**
	 * Articles List
	 * index
	 *
	 */
	public function articles()
	{
		// Nb articles by pagination page
		$this->template['nb'] = self::$_NB_ARTICLES_PAGINATION;

		// Dropdown menus
		$data = $this->menu_model->get_select();
		$data = array('0' => lang('ionize_select_no_one')) + $data;

		$this->template['menus'] =	form_dropdown('id_menu', $data, 1, 'id="id_menu" class="select"');


		$this->output('articles/index');
	}


	// ------------------------------------------------------------------------


	/**
	* Articles List
	* Called through XHR from articles panel
	* 
	*/
	public function get_articles_list($page=1)
	{
		// Nb and Minimum
		$nb = $this->input->post('nb') ? $this->input->post('nb') : self::$_NB_ARTICLES_PAGINATION;

		// Pagination
		$nb_lang = count(Settings::get_languages());
		//	if ($nb < self::$_NB_ARTICLES_PAGINATION) $nb = self::$_NB_ARTICLES_PAGINATION;
		$page = $page - 1;
		$offset = $page * $nb;

		$where = array(
			'limit' => $nb * $nb_lang,
			'offset' => $offset * $nb_lang
		);

		// Filter
		$filter_title = $this->input->post('title');
		if ($filter_title)
			$where['like'] = array('article_lang.title' => $filter_title);

		$filter_content = $this->input->post('content');
		if ($filter_content)
			$where['like'] = array('article_lang.content' => $filter_content);

		// ID Page
		$filter_id_page = $this->input->post('id_parent');
		if ($filter_id_page)
		{
			$children_ids = $this->page_model->get_children_ids($filter_id_page, TRUE);
			$where['where_in'] = array('page_article.id_page' => $children_ids);
		}

		// Menu
		$filter_id_menu = $this->input->post('id_menu');
		if ($filter_id_menu)
			$where['page.id_menu'] = $filter_id_menu;

		$articles = $this->article_model->get_all_lang_list($where);

		// Get Pages and Breadcrumb to pages for each linked page
		$_pages_breadcrumbs = array();

		foreach($articles as &$article)
		{
			$article['data']['pages'] = array();

			// Main Parent page ID
			if ( empty($article['data']['page_ids']))
			{
				$article['data']['page_ids'] = '0';
			}
			else
			{
				$page_ids = explode('/', $article['data']['path_ids']);
				if (isset($page_ids[count($page_ids)-2]))
					$article['data']['id_page'] = $page_ids[count($page_ids)-2];
				else
					$article['data']['id_page'] = '0';
			}

			// Pages Breadcrumbs
			$page_ids = explode(';', $article['data']['page_ids']);

			if ( ! empty($page_ids))
			{
				foreach($page_ids as $id_page)
				{
					$breadcrumb = in_array($id_page, array_keys($_pages_breadcrumbs)) ? $_pages_breadcrumbs[$id_page] : $this->page_model->get_breadcrumb_string($id_page);
					$_pages_breadcrumbs[$id_page] = $breadcrumb;

					//$breadcrumb = '';
					if ( ! empty($id_page))
					{
						$article['data']['pages'][] = array(
							'id_page' => $id_page,
							'breadcrumb' => $breadcrumb
						);
					}
				}
			}
		}

		// Pagination
		$this->template['current_page'] = $page + 1;
		$this->template['nb'] = $nb;

		unset($where['limit']);
		unset($where['offset']);
		$this->template['articles_count'] = $this->article_model->count_all_lang_list($where) / $nb_lang;
		$this->template['articles_pages'] = ceil($this->template['articles_count'] / $nb);

		$this->template['articles'] = $articles;

		$this->output('articles/list');
	}


	// ------------------------------------------------------------------------


	public function get_list()
	{
		$id_page = $this->input->post('id_page');
	
		if ($id_page)
		{
			// Get articles
			$articles = $this->article_model->get_lang_list(array('id_page'=>$id_page), Settings::get_lang('default'));
			$this->article_model->add_lang_data($articles);

			// Dropdowns Views
			$views = array();
			if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
				require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');

			$data = isset($views['article']) ? $views['article'] : NULL ;
			if ( ! is_null($data))
			{
				$data = array('' => lang('ionize_select_default_view')) + $data;
				$this->template['all_article_views'] = $data;
			}
			else
			{
				$this->template['all_article_views'] = NULL;
			}

			// All articles type to template
			$data = $this->article_type_model->get_types_select();
			if ( ! empty($data))
			{
				$data = array('' => lang('ionize_select_no_type')) + $data;
				$this->template['all_article_types'] = $data;
			}
			else
			{
				$this->template['all_article_types'] = NULL;
			}
			
			// Article's all pages contexts
			$articles_id = array();
			foreach($articles as $article)
			{
				$articles_id[] = $article['id_article'];
			}
			$pages_context = $this->page_model->get_lang_contexts($articles_id, Settings::get_lang('default'));
	
			// Add pages contexts data to articles
			foreach($articles as &$article)
			{
				$article['pages'] = array_values(array_filter($pages_context, create_function('$row', 'return $row["id_article"] == '. $article['id_article'] .';')));
			}
	
			$this->template['articles'] = $articles;
			$this->template['id_page'] = $id_page;
			$this->output('article/list');
		}
	}	


	// ------------------------------------------------------------------------


	/** 
	 * Create one article
	 * @TODO	Developp the "existing tags" functionality
	 *
	 * @param	string 	page ID. Article parent.
	 *
	 */
	public function create($id_page = NULL)
	{
		// Page
		if ( ! is_null($id_page))
		{
			$page = $this->page_model->get_by_id($id_page);
		}
		else
		{
			$id_page = '0';
			$page = array(
				'id_menu' => '1'
			);
		}

		// Create blank data for this article
		$this->article_model->feed_blank_template($this->template);
		$this->article_model->feed_blank_lang_template($this->template);

		// Put the page ID to the template
		$this->template['id_page'] = $id_page;

		// Tags : Default no one
		$this->template['tags'] = '';
		
		// All other pages articles
		$this->template['articles'] = $this->article_model->get_lang_list(array('id_page'=>$id_page), Settings::get_lang('default'));
		
		// Dropdown menus
		$datas = $this->menu_model->get_select();
		$this->template['menus'] =	form_dropdown('id_menu', $datas, $page['id_menu'], 'id="id_menu" class="select"');
		
		// Menu Info
		$menu = '';
		foreach($datas as $id=>$value)
		{
			if ($page['id_menu'] == $id) $menu = $value;
		}
		$this->template['menu'] = $menu;

		// Dropdown parents
		$datas = $this->page_model->get_lang_list(array('id_menu' => $page['id_menu']), Settings::get_lang('default'));
		
		$parents = array(
			'0' => lang('ionize_select_no_parent')
		);
		($parents_array = $this->structure->get_parent_select($datas) ) ? $parents += $parents_array : '';
		$this->template['parent_select'] = form_dropdown('id_page', $parents, $id_page, 'id="id_page" class="select"');
	
		// Parent info
		$breadcrumbs = array();
		$b_id_page = $id_page;
		$level = 1;

		while ($level > -1)
		{
			foreach($datas as $page)
			{
				if ($b_id_page == $page['id_page'])
				{
					$level = $page['level'];
					$breadcrumbs[] = $page;
					$b_id_page = $page['id_parent'];
					break;
				}
			}
			$level--;
		}
		$breadcrumbs = array_reverse($breadcrumbs);
		$this->template['breadcrumbs'] = $breadcrumbs;
	
		// Dropdown articles views
		$views = array();
		if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
			require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');

		$datas = isset($views['article']) ? $views['article'] : array() ;

		if(count($datas) > 0)
		{
			$datas = array('0' => lang('ionize_select_default_view')) + $datas; 
			$this->template['article_views'] = form_dropdown('view', $datas, FALSE, 'class="select w160"');
		}

		// Categories
		$categories = $this->category_model->get_categories_select();
		$this->template['categories'] =	form_dropdown('categories[]', $categories, FALSE, 'class="select" multiple="multiple"');

		// Article types
		$types = $this->article_type_model->get_types_select();
		$this->template['article_types'] =	form_dropdown('id_type', $types, FALSE, 'class="select"');
		
		// Extends fields
		$extend_fields = $this->extend_field_model->get_element_extend_fields('article');
		$this->template['has_translated_extend_fields'] = $this->_has_translated_extend_fields($extend_fields);
		$this->template['extend_fields'] = $extend_fields;

		// Context data initialized when article creation
		$this->template['online'] = '0';
		$this->template['main_parent'] = '1';
		$this->template['has_url'] = '1';

		$this->output('article/article');
	}	


	// ------------------------------------------------------------------------


	/**
	 * Saves one article
	 *
	 * @param	boolean		if true, the transport is through XHR
	 */
	public function save()
	{
		if ( ! Authority::can('edit', 'admin/article')) {
			$this->error(lang('permission_denied'));
		}

		/* Check if the default lang URL or the default lang title are set
		 * One of these need to be set to save the article
		 *
		 */
		if ($this->_check_before_save() == TRUE)
		{
			// Clear the cache
			Cache()->clear_cache();

			$rel = $this->input->post('rel');
			
			// IDs
			$rel = explode(".", $rel);
			$this->data['id_page'] = ( !empty($rel[1] )) ? $rel[0] : '0';

			$post_id_article = $this->input->post('id_article');
			
			// Prepare data before saving
			$this->_prepare_data();

			// Event : On before save
			$event_data = array(
				'base' => $this->data,
                'lang' => $this->lang_data,
                'post' => $this->input->post()
			);
			$event_received = Event::fire('Article.save.before', $event_data);
			$event_received = array_pop($event_received);
			if ( ! empty($event_received['base']))
				$this->data = $event_received['base'];
			if ( !empty($event_received['lang']))
				$this->lang_data = $event_received['lang'];

			// Saves article to DB and get the saved ID
			$id_article = $this->article_model->save($this->data, $this->lang_data);

			// Link to page
			if ( ! empty($this->data['id_page']))
			{
				$this->data['online'] = $this->input->post('online');
				$this->data['main_parent'] = $this->input->post('main_parent');
				$this->article_model->link_to_page($this->data['id_page'], $id_article, $this->data);
			}
			else
				$this->data['id_page'] = '0';				

			// Correct DB integrity : Links IDs
			if ( ! empty($post_id_article) )
				$this->article_model->correct_integrity($this->data, $this->lang_data);
				
			// Saves linked categories
			$this->base_model->join_items_keys_to('category', $this->input->post('categories'), 'article', $id_article);

			// Save extend fields data
			$this->extend_field_model->save_data('article', $id_article, $_POST);

			// Save URLs
			$this->article_model->save_urls($id_article);

			// Save the Sitemap
			$this->structure->build_sitemap();

			// Event : On after save
			$event_data = array(
				'base' => $this->data,
				'lang' => $this->lang_data,
                'post' => $this->input->post()
			);

			Event::fire('Article.save.success', $event_data);

			/*
			 * JSON Answer
			 *
			 * Updates the structure tree
			 * The data var is merged to the default lang data_lang var,
			 * in order to send the lang values to the browser without making another SQL request
			 */
			// Get the context info
			$context = $this->article_model->get_context($id_article, $this->data['id_page'], Settings::get_lang('default'));
			$this->data = array_merge($this->data, $context);
			
			// Remove HTML tags from returned array
			strip_html($this->data);

			// Insert Case
			if ( empty($post_id_article) )
			{
				$menu = $this->menu_model->get_from_page($this->data['id_page']);
				$this->data['menu'] = $menu;
				
				// Used by JS Tree to detect if article in inserted in tree or not
				$this->data['inserted'] = TRUE;

				// Context update
				$this->update_contexts($this->data['id_article']);

				// Insert article to tree if menu is found (for id_page = 0, no one is found)
				if (!empty($menu))
				{
					$this->callback[] = array(
						'fn' => $menu['name'].'Tree.insertElement',
						'args' => array($this->data, 'article')
					);
				}

				// Reloads the edition panel
				$this->_reload_panel($this->data['id_page'], $id_article);

				// Answer
				$this->success(lang('ionize_message_article_saved'));
			}
			else
			{
				// Save options : as callback
				$this->callback[] = array(
					'fn' => 'ION.sendForm',
					'args' => array(
						'article/save_options',
						'articleOptionsForm'
					)
				);
				$this->response();
			}
		}
		else
		{
			Event::fire('Article.save.error');

			$this->error(lang('ionize_message_article_needs_url_or_title'));
		}

	}


	// ------------------------------------------------------------------------


	public function save_options()
	{
		$rel = $this->input->post('rel');

		// IDs
		$rel = explode(".", $rel);
		$id_page = $this->data['id_page'] = ( !empty($rel[1] )) ? $rel[0] : '0';

		// $id_article = $this->input->post('id_article');

		$this->_prepare_options_data();

        // Event : On before save
        $event_data = array(
            'base' => $this->data,
            'lang' => $this->lang_data,
            'post' => $this->input->post()
        );

        Event::fire('Article.options.save.before', $event_data);

		// Saves article to DB and get the saved ID
		$id_article = $this->article_model->save($this->data, $this->lang_data);

		// Saves linked categories
		$this->base_model->join_items_keys_to('category', $this->input->post('categories'), 'article', $id_article);

		// Saves Tags
		$this->tag_model->save_element_tags($this->input->post('tags'), 'article', $id_article);

		// Rules
		if (Authority::can('access', 'admin/article/permissions/backend'))
		{
			$resource = $this->_get_resource_name('backend', 'article', $id_article);
			$this->rule_model->save_element_roles_rules($resource, $this->input->post('backend_rule'));
		}

		if (Authority::can('access', 'admin/article/permissions/frontend'))
		{
			$resource = $this->_get_resource_name('frontend', 'article', $id_article);
			$this->rule_model->save_element_roles_rules($resource, $this->input->post('frontend_rule'));
		}

        // Event : On after save
        Event::fire('Article.options.save.success', $event_data);

		// Context update
		$this->update_contexts($id_article);

		// Reloads the edition panel
		$this->_reload_panel($id_page, $id_article);

		// Answer
		$this->success(lang('ionize_message_article_saved'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Edit one article
	 *
	 * @param	string		article REL. Composed by the page ID and the article ID
	 *						Example : 1.23
	 *						1 : id_page
	 *						23 : id_article
	 */
	public function edit($rel)
	{
		// IDs
		$rel = explode(".", $rel);
		$id_page = ( !empty($rel[1] )) ? $rel[0] : '0';
		$id_article = ( !empty($rel[1] )) ? $rel[1] : NULL;

		$resource = $this->_get_resource_name('backend', 'article', $id_article);
		$page_resource = $this->_get_resource_name('backend', 'page', $id_page);

		if (
			Authority::can('edit', 'admin/article') && Authority::can('edit', $resource, NULL, TRUE)
			&& Authority::can('edit', $page_resource, NULL, TRUE)
		)
		{
			// Edit article if ID exists
			if ( ! is_null($id_article) )
			{
				$article = $this->article_model->get_by_id($id_article);

				if( ! empty($article) )
				{
					// Loads the modules addons
					$this->load_modules_addons($article);

					// Page context of the current edited article
					$article['id_page'] = $id_page;

					// Data & Lang Data
					$this->template = array_merge($this->template, $article);
					$this->article_model->feed_lang_template($id_article, $this->template);

					// Extends fields
					$extend_fields = $this->extend_field_model->get_element_extend_fields('article', $id_article);
					$this->template['has_translated_extend_fields'] = $this->_has_translated_extend_fields($extend_fields);
					$this->template['extend_fields'] = $extend_fields;

					// Link : Depending on the context
					$context = $this->article_model->get_context($id_article, $id_page);

					if ( ! empty($context))
					{
						$this->template['main_parent'] = $context['main_parent'];

						$pages = $this->page_model->get_parent_array($id_page, array(), Settings::get_lang('default'));

						// Breadcrump
						$breadcrump = array();
						foreach($pages as $page)
							$breadcrump[] = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
						$this->template['breadcrump'] = implode(' > ', $breadcrump);
					}
					else
					{
						$this->template['main_parent'] = '0';
					}

					Event::fire('Article.edit', $this->template);

					$this->output('article/article');
				}
			}
		}
		else
		{
			$this->output(self::$_DENY_MAIN_VIEW);
		}
	}


	// ------------------------------------------------------------------------


	public function get_options($rel)
	{
		// IDs
		$rel = explode(".", $rel);
		$id_page = ( !empty($rel[1] )) ? $rel[0] : '0';
		$id_article = ( !empty($rel[1] )) ? $rel[1] : NULL;

		$resource = $this->_get_resource_name('backend', 'article', $id_article);

		if (Authority::can('edit', $resource, NULL, TRUE))
		{
			// Edit article if ID exists
			if ( ! is_null($id_article) )
			{
				$article = $this->article_model->get_by_id($id_article);

				if( ! empty($article) )
				{
					$this->load_modules_addons($article);

					// Page context of the current edited article
					$article['id_page'] = $id_page;

					// Merge article's data with template
					$this->template = array_merge($this->template, $article);
					$this->article_model->feed_lang_template($id_article, $this->template);

					// Linked pages list
					$this->template['pages_list'] = $this->article_model->get_pages_list($id_article);

					// Categories
					$categories = $this->category_model->get_categories_select();
					$current_categories = $this->category_model->get_current_categories('article', $id_article);
					$this->template['categories'] =	form_dropdown('categories[]', $categories, $current_categories, 'class="select w100p" multiple="multiple"');

					// Permissions
					$frontend_roles_resources = $this->resource_model->get_element_roles_resources(
						'article',
						$id_article,
						self::$_AUTHORITY_FRONTEND_ACTIONS,
						'frontend'
					);
					$this->template['frontend_roles_resources'] = $frontend_roles_resources;

					$backend_roles_resources = $this->resource_model->get_element_roles_resources(
						'article',
						$id_article,
						self::$_AUTHORITY_BACKEND_ACTIONS,
						'backend'
					);
					$this->template['backend_roles_resources'] = $backend_roles_resources;

					// Default Deny Action
					if (empty($article['deny_code']))
						$this->template['deny_code'] = '404';

					// Roles which have permission set for this page
					$this->template['frontend_role_ids'] = $this->rule_model->get_element_role_ids('article', $id_article);
					$this->template['backend_role_ids'] = $this->rule_model->get_element_role_ids('article', $id_article, 'backend');

					// Output
					$this->output('article/options');
				}
				else
				{
					// Article not found
					$this->error(lang('ionize_message_article_not_exist'));
				}
			}
			else
			{
				// Article not found
				$this->error(lang('ionize_message_article_not_exist'));
			}
		}
		else
		{
			$this->output(self::$_DENY_DEFAULT_VIEW);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates the page name
	 *
	 */
	public function update_name()
	{
		$id_page = $this->input->post('id_page');
		$id_article = $this->input->post('id');
		$value = $this->input->post('value');

		if ($id_article && !empty($value))
		{
			$value = $this->article_model->get_unique_name($value, $id_article);

			$result = $this->article_model->update(array('id_article' => $id_article), array('name' => $value));

			if ($result)
			{
				$this->_reload_panel($id_page, $id_article);
			}
		}
		$this->response();
	}


	// ------------------------------------------------------------------------


	public function update_field()
	{
		$field = $this->input->post('field');
		$id_article = $this->input->post('id_article');
		$type = $this->input->post('type');
		
		if ($id_article && $field)
		{
			$value = $this->input->post('value');
			
			// Check the type of data, for special process
			if ($type == 'date')
			{
				$value = ($value) ? getMysqlDatetime($value) : '0000-00-00 00:00:00';
			}

			// Update
			$result = $this->article_model->update(array('id_article' => $id_article), array($field => $value));

			if ($result)
			{
				$this->update_contexts($id_article);
				$this->success(lang('ionize_message_article_saved'));
			}
			else
			{
				$this->response();
			}
		}
	}


	// ------------------------------------------------------------------------


	public function update_categories()
	{
		$id_article = $this->input->post('id_article');

		if ($id_article)
		{
			$this->article_model->join_items_keys_to('category', $this->input->post('categories'), 'article', $id_article);

			$this->callback[] = array(
				'fn' => 'ION.notification',
				'args' => array('success', lang('ionize_message_article_saved'))
			);

			$this->response();
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates the articles contexts (in tree for example)
	 * Called after article->save() && article->save_context()
	 *
	 */
	public function update_contexts($id_article)
	{
		$contexts = $this->article_model->get_lang_contexts($id_article, Settings::get_lang('default'));
		strip_html($contexts);
		
		$this->callback[] = array (
			'fn' => 'ION.updateArticleContext',
			'args' => array($contexts)
		);
	}


	// ------------------------------------------------------------------------


	/**
	 * Save the article context for the defined parent page
	 *
	 */
	public function save_context()
	{
		$data = array();
		
		// Standard fields
		$fields = $this->db->list_fields('page_article');
		
		// Set the data to the posted value.
		foreach ($fields as $field)
		{
			if ( isset($_REQUEST[$field])) $data[$field] = $this->input->post($field);
		}

		// Remove 'ordering' & 'online' from data : not done here
		unset($data['ordering']);
		unset($data['online']);

		// DB save
		$return = $this->article_model->save_context($data);
		
		if ($return)
		{
			// Clear the cache
			Cache()->clear_cache();

			$this->update_contexts($data['id_article']);

			// Answer
			$this->success(lang('ionize_message_article_context_saved'));
		}
		else
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	public function save_main_parent()
	{
		$id_article = $this->input->post('id_article');
		$id_page = $this->input->post('id_page');
		
		$return = $this->article_model->save_main_parent($id_article, $id_page);
		
		if ($return)
		{
			// Clear the cache
			Cache()->clear_cache();

			$this->callback[] = array
			(
				'fn' => 'ION.notification',
				'args' => array('success', lang('ionize_message_article_main_parent_saved'))
			);
			$this->response();
		}
		else
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	* Link an article to a page
	* Called by XHR : ION.linkArticleToPage()
	* Slighsly the same as add_parent() but callbacks are different.
	*
	*
	*/
	public function link_to_page()
	{
		if (!Authority::can('move', 'admin/article')) {
			$this->error(lang('permission_denied'));
		}

		$id_page = $this->input->post('id_page');
		$id_article = $this->input->post('id_article');
		$id_page_origin = $this->input->post('id_page_origin');

		$copy = $this->input->post('copy');

		if ( !empty($id_page) && !empty($id_article))
		{
			// Get the original context
			$original_context = $this->article_model->get_context($id_article, $id_page_origin);

			// Clean of context : On copy
			if ($copy) {
				$original_context['online'] = '0';
				$original_context['main_parent'] = '0';
			}

			// Ordering : last position
			$original_context['ordering'] = $this->_get_ordering('last', $id_page);

			if ($this->article_model->link_to_page($id_page, $id_article, $original_context) === TRUE)
			{
				// Clear the cache
				Cache()->clear_cache();

				// Get the page, menu and articles details for the JSON answer
				$page = $this->page_model->get_by_id($id_page, Settings::get_lang('default'));
				$page['id_article'] = $id_article;

				$menu = $this->menu_model->get_from_page($id_page);

				// Articles
				$articles = $this->article_model->get_lang_list(array('id_article'=>$id_article, 'id_page'=>$id_page), Settings::get_lang('default'));

				// Context : To get the inserted context
				$inserted_context = $this->article_model->get_context($id_article, $id_page, Settings::get_lang('default'));

				// Set the article
				$article = array();
				if ( ! empty($articles))
				{
					$article = $articles[0];
					$article['title'] = htmlspecialchars_decode($article['title'], ENT_QUOTES);

					// Correct online information
					$article['online'] = $inserted_context['online'];

					// Used by JS Tree to detect if article is inserted in tree or not
					$article['inserted'] = TRUE;
				}

				$this->callback = array
				(
					// Add the page to the Article parents list
					array(
						'fn' => 'ION.addPageToArticleParentListDOM',
						'args' => $page
					),
					// Insert the article to the parent in the structure tree
					array(
						'fn' => $menu['name'].'Tree.insertElement',
						'args' => array($article, 'article')
					),
					// Reload the Page articles list
					array(
						'fn' => 'ION.reloadPageArticleList',
						'args' => $id_page
					),
					// Clean the orphan article list (Dashboard) : TODO
					array(
						'fn' => 'ION.removeArticleFromOrphan',
						'args' => $article
					)
				);

				// Article moved ?
				if ($copy == FALSE && $id_page_origin != FALSE)
				{
					// Check and correct content of other articles if they refers to this one from their content
					$this->article_model->correct_internal_links($id_article, $id_page_origin, $id_page);

					// Unlink from first parent : Corrects also the main parent
					$affected_rows = $this->article_model->unlink($id_article, $id_page_origin);

					$ordering = $this->article_model->get_articles_ordering($id_page_origin);

					$this->article_model->save_ordering($ordering, 'page', $id_page_origin);

					$this->callback[] = array(
						'fn' => 'ION.unlinkArticleFromPageDOM',
						'args' => array('id_page' => $id_page_origin, 'id_article' => $id_article)
					);

					$this->callback[] = array(
						'fn' => 'ION.notification',
						'args' => array('success', lang('ionize_message_article_moved'))
					);
				}
				else
				{
					$this->callback[] = array(
						'fn' => 'ION.notification',
						'args' => array('success', lang('ionize_message_article_linked_to_page'))
					);
				}

				// Context update
				$this->update_contexts($id_article);

				$this->response();
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Unlinks one article from one page
	 * 
	 */
	public function unlink($id_page, $id_article)
	{
		if ((!empty($id_page)) && (!empty($id_article)))
		{
			$affected_rows = $this->article_model->unlink($id_article, $id_page);

			if ($affected_rows > 0)
			{
				// Clean URL table
				$this->url_model->clean_table();

				// Update ordering
				$ordering = $this->article_model->get_articles_ordering($id_page);
				
				$this->article_model->save_ordering($ordering, 'page', $id_page);
				
				$this->callback = array(
					'fn' => 'ION.unlinkArticleFromPageDOM',
					'args' => array('id_page' => $id_page, 'id_article' => $id_article)
				);
			
				$this->success(lang('ionize_message_parent_page_unlinked'));
			}
			else
			{
				$this->error(lang('ionize_message_operation_nok'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	public function get_link()
	{
		// Get the article in the context of the page
		$id_page = $this->input->post('id_page');
		$id_article = $this->input->post('id_article');

		// Get the receiver's context
		$context = $this->article_model->get_context($id_article, $id_page);

		$this->template = array('parent' => 'article');

		if ( ! empty($context['link']) )		
		{
			$title = NULL;
			$breadcrumb = '';

			// Prep the Link
			switch($context['link_type'])
			{
				case 'page' :
					
					$link = $this->page_model->get_by_id($context['link_id'], Settings::get_lang('default'));
					$breadcrumb = $this->page_model->get_breadcrumb_string($context['link_id']);

					// Correct missing link
					if ( empty($link) )
					{
						$this->_remove_link($id_page, $id_article);
						break;
					}
					
					$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
					break;
					
				case 'article' :
					
					$link_rel = explode('.', $context['link_id']);
					$link = $this->article_model->get_by_id($link_rel[1], Settings::get_lang('default'));

					$breadcrumb = $this->page_model->get_breadcrumb_string($link_rel[0]);

					// Correct missing link
					if ( empty($link) )
					{
						$this->_remove_link($id_page, $id_article);
						break;
					}
					
					$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];

					$breadcrumb .= ' > ' . $title;

					break;
				
				case 'external' :
					
					$link_rel = '';
					$title = $context['link'];
					break;
			}
			
			if ( ! is_null($title))
			{
				$this->template = array(
					'parent' => 'article',
					'rel' => $id_page.'.'.$id_article,
					'link_id' => $context['link_id'],
					'link_type' => $context['link_type'],
					'link' => $title,
					'breadcrumb' => $breadcrumb
				);
			}
		}

		$this->output('shared/link');
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds a link to an article
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
		// Sent by ION.dropElementAsLink() or ION.addExternalLink()
		$receiver_rel = explode('.', $this->input->post('receiver_rel'));
		$link_type = $this->input->post('link_type');
		$link_rel = $this->input->post('link_rel');
	
		// If the receiver is an article in a given page context : ok
		if (count($receiver_rel > 1))
		{
			// Get the receiver's context
			$context = $this->article_model->get_context($receiver_rel[1], $receiver_rel[0]);

			// Link name (default lang title, for display)
			$title = NULL;
			
			// Prep the Link
			switch($link_type)
			{
				case 'page' :
					$link = $this->page_model->get_by_id($link_rel, Settings::get_lang('default'));
					$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
					break;
					
				case 'article' :
					$rel = explode('.', $link_rel);
					$link = $this->article_model->get_by_id($rel[1], Settings::get_lang('default'));
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

			$context['link_type'] = $link_type;
			$context['link_id'] = $link_rel;
			$context['id_page'] = $receiver_rel[0];
			$context['link'] = $title;

			// Save the context		
			$this->article_model->save_context($context);
			
			if ($title != '')
			{
				$this->callback = array(
					array(
						'fn' => 'ION.HTML',
						'args' => array('article/get_link', array('id_page' => $receiver_rel[0], 'id_article'=> $receiver_rel[1]), array('update' => 'linkContainer'))
					),
					array(
						'fn' => 'ION.updateArticleContext',
						'args' => array(array($context))
					),
					array(
						'fn' => 'ION.notification',
						'args' => array('success', lang('ionize_message_link_added'))
					)
				);
			}

			$this->response();
		}
	}


	// ------------------------------------------------------------------------


	public function remove_link()
	{
		$receiver_rel = explode('.', $this->input->post('rel'));
		
		if (count($receiver_rel > 1))
		{
			// Clear the cache
			Cache()->clear_cache();
			
			$this->_remove_link($receiver_rel[0], $receiver_rel[1]);

			$this->callback = array(
				array(
					'fn' => 'ION.HTML',
					'args' => array('article/get_link', array('id_page' => $receiver_rel[0],'id_article' => $receiver_rel[1]), array('update' => 'linkContainer'))
				)
			);

			// Context update
			$this->update_contexts($receiver_rel[1]);

			$this->response();
		}
	}


	// ------------------------------------------------------------------------


	private function _remove_link($id_page, $id_article)
	{
		$context = array(
			'link_type' => '',
			'link_id' => '',
			'link' => '',
			'id_page' => $id_page,
			'id_article' => $id_article
		);

		// Save the context		
		return $this->article_model->save_context($context);
	}


	// ------------------------------------------------------------------------


	/**
	 * Duplicates one article
	 * Called by /views/toolboxes/article_toolbox
	 *
	 * @param	int		Source article ID
	 * 
	 * TODO :	Check if the article exists and display an error window if not.
	 *			JS Callbacks of MUI.formWindow() needs to be implemented
	 *
	 */
	public function duplicate($id)
	{
		// Source article
		$cond = array
		(
			'id_page' => $this->input->post('id_page'),
			'id_article' => $id
		);
		
		if ($this->input->post('id_page'))
			$source_article = array_shift($this->article_model->get_linked_lang_items('page', 'article', $cond, Settings::get_lang('default')) );
		else
		{
			unset($cond['id_page']);
			$source_article = $this->article_model->get($cond, Settings::get_lang('default'));
		}

		// Context page, if any
		$this->template['page'] = $this->page_model->get_by_id($this->input->post('id_page'), Settings::get_lang('default'));

		
		// Dropdowns Views
		if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
			require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');

		$views = isset($views['article']) ? $views['article'] : array() ;
		if(count($views) > 0)
		{
			if ( ! isset($source_article['view'])) $source_article['view'] = FALSE;
			$views = array('' => lang('ionize_select_default_view')) + $views; 

			$this->template['views'] = form_dropdown('article_view', $views, $source_article['view'], 'class="select w160"');
		}

		$this->template['all_views'] = $views;


		// All articles type to template
		$types = $this->article_type_model->get_types_select();
		$types = array('' => lang('ionize_select_no_type')) + $types; 
		$this->template['all_types'] = $types;
		if ( ! isset($source_article['id_type'])) $source_article['id_type'] = FALSE;

		$this->template = array_merge($this->template, $source_article);

		$this->template['name'] = $source_article['name'];
		// $this->template['has_url'] = $source_article['has_url'];
		$this->template['title'] = ($source_article['title'] != '') ? $source_article['title'] : $source_article['name'];

		// Dropdown menus
		$datas = $this->menu_model->get_select();
		$this->template['menus'] =	form_dropdown('dup_id_menu', $datas, '1', 'id="dup_id_menu" class="select"');

		$this->output('article/duplicate');
	}


	// ------------------------------------------------------------------------


	public function save_duplicate()
	{
		if( $this->input->post('dup_url') != '' && $this->input->post('dup_id_page') > 0)
		{
			// No name change : exit
			if (url_title($this->input->post('dup_url')) == $this->input->post('name'))
			{
				$this->error(lang('ionize_message_article_duplicate_no_name_change'));
			}
			
			/* New article data :
			 * - The updater is set to nobody
			 * - The author become the current connected user
			 *
			 */
			$user = User()->get_user();
			
			$data = array(
				'name' => url_title($this->input->post('dup_url')),
				'id_page' => $this->input->post('dup_id_page'),
				'view' => $this->input->post('view'),
				'id_type' => $this->input->post('id_type'),
				'updater' => $user['username'],
				'author' => $user['username']
			);
			
			// Duplicate the article base data and get the new ID
			$id_new_article = $this->article_model->duplicate($this->input->post('id_article'), $data, $this->input->post('ordering_select') );
		
			if ($id_new_article !== FALSE)
			{
				// Clear the cache
				Cache()->clear_cache();

				// Update URLs
				$this->article_model->save_urls($id_new_article);

				// Update the content structure tree
				// The data var is merged to the default lang data_lang var,
				// in order to send the lang values to the browser without making another SQL request
				$menu = $this->menu_model->get_from_page($this->input->post('dup_id_page'));
				
				$article = array_shift($this->article_model->get_linked_lang_items('page', 'article', array('id_article'=>$id_new_article), Settings::get_lang('default')) );

				$this->data = $article;
				$this->data['title'] = htmlspecialchars_decode($article['title'], ENT_QUOTES);
				$this->data['id_article'] = $id_new_article;
				$this->data['id_page'] = $this->input->post('dup_id_page');
				$this->data['element'] = 'article';
				$this->data['menu'] = $menu;
				$this->data['online'] = 0;
				
				// Used by JS Tree to detect if article in inserted in tree or not
				$this->data['inserted'] = TRUE;

				// Panels Update
				$this->_reload_panel($this->data['id_page'], $id_new_article);

				$this->callback[] = array(
					'fn' => $menu['name'].'Tree.insertElement',
					'args' => array($this->data, 'article')
				);

				// Answer send
				$this->success(lang('ionize_message_article_duplicated'));
			}
			else
			{
				$this->error(lang('ionize_message_article_not_duplicated'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_article_not_duplicated'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Set an item online / offline depending on its current context and status
	 *
	 * @param	int		item ID
	 * @param	int		item ID
	 *
	 */
	public function switch_online($id_page, $id_article)
	{
		// Clear the cache
		Cache()->clear_cache();

		$status = $this->article_model->switch_online($id_page, $id_article);

		$this->callback = array(
			array(
				'fn' => 'ION.switchOnlineStatus',
				'args' => array(
					'status' => $status,
					'selector' => '.article'.$id_page.'x'.$id_article
				)
			)
		);

		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves article ordering
	 * 
	 */
	public function save_ordering($parent, $id_parent)
	{
		if (!Authority::can('move', 'admin/article')) {
			$this->error(lang('permission_denied'));
		}

		$order = $this->input->post('order');
		
		if( $order !== FALSE )
		{
			// Clear the cache
			Cache()->clear_cache();

			// Saves the new ordering
			$this->article_model->save_ordering($order, $parent, $id_parent);
			
			// Update the panels
			$this->callback = array(
				'fn' => 'ION.updateArticleOrder',
				'args' => array(
					'id_page' => $id_parent,
					'order' => $order
				)
			);
			
			// Answer send
			$this->success(lang('ionize_message_article_ordered'));
		}
		else 
		{
			// Answer send
			$this->error(lang('ionize_message_operation_nok'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets the article list for the ordering select dropdown
	 * @param	int		Page ID
	 *
	 * @returns	string	HTML string of options items
	 *
	 */
	public function get_ordering_article_select($id_page)
	{
		// Articles array
		$this->template['articles'] = $this->article_model->get_lang_list(array('id_page'=>$id_page), Settings::get_lang('default'));
		
		$this->output('article/ordering_select');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Deletes one article
	 *
	 * @param	int 		Article ID
	 *
	 */
	public function delete($id)
	{
		if (!Authority::can('delete', 'admin/article')) {
			$this->error(lang('permission_denied'));
		}

		$affected_rows = $this->article_model->delete($id);
		
		// Delete was successful
		if ($affected_rows > 0)
		{
			// Clear the cache
			Cache()->clear_cache();

			// Clean URL table
			$this->url_model->clean_table();

			// Remove deleted article from DOM
			$this->callback[] = array(
				'fn' => 'ION.deleteDomElements',
				'args' => array('.article' . $id)
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


	public function multiple_action()
	{
		$ids = $this->input->post('ids');
		$action = $this->input->post('action');
		$id_page = $this->input->post('id_page');
		$returned_ids = array();

		if ( ! empty($ids))
		{
			switch($action)
			{
				case 'delete':
					foreach($ids as $id)
					{
						$nb = $this->article_model->delete($id);
						if ($nb > 0) $returned_ids[] = $id;
					}

					break;

				case 'unlink':
					foreach($ids as $id)
					{
						$nb = $this->article_model->unlink($id, $id_page);
						if ($nb > 0) $returned_ids[] = $id;
					}
					break;

				case 'offline':
					foreach($ids as $id)
						$this->article_model->switch_online($id_page, $id, 0);
					$returned_ids = $ids;
					break;

				case 'online':
					foreach($ids as $id)
						$this->article_model->switch_online($id_page, $id, 1);
					$returned_ids = $ids;
					break;
			}

			$this->url_model->clean_table();

			$this->xhr_output(array(
				'action' => $action,
				'id_page' => $id_page,
				'ids' => $returned_ids
			));
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


	/** 
	 * Prepares data before saving
	 *
	 */
	protected function _prepare_data()
	{
		// Standard fields
		$fields = $this->db->list_fields('article');
		
		// Set the data to the posted value.
		foreach ($fields as $field)
		{
			if ($this->input->post($field) !== FALSE OR in_array($field, $this->boolean_data))
			{
				if ( ! in_array($field, $this->no_htmlspecialchars))
					$this->data[$field] = htmlspecialchars($this->input->post($field), ENT_QUOTES, 'utf-8');
				else
					$this->data[$field] = $this->input->post($field);
			}
		}

		// Page ID : Only on creation
		if ($this->input->post('id_page'))
		{
			$this->data['id_page'] = $this->input->post('id_page');
		
			// Ordering : Only done for a new article, else, don't touch
			if ( ! $this->input->post('id_article'))
				$this->data['ordering'] = $this->_get_ordering($this->input->post('ordering_select'), $this->data['id_page'], $this->input->post('ordering_after'));
		}
		
		// Author & updater
		$user = User()->get_user();
		if ($this->input->post('id_article'))
			$this->data['updater'] = $user['username'];
		else
			$this->data['author'] =  $user['username'];


		// URLs : Feed the other languages URL with the default one if the URL is missing
		$urls = $this->_get_urls(TRUE);

		// Update the name (not used anymore in the frontend, but used in the backend)
		$this->data['name'] = $urls[Settings::get_lang('default')];
		$this->data['name'] = $this->article_model->get_unique_name($this->data['name'], $this->input->post('id_article'));

		/*
		 * Lang data
		 *
		 */
		$fields = $this->db->list_fields('article_lang');

		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				// Do not filter
				if ( in_array($field, $this->no_xss_filter))
				{
					$content = $_REQUEST[$field.'_'.$language['lang']];
					$content = stripslashes($content);
				}
				// Filter
				else
					$content = $this->input->post($field.'_'.$language['lang']);

				if (in_array($field, $this->htmlspecialchars))
					$content = htmlspecialchars($content, ENT_QUOTES, 'utf-8');

				if ( $field != 'url' && $content !== FALSE)
				{
					// Allowed tags filter
					$allowed_tags = explode(',', Settings::get('article_allowed_tags'));
					$allowed_tags = '<' . implode('>,<', $allowed_tags ) . '>';

					$content = strip_tags($content, $allowed_tags);
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


	protected function _prepare_options_data()
	{
		// Standard fields
		$fields = $this->db->list_fields('article');

		// Set the data to the posted value.
		foreach ($fields as $field)
		{
			if ($this->input->post($field) !== FALSE OR in_array($field, $this->boolean_options))
				$this->data[$field] = $this->input->post($field);
		}
		// Lang data
		$fields = $this->db->list_fields('article_lang');

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
	 * Return TRUE if the extend fields array contains at least one translated extend field
	 *
	 * @param $extend_fields
	 *
	 * @return bool
	 *
	 */
	protected function _has_translated_extend_fields($extend_fields)
	{
		$result = FALSE;
		foreach($extend_fields as $ef)
		{
			if ($ef['translated'] == 1)
			{
				$result = TRUE;
				break;
			}
		}
		return $result;
	}


	// ------------------------------------------------------------------------


 	/**
 	 * Gets the article's ordering
 	 * Also reorder the context table
 	 *
 	 * @param	string		place of the new inserted article. 'first, 'last' or 'after'
 	 * @param	int			ID of the page.
 	 * @param	int			ID of the referent article. Must be set if place is 'after'
 	 *
	 * @return	int			place of the article
 	 */
	protected function _get_ordering($place, $id_page, $id_ref = NULL)
	{
		$existing_ordering = $this->article_model->get_articles_ordering($id_page);

		$ordering = '0';

		switch($place)
		{
			case 'first' :
			
				$this->article_model->shift_article_ordering($id_page);				

				$ordering = '1';
				
				break;
			
			case 'last' :
			
				$ordering = count($existing_ordering) + 1 ;
				
				break;

			case 'after' :
			
				$new_pos = array_search($id_ref, $existing_ordering) + 2;

				// Shift every article with a greather pos than ordering_after
				$this->article_model->shift_article_ordering($id_page, $new_pos);				
				
				$ordering = $new_pos;
			
				break;
		}
		return $ordering;
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
		
		// Fill in potential empty URLs		
		foreach($urls as $lang => $url)
			if ($url == '')	$urls[$lang] = $urls[Settings::get_lang('default')];
		
		return $urls;
	}


	// ------------------------------------------------------------------------


	/**
	 * Reloads the Edition panel
	 *
	 * @param $id_page
	 * @param $id_article
	 */
	protected function _reload_panel($id_page, $id_article)
	{
		$page = $this->page_model->get_by_id($id_page);
		$page['menu'] = $this->menu_model->get($page['id_menu']);

		// Main data
		$article = $this->article_model->get_by_id($id_article);

		$article_lang = $this->article_model->get_by_id($id_article, Settings::get_lang('default'));
		$title = empty($article_lang['title']) ? $article_lang['name'] : $article_lang['title'];

		// Correcting some lang data
		$article_lang['online'] = $article['online'];

		$this->callback[] =	array(
			'fn' => 'ION.splitPanel',
			'args' => array(
				'urlMain'=> admin_url(TRUE) . 'article/edit/'.$id_page.'.'.$id_article,
				'urlOptions'=> admin_url(TRUE) . 'article/get_options/'.$id_page.'.'.$id_article,
				'title'=> lang('ionize_title_edit_article') . ' : ' . $title
			)
		);
		$this->callback[] = array(
			'fn' => $page['menu']['name'].'Tree.updateElement',
			'args' => array($article_lang, 'article')
		);
	}
}
