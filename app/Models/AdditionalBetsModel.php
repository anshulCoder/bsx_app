<?php namespace App\Models;

use CodeIgniter\Model;

class AdditionalBetsModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'additional_bets';
    protected $primaryKey = 'additional_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['additional_id','bet_battle_id', 'user_id', 'bet_amount','rooting_for_user'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function fetch_participated_battles($user_id)
    {
        $builder = $this->db->table('additional_bets');
        $query = $builder->select('media.name, additional_bets.bet_amount, 
                        (CASE WHEN bet_battle.player1_id = additional_bets.rooting_for_user THEN bet_battle.player1_battle_description ELSE bet_battle.player2_battle_description END) AS battle_description, rooting_user.username, bet_battle.battle_end_date, bet_battle.battle_status, additional_bets.created_datetime')
                        ->join('bet_battle', 'bet_battle.battle_id = additional_bets.bet_battle_id')
                        ->join('media','media.id = bet_battle.media_selected_id')
                        ->join('user as rooting_user', 'rooting_user.id = additional_bets.rooting_for_user')
                        ->where('additional_bets.user_id', $user_id)->get();
        return $query->getResultArray();
    }
}