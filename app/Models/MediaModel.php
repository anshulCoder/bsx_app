<?php namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
	protected $DBGroup = 'default';
	protected $table      = 'media';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['name', 'description', 'release_date', 'betting_allowed', 'rating', 'casting_info', 'current_value', 'media_images', 'actors', 'actresses', 'directors', 'active'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function get_medias_for_bet()
    {
        $builder = $this->db->table('media');
        $query = $builder->select('id, name, release_date')->where('active', 1)->get();
        return $query->getResultArray();
    }
}