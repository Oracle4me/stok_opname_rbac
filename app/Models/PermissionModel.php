<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama',
        'label',
        'group_name'
    ];

    protected $useTimestamps = false; 

}