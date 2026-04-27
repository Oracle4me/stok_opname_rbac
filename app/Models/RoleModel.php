<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama',
        'is_active',
        'created_by',
        'last_edited_by',
        'is_deleted'
    ];

    protected $useTimestamps = false; 

}