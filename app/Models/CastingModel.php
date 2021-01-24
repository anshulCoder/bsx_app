<?php namespace App\Models;

use CodeIgniter\Model;

class CastingModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'casting_master';
    protected $primaryKey = 'cast_id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['cast_id', 'cast_name', 'cast_gender', 'cast_type'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}