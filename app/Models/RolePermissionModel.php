<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table = 'role_permissions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'role_id',
        'permission_id',
        'value',
        'created_by',
        'last_edited_by'
    ];

    protected $useTimestamps = false; 

}