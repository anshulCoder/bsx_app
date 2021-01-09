<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'user';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['name', 'username', 'email', 'password', 'wallet_balance', 'if_active'];

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
}