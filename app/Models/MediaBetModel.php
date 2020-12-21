<?php namespace App\Models;

use CodeIgniter\Model;

class MediaBetModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'media_bet';
    protected $primaryKey = 'bet_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['media_id', 'user_id', 'betting_amount', 'predicted_amount', 'betting_date'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function get_media_bets()
    {
        $builder = $this->db->table('media_bet');
        $query = $builder->select('media.name, bet_id, betting_amount, predicted_amount, betting_date')
                        ->join('media','media.id = media_bet.media_id')->get();
        return $query->getResultArray();
    }
}