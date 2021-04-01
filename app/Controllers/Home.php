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
		$mediaModel = model('App\Models\MediaModel', false);
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$data['header'] = view('common/HeaderNew');
		$data['public_battles'] = $betBattleModel->get_public_battles();
		$data['medias'] = $mediaModel->where('active', 1)->findAll(20, 1);
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('Home/index', $data);
	}

	public function fetch_top_ranks()
	{
		$data = array();
		$userModel = model('App\Models\UserModel', false);

		$data['ranks'] = $userModel->orderBy('points_total', 'DESC')->findAll(5);
		echo json_encode($data);
	}

}
