<?php namespace App\Models;

use CodeIgniter\Model;

class SequelBetsModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'sequel_bets';
    protected $primaryKey = 'sequel_bet_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['media_id', 'user_id', 'sequel_bet_amount', 'sequel_bet_day', 'sequel_bet_month', 'sequel_bet_year', 'sequel_bet_actors', 'sequel_bet_actresses', 'sequel_bet_directors'];

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
}