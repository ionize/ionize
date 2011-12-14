<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize, creative CMS Article Controller
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Article
 * @author		Ionize Dev Team
 *
 */

class Article extends MY_admin 
{

	/**
	 * Fields on wich the htmlspecialchars function will not be used before saving
	 * 
	 * @var array
	 */
	protected $no_htmlspecialchars = array('content', 'subtitle');


	/**
	 * Fields on wich no XSS filtering is done
	 * 
	 * @var array
	 */
	protected $no_xss_filter = array('content');


	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('menu_model', '', true);
		$this->load->model('page_model', '', true);
		$this->load->model('article_model', '', true);
		$this->load->model('category_model', '', true);
		$this->load->model('article_type_model', '', true);
		$this->load->model('tag_model', '', true);
		$this->load->model('extend_field_model', '', true);
		$this->load->model('url_model', '', true);
		
		$this->load->library('structure');
		
		$this->load->helper('string_helper');
	}


	// ------------------------------------------------------------------------


	/**
	 * Default : Do nothing
	 *
	 */
	function index()
	{
		return;
	}
	

	// ------------------------------------------------------------------------


	/**
	* Displays the articles panel
	* All articles with parent list
	* 
	* @returns	View of the articles list
	*
	*/
	function list_articles()
	{
		// Get articles
		$articles = $this->article_model->get_list(array('order_by' => 'title ASC'));

		// Get all lang info for all articles
		$articles_lang = $this->article_model->get_lang();
		
		// Get all contexts : links between pages and articles
		$page_article = $this->article_model->get_all_context();
		
		// Get pages
		$pages = $this->page_model->get_lang_list(false, Settings::get_lang('default'));

		
		// Add page data to each context
		foreach($page_article as &$pa)
		{
			$pa['page'] = array();
			foreach($pages as $page)
			{
				if($page['id_page'] == $pa['id_page'])
				{
					$pa['page'] = $page;
				}
			
			}
			// $page = array_values(array_filter($pages, create_function('$row','return $row["id_page"] == "'. $pa['id_page'] .'";')));
			// $pa['page'] = (!empty($page) ? $page[0] : array() );
		}
		
		// Link articles to pages
		foreach($articles as &$article)
		{
			$article['langs'] = array();
		
			$langs = array_values(array_filter($articles_lang, create_function('$row','return $row["id_article"] == "'. $article['id_article'] .'";')));
			
			foreach(Settings::get_languages() as $lang)
			{
				$article['langs'][$lang['lang']] = array_pop(array_filter($langs, create_function('$row','return $row["lang"] == "'. $lang['lang'] .'";')));
			}
			
			$article['pages'] = array_values(array_filter($page_article, create_function('$row','return $row["id_article"] == "'. $article['id_article'] .'";')));
			
		}

		$this->template['articles'] = $articles;


		$this->output('articles');
	}


	// ------------------------------------------------------------------------


	function get_list()
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

			$datas = isset($views['article']) ? $views['article'] : array() ;
			$datas = array('' => lang('ionize_select_default_view')) + $datas; 
			$this->template['all_article_views'] = $datas;


			// All articles type to template
			$datas = $this->article_type_model->get_types_select();
			$datas = array('' => lang('ionize_select_no_type')) + $datas; 
			$this->template['all_article_types'] = $datas;
			
			
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
			$this->output('article_list');
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
	function create($id_page = NULL) 
	{
		// Page
		if ( ! is_null($id_page))
		{
			$page = $this->page_model->get($id_page);
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
		
		// Existing Tags in all other articles
// Has to be checked
		$this->template['existing_tags'] = $this->tag_model->get_list();
		
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
			$this->template['article_views'] = form_dropdown('view', $datas, false, 'class="select w160"');
		}

		// Categories
		$categories = $this->category_model->get_categories_select();
		$this->template['categories'] =	form_dropdown('categories[]', $categories, false, 'class="select" multiple="multiple"');


		// Article types
		$types = $this->article_type_model->get_types_select();
		$this->template['article_types'] =	form_dropdown('id_type', $types, false, 'class="select"');
		
		// Extends fields
		$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('article');
		
		// Context data initialized when article creation
		$this->template['online'] = '0';
		$this->template['main_parent'] = '1';
		$this->template['has_url'] = '1';

		$this->output('article');
	}	
	

	// ------------------------------------------------------------------------

	
	/**
	 * Saves one article
	 *
	 * @param	boolean		if true, the transport is through XHR
	 */
	function save()
	{
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

			$id_article = $this->input->post('id_article');
			
			// Prepare data before saving
			$this->_prepare_data();

			// Saves article to DB and get the saved ID
			$this->id = $this->article_model->save($this->data, $this->lang_data);

			// Link to page
			if ( ! empty($this->data['id_page']))
			{
				$this->data['online'] = $this->input->post('online');
				$this->data['main_parent'] = $this->input->post('main_parent');
				$this->article_model->link_to_page($this->data['id_page'], $this->id, $this->data);
			}
			else
				$this->data['id_page'] = '0';				

			// Correct DB integrity : Links IDs
			if ( ! empty($id_article) )
				$this->article_model->correct_integrity($this->data, $this->lang_data);
				
			// Saves linked categories
			$this->base_model->join_items_keys_to('category', $this->input->post('categories'), 'article', $this->id);

			// Saves tags
			$this->tag_model->save_tags($this->input->post('tags'), 'article', $this->id);

			// Save extend fields data
			$this->extend_field_model->save_data('article', $this->id, $_POST);

			// Save URLs
			$this->article_model->save_urls($this->id);
			
			
			/* 
			 * JSON Answer
			 *
			 * Updates the structure tree
			 * The data var is merged to the default lang data_lang var,
			 * in order to send the lang values to the browser without making another SQL request
			 */
			
			// Get the context info
			$context = $this->article_model->get_context($this->id, $this->data['id_page'], Settings::get_lang('default'));
			$this->data = array_merge($this->data, $context);
			
			
			// Remove HTML tags from returned array
			strip_html($this->data);
			
			
			// Insert Case
			if ( empty($id_article) )
			{
				$menu = $this->menu_model->get_from_page($this->data['id_page']);
				$this->data['menu'] = $menu;
				
				// Used by JS Tree to detect if article in inserted in tree or not
				$this->data['inserted'] = TRUE;
				
				// Insert article to tree if menu is found (for id_page = 0, no one is found)
				if (!empty($menu))
				{
					$this->callback[] = array(
						'fn' => $menu['name'].'Tree.insertElement',
						'args' => array($this->data, 'article')
					);
				}
			}
			
			// Reloads the article
			$this->callback[] = array(
				'fn' => 'ION.updateElement',
				'args' => array(
					'element'=> 'mainPanel',
					'url' => 'article/edit/' .$this->data['id_page'].'.'.$this->id
				)
			);
			
			// Success Message
			$this->callback[] = array(
				'fn' => 'ION.notification',
				'args' => array('success', lang('ionize_message_article_saved'))
			);
			
			// Context update
			$this->update_contexts($this->data['id_article']);
		}
		else
		{
			$this->error(lang('ionize_message_article_needs_url_or_title'));
		}

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
	function edit($rel)
	{
		// IDs
		$rel = explode(".", $rel);
		$id_page = ( !empty($rel[1] )) ? $rel[0] : '0';
		$id_article = ( !empty($rel[1] )) ? $rel[1] : NULL;
		
		// Edit article if ID exists
		if ( ! is_null($id_article) )
		{
			$article = $this->article_model->get($id_article);

			if( ! empty($article) )
			{
				$this->load_modules_addons($article);

				// Page context of the current edited article
				$article['id_page'] = $id_page;
				
				// Merge article's data with template
				$this->template = array_merge($this->template, $article);

				// Linked pages list
				$this->template['pages_list'] = $this->article_model->get_pages_list($id_article);

				// Categories
				$categories = $this->category_model->get_categories_select();
				$current_categories = $this->category_model->get_current_categories('article', $id_article);
				$this->template['categories'] =	form_dropdown('categories[]', $categories, $current_categories, 'class="select w140" multiple="multiple"');
	
				// Tags
				$this->template['tags'] =	$this->tag_model->get_tags_from_parent('article', $id_article, 'string');
				
				// Existing tags
				$this->template['existing_tags'] =	$this->tag_model->get_tags('string');
				
				// Extends fields
				$this->template['extend_fields'] = array();
				$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('article', $id_article);
				
				// Link : Depending on the context
				$context = $this->article_model->get_context($id_article, $id_page);
				
				if ( ! empty($context))
				{
					$this->template['link'] = $context['link'];
					$this->template['link_id'] = $context['link_id'];
					$this->template['link_type'] = $context['link_type'];
					$this->template['main_parent'] = $context['main_parent'];
					
					$pages = $this->page_model->get_parent_array(array('id_page' => $id_page), array(), Settings::get_lang('default'));
					
					$breadcrump = array();
					foreach($pages as $page)
					{
						$breadcrump[] = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
					}
					$this->template['breadcrump'] = implode(' > ', $breadcrump);
				}
				else
				{
					$this->template['main_parent'] = '0';
				}
				
				// Lang data
				$this->article_model->feed_lang_template($id_article, $this->template);

				$this->output('article');
			}		
		}
	}
	
	
	function options($rel)
	{
		// IDs
		$rel = explode(".", $rel);
		$id_page = ( !empty($rel[1] )) ? $rel[0] : '0';
		$id_article = ( !empty($rel[1] )) ? $rel[1] : NULL;
		
		// Edit article if ID exists
		if ( ! is_null($id_article) )
		{
			$article = $this->article_model->get($id_article);

			if( ! empty($article) )
			{
				// Page context of the current edited article
				$article['id_page'] = $id_page;
				
				// Merge article's data with template
				$this->template = array_merge($this->template, $article);

				// Linked pages list
				$this->template['pages_list'] = $this->article_model->get_pages_list($id_article);

				// Categories
				$categories = $this->category_model->get_categories_select();
				$current_categories = $this->category_model->get_current_categories('article', $id_article);
				$this->template['categories'] =	form_dropdown('categories[]', $categories, $current_categories, 'class="select" multiple="multiple"');
	
				// Tags
				$this->template['tags'] =	$this->tag_model->get_tags_from_parent('article', $id_article, 'string');
				
				// Existing tags
				$this->template['existing_tags'] =	$this->tag_model->get_tags('string');
				
				// Extends fields
				$this->template['extend_fields'] = array();
				$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('article', $id_article);
				
				// Link : Depending on the context
				$context = $this->article_model->get_context($id_article, $id_page);
				
				if ( ! empty($context))
				{
					$this->template['link'] = $context['link'];
					$this->template['link_id'] = $context['link_id'];
					$this->template['link_type'] = $context['link_type'];
					
					$pages = $this->page_model->get_parent_array(array('id_page' => $id_page), array(), Settings::get_lang('default'));
					$breadcrump = array();
					foreach($pages as $page)
					{
						$breadcrump[] = ( ! empty($page['title'])) ? $page['title'] : $page['name'];
					}
					$this->template['breadcrump'] = implode(' &raquo; ', $breadcrump);
				}
				
				// Lang data
				$this->article_model->feed_lang_template($id_article, $this->template);

				$this->output('panels/article_options');
			}		
		}
	
	}


	function update_field()
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
				$this->callback[] = array(
					'fn' => 'ION.notification',
					'args' => array('success', lang('ionize_message_article_saved'))
				);

				$this->update_contexts($id_article);
			}
			else
			{
				$this->response();
			}
		}
	}
	
	
	function update_indexed()
	{
		$id_article = $this->input->post('id_article');
		$indexed = $this->input->post('indexed');
		
		if ($id_article)
		{
			$result = $this->article_model->update(array('id_article'=>$id_article), array('indexed'=>$indexed));
			
			if ($result)
			{
				$this->callback[] = array(
					'fn' => 'ION.notification',
					'args' => array('success', lang('ionize_message_article_saved'))
				);

				$this->update_contexts($id_article);
			}
		}
	}
	
	function update_categories()
	{
		$id_article = $this->input->post('id_article');

		if ($id_article)
		{
			$result = $this->article_model->join_items_keys_to('category', $this->input->post('categories'), 'article', $id_article);

			if ($result)
			{
				$this->callback[] = array(
					'fn' => 'ION.notification',
					'args' => array('success', lang('ionize_message_article_saved'))
				);

				$this->response();
			}
		}
	}
	
	
	/**
	 * Updates the articles contexts (in tree for example)
	 * Called after article->save() && article->save_context()
	 *
	 */
	function update_contexts($id_article)
	{
		$contexts = $this->article_model->get_lang_contexts($id_article, Settings::get_lang('default'));
		
		strip_html($contexts);
		
		$this->callback[] = array (
			'fn' => 'ION.updateArticleContext',
			'args' => array($contexts)
		);
		
		$this->response();
	}

	// ------------------------------------------------------------------------

	
	/**
	 * Edit the article context for the defined parent page
	 * An article can have a view / parent
	 *
	 */
	function edit_context($id_page, $id_article)
	{
		// Article datas from page context
		$article = $this->article_model->get_context($id_article, $id_page);

		// Page datas
		$page = $this->page_model->get($id_page, Settings::get_lang('default'));
		
		// Dropdown article views (templates)
		$views = array();
		if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
			require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');
		
		$datas = isset($views['article']) ? $views['article'] : array() ;
		if(count($datas) > 0)
		{
			$datas = array('0' => lang('ionize_select_default_view')) + $datas; 
			$this->template['article_views'] = form_dropdown('view', $datas, $article['view'], 'class="select"');
		}
		
		// Article ordering : needs All other articles from this page
		$this->template['articles'] = $this->article_model->get_list(array('id_page'=>$id_page, 'order_by' => 'article.ordering ASC'));

		// Articles Types
		$types = $this->article_type_model->get_types_select();
		$this->template['article_types'] =	form_dropdown('id_type', $types, $article['id_type'], 'class="select"');

		$this->template['article'] = $article;
		$this->template['page'] = $page;

		// Context ID
		$this->template['id_context'] = $id_page.'x'.$id_article;
		
		$this->output('article_context');
	}

	
	// ------------------------------------------------------------------------

	
	/**
	 * Save the article context for the defined parent page
	 *
	 */
	function save_context()
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

			$this->callback[] = array
			(
				'fn' => 'ION.notification',
				'args' => array('success', lang('ionize_message_article_context_saved'))
			);
			$this->update_contexts($data['id_article']);
		}
		else
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}
	
	
	function save_main_parent()
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
	function link_to_page()
	{
		$id_page = $this->input->post('id_page');
		$id_article = $this->input->post('id_article');
		$id_page_origin = $this->input->post('id_page_origin');
		$rel = $id_page.'.'.$id_article;
		$flat_rel = $id_page.'x'.$id_article;
		$copy = $this->input->post('copy');

		if ((!empty($id_page)) && (!empty($id_article)))
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
				$page = $this->page_model->get($id_page, Settings::get_lang('default'));
				$page['id_article'] = $id_article;

				$menu = $this->menu_model->get_from_page($id_page);
				
				// Articles
				$articles = $this->article_model->get_lang_list(array('id_article'=>$id_article, 'id_page'=>$id_page), Settings::get_lang('default'));

				// Set the article
				$article = array();
				if ( ! empty($articles))
				{
					$article = $articles[0];
					$article['title'] = htmlspecialchars_decode($article['title'], ENT_QUOTES);
					
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
					),
					array(
						'fn' => 'ION.notification',
						'args' => array('success', lang('ionize_message_article_linked_to_page'))
					)
				);
				
				// Article moved ?
				if ($copy == FALSE && $id_page_origin != FALSE)
				{
					// Unlink from first parent : Corrects also the main parent
					$affected_rows = $this->article_model->unlink($id_article, $id_page_origin);
					
					$ordering = $this->article_model->get_articles_ordering($id_page_origin);
					
					$this->article_model->save_ordering($ordering, 'page', $id_page_origin);
					
					$this->callback[] = array(
						'fn' => 'ION.unlinkArticleFromPageDOM',
						'args' => array('id_page' => $id_page_origin, 'id_article' => $id_article)
					);
				}
				
				// Context update
				$this->update_contexts($id_article);
			}
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	
	/**
	 * Unlinks one article from one page
	 * 
	 */
	function unlink($id_page, $id_article)
	{
		if ((!empty($id_page)) && (!empty($id_article)))
		{
			$affected_rows = $this->article_model->unlink($id_article, $id_page);

			if ($affected_rows > 0)
			{
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


	function get_link()
	{
		// Get the articel in the context of the page
		$id_page = $this->input->post('id_page');
		$id_article = $this->input->post('id_article');

		// Get the receiver's context
		$context = $this->article_model->get_context($id_article, $id_page);

		$this->template = array('parent' => 'article');

		if ($context['link'] !='')		
		{
			$title = NULL;
			
			// Prep the Link
			switch($context['link_type'])
			{
				case 'page' :
					
					$link = $this->page_model->get(array('id_page' => $context['link_id']), Settings::get_lang('default'));
					
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
					$link = $this->article_model->get(array('id_article' => $link_rel[1]), Settings::get_lang('default'));
					
					// Correct missing link
					if ( empty($link) )
					{
						$this->_remove_link($id_page, $id_article);
						break;
					}
					
					$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
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
					'link' => $title
				);
			}
		}

		$this->output('link');
	}
	

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
	function add_link()
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
					$link = $this->page_model->get(array('id_page' => $link_rel), Settings::get_lang('default'));
					$title = ( ! empty($link['title'])) ? $link['title'] : $link['name'];
					break;
					
				case 'article' :
					$rel = explode('.', $link_rel);
					$link = $this->article_model->get(array('id_article' => $rel[1]), Settings::get_lang('default'));
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
			
			// Test the external link
			/*
			if ($link_type == 'external')
			{
				$check = check_url($title);
				
				if($check === false)
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


	function remove_link()
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
	function duplicate($id, $name)
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
		$this->template['page'] = $this->page_model->get(array('id_page' => $this->input->post('id_page')), Settings::get_lang('default'));

		
		// Dropdowns Views
		if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
			require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');

		$views = isset($views['article']) ? $views['article'] : array() ;
		if(count($views) > 0)
		{
			if ( ! isset($source_article['view'])) $source_article['view'] = false;
			$views = array('' => lang('ionize_select_default_view')) + $views; 

			$this->template['views'] = form_dropdown('article_view', $views, $source_article['view'], 'class="select w160"');
		}

		$this->template['all_views'] = $views;


		// All articles type to template
		$types = $this->article_type_model->get_types_select();
		$types = array('' => lang('ionize_select_no_type')) + $types; 
		$this->template['all_types'] = $types;
		if ( ! isset($source_article['id_type'])) $source_article['id_type'] = false;

		$this->template = array_merge($this->template, $source_article);

		$this->template['name'] = $source_article['name'];
//		$this->template['has_url'] = $source_article['has_url'];
		$this->template['title'] = ($source_article['title'] != '') ? $source_article['title'] : $source_article['name'];

		// Dropdown menus
		$datas = $this->menu_model->get_select();
		$this->template['menus'] =	form_dropdown('dup_id_menu', $datas, '1', 'id="dup_id_menu" class="select"');

		// Dropdown parents
		$datas = $this->page_model->get_lang_list(array('id_menu' => '1'), Settings::get_lang('default'));
		$parents = array();
		($parents_array = $this->structure->get_parent_select($datas) ) ? $parents += $parents_array : '';
		$this->template['parent_select'] = form_dropdown('dup_id_page', $parents, false, 'id="dup_id_page" class="select"');
		
		$this->output('article_duplicate');
	}


	// ------------------------------------------------------------------------


	function save_duplicate()
	{
		if( $this->input->post('dup_url') != '' )
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
			$user = $this->connect->get_current_user();
			
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

				/* Update the content structure tree
				 * The data var is merged to the default lang data_lang var,
				 * in order to send the lang values to the browser without making another SQL request
				 */
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

				// Panels Update array
				$this->update[] = array(
					'element' => 'mainPanel',
					'url' => admin_url() . 'article/edit/'.$this->data['id_page'].'.'.$id_new_article,
					'title' => lang('ionize_title_edit_article')
				);
				
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
	function switch_online($id_page, $id_article)
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
	function save_ordering($parent, $id_parent)
	{
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
	function get_ordering_article_select($id_page)
	{
		// Articles array
		$this->template['articles'] = $this->article_model->get_lang_list(array('id_page'=>$id_page), Settings::get_lang('default'));
		
		$this->output('article_ordering_select');
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Gets the parent list list for the parent select dropdown
	 * @param	int		Menu ID
	 * @param	int		Page parent ID
	 *
	 * @returns	string	HTML string of options items
	 *
	function get_parents_select($id_menu, $id_parent=0)
	{
		$datas = $this->page_model->get_lang_list(array('id_menu' => $id_menu), Settings::get_lang('default'));

		$parents = array(
			'0' => lang('ionize_select_no_parent')
		);
		($parents += $this->structure->get_parent_select($datas, 0) ) ? $parent : '';
		
		$this->template['pages'] = $parents;
		$this->template['id_selected'] = $id_parent;
		
		$this->output('page_parent_select');
	}
	*/


	// ------------------------------------------------------------------------


	/** 
	 * Deletes one article
	 *
	 * @param	int 		Article ID
	 *
	 */
	function delete($id)
	{
		$article = $this->article_model->get(array('id_article' => $id));
	
		$affected_rows = $this->article_model->delete($id);
		
		// Delete was successfull
		if ($affected_rows > 0)
		{
			// Clear the cache
			Cache()->clear_cache();

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


	/** 
	 * Prepares data before saving
	 *
	 */
	function _prepare_data() 
	{
		// Standard fields
		$fields = $this->db->list_fields('article');
		
		// Set the data to the posted value.
		foreach ($fields as $field)
		{
			if ( ! in_array($field, $this->no_htmlspecialchars))
				$this->data[$field] = htmlspecialchars($this->input->post($field), ENT_QUOTES, 'utf-8');
			else
				$this->data[$field] = $this->input->post($field);
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
		$user = $this->connect->get_current_user();
		if ($this->input->post('id_article'))
			$this->data['updater'] = $user['username'];
		else
			$this->data['author'] =  $user['username'];


		// URLs : Feed the other languages URL with the default one if the URL is missing
		$urls = $this->_get_urls(TRUE);

/*
		$default_lang_url = $urls[Settings::get_lang('default')];
		
		foreach($urls as $lang => $url)
			if ($url == '')	$urls[$lang] = $default_lang_url;
*/		

		// Update the page name (not used anymore in the frontend, but used in the backend)
		$this->data['name'] = $urls[Settings::get_lang('default')];


		/*
		 * Lang data
		 *
		 */
		$this->lang_data = array();

		$fields = $this->db->list_fields('article_lang');

		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				if ( $field != 'url' && $this->input->post($field.'_'.$language['lang']) !== false)
				{
					$content = $this->input->post($field.'_'.$language['lang']);
					
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


 	/**
 	 * Gets the article's ordering
 	 * Also reorder the context table
 	 *
 	 * @param	string		place of the new insertted article. 'first, 'last' or 'after'
 	 * @param	int			ID of the page.
 	 * @param	int			ID of the referent article. Must be set if place is 'after'
 	 *
 	 */
	function _get_ordering($place, $id_page, $id_ref = NULL)
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
		
		// Fill in potential empty URLs		
		foreach($urls as $lang => $url)
			if ($url == '')	$urls[$lang] = $urls[Settings::get_lang('default')];
		
		return $urls;
	}
	
	
}


/* End of file article.php */
/* Location: ./application/controllers/admin/article.php */