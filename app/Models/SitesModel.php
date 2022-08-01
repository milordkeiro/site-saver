<?php

namespace App\Models;

use CodeIgniter\Model;

class SitesModel extends Model
{
    protected $table      = 'site';
    protected $primaryKey = 'idsite';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    //protected $useSoftDeletes = true;

    protected $allowedFields = ['domain', 'nick', 'tagcontent','classcontent', 'idcontent', 'xmlpages', 'title', 'act'];

    //protected $useTimestamps = false;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // protected $validationRules    = [];
    // protected $validationMessages = [];
    // protected $skipValidation     = false;
}