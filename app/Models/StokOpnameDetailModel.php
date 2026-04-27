<?php

namespace App\Models;

use CodeIgniter\Model;

class StokOpnameDetailModel extends Model
{
    protected $table = 'stok_opname_detail';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'stok_opname_id',
        'barang_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
}