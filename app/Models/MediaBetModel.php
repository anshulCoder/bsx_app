<?php namespace App\Models;

use CodeIgniter\Model;

class MediaBetModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'media_bet';
    protected $primaryKey = 'bet_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['media_id', 'user_id', 'betting_amount', 'predicted_amount', 'betting_date', 'bet_status'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function get_media_bets($user_id)
    {
        $builder = $this->db->table('media_bet');
        $query = $builder->select('media.name, bet_id, betting_amount, predicted_amount, betting_date, bet_status')
                        ->join('media','media.id = media_bet.media_id')
                        ->where('media_bet.user_id', $user_id)->get();
        return $query->getResultArray();
    }

    public function user_slips_for_exchange($user_id, $except_ids = array())
    {
        $builder = $this->db->table('media_bet');
        $builder->select("media.name, media.description, media.release_date, bet_id, betting_amount as accuracy_betting_amount, predicted_amount as accuracy_predicted_amount, betting_date as accuracy_betting_date")
                    ->join('media','media.id = media_bet.media_id')
                    ->where('media_bet.user_id', $user_id)
                    ->where('media_bet.betting_date >=', date('Y-m-d'));
        if(count($except_ids)>0)
        {
            $builder->whereNotIn('bet_id', $except_ids);
        }
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function bet_slips_by_ids($bet_ids)
    {
        $builder = $this->db->table('media_bet');
        $query = $builder->select("media.name, media.description, media.release_date, bet_id, betting_amount as accuracy_betting_amount, predicted_amount as accuracy_predicted_amount, betting_date as accuracy_betting_date")
                    ->join('media','media.id = media_bet.media_id')
                    ->where('media_bet.bet_status', 0)
                    ->whereIn('media_bet.bet_id', $bet_ids)->get();
        return $query->getResultArray();
    }
}