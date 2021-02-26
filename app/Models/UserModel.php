<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'user';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['name', 'username', 'email', 'password', 'wallet_balance', 'points_bet_accuracy', 'points_bet_battle', 'points_sequel_bet', 'points_total', 'if_active'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function update_wallet_balance($user_id, $balance)
    {
        $builder = $this->db->table('user');
        $query = $builder->where('id', $user_id)
                         ->set('wallet_balance', '`wallet_balance`'.$balance, FALSE)
                         ->update();
        return true;
    }

    public function increment_column_value($column, $value, $user_id)
    {
        $builder = $this->db->table('user');
        $builder->where('id', $user_id)
                ->increment($column, $value);
        return true;
    }

    public function decrement_column_value($column, $value, $user_id)
    {
        $builder = $this->db->table('user');
        $builder->where('id', $user_id)
                ->decrement($column, $value);
        return true;
    }
}