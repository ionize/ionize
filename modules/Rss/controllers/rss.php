<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* RSS controller
*
* The controller that handles actions 
* related to RSS module.
*
* @author	Ionize Dev Team
*/
class Rss extends Base_Controller 
{
	/**
	* Constructor
	*
	* @access	public
	* @return	void
	*/
	public function __construct()
	{
		parent::__construct();
		
		// Article Model : Needed by RSS model to extend Article_model
		$this->load->model('article_model');
		$this->load->model('rss_model');
		
		// Page Model : Used to get the main article's parent
		$this->load->model('page_model');
		
		$this->load->helper('xml');
		$this->load->helper('text');
	}

	/**
	* Index
	*
	* @access	public
	* @return	parsed view
	*/
	function index($lang = FALSE)
	{
		if ($lang == FALSE)
			$lang = Settings::get_lang('default');

		return $this->feed($lang);
	}
	
	/**
	* Show the feed
	*
	* @access	public
	* @return	parsed view
	*/
	function feed($lang = FALSE)
	{
		$id_pages = explode(',', config_item('module_rss_pages'));
		
		$articles = $this->rss_model->get_articles($id_pages, $lang);

		// Articles ID array
		$articles_id = array();
		foreach($articles as $article)
		{
			$articles_id[] = $article['id_article'];
		}
		
		// Pages context, regarding the articles ID
		$pages_context = $this->page_model->get_lang_contexts($articles_id, $lang);

		// Add pages contexts data to articles
		foreach($articles as &$article)
		{
			$contexts = array();
			foreach($pages_context as $context)
			{
				if ($context['id_article'] == $article['id_article'])
					$contexts[] = $context;
			}
			
			$page = array_shift($contexts);

			// Find the Main Parent
			if ( ! empty($contexts))
			{
				foreach($contexts as $context)
				{
					if ($context['main_parent'] == '1')
						$page = $context;
				}
			}
			
			// Article's URL
			$url = $article['url'];

			if ( count(Settings::get_online_languages()) > 1 )
			{
				$article['url'] = base_url() . $lang . '/' . $page['url'] . '/' . $url;
			}
			else
			{
				$article['url'] = base_url() . $page['url'] . '/' . $url;			
			}
		}
		
		unset($article);

		// Sort the articles on date
		$kdate = array();
		foreach($articles as $key => $article)
		{
			$kdate[$key] = strtotime($article['date']);
		}

		// Sort the results by realm occurences DESC first, by date DESC second.			
		array_multisort($kdate, SORT_DESC, $articles);
		

		// Output the feed
		$this->output->set_header("Content-Type: application/rss+xml; charset=UTF-8");		
		$this->load->view('rss', array
		(
			'charset'			=> 'utf-8',
			'language'		=> $lang,
			'articles'			=> $articles
		));
	}
}

/* End of file rss.php */
/* Location: /modules/Rss/controllers/rss.php */
