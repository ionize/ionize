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
	}
}
