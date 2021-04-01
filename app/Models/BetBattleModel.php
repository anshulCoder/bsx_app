<?php namespace App\Models;

use CodeIgniter\Model;

class BetBattleModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'bet_battle';
    protected $primaryKey = 'battle_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['battle_id', 'player1_id', 'player2_id', 'battle_description', 'player_for', 'player_against', 'battle_amount', 'battle_mode', 'media_selected_id', 'battle_end_date', 'battle_status', 'additional_bet_amount'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function get_battles_by_user($user_id)
    {
        $builder = $this->db->table('bet_battle');
        $query = $builder->select('media.name, bet_battle.*, user1.username as user1_name, user1.id as user1_id, user2.username as user2_name, user2.id as user2_id, (SELECT COUNT(*) FROM additional_bets WHERE bet_battle_id = bet_battle.battle_id and rooting_for_user = bet_battle.player1_id) AS player1_additional, (SELECT COUNT(*) FROM additional_bets WHERE bet_battle_id = bet_battle.battle_id and rooting_for_user = bet_battle.player2_id) AS player2_additional')
                        ->join('media','media.id = bet_battle.media_selected_id', 'left')
                        ->join('user as user1', 'user1.id = bet_battle.player1_id', 'left')
                        ->join('user as user2', 'user2.id = bet_battle.player2_id', 'left')
                        ->where('bet_battle.player1_id', $user_id)
                        ->orWhere('bet_battle.player2_id', $user_id)
                        ->whereNotIn('bet_battle.battle_status', [BATTLE_PENDING_PLAYER2, BATTLE_PENDING_PLAYER1])->get();
        return $query->getResultArray();
    }

    public function get_requested_battles($user_id)
    {
        $builder = $this->db->table('bet_battle');
        $query = $builder->select('media.name, bet_battle.*, user.username as battle_with')
                        ->join('media','media.id = bet_battle.media_selected_id', 'left')
                        ->join('user', 'user.id = bet_battle.player1_id', 'left')
                        ->where('bet_battle.player2_id', $user_id)
                        ->where('bet_battle.battle_status', BATTLE_PENDING_PLAYER2)
                        ->orWhere('bet_battle.player1_id', $user_id)
                        ->where('bet_battle.battle_status', BATTLE_PENDING_PLAYER1)
                        ->get();
        return $query->getResultArray();
    }

    public function get_public_battles()
    {
        $builder = $this->db->table('bet_battle');
        $query = $builder->select('media.name as media_name, media.media_images, user1.name as player1_name, user2.name as player2_name, bet_battle.battle_description, bet_battle.player_for, bet_battle.player_against, bet_battle.player1_id, bet_battle.player2_id, bet_battle.battle_id, bet_battle.battle_amount, bet_battle.additional_bet_amount, (SELECT COUNT(*) FROM additional_bets WHERE bet_battle_id = bet_battle.battle_id and rooting_for_user = bet_battle.player1_id) AS player1_additional, (SELECT COUNT(*) FROM additional_bets WHERE bet_battle_id = bet_battle.battle_id and rooting_for_user = bet_battle.player2_id) AS player2_additional, bet_battle.battle_end_date')
                         ->join('media','media.id = bet_battle.media_selected_id', 'left')
                         ->join('user as user1', 'user1.id = bet_battle.player1_id', 'left')
                         ->join('user as user2', 'user2.id = bet_battle.player2_id', 'left')
                         ->where('bet_battle.battle_status', BATTLE_LIVE)
                         ->where('bet_battle.battle_mode', 'public')
                         ->orderby('bet_battle.battle_end_date', 'ASC')
                         ->get();
        return $query->getResultArray();
    }

    public function fetch_battle_for_additional($battle_id)
    {
        $builder = $this->db->table('bet_battle');
        $query = $builder->select('media.name as media_name, user1.name as player1_name, user2.name as player2_name, bet_battle.player1_id, bet_battle.player2_id, bet_battle.player1_battle_description, bet_battle.player2_battle_description, bet_battle.battle_id, bet_battle.additional_bet_amount')
                         ->join('media','media.id = bet_battle.media_selected_id', 'left')
                         ->join('user as user1', 'user1.id = bet_battle.player1_id', 'left')
                         ->join('user as user2', 'user2.id = bet_battle.player2_id', 'left')
                         ->where('bet_battle.battle_status', BATTLE_LIVE)
                         ->where('bet_battle.battle_mode', 'public')
                         ->where('bet_battle.battle_id', $battle_id)
                         ->get();
        return $query->getResultArray();
    }

    public function user_slips_for_exchange($user_id, $except_ids = array())
    {
        $builder = $this->db->table('bet_battle');
        $builder->select("media.name, media.description, media.release_date, battle_id, battle_description, player_for, player_against, battle_amount, battle_mode, battle_end_date, user.name as battle_opponent")
                    ->join('media','media.id = bet_battle.media_selected_id')
                    ->join('user', 'user.id = bet_battle.player2_id')
                    ->where('bet_battle.player1_id', $user_id)
                    ->where('bet_battle.battle_status', BATTLE_LIVE)
                    ->where('bet_battle.battle_end_date >=', date('Y-m-d'));
        if(count($except_ids)>0) {
            $builder->whereNotIn('battle_id', $except_ids);
        }
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function bet_slips_by_ids($battle_ids)
    {
        $builder = $this->db->table('bet_battle');
        $query = $builder->select("media.name, media.description, media.release_date, battle_id, battle_description, player_for, player_against, battle_amount, battle_mode, battle_end_date, user1.name as player1_name, user2.name as player2_name")
                    ->join('media','media.id = bet_battle.media_selected_id')
                    ->join('user as user1', 'user1.id = bet_battle.player1_id', 'left')
                    ->join('user as user2', 'user2.id = bet_battle.player2_id', 'left')
                    ->whereIn('bet_battle.battle_id', $battle_ids)->get();
        return $query->getResultArray();
    }

}