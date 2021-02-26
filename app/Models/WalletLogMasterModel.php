<?php namespace App\Models;

use CodeIgniter\Model;

class WalletLogMasterModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'wallet_log_master';
    protected $primaryKey = 'log_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['log_id', 'log_title', 'log_description', 'amount', 'amount_action', 'points_for_bet_accuracy', 'points_for_bet_battle', 'points_for_sequel_bet', 'points_for_total', 'user_id'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}