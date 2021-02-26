<?php namespace App\Models;

use CodeIgniter\Model;

class BetAuctionBidsModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'bet_auction_bids';
    protected $primaryKey = 'auction_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['auction_id', 'bet_exchange_id', 'user_id', 'bid_amount'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;


    public function fetch_bids_by_exchange($exchange_id)
    {
        $builder = $this->db->table('bet_auction_bids');
        
        $query = $builder->select('user.name, bet_auction_bids.bid_amount, bet_auction_bids.created_datetime')
                        ->join('user', 'user.id = bet_auction_bids.user_id')
                        ->where('bet_auction_bids.bet_exchange_id', $exchange_id)
                        ->orderBy('bet_auction_bids.bid_amount','DESC')->get();
        return $query->getResultArray();
    }

    public function bids_sent($user_id)
    {
        $builder = $this->db->table('bet_auction_bids');
        $query = $builder->select("bet_exchange.slip_type, bet_auction_bids.bid_amount, bet_auction_bids.created_datetime")
                        ->join('bet_exchange', 'bet_exchange.exchange_id = bet_auction_bids.bet_exchange_id')
                        ->where('bet_auction_bids.user_id', $user_id)
                        ->orderBy('bet_auction_bids.bid_amount','DESC')->get();

        return $query->getResultArray();
    }
}