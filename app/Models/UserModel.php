<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'username',
        'nama',
        'email',
        'password',
        'role_id',
        'status',
        'created_by',
        'last_edited_by',
        'is_deleted'
    ];

    protected $useTimestamps = false;
}