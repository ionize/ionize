<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Demo extends My_Module
{
	public function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->template['title'] = 'Demo module title';
		$this->output('demo');

		// Tags
		// Uncomment the previous lines
		// PHP $this->template['title'] = 'Demo module title' will not be supported

		// $this->render('demo_tags');

	}

	public function test1()
	{
		echo 'coucou';
	}

}
