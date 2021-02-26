<?php namespace App\Models;

use CodeIgniter\Model;

class ExchangeBetsModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'bet_exchange';
    protected $primaryKey = 'exchange_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['exchange_id', 'user_id', 'slip_id', 'slip_type', 'exchange_type', 'fixed_selling_price', 'exchange_status'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function get_exchange_bets($user_id)
    {
        $builder = $this->db->table('bet_exchange');
        
        $query = $builder->select('media.name, COALESCE(sequel_bets.sequel_bet_amount, media_bet.betting_amount, bet_battle.battle_amount) AS slip_bet_amount, bet_exchange.slip_type, bet_exchange.exchange_type, bet_exchange.fixed_selling_price, bet_exchange.exchange_status, bet_exchange.created_datetime')
                        ->join('sequel_bets', 'sequel_bets.sequel_bet_id = bet_exchange.slip_id')
                        ->join('media_bet', 'media_bet.bet_id = bet_exchange.slip_id')
                        ->join('bet_battle', 'bet_battle.battle_id = bet_exchange.slip_id')
                        ->join('media','media.id = sequel_bets.media_id OR media.id = media_bet.media_id OR media.id = bet_battle.media_selected_id')
                        ->where('bet_exchange.user_id', $user_id)->get();
        return $query->getResultArray();
    }
}