<?php

namespace App\Models;

use CodeIgniter\Model;

class PageImageModel extends Model
{
    protected $table      = 'page_image';
    protected $primaryKey = 'idpage';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    //protected $useSoftDeletes = true;

    protected $allowedFields = ['idpage', 'idimage'];

    //protected $useTimestamps = false;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // protected $validationRules    = [];
    // protected $validationMessages = [];
    // protected $skipValidation     = false;
}