<?php namespace App\Controllers;

class Home extends BaseController
{
	public function __construct()
	{
		$this->session = \Config\Services::session();
	}
	public function index()
	{
		$data = array();
		$data['header'] = view('common/Header');
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('Home/index', $data);
	}

	//--------------------------------------------------------------------

}
