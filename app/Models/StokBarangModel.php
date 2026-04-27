<?php

namespace App\Models;

use CodeIgniter\Model;

class StokBarangModel extends Model
{
    protected $table = 'stok_barang';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'barang_id',
        'qty',
        'updated_at'
    ];

    protected $useTimestamps = true;
}