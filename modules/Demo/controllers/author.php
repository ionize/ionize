<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Author extends Base_Controller
{
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model('demo_author_model', 'author_model', TRUE);
	}

	public function index()
	{
		echo 'Author Controller > Index()';
	}

	public function get_list()
	{
		$authors = $this->author_model->get_lang_list();

		$this->template['authors'] = $authors;

		$this->output('author_list');
	}
}

