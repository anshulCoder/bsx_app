<?php namespace App\Controllers;

class User extends BaseController
{
	protected $session, $validation;

	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->validation =  \Config\Services::validation();
	}

	public function index()
	{
		$data = array();
		$mediaBetModel = model('App\Models\MediaBetModel', false);
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$additionalBetsModel = model('App\Models\AdditionalBetsModel', false);
		$sequelBetModel = model('App\Models\SequelBetsModel', false);
		$fixedExchangeBuyerRequestsModel = model('App\Models\FixedExchangeBuyerRequestsModel', false);
		$betAuctionBidsModel = model('App\Models\BetAuctionBidsModel', false);
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
		$data['open_public_battles'] = []; // $betBattleModel->get_public_battles($user_id);
		$data['sequel_bets'] = $sequelBetModel->get_sequel_bets($user_id);

		$data['exchange_bets'] = $this->fetch_exchange_bet_slips($user_id);
		$data['slips_for_exchange'] = $this->fetch_exchange_bet_slips(null);
		$data['existing_offers'] = $fixedExchangeBuyerRequestsModel->where('user_id', $user_id)
									->where('status', 0)->findAll();
		$data['offers_sent'] = $fixedExchangeBuyerRequestsModel->offers_sent($user_id);
		$data['bids_sent'] = $betAuctionBidsModel->bids_sent($user_id);

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
			$userModel = model('App\Models\UserModel', false);
			$user_data = $userModel->where('id', $user_id)->first();
			$updated_wallet_balance = $user_data['wallet_balance'] - $bet_amount;
			if($updated_wallet_balance < 0)
			{
				$this->session->setFlashdata('wallet_error', 'Insufficient balance to place bet!');
				return redirect()->to('/user/new-accuracy-bet');
			}

			$media_bet_data = array(
				'media_id' => $media_id,
				'user_id' => $user_id,
				'betting_amount' => $bet_amount,
				'predicted_amount' => $predicted_amount,
				'betting_date' => $bet_date
			);

			$mediaBetModel = model('App\Models\MediaBetModel', false);
			$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);
			$mediaBetModel->insert($media_bet_data);

			$userModel->decrement_column_value('wallet_balance', $bet_amount, $user_id);
			$this->session->set('user_wallet_balance', $updated_wallet_balance);
			$walletLogMasterModel->insert(array(
				'log_title' => 'Bet Accuracy slip purchased',
				'log_description' => 'Accuracy bet purchased for '.$bet_amount.' bet amount',
				'amount' => $bet_amount,
				'amount_action' => 1,
				'user_id' => $user_id
			));
			return redirect()->to('/user');
		}
		else
		{
			return redirect()->to('/user/new-accuracy-bet');
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
					'user_wallet_balance' => $user['wallet_balance'],
					'user_total_points' => $user['points_total']
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
			'prediction_type' => 'required',
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
        	$user_id = $this->session->get('user_id');
        	$userModel = model('App\Models\UserModel', false);
        	$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);
			$user_data = $userModel->where('id', $user_id)->first();
			$updated_wallet_balance = $user_data['wallet_balance'] - $this->request->getVar('battle_amount');
			if($updated_wallet_balance < 0)
			{
				$this->session->setFlashdata('wallet_error', 'Insufficient balance to place bet!');
				return redirect()->to('/user/new-battle-bet');
			}

        	$betBattleModel = model('App\Models\BetBattleModel', false);
        	$battle_details = array(
        		'player1_id' => $user_id,
        		'player2_id' => $this->request->getVar('player2_id'),
        		'battle_description' => $this->request->getVar('battle_description'),
        		'player_for' => ($this->request->getVar('prediction_type') == '1' ? 1 : 2),
        		'player_against' => ($this->request->getVar('prediction_type') == '2' ? 1 : 2),
				'battle_amount' => $this->request->getVar('battle_amount'),
				'battle_mode' => $this->request->getVar('battle_mode'),
				'additional_bet_amount' => $this->request->getVar('additional_bet_amount') ?: 0,
				'media_selected_id' => $this->request->getVar('media_selected_id'),
				'battle_end_date' => $this->request->getVar('battle_end_date'),
				'battle_status' => 0
        	);
        	$betBattleModel->insert($battle_details);

        	$userModel->decrement_column_value('wallet_balance', $this->request->getVar('battle_amount'), $user_id);
			$this->session->set('user_wallet_balance', $updated_wallet_balance);
			$walletLogMasterModel->insert(array(
				'log_title' => 'Bet Battle slip purchased',
				'log_description' => 'Battle bet purchased for '.$this->request->getVar('battle_amount').' bet amount',
				'amount' => $this->request->getVar('battle_amount'),
				'amount_action' => 1,
				'user_id' => $user_id
			));
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
		$battle_data = $betBattleModel->where('battle_id', $battle_id)->first();

		$updated_wallet_balance = $this->session->get('user_wallet_balance') - $battle_data['battle_amount'];
		if($updated_wallet_balance < 0)
		{
			$this->session->setFlashdata('wallet_error', 'Insufficient balance to place bet!');
			return redirect()->to('/user');
		}

		$betBattleModel = model('App\Models\BetBattleModel', false);
		$userModel = model('App\Models\UserModel', false);
		$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);
		$data = array(
			'battle_status' => BATTLE_LIVE
		);
		$betBattleModel->update($battle_id, $data);
		$userModel->decrement_column_value('wallet_balance', $battle_data['battle_amount'], $this->session->get('user_id'));
		$this->session->set('user_wallet_balance', $updated_wallet_balance);
		$walletLogMasterModel->insert(array(
			'log_title' => 'Bet Battle Accepted',
			'log_description' => 'Battle bet(# '.$battle_id.') Accepted for '.$battle_data['battle_amount'].' bet amount',
			'amount' => $battle_data['battle_amount'],
			'amount_action' => 1,
			'user_id' => $this->session->get('user_id')
		));
		return redirect()->to('/user');
	}

	public function deny_battle($battle_id)
	{
		if(empty($battle_id))
		{
			return redirect()->to('/user');
		}
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$battle_data = $betBattleModel->where('battle_id', $battle_id)->first();

		$userModel = model('App\Models\UserModel', false);
		$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);
		$userModel->increment_column_value('wallet_balance', $battle_data['battle_amount'], $battle_data['player1_id']);
		$walletLogMasterModel->insert(array(
			'log_title' => 'Bet Battle Rejected',
			'log_description' => 'Battle bet(# '.$battle_id.') Rejected by opponent '.$battle_data['battle_amount'].' bet amount reversed',
			'amount' => $battle_data['battle_amount'],
			'amount_action' => 2,
			'user_id' => $battle_data['player1_id']
		));
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$data = array(
			'battle_status' => BATTLE_REJECTED
		);
		$betBattleModel->update($battle_id, $data);
		return redirect()->to('/user');
	}

	// not using
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
		$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);

		$claimed_user_id = $this->session->get('user_id');
		$winner_user_data = $userModel->where('id', $claimed_user_id)->first();

		$additional_bets = array();
		$winner_additional_amount = 0;
		if($battle_info['battle_mode'] == 'public')
		{
			$additional_bets = $additionalBetsModel->where('bet_battle_id', $battle_id)->findAll();
		}

		//winner update

		if(count($additional_bets)>0)
		{
			$additional_earnings = array_sum(array_column($additional_bets, 'bet_amount'));
			$additional_winner_count = count($additionalBetsModel->where('bet_battle_id', $battle_id)->where('rooting_for_user', $claimed_user_id)->findAll());
			$additional_winner_percentage = round(($additional_winner_count/count($additional_bets))*100, 2);
			if($additional_winner_percentage <= 90)
			{
				$winner_additional_amount += $additional_earnings * 0.1;
			}
			$additional_users_winning = round(($additional_earnings - $winner_additional_amount) / $additional_winner_count, 2);
			foreach($additional_bets as $addKey => $addRow)
			{
				if($addRow['rooting_for_user'] == $claimed_user_id)
				{
					// winner update
					$userModel->update_wallet_balance($addRow['user_id'], '+'.$additional_users_winning);
					$walletLogMasterModel->insert(array(
						'log_title' => 'Bet Battle won',
						'log_description' => 'Additional Bet on battle won, you earned '.$additional_users_winning.' wallet balance',
						'amount' => $additional_users_winning,
						'amount_action' => 2,
						'user_id' => $addRow['user_id']
					));
				}
			}
		}

		$balance_won = (double)$battle_info['battle_amount'] + $winner_additional_amount;
		$new_wallet_balance = $winner_user_data['wallet_balance'] + $balance_won;

		// points calculation for winner
		$all_bets_by_winner = $battleResultsModel->where('winner_player_id', $claimed_user_id)->orWhere('loser_player_id',$claimed_user_id)->findAll();
		$points_bb = 0;
		$k = 30;
		$total_bets_won = array_filter($all_bets_by_winner, function($k, $v) {
			return ($k == 'winner_player_id' && $v == $claimed_user_id);
		}, ARRAY_FILTER_USE_BOTH);
		$we = 0;
		$w = 1;
		if(count($all_bets_by_winner)>0)
		{
			$we = round(count($total_bets_won)/count($all_bets_by_winner), 2);
		}
		$points_bb = $k * (round($w - $we, 2));
		$new_total_points = $winner_user_data['points_total'] + $points_bb;
		$new_points_bb = $winner_user_data['points_bet_battle'] + $points_bb;

		$userModel->update($claimed_user_id, array(
			'wallet_balance' => $new_wallet_balance,
			'points_bet_battle' => $new_points_bb,
			'points_total' => $new_total_points
		));
		$this->session->set('user_wallet_balance', $new_wallet_balance);
		$this->session->set('user_total_points', $new_total_points);

		$walletLogMasterModel->insert(array(
			'log_title' => 'Bet Battle won',
			'log_description' => 'Bet battle(# '.$battle_id.') won, you earned '.$balance_won.' wallet balance and '.$points_bb.' Points',
			'amount' => $balance_won,
			'amount_action' => 2,
			'points_for_bet_battle' => $points_bb,
			'points_for_total' => $new_total_points,
			'user_id' => $claimed_user_id
		));

		//loser update
		$lost_user_id = ($battle_info['player1_id'] == $claimed_user_id ? $battle_info['player2_id'] : $battle_info['player1_id']);
		$loser_user_data = $userModel->where('id', $lost_user_id)->first();

		// points calculation for loser
		$all_bets_by_looser = $battleResultsModel->where('winner_player_id', $lost_user_id)->orWhere('loser_player_id',$lost_user_id)->findAll();
		$points_bb = 0;
		$k = 30;
		$total_bets_won = array_filter($all_bets_by_looser, function($k, $v) {
			return ($k == 'winner_player_id' && $v == $lost_user_id);
		}, ARRAY_FILTER_USE_BOTH);
		$we = 0;
		$w = 0;
		if(count($all_bets_by_looser)>0)
		{
			$we = round(count($total_bets_won)/count($all_bets_by_looser), 2);
		}
		$points_bb = $k * (round($w - $we, 2));
		$new_total_points = $loser_user_data['points_total'] + $points_bb;
		$new_points_bb = $loser_user_data['points_bet_battle'] + $points_bb;
		$userModel->update($lost_user_id, array(
			'points_bet_battle' => $new_points_bb,
			'points_total' => $new_total_points
		));

		$walletLogMasterModel->insert(array(
			'log_title' => 'Bet Battle Lost',
			'log_description' => 'Bet battle(# '.$battle_id.') lost, you get '.$points_bb.' Points',
			'amount' => $battle_info['battle_amount'],
			'amount_action' => 1,
			'points_for_bet_battle' => $points_bb,
			'points_for_total' => $new_total_points,
			'user_id' => $lost_user_id
		));

		//saving result
		$battle_result = array(
			'battle_id' => $battle_id,
			'winner_player_id' => $claimed_user_id,
			'loser_player_id' => $lost_user_id,
			'win_amount' => $balance_won
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

	public function save_additional_bet($reqType = 'redirect')
	{
		$rooting_for_user = $this->request->getVar('rooting_for_user');

		if(empty($rooting_for_user))
		{
			if($reqType == 'json')
			{
				return json_encode(array(
					'status' => false,
					'error' => 'Data is missing!'
				));
			}
			else
			{
				return redirect()->back();
			}
		}
		else
		{
			$additionalBetsModel = model('App\Models\AdditionalBetsModel', false);
			$userModel = model('App\Models\UserModel', false);
			$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);
			$user_id = $this->session->get('user_id');
			$user_data = $userModel->where('id', $user_id)->first();
			$bet_amount = $this->request->getVar('bet_amount');

			$updated_wallet_balance = $user_data['wallet_balance'] - (double)$bet_amount;

			// Check if already participated
			$alreadyExists = $additionalBetsModel
								->where('bet_battle_id', $this->request->getVar('bet_battle_id'))
								->where('user_id', $user_id)
								->where('rooting_for_user', $rooting_for_user)->first();

			if(!empty($alreadyExists['additional_id']))
			{
				if($reqType == 'json')
				{
					return json_encode(array(
						'status' => false,
						'error' => 'Already participated in battle!'
					));
				}
				else
				{
					$this->session->setFlashdata('participation_error', 'Already participated in battle!');
					return redirect()->to('/user/participate-public-battle/'.$this->request->getVar('bet_battle_id'));
				}
			}

			if($updated_wallet_balance < 0)
			{
				if($reqType == 'json')
				{
					return json_encode(array(
						'status' => false,
						'error' => 'Insufficient balance to place bet!'
					));
				}
				else
				{
					$this->session->setFlashdata('wallet_error', 'Insufficient balance to place bet!');
					return redirect()->to('/user/participate-public-battle/'.$this->request->getVar('bet_battle_id'));
				}
			}


			$add_data = array(
				'bet_battle_id' => $this->request->getVar('bet_battle_id'),
				'user_id' => $user_id,
				'bet_amount' => $bet_amount,
				'rooting_for_user' => $rooting_for_user
			);
			$additionalBetsModel->insert($add_data);
			$userModel->decrement_column_value('wallet_balance', (double)$bet_amount, $user_id);
			$current_wallet_session = $this->session->get('user_wallet_balance');
			$this->session->set('user_wallet_balance', $updated_wallet_balance);
			$walletLogMasterModel->insert(array(
				'log_title' => 'Additional Bet placed',
				'log_description' => 'Additional bet placed on Bet battle(# '.$this->request->getVar('bet_battle_id').') for amount '.$bet_amount,
				'amount' => $bet_amount,
				'amount_action' => 1,
				'user_id' => $user_id
			));
			if($reqType == 'json')
			{
				return json_encode(array(
					'status' => true
				));
			}
			else
			{
				return redirect()->to('/user');
			}
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

	public function fetch_casting($media_id)
	{
		$data = array();

		if(empty($media_id)) {
			$data['status'] = false;
			$data['error'] = 'Media Not found!';
		} else {
			$mediaModel = model('App\Models\MediaModel', false);
			$castingModel = model('App\Models\CastingModel', false);
			$data['media_data'] = $mediaModel->where('id', $media_id)->first();
			$casting_data = array();
			$casting_data['actors_list'] = $castingModel->where('cast_type', 1)->orderBy('cast_name','ASC')->findAll();
			$casting_data['actresses_list'] = $castingModel->where('cast_type', 2)->orderBy('cast_name','ASC')->findAll();
			$casting_data['directors_list'] = $castingModel->where('cast_type', 3)->orderBy('cast_name','ASC')->findAll();
			$data['casting_data'] = $casting_data;
			$data['status'] = true;
		}

		echo json_encode($data);
	}

	public function save_sequel_bet()
	{
		$user_id = $this->request->getVar('user_id');

		if(empty($user_id))
		{
			return redirect()->back();
		}
		else
		{
			$sequelBetsModel = model('App\Models\SequelBetsModel', false);
			$userModel = model('App\Models\UserModel', false);
			$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);
			$user_data = $userModel->where('id', $user_id)->first();
			$updated_wallet_balance = $user_data['wallet_balance'] - (double)$this->request->getVar('bet_amount');

			if($updated_wallet_balance < 0)
			{
				$this->session->setFlashdata('wallet_error', 'Insufficient balance to place bet!');
				return redirect()->to('/user/new-sequel-bet');
			}

			$actors = array_map(function($v) {return $v ?: 'NA';}, $this->request->getVar('actors'));
			$actresses = array_map(function($v) {return $v ?: 'NA';}, $this->request->getVar('actresses'));
			$directors = array_map(function($v) {return $v ?: 'NA';}, $this->request->getVar('directors'));

			$add_data = array(
				'media_id' => $this->request->getVar('media_name'),
				'user_id' => $user_id,
				'sequel_bet_amount' => $this->request->getVar('bet_amount'),
				'sequel_bet_day' => $this->request->getVar('bet_day_start').'-'.$this->request->getVar('bet_day_end'),
				'sequel_bet_month' => $this->request->getVar('bet_month'),
				'sequel_bet_year' => $this->request->getVar('bet_year'),
				'sequel_bet_actors' => json_encode($actors),
				'sequel_bet_actresses' => json_encode($actresses),
				'sequel_bet_directors' => json_encode($directors)
			);
			$sequelBetsModel->insert($add_data);
			$userModel->decrement_column_value('wallet_balance', (double)$this->request->getVar('bet_amount'), $user_id);
			$this->session->set('user_wallet_balance', $updated_wallet_balance);
			$walletLogMasterModel->insert(array(
				'log_title' => 'Sequel Bet created',
				'log_description' => 'Sequel bet created for bet amount '.$this->request->getVar('bet_amount'),
				'amount' => $this->request->getVar('bet_amount'),
				'amount_action' => 1,
				'user_id' => $user_id
			));
			return redirect()->to('/user');
		}
	}

	public function add_exchange_bet()
	{
		$data = array();
		$user_id = $this->session->get('user_id');
		//fetching all medias available
		$mediaBetModel = model('App\Models\MediaBetModel', false);
		$sequelBetModel = model('App\Models\SequelBetsModel', false);
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$exchangeBetsModel = model('App\Models\ExchangeBetsModel', false);

		$existing_exchanges = $exchangeBetsModel->where('user_id', $user_id)->findAll();
		$all_exchange_ids = array_column($existing_exchanges, 'slip_type', 'slip_id');
		$slip_ids = array(
			'bet_sequel' => array(),
			'bet_battle' => array(),
			'bet_accuracy' => array()
		);
		foreach($all_exchange_ids as $key => $row)
		{
			// $key = slip_id, $row = slip_type
			if($row == 'Bet Sequel') {
				$slip_ids['bet_sequel'][] = $key;
			}
			elseif ($row == 'Bet Battle') {
				$slip_ids['bet_battle'][] = $key;
			}
			else {
				$slip_ids['bet_accuracy'][] = $key;
			}
		}
		$media_bets = $mediaBetModel->user_slips_for_exchange($user_id, $slip_ids['bet_accuracy']);
		$sequel_bets = $sequelBetModel->user_slips_for_exchange($user_id, $slip_ids['bet_sequel']);
		$battle_bets = $betBattleModel->user_slips_for_exchange($user_id, $slip_ids['bet_battle']);

		$all_bets = $media_bets;
		if(count($sequel_bets)>0) {
			array_push($all_bets, ...$sequel_bets);
		}
		if(count($battle_bets)>0) {
			array_push($all_bets, ...$battle_bets);
		}

		$data['bet_slips'] = $all_bets;
		$data['header'] = view('common/Header');
		echo view('common/commoncss');
		echo view('common/commonjs');
		echo view('User/add_new_exchange', $data);
	}

	public function save_exchange_bet()
	{
		$user_id = $this->request->getVar('user_id');

		if(empty($user_id))
		{
			return redirect()->back();
		}
		else
		{
			$exchangeBetsModel = model('App\Models\ExchangeBetsModel', false);
			$userModel = model('App\Models\UserModel', false);

			$add_data = array(
				'user_id' => $this->request->getVar('user_id'),
				'slip_id' => $this->request->getVar('slip_id'),
				'slip_type' => $this->request->getVar('slip_type'),
				'exchange_type' => $this->request->getVar('exchange_type'),
				'fixed_selling_price' => $this->request->getVar('fixed_selling_price'),
				'exchange_status' => 0
			);
			$exchangeBetsModel->insert($add_data);
			return redirect()->to('/user');
		}
	}

	function fetch_exchange_bet_slips($user_id) {

		$exchangeBetsModel = model('App\Models\ExchangeBetsModel', false);
		$betBattleModel = model('App\Models\BetBattleModel', false);
		$mediaBetModel = model('App\Models\MediaBetModel', false);
		$sequelBetsModel = model('App\Models\SequelBetsModel', false);
		$all_exchanges = array();
		if(isset($user_id)) {
			$all_exchanges = $exchangeBetsModel
								->where('user_id', $user_id)
								->orderBy('created_datetime', 'DESC')->findAll();
		} else {
			$all_exchanges = $exchangeBetsModel->where('exchange_status', 0)
								->orderBy('created_datetime', 'DESC')->findAll();
		}

		if(count($all_exchanges) > 0) {
			$slip_ids = array(
				'bet_sequel' => array(),
				'bet_battle' => array(),
				'bet_accuracy' => array(),
				'bet_sequel_exchange_ids' => array()
			);
			foreach($all_exchanges as $key => $row)
			{
				// $key = slip_id, $row = slip_type
				if($row['slip_type'] == 'Bet Sequel') {
					$slip_ids['bet_sequel'][] = $row['slip_id'];
					$slip_ids['bet_sequel_exchange_ids'][] = $row['exchange_id'];
				}
				elseif ($row['slip_type'] == 'Bet Battle') {
					$slip_ids['bet_battle'][] = $row['slip_id'];
				}
				else {
					$slip_ids['bet_accuracy'][] = $row['slip_id'];
				}
			}
			$accuracy_slips = $mediaBetModel->bet_slips_by_ids($slip_ids['bet_accuracy']);
			$sequel_slips = $sequelBetsModel->bet_slips_by_ids($slip_ids['bet_sequel']);
			$battle_slips = $betBattleModel->bet_slips_by_ids($slip_ids['bet_battle']);
			$all_slips = $accuracy_slips;
			if(count($sequel_slips)>0) {
				array_push($all_slips, ...$sequel_slips);
			}
			if(count($battle_slips)>0) {
				array_push($all_slips, ...$battle_slips);
			}
			return array(
				'slips' => $all_slips,
				'exchanges' => $all_exchanges
			);
		}
		else {
			return array();
		}
	}

	public function send_bet_offer()
	{
		$user_id = $this->session->get('user_id');

		if(empty($user_id))
		{
			return redirect()->back();
		}
		else
		{
			$fixedExchangeBuyerRequestsModel = model('App\Models\FixedExchangeBuyerRequestsModel', false);
			$userModel = model('App\Models\UserModel', false);

			$add_data = array(
				'user_id' => $user_id,
				'bet_exchange_id' => $this->request->getVar('exchange_id'),
				'requested_price' => $this->request->getVar('offer_amount'),
				'status' => 0
			);
			$fixedExchangeBuyerRequestsModel->insert($add_data);
			return redirect()->to('/user');
		}
	}

	public function send_auction_bid()
	{
		$user_id = $this->session->get('user_id');

		if(empty($user_id))
		{
			return redirect()->back();
		}
		else
		{
			$betAuctionBidsModel = model('App\Models\BetAuctionBidsModel', false);
			$userModel = model('App\Models\UserModel', false);

			$add_data = array(
				'user_id' => $user_id,
				'bet_exchange_id' => $this->request->getVar('exchange_id'),
				'bid_amount' => $this->request->getVar('bid_amount')
			);
			$betAuctionBidsModel->insert($add_data);
			return redirect()->to('/user');
		}
	}

	public function buy_bet_slip()
	{
		$user_id = $this->session->get('user_id');
		$data = array();

		if(empty($user_id))
		{
			$data['status'] = false;
			$data['error'] = "Session Timedout, please relogin!";
		}
		else
		{
			$exchange_id = $this->request->getVar('exchange_id');
			if(empty($exchange_id))
			{
				$data['status'] = false;
				$data['error'] = "Exchange ID is required!";
			}
			else
			{
				$exchangeBetsModel = model('App\Models\ExchangeBetsModel', false);
				$exchange_data = $exchangeBetsModel->where('exchange_id', $exchange_id)->first();

				$existing_wallet_balance = $this->session->get('user_wallet_balance');
				$final_bal = $existing_wallet_balance - (double)$exchange_data['fixed_selling_price'];
				if($final_bal < 0)
				{
					$data['status'] = false;
					$data['error'] = "Not enough Wallet balance!";
				}
				else
				{
					
					$userModel = model('App\Models\UserModel', false);
					$mediaBetModel = model('App\Models\MediaBetModel', false);
					$betBattleModel = model('App\Models\BetBattleModel', false);
					$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);

					if($exchange_data['slip_type'] == 'Bet Accuracy')
					{
						$mediaBetModel->update($exchange_data['slip_id'], array('user_id' => $user_id));
					}
					elseif($exchange_data['slip_type'] == 'Bet Battle')
					{
						$betBattleModel->update($exchange_data['slip_id'], array('player1_id' => $user_id));
					}
					$exchangeBetsModel->update($exchange_id, array('exchange_status'=>1));

					$userModel->update_wallet_balance($user_id, '-'.(double)$exchange_data['fixed_selling_price']);
					$userModel->update_wallet_balance($exchange_data['user_id'], '+'.(double)$exchange_data['fixed_selling_price']);
					$current_wallet_session = $this->session->get('user_wallet_balance');
					$this->session->set('user_wallet_balance', (double)$current_wallet_session - (double)$exchange_data['fixed_selling_price']);

					$walletLogMasterModel->insert(array(
						'log_title' => $exchange_data['slip_type'].' exchange slip purchased',
						'log_description' => $exchange_data['slip_type'].' exchange slip(# '.$exchange_id.') purchased for amount '.$exchange_data['fixed_selling_price'],
						'amount' => $exchange_data['fixed_selling_price'],
						'amount_action' => 1,
						'user_id' => $user_id
					));

					$walletLogMasterModel->insert(array(
						'log_title' => $exchange_data['slip_type'].' exchange slip sold',
						'log_description' => $exchange_data['slip_type'].' exchange slip(# '.$exchange_id.') sold for amount '.$exchange_data['fixed_selling_price'],
						'amount' => $exchange_data['fixed_selling_price'],
						'amount_action' => 2,
						'user_id' => $exchange_data['user_id']
					));

					$data['status'] = true;
				}

			}
		}

		echo json_encode($data);

	}

	public function get_offers($exchange_id)
	{
		$user_id = $this->session->get('user_id');
		$data = array();

		if(empty($user_id))
		{
			$data['status'] = false;
			$data['error'] = "Session Timedout, please relogin!";
		}
		else
		{
			if(empty($exchange_id))
			{
				$data['status'] = false;
				$data['error'] = "Exchange ID is required!";
			}
			else
			{
				$fixedExchangeBuyerRequestsModel = model('App\Models\FixedExchangeBuyerRequestsModel', false);
				$offers = $fixedExchangeBuyerRequestsModel->fetch_requests_by_exchange($exchange_id);
				$data['status'] = true;
				$data['offers'] = $offers;
			}
		}
		echo json_encode($data);
		return true;
	}

	public function approve_offer($request_id) 
	{
		$user_id = $this->session->get('user_id');
		$data = array();

		if(empty($user_id))
		{
			$data['status'] = false;
			$data['error'] = "Session timeout, Please login again!";
			echo json_encode($data);
			return false;
		}

		if(empty($request_id))
		{
			$data['status'] = false;
			$data['error'] = "Offer request ID required!";
			echo json_encode($data);
			return false;
		}

		$exchangeBetsModel = model('App\Models\ExchangeBetsModel', false);
		$userModel = model('App\Models\UserModel', false);
		$fixedExchangeBuyerRequestsModel = model('App\Models\FixedExchangeBuyerRequestsModel', false);
		$request_data = $fixedExchangeBuyerRequestsModel->where('buyer_request_id', $request_id)->first();
		$exchange_data = $exchangeBetsModel->where('exchange_id', $request_data['bet_exchange_id'])->first();

		$requesting_user_data = $userModel->where('id', $request_data['user_id'])->first();


		$existing_wallet_balance = (double)$requesting_user_data['wallet_balance'];
		$final_bal = $existing_wallet_balance - (double)$request_data['requested_price'];
		if($final_bal < 0)
		{
			$data['status'] = false;
			$data['error'] = "Buyer's Wallet balance is not enough!";
		}
		else
		{
			$userModel = model('App\Models\UserModel', false);
			$mediaBetModel = model('App\Models\MediaBetModel', false);
			$betBattleModel = model('App\Models\BetBattleModel', false);
			$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);

			if($exchange_data['slip_type'] == 'Bet Accuracy')
			{
				$mediaBetModel->update($exchange_data['slip_id'], array('user_id' => $request_data['user_id']));
			}
			elseif($exchange_data['slip_type'] == 'Bet Battle')
			{
				$betBattleModel->update($exchange_data['slip_id'], array('player1_id' => $user_id));
			}
			$exchangeBetsModel->update($request_data['bet_exchange_id'], array('exchange_status'=>1));
			$fixedExchangeBuyerRequestsModel->update($request_id, array("status"=>1));

			$userModel->update_wallet_balance($user_id, '+'.(double)$request_data['requested_price']);
			$userModel->update_wallet_balance($request_data['user_id'], '-'.(double)$request_data['requested_price']);
			$current_wallet_session = $this->session->get('user_wallet_balance');
			$this->session->set('user_wallet_balance', (double)$current_wallet_session + (double)$request_data['requested_price']);

			$walletLogMasterModel->insert(array(
				'log_title' => $exchange_data['slip_type'].' exchange slip bid won',
				'log_description' => $exchange_data['slip_type'].' exchange slip(# '.$exchange_id.') bid of amount '.$request_data['requested_price'].' won',
				'amount' => $request_data['requested_price'],
				'amount_action' => 2,
				'user_id' => $user_id
			));

			$walletLogMasterModel->insert(array(
				'log_title' => $exchange_data['slip_type'].' exchange slip bid accepted',
				'log_description' => $exchange_data['slip_type'].' exchange slip(# '.$exchange_id.') bid of amount '.$request_data['requested_price'].' accepted',
				'amount' => $request_data['requested_price'],
				'amount_action' => 1,
				'user_id' => $request_data['user_id']
			));

			$data['status'] = true;
		}
		echo json_encode($data);
		return true;
	}

	public function reject_offer($request_id)
	{
		$user_id = $this->session->get('user_id');

		if(empty($user_id))
		{
			return redirect()->to('/user');
		}

		$fixedExchangeBuyerRequestsModel = model('App\Models\FixedExchangeBuyerRequestsModel', false);	
		$fixedExchangeBuyerRequestsModel->update($request_id, array('status' => 2));
		return redirect()->to('/user');
	}

	public function get_bids($exchange_id)
	{
		$user_id = $this->session->get('user_id');
		$data = array();

		if(empty($user_id))
		{
			$data['status'] = false;
			$data['error'] = "Session Timedout, please relogin!";
		}
		else
		{
			if(empty($exchange_id))
			{
				$data['status'] = false;
				$data['error'] = "Exchange ID is required!";
			}
			else
			{
				$betAuctionBidsModel = model('App\Models\BetAuctionBidsModel', false);
				$bids = $betAuctionBidsModel->fetch_bids_by_exchange($exchange_id);
				$data['status'] = true;
				$data['bids'] = $bids;
			}
		}
		echo json_encode($data);
		return true;	
	}

	public function settle_bet_accuracy()
	{
		$data = array();
		$user_id = $this->session->get('user_id');


		if(empty($user_id))
		{
			$data['status'] = false;
			$data['error'] = "Session Timedout, please relogin!";
			return json_encode($data);
		}

		$bet_id = $this->request->getVar('bet_id');
		$accuracy = $this->request->getVar('accuracy');
		if(empty($bet_id) || empty($accuracy))
		{
			$data['status'] = false;
			$data['error'] = "Required data is missing!";
			echo json_encode($data);
			return true;
		}

		$userModel = model('App\Models\UserModel', false);
		$mediaBetModel = model('App\Models\MediaBetModel', false);
		$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);

		$user_data = $userModel->where('id', $user_id)->first();
		$bet_data = $mediaBetModel->where('bet_id', $bet_id)->first();
		$all_bets = $mediaBetModel->where('user_id', $user_id)->whereIn('bet_status',array(1,2))->findAll();
		$result_amt = 0;
		$points_ba = 0;
		$k = 30;
		$log_description = "Bet Accuracy (ID: #".$bet_id.") with prediction: ".$accuracy."% ";
		$total_bets_won = array_filter($all_bets, function($k, $v) {
			return ($k == 'bet_status' && $v == 1);
		}, ARRAY_FILTER_USE_BOTH);
		$we = 0;
		$w = 0;
		if(count($all_bets)>0)
		{
			$we = round(count($total_bets_won)/count($all_bets), 2);
		}
		

		if($accuracy == 97)
		{
			// x1 won money & points
			$w = 1;
			$result_amt = $bet_data['betting_amount'];
		}
		elseif($accuracy == 98)
		{
			// x1.3 won money & points
			$w = 1;
			$result_amt = round($bet_data['betting_amount'] * 1.3, 2);
		}
		elseif($accuracy == 99)
		{
			// x1.6 won money & points
			$w = 1;
			$result_amt = round($bet_data['betting_amount'] * 1.6, 2);
		}
		elseif($accuracy == 100)
		{
			// x2 won money & points
			$w = 1;
			$result_amt = round($bet_data['betting_amount'] * 2, 2);
		}
		if($accuracy >= 80 && $accuracy < 100)
		{
			// won points only
			$w = 1;
		}

		$points_ba = $k * (round($w - $we, 2));

		$log_description .= ($w==0? 'Lost' : 'Won').". Points earned: ".$points_ba.", Money Earned: ".$result_amt;

		$userModel->increment_column_value('wallet_balance', $result_amt, $user_id);
		$this->session->set('user_wallet_balance', $this->session->get('user_wallet_balance') + $result_amt);
		$new_points_ba = $user_data['points_bet_accuracy'] + $points_ba;
		$new_points_total = $user_data['points_total'] + $new_points_ba;
		if($new_points_ba<0) {
			$userModel->update($user_id, array('points_bet_accuracy' => 0));
			if($new_points_total<0)
			{
				$userModel->update($user_id, array('points_total' => 0));
				$this->session->set('user_total_points', 0);
			}
		}
		else
		{
			$userModel->increment_column_value('points_bet_accuracy', $points_ba, $user_id);
			$userModel->increment_column_value('points_total', $points_ba, $user_id);
			$this->session->set('user_total_points', ($user_data['points_total']+ $points_ba));
		}
		
		$mediaBetModel->update($bet_id, array(
			'bet_status' => ($w == 0) ? 2 : 1
		));

		$log_transaction = array(
			'log_title' => 'Bet Accuracy bet '.($w==0? 'Lost' : 'Won'),
			'log_description' => $log_description,
			'amount' => $result_amt,
			'amount_action' => 2,
			'points_for_bet_accuracy' => $points_ba,
			'points_for_bet_battle' => 0,
			'points_for_sequel_bet' => 0,
			'points_for_total' => $new_points_total,
			'user_id' => $user_id
		);
		$walletLogMasterModel->insert($log_transaction);
		$data['status'] = true;
		return json_encode($data);
	}

	public function settle_sequel_bet()
	{
		$data = array();
		$user_id = $this->session->get('user_id');


		if(empty($user_id))
		{
			$data['status'] = false;
			$data['error'] = "Session Timedout, please relogin!";
			return json_encode($data);
		}

		$sequel_bet_id = $this->request->getVar('sequel_bet_id');
		$movie_release_date = $this->request->getVar('movie_release_date');
		$lead_actor = $this->request->getVar('lead_actor');
		$lead_actress = $this->request->getVar('lead_actress');
		$lead_director = $this->request->getVar('lead_director');
		if(empty($sequel_bet_id) || empty($movie_release_date)
			|| empty($lead_actor) || empty($lead_actress)
			|| empty($lead_director))
		{
			$data['status'] = false;
			$data['error'] = "Required data is missing!";
			echo json_encode($data);
			return true;
		}

		$userModel = model('App\Models\UserModel', false);
		$SequelBetsModel = model('App\Models\SequelBetsModel', false);
		$walletLogMasterModel = model('App\Models\WalletLogMasterModel', false);
		$user_data = $userModel->where('id', $user_id)->first();
		$bet_data = $SequelBetsModel->where('sequel_bet_id', $sequel_bet_id)->first();
		$all_bets = $SequelBetsModel->where('user_id', $user_id)->whereIn('bet_status',array(1,2))->findAll();

		$sequel_bet_day = explode('-', $bet_data['sequel_bet_day']);
		$sequel_bet_actors = json_decode($bet_data['sequel_bet_actors'], TRUE);
		$sequel_bet_actresses = json_decode($bet_data['sequel_bet_actresses'], TRUE);
		$sequel_bet_directors = json_decode($bet_data['sequel_bet_directors'], TRUE);

		$predicted_start_date = date($bet_data['sequel_bet_year'].'-'.$bet_data['sequel_bet_month'].'-'.$sequel_bet_day[0]);
		$predicted_end_date = date($bet_data['sequel_bet_year'].'-'.$bet_data['sequel_bet_month'].'-'.$sequel_bet_day[1]);
		$result_amt = 0;
		$points_bs = 0;
		$k = 30;
		$log_description = "You ";
		$total_bets_won = array_filter($all_bets, function($k, $v) {
			return ($k == 'bet_status' && $v == 1);
		}, ARRAY_FILTER_USE_BOTH);
		$we = 0;
		$w = 0;
		if(count($all_bets)>0)
		{
			$we = round(count($total_bets_won)/count($all_bets), 2);
		}

		// main logic for points and money
		if($movie_release_date >= $predicted_start_date && $movie_release_date <= $predicted_end_date)
		{
			$log_description .= 'Won Sequel Bet (#'.$sequel_bet_id.'), ';
			$w = 1;
			$right_predictions = 0;
			$multiplier = 1.25;
			if(strtolower($lead_actor) == strtolower($sequel_bet_actors[0]))
			{
				$right_predictions++;
				$log_description .= 'Lead Actor is right, ';
			}

			if(strtolower($lead_actress) == strtolower($sequel_bet_actresses[0]))
			{
				$right_predictions++;
				$log_description .= 'Lead Actress is right, ';
			}

			if(strtolower($lead_director) == strtolower($sequel_bet_directors[0]))
			{
				$right_predictions++;
				$log_description .= 'Lead Director is right, ';
			}

			switch ($right_predictions) {
				case 1:
					$multiplier = 1.5;
					break;
				case 2:
					$multiplier = 1.75;
					break;
				case 3:
					$multiplier = 2;
					break;
			}

			$result_amt = round($bet_data['sequel_bet_amount'] * $multiplier, 2);

			$log_description .= 'And won '.$result_amt.' Wallet balance';

		}
		else
		{
			$w=0;
			$log_description .= 'Lost Sequel Bet (#'.$sequel_bet_id.')';
		}

		$points_bs = $k * (round($w - $we, 2));
		$log_description .= ' And received '.$points_bs.' points';

		$userModel->increment_column_value('wallet_balance', $result_amt, $user_id);
		$this->session->set('user_wallet_balance', $this->session->get('user_wallet_balance') + $result_amt);
		$new_points_bs = $user_data['points_sequel_bet'] + $points_bs;
		$new_points_total = $user_data['points_total'] + $new_points_bs;
		if($new_points_bs<0) {
			$userModel->update($user_id, array('points_sequel_bet' => 0));
			if($new_points_total<0)
			{
				$userModel->update($user_id, array('points_total' => 0));
				$this->session->set('user_total_points', 0);
			}
		}
		else
		{
			$userModel->increment_column_value('points_sequel_bet', $points_bs, $user_id);
			$userModel->increment_column_value('points_total', $points_bs, $user_id);
			$this->session->set('user_total_points', ($user_data['points_total'] + $points_bs));
		}
		
		$SequelBetsModel->update($sequel_bet_id, array(
			'bet_status' => ($w == 0) ? 2 : 1
		));

		$log_transaction = array(
			'log_title' => 'Bet Sequel bet '.($w==0? 'Lost' : 'Won'),
			'log_description' => $log_description,
			'amount' => $result_amt,
			'amount_action' => 2,
			'points_for_bet_accuracy' => 0,
			'points_for_bet_battle' => 0,
			'points_for_sequel_bet' => $points_bs,
			'points_for_total' => $new_points_total,
			'user_id' => $user_id
		);
		$walletLogMasterModel->insert($log_transaction);
		$data['status'] = true;
		$data['msg'] = $log_description;
		return json_encode($data);
	}

	public function test_page()
	{
		$battleResultsModel = model('App\Models\BattleResultsModel', false);
			
		$all_bets = $battleResultsModel->where('winner_player_id', $claimed_user_id)->orWhere('loser_player_id',$claimed_user_id)->findAll();
		echo '<pre>';
		var_dump($all_bets);
	}

}