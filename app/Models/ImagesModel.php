<?php

namespace App\Models;

use CodeIgniter\Model;

class ImagesModel extends Model
{
    protected $table      = 'image';
    protected $primaryKey = 'idimage';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    //protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'url', 'urlnnu', 'act'];

    //protected $useTimestamps = false;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // protected $validationRules    = [];
    // protected $validationMessages = [];
    // protected $skipValidation     = false;

    public function imagesByPage($idPage)
    {
        $db = \Config\Database::connect();
        $query   = $db->query('SELECT i.* FROM image i, page_image pi, page p WHERE i.act = 1 AND i.idimage = pi.idimage AND pi.idpage = p.idpage AND p.idpage = '.$idPage);
        $results = $query->getResult();
        return $results;
    }
}