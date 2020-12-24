<?php namespace App\Models;

use CodeIgniter\Model;

class BattleResultsModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'battle_results';
    protected $primaryKey = 'battle_result_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['battle_result_id','battle_id', 'winner_player_id', 'loser_player_id','win_amount', 'win_amount_additionals'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

}