<?php namespace App\Models;

use CodeIgniter\Model;

class BetBattleModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'bet_battle';
    protected $primaryKey = 'battle_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['battle_id', 'player1_id', 'player2_id', 'player1_battle_description', 'player2_battle_description', 'battle_amount', 'battle_mode', 'media_selected_id', 'battle_end_date', 'battle_status'];

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
        $query = $builder->select('media.name, bet_battle.*, user1.username as user1_name, user1.id as user1_id, user2.username as user2_name, user2.id as user2_id')
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

}