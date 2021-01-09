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
		$additionalBetsModel = model('App\Models\AdditionalBetsModel', false);
		$sequelBetModel = model('App\Models\SequelBetsModel', false);
		$user_id = $this->session->get('user_id');
		$data['bets'] = $mediaBetModel->get_media_bets($user_id);
		$battles = $betBattleModel->get_battles_by_user($user_id);

		$public_battles = array();
		$private_battles = array();
		foreach($battles as $key => $row) {
			if ($row['battle_mode'] == 'private') {
				$private_battles[] = $row;
			} else {
				$public_battles[] = $row;
			}
		}
		$data['public_battles'] = $public_battles;
		$data['private_battles'] = $private_battles;
		$data['participated_battles'] = $additionalBetsModel->fetch_participated_battles($user_id);
		$data['requested_battles'] = $betBattleModel->get_requested_battles($user_id);
		$data['open_public_battles'] = $betBattleModel->get_public_battles($user_id);
		$data['sequel_bets'] = $sequelBetModel->get_sequel_bets($user_id);
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
			if ($accuracy>100) $accuracy = 100 - $accuracy;
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
							  ->where('password', md5($pass))
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
					'user_email' => $user['email'],
					'user_wallet_balance' => $user['wallet_balance']
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

		$input = $this->validate([
			'player2_id' => 'required',
			'battle_description' => 'required',
			'battle_amount' => 'required|numeric',
			'battle_mode' => 'required',
			'media_selected_id' => 'required',
			'battle_end_date' => 'required'
		]);
  
		if (!$input)
        {
            return redirect()->to('/user/new-battle-bet');
        }
        else
        {
        	$betBattleModel = model('App\Models\BetBattleModel', false);
        	$battle_details = array(
        		'player1_id' => $this->session->get('user_id'),
        		'player2_id' => $this->request->getVar('player2_id'),
        		'player1_battle_description' => $this->request->getVar('battle_description'),
				'battle_amount' => $this->request->getVar('battle_amount'),
				'battle_mode' => $this->request->getVar('battle_mode'),
				'additional_bet_amount' => $this->request->getVar('additional_bet_amount') ?: 0,
				'media_selected_id' => $this->request->getVar('media_selected_id'),
				'battle_end_date' => $this->request->getVar('battle_end_date'),
				'battle_status' => 0
        	);
        	$betBattleModel->insert($battle_details);
            return redirect()->to('/user');
        }
	}

	public function accept_battle($battle_id)
	{
		if(empty($battle_id))
		{
			return redirect()->to('/user');
		}

		$betBattleModel = model('App\Models\BetBattleModel', false);
		$data = array(
			'battle_status' => BATTLE_LIVE
		);
		$betBattleModel->update($battle_id, $data);
		return redirect()->to('/user');
	}

	public function deny_battle($battle_id)
	{
		if(empty($battle_id))
		{
			return redirect()->to('/user');
		}

		$betBattleModel = model('App\Models\BetBattleModel', false);
		$data = array(
			'battle_status' => BATTLE_REJECTED
		);
		$betBattleModel->update($battle_id, $data);
		return redirect()->to('/user');
	}

	public function player2_battle_approval()
	{
		$battle_id = $this->request->getVar('bet_battle_id');
		$battle_description = $this->request->getVar('battle_description');

		if (!empty($battle_id) && !empty($battle_description))
		{
			$betBattleModel = model('App\Models\BetBattleModel', false);
			$data = array(
				'battle_status' => BATTLE_PENDING_PLAYER1,
				'player2_battle_description' => $battle_description
			);
			$betBattleModel->update($battle_id, $data);
			return redirect()->to('/user');
		}
		else
		{
			return redirect()->to('/user');
		}
	}

	public function claim_battle()
	{
		$battle_id = $this->request->getVar('battle_id');
		$data = array();

		if(empty($battle_id))
		{
			$data['error'] = 'Battle ID is required!';
			$data['status'] = false;
		}
		else
		{
			$betBattleModel = model('App\Models\BetBattleModel', false);
			$userModel = model('App\Models\UserModel', false);
			$battleResultsModel = model('App\Models\BattleResultsModel', false);
			
			$battle_info = $betBattleModel->find($battle_id);
			if(empty($battle_info))
			{
				$data['error'] = 'Battle Data not found!';
				$data['status'] = false;
			}
			else
			{
				$this->settle_claim($battle_id, $battle_info);
				$data['status'] = true;
			}
		}

		echo json_encode($data);
	}

	function settle_claim($battle_id, $battle_info)
	{
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$userModel = model('App\Models\UserModel', false);
		$battleResultsModel = model('App\Models\BattleResultsModel', false);
		$additionalBetsModel = model('App\Models\AdditionalBetsModel', false);

		$claimed_user_id = $this->session->get('user_id');
		$current_wallet_balance = $this->session->get('user_wallet_balance');

		$additional_bets = array();
		$winner_additional_amount = 0;
		if($battle_info['battle_mode'] == 'public')
		{
			$additional_bets = $additionalBetsModel->where('bet_battle_id', $battle_id)->findAll();
		}

		//winner update

		if(count($additional_bets)>0)
		{
			foreach($additional_bets as $addKey => $addRow)
			{
				if($addRow['rooting_for_user'] == $claimed_user_id)
				{
					$winner_additional_amount += (double)($addRow['bet_amount']/2);
					$userModel->update_wallet_balance($addRow['user_id'], '+'.($addRow['bet_amount']/2));
				}
			}
		}

		$updated_winner_balance = (double)$battle_info['battle_amount'] + $winner_additional_amount;
		$userModel->update_wallet_balance($claimed_user_id, '+'.$updated_winner_balance);
		$this->session->set('user_wallet_balance', $current_wallet_balance + $updated_winner_balance);

		//loser update
		$lost_user_id = ($battle_info['player1_id'] == $claimed_user_id ? $battle_info['player2_id'] : $battle_info['player1_id']);
		$userModel->update_wallet_balance($lost_user_id, '-'.(double)$battle_info['battle_amount']);

		//saving result
		$battle_result = array(
			'battle_id' => $battle_id,
			'winner_player_id' => $claimed_user_id,
			'loser_player_id' => $lost_user_id,
			'win_amount' => $updated_winner_balance
		);
		$battleResultsModel->insert($battle_result);

		$battle_update_data = array(
			'battle_status' => BATTLE_FINISHED
		);
		$betBattleModel->update($battle_id, $battle_update_data);
	}

	public function participate_public_battle($battle_id)
	{
		$data = array();
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$data['header'] = view('common/Header');
		$battle_info = $betBattleModel->fetch_battle_for_additional($battle_id);
		$data['battle_info'] = $battle_info[0];
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('User/add_new_additional', $data);
	}

	public function save_additional_bet()
	{
		$rooting_for_user = $this->request->getVar('rooting_for_user');

		if(empty($rooting_for_user))
		{
			return redirect()->back();
		}
		else
		{
			$additionalBetsModel = model('App\Models\AdditionalBetsModel', false);
			$userModel = model('App\Models\UserModel', false);
			$add_data = array(
				'bet_battle_id' => $this->request->getVar('bet_battle_id'),
				'user_id' => $this->request->getVar('user_id'),
				'bet_amount' => $this->request->getVar('bet_amount'),
				'rooting_for_user' => $rooting_for_user
			);
			$additionalBetsModel->insert($add_data);
			$userModel->update_wallet_balance($this->request->getVar('user_id'), '-'.(double)$this->request->getVar('bet_amount'));
			$current_wallet_session = $this->session->get('user_wallet_balance');
			$this->session->set('user_wallet_balance', (double)$current_wallet_session - (double)$this->request->getVar('bet_amount'));
			return redirect()->to('/user');
		}
	}

	public function add_sequel_bet()
	{
		$data = array();

		//fetching all medias available
		$mediaModel = model('App\Models\MediaModel', false);
		$data['medias'] = $mediaModel->where('active', 1)->findAll();
		$data['header'] = view('common/Header');
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('User/add_new_sequel_bet', $data);
	}
}