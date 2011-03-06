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
		$this->load->model('structure_model', '', true);
		$this->load->model('category_model', '', true);
		$this->load->model('article_type_model', '', true);
		$this->load->model('tag_model', '', true);
		if (Settings::get('use_extend_fields') == '1')
		{
			$this->load->model('extend_field_model', '', true);
		}
		
		$this->load->library('structure');
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
		$articles = $this->article_model->get_list(false, 'title ASC');

		// Get all lang info for all articles
		$articles_lang = $this->article_model->get_lang();
		
		// Get all contexts : links between pages and articles
		$page_article = $this->article_model->get_all_context();
		
		// Get pages
		$pages = $this->page_model->get_lang_list(false, Settings::get_lang('default'));

		
		// Add page data to each context
		foreach($page_article as &$pa)
		{
			 $page = array_values(array_filter($pages, create_function('$row','return $row["id_page"] == "'. $pa['id_page'] .'";')));
			 $pa['page'] = (!empty($page) ? $page[0] : array() );
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

		// Categories list
		$this->template['categories'] = $this->category_model->get_list($where = FALSE, $orderby='ordering ASC');

		// Types list
		$this->template['types'] = $this->article_type_model->get_list($where = FALSE, $orderby='ordering ASC');

		$this->output('articles');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Create one article
	 * @TODO	Developp the "existing tags" functionality
	 
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
		$parent = '';

		foreach($datas as $page)
		{
			if ($id_page == $page['id_page']) $parent = $page['title'];
		}
		$this->template['parent'] = $parent;
	
	
		// Dropdown articles views
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
		$this->template['extend_fields'] = array();
		if (Settings::get('use_extend_fields') == '1')
		{
			$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('article');
		}
		
		// Context data initialized when article creation
		$this->template['online'] = '0';

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

/* Article in the context of one page : NOt for the moment.

			$rel = $this->input->post('rel');
			
			// IDs
			$rel = explode(".", $rel);
			$id_article = ( !empty($rel[1] )) ? $rel[1] : '';
			$this->data['id_page'] = ( !empty($rel[1] )) ? $rel[0] : '0';
*/			
			$id_article = $this->input->post('id_article');
			
			// Check the articles URL (all Urls)
			// 1. Try to get the page with one of the form provided URL
			$urls = array_values($this->_get_urls());

			// 2. Get the list of all articles having the same URLs
			$articles = $this->article_model->get_from_urls($urls, $exclude = $id_article);

			// If no article ID (means new one) and this article URL already exists in DB : No save 
			if ( !empty($articles) )
			{
				$this->error(lang('ionize_message_article_url_exists'));
			}
			// else, save...
			else
			{
				// Prepare data before saving
				$this->_prepare_data();

				// Saves article to DB
				$this->id = $this->article_model->save($this->data, $this->lang_data);

				// Link to page
				if ( ! empty($this->data['id_page']))
				{
					$this->data['online'] = $this->input->post('online');
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
				if (Settings::get('use_extend_fields') == '1')
					$this->extend_field_model->save_data('article', $this->id, $_POST);

				
				/* 
				 * JSON Answer
				 *
				 * Updates the structure tree
				 * The data var is merged to the default lang data_lang var,
				 * in order to send the lang values to the browser without making another SQL request
				 */
				
				$this->data = array_merge($this->lang_data[Settings::get_lang('default')], $this->data);
				$this->data['title'] = htmlspecialchars_decode($this->data['title'], ENT_QUOTES);
				$this->data['id_article'] = $this->id;
				$this->data['element'] = 'article';
				
				// Insert Case
				if ( empty($id_article) )
				{
					$menu = $this->menu_model->get_from_page($this->data['id_page']);
					$this->data['menu'] = $menu;
									
					// Insert article to tree if menu is found (for id_page = 0, no one is found)
					if (!empty($menu))
					{
						$this->callback = array(
							'fn' => $menu['name'].'Tree.insertTreeArticle',
							'args' => $this->data
						);
					}
				}
				// Update case
				else
				{
					$this->callback = array(
						array(
							'fn' => 'ION.updateTreeArticles',
							'args' => $this->data
						),
						array(
							'fn' => 'ION.updateLinkInfo',
							'args' => array(
								'type' => $this->data['link_type'],
								'id' => $this->data['link_id'],
								'text' => $this->data['link']
							)
						)
					);
				}
				
				// Updates the main panel
				$this->update[] = array(
					'element' => 'mainPanel',
					'url' => admin_url() . 'article/edit/'.$this->data['id_page'].'.'.$this->id,
					'title' => lang('ionize_title_edit_article')
				);
					

				$this->success(lang('ionize_message_article_saved'));
			}
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
				if (Settings::get('use_extend_fields') == '1')
				{
					$this->template['extend_fields'] = $this->extend_field_model->get_element_extend_fields('article', $id_article);
				}
	
				// Lang data
				$this->article_model->feed_lang_template($id_article, $this->template);

				$this->output('article');
			}		
		}
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
		if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
			require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');
		
		$datas = isset($views['article']) ? $views['article'] : array() ;
		if(count($datas) > 0)
		{
			$datas = array('0' => lang('ionize_select_default_view')) + $datas; 
			$this->template['article_views'] = form_dropdown('view', $datas, $article['view'], 'class="select"');
		}
		
		// Article ordering : needs All other articles from this page
		$this->template['articles'] = $this->article_model->get_list(array('id_page'=>$id_page), 'article.ordering ASC');

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
			$this->success(lang('ionize_message_article_context_saved'));
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

		if ((!empty($id_page)) && (!empty($id_article)))
		{
			// Get the original context
			$original_context = $this->article_model->get_context($id_article, $id_page_origin);
			
			// Clean of online value
			$original_context['online'] = '0';
			
			// Ordering : last position
			$original_context['ordering'] = $this->_get_ordering('last', $id_page);


			if ($this->article_model->link_to_page($id_page, $id_article, $original_context) === TRUE)
			{
				// Get the page, menu and articles details for the JSON answer
				$page = $this->page_model->get($id_page, Settings::get_lang('default'));
				$page['id_article'] = $id_article;

				$menu = $this->menu_model->get_from_page($id_page);
				
				// Articles
				$articles = $this->article_model->get_lang_list(array('id_article'=>$id_article, 'id_page'=>$id_page), Settings::get_lang('default'));

				// Lang data
				$this->article_model->add_lang_data($articles);

				// Set the article
				$article = array();
				if ( ! empty($articles))
				{
					$article = $articles[0];
					$article['title'] = htmlspecialchars_decode($article['title'], ENT_QUOTES);
				}




				// Add view logical name to article list (from theme/config/views.php file)
				if (is_file(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php'))
					require_once(APPPATH.'../themes/'.Settings::get('theme').'/config/views.php');
				else
					$views = false;
				

				// Views dropdown
				$datas = isset($views['article']) ? $views['article'] : array() ;
//				if(count($datas) > 0)
//				{
					$datas = array('' => lang('ionize_select_default_view')) + $datas; 
					$article['views'] = form_dropdown('view', $datas, $article['view'], 'id="view'.$flat_rel.'" class="select w120" style="padding:0;" rel="'.$rel.'"');
//				}

				// Types dropdown
				$datas = $this->article_type_model->get_types_select();
//				if(count($datas) > 0)
//				{
					$datas = array('' => lang('ionize_select_no_type')) + $datas; 
					$article['types'] = form_dropdown('type', $datas, $article['id_type'], 'id="type'.$flat_rel.'" class="select w120" style="padding:0;" rel="'.$rel.'"');
//				}


				$this->callback = array
				(
					// Add the page to the Article parents list
					array(
						'fn' => 'ION.addPageToArticleParentListDOM',
						'args' => $page
					),
					// Insert the article to the parent in the structure tree
					array(
						'fn' => $menu['name'].'Tree.insertTreeArticle',
						'args' => $article
					),
					// Add the article to the Page articles list
					array(
						'fn' => 'ION.addArticleToPageArticleListDOM',
						'args' => $article
					),
					// Clean the orphan article list (Dashboard) : TODO
					array(
						'fn' => 'ION.removeArticleFromOrphan',
						'args' => $article
					)
					
				);

				$this->success(lang('ionize_message_article_linked_to_page'));
			}
			else
			{
				$this->error(lang('ionize_message_article_already_linked_to_page'));
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

				
		
//		if ( ! empty($source))
//		{
			$this->template = array_merge($this->template, $source_article);

			$this->template['name'] = $source_article['name'];
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
//		}
//		else
//		{
//			$this->error(lang('ionize_message_element_not_found'));
//		}		
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

				// Panels Update array
				$this->update[] = array(
					'element' => 'mainPanel',
					'url' => admin_url() . 'article/edit/'.$this->data['id_page'].'.'.$id_new_article,
					'title' => lang('ionize_title_edit_article')
				);
				
				$this->callback = array(
					'fn' => $menu['name'].'Tree.insertTreeArticle',
					'args' => $this->data
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
		$status = $this->article_model->switch_online($id_page, $id_article);

		// Additional JSON data
		$data = array(
			'status' => $status,
			'id_article' => $id_article,
			'rel' => $id_page . '.' . $id_article
		);
		
		$this->success(lang('ionize_message_operation_ok'), $data);
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves article ordering
	 * 
	 */
	function save_ordering($parent, $id_parent)
	{
		if( $order = $this->input->post('order') )
		{
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
	 */
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
			$this->id = $id;
			$addon_data = array('element' => 'article');
		
			$this->success(lang('ionize_message_operation_ok'), $addon_data);
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

		$default_lang_url = $urls[Settings::get_lang('default')];
		
		foreach($urls as $lang => $url)
			if ($url == '')	$urls[$lang] = $default_lang_url;
		
		// Update the page name (not used anymore in the frontend, but used in the backend)
		$this->data['name'] = $default_lang_url;


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
					// Avoid or not security XSS filter
					if ( ! in_array($field, $this->no_xss_filter))
						$content = $this->input->post($field.'_'.$language['lang']);
					else
					{
						$content = stripslashes($_REQUEST[$field.'_'.$language['lang']]);
					}

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
		// Clean languages link
		$this->data['link'] = '';
		
		foreach(Settings::get_languages() as $language)
		{
			$this->lang_data[$language['lang']]['link'] = '';
		}

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
				if (isset($this->{$this->data['link_type'].'_model'}))
				{
					$elements = $this->{$this->data['link_type'].'_model'}->get_lang($this->data['link_id']);
		
					foreach ($elements as $element)
					{
						$this->lang_data[$element['lang']]['link'] = $element['url'];
					}
				}
			}
		}
		/*
		// Clean languages link
		else
		{
			$this->data['link'] = '';
			
			foreach(Settings::get_languages() as $language)
			{
				$this->lang_data[$language['lang']]['link'] = '';
			}
		}
		*/
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
		
		return $urls;
	}
}


/* End of file article.php */
/* Location: ./application/controllers/admin/article.php */