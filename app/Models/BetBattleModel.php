<?php namespace App\Models;

use CodeIgniter\Model;

class BetBattleModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'bet_battle';
    protected $primaryKey = 'battle_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['battle_id', 'player1_id', 'player2_id', 'battle_description', 'battle_amount', 'battle_mode', 'media_selected_id', 'battle_end_date'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

}