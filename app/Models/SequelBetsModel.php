<?php namespace App\Models;

use CodeIgniter\Model;

class SequelBetsModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'sequel_bets';
    protected $primaryKey = 'sequel_bet_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['media_id', 'user_id', 'sequel_bet_amount', 'sequel_bet_day', 'sequel_bet_month', 'sequel_bet_year', 'sequel_bet_actors', 'sequel_bet_actresses', 'sequel_bet_directors', 'bet_status'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function get_sequel_bets($user_id)
    {
        $builder = $this->db->table('sequel_bets');
        $query = $builder->select('media.name, sequel_bets.*')
                        ->join('media','media.id = sequel_bets.media_id')
                        ->where('sequel_bets.user_id', $user_id)->get();
        return $query->getResultArray();
    }

    public function user_slips_for_exchange($user_id, $except_ids = array())
    {
        $builder = $this->db->table('sequel_bets');
        $builder->select("media.name, media.description, media.release_date, sequel_bet_id, sequel_bet_amount, sequel_bet_day, sequel_bet_month, sequel_bet_year, sequel_bet_actors, sequel_bet_actresses, sequel_bet_directors")
                    ->join('media','media.id = sequel_bets.media_id')
                    ->where('sequel_bets.user_id', $user_id);
        if(count($except_ids)>0) {
            $builder->whereNotIn('sequel_bet_id', $except_ids);
        }
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function bet_slips_by_ids($sequel_bet_ids)
    {
        $builder = $this->db->table('sequel_bets');
        $query = $builder->select("media.name, media.description, media.release_date, sequel_bet_id, sequel_bet_amount, sequel_bet_day, sequel_bet_month, sequel_bet_year, sequel_bet_actors, sequel_bet_actresses, sequel_bet_directors, (SELECT max(bet_auction_bids.bid_amount) FROM bet_auction_bids WHERE bet_auction_bids.bet_exchange_id = bet_exchange.exchange_id) as max_bid_amount")
                    ->join('media','media.id = sequel_bets.media_id', 'left')
                    ->join('bet_exchange', 'bet_exchange.slip_id = sequel_bets.sequel_bet_id AND bet_exchange.slip_type = \'Bet Sequel\'', 'left')
                    ->where('sequel_bets.bet_status', 0)
                    ->whereIn('sequel_bets.sequel_bet_id', $sequel_bet_ids)->get();
        return $query->getResultArray();
    }
}