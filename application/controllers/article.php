<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Article extends MY_Controller {


	private $data = array();	// Included view data array;


	public function __construct()
	{
		parent::__construct();
	}


	function index()
	{
		$this->load->database();

		$data['articles'] = $this->db->select()->from('article')->get();

		// Included view loading
		$this->template['content'] = $this->load->view('article/article-list', $data, true);
		
		
		// Template view
		Theme::output('template', $this->template);

	}
}

/* End of file article.php */
/* Location: ./application/controllers/article.php */