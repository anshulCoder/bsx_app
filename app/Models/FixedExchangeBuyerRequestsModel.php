<?php namespace App\Models;

use CodeIgniter\Model;

class FixedExchangeBuyerRequestsModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'fixed_exchange_buyer_requests';
    protected $primaryKey = 'buyer_request_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['buyer_request_id', 'bet_exchange_id', 'user_id', 'slip_id', 'requested_price', 'status'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function fetch_requests_by_exchange($exchange_id)
    {
        $builder = $this->db->table('fixed_exchange_buyer_requests');
        
        $query = $builder->select('user.name, fixed_exchange_buyer_requests.requested_price, fixed_exchange_buyer_requests.status, fixed_exchange_buyer_requests.buyer_request_id')
                        ->join('user', 'user.id = fixed_exchange_buyer_requests.user_id')
                        ->where('fixed_exchange_buyer_requests.bet_exchange_id', $exchange_id)->get();
        return $query->getResultArray();
    }

    public function offers_sent($user_id)
    {
        $builder = $this->db->table('fixed_exchange_buyer_requests');
        $query = $builder->select("bet_exchange.slip_type, bet_exchange.exchange_type, bet_exchange.fixed_selling_price, fixed_exchange_buyer_requests.requested_price, fixed_exchange_buyer_requests.status, fixed_exchange_buyer_requests.created_datetime")
                    ->join('bet_exchange', 'bet_exchange.exchange_id = fixed_exchange_buyer_requests.bet_exchange_id')
                    ->where('fixed_exchange_buyer_requests.user_id', $user_id)->get();

        return $query->getResultArray();
    }

}