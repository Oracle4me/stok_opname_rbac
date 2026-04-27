<?php

namespace App\Models;

use CodeIgniter\Model;

class StokOpnameModel extends Model
{
    protected $table = 'stok_opname';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tanggal',
        'keterangan',
        'status',
        'created_at',
        'created_by'
    ];

    protected $useTimestamps = true;
}