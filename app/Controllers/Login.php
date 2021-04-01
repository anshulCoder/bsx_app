<?php namespace App\Controllers;

class Login extends BaseController
{
	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->validation =  \Config\Services::validation();
	}

	public function index()
	{
		if (!empty($this->session->get('user_id')))
		{
			return redirect()->to('/');
		}
		$data = array();
		$data['header'] = view('common/HeaderNew');
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('Login/index', $data);
	}

	public function check_login()
	{
		$data = array();

		$username = $this->request->getVar('username');
		$pass = $this->request->getVar('password');

		if(!empty($username) && !empty($pass))
		{
			$userModel = model('App\Models\UserModel', false);
			$user = $userModel->where('username', $username)
							  ->where('password', md5($pass))
							  ->first();

			if(empty($user['id']))
			{
				$this->session->setFlashData('error', 'Invalid username or password!');
				return redirect()->to('/login');
			}
			else
			{
				$sess_data = array(
					'user_id' => $user['id'],
					'user_name' => $user['username'],
					'user_email' => $user['email'],
					'user_wallet_balance' => $user['wallet_balance'],
					'user_total_points' => $user['points_total']
				);
				$this->session->set($sess_data);
				return redirect()->to('/');
			}
		}
		else
		{
			$this->session->setFlashData('error', 'Empty username or password!');
			return redirect()->to('/login');
		}
	}

	public function logout()
	{
		$this->session->destroy();
		return redirect()->to('/');
	}
}
