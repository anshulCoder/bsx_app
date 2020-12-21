<?php namespace App\Controllers;

class User extends BaseController
{
	protected $session, $validation;

	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->validation =  \Config\Services::validation();
	}

	public function login()
	{
		if (!empty($this->session->get('user_id')))
		{
			return redirect()->to('/user');
		}
		echo view('common/commoncss');
		echo view('common/commonjs');
		$data = array();
		$data['header'] = view('common/Header');
		echo view('User/login', $data);
	}
	public function index()
	{
		$data = array();
		$mediaBetModel = model('App\Models\MediaBetModel', false);
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$data['bets'] = $mediaBetModel->get_media_bets();
		$data['bet_battles'] = $betBattleModel->where('player1_id', $this->session->get('user_id'))
								->findAll();
		$data['header'] = view('common/Header');
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('User/index', $data);
	}

	public function add_accuracy_bet()
	{
		// fetch medias and write code to change date picker according to release date.
		$data = array();

		//fetching all medias available
		$mediaModel = model('App\Models\MediaModel', false);
		$data['medias'] = $mediaModel->get_medias_for_bet();
		$data['header'] = view('common/Header');
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('User/add_new_bet', $data);
	}

	public function save_accuracy_bet()
	{
		$media_id = $this->request->getVar('media_name');
		$bet_amount = $this->request->getVar('bet_amount');
		$predicted_amount = $this->request->getVar('predicted_amount');
		$bet_date = $this->request->getVar('bet_date');
		$user_id = $this->request->getVar('user_id');

		if(!empty($media_id) && !empty($bet_amount) && !empty($predicted_amount) && !empty($bet_date))
		{
			$media_bet_data = array(
				'media_id' => $media_id,
				'user_id' => $user_id,
				'betting_amount' => $bet_amount,
				'predicted_amount' => $predicted_amount,
				'betting_date' => $bet_date
			);
			$mediaBetModel = model('App\Models\MediaBetModel', false);
			$mediaBetModel->insert($media_bet_data);
			return redirect()->to('/user');
		}
		else
		{
			return redirect()->back();
		}
	}

	public function calc_prediction_accuracy()
	{
		$bet_id = $this->request->getVar('bet_id');
		$media_amount = $this->request->getVar('media_earning');
		$data = array();

		if(!empty($bet_id) && !empty($media_amount))
		{
			$mediaBetModel = model('App\Models\MediaBetModel', false);
			$bet_data = $mediaBetModel->find($bet_id);
			$accuracy = round(((double)$bet_data['predicted_amount'] / (double)$media_amount) * 100);
			$data['status'] = true;
			$data['accuracy'] = $accuracy;
		}
		else
		{
			$data['status'] = false;
			$data['error'] = 'Invalid data provided!';
		}
		return json_encode($data);
	}

	public function check_user_login()
	{
		$data = array();

		$username = $this->request->getVar('username');
		$pass = $this->request->getVar('password');

		if(!empty($username) && !empty($pass))
		{
			$userModel = model('App\Models\UserModel', false);
			$user = $userModel->where('username', $username)
							  ->where('password', $pass)
							  ->first();

			if(empty($user['id']))
			{
				$this->session->setFlashData('error', 'Invalid username or password!');
				return redirect()->to('/user/login');
			}
			else
			{
				$sess_data = array(
					'user_id' => $user['id'],
					'user_name' => $user['username'],
					'user_email' => $user['email']
				);
				$this->session->set($sess_data);
				return redirect()->to('/user');
			}
		}
		else
		{
			$this->session->setFlashData('error', 'Empty username or password!');
			return redirect()->to('/user/login');
		}
	}

	public function logout()
	{
		$this->session->destroy();
		return redirect()->to('/user');
	}

	public function add_battle_bet()
	{
		helper(['form', 'url']);
		$data = array();

		//fetching all medias available
		$mediaModel = model('App\Models\MediaModel', false);
		$data['medias'] = $mediaModel->get_medias_for_bet();
		$userModel = model('App\Models\UserModel', false);
		$data['users'] = $userModel->where('id !=', $this->session->get('user_id'))->findAll();
		$data['header'] = view('common/Header');
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('User/add_new_battle', $data);
	}

	public function save_battle_bet()
	{
		helper(['form', 'url']);

		$this->validation->withRequest($this->request)
           ->run();
		if (! $this->validate([]))
        {
            echo view('Signup', [
                'validation' => $this->validator
            ]);
        }
        else
        {
            echo view('Success');
        }
	}
}