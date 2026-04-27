<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table      = 'barang';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tanggal',
        'code',
        'nama',
        'kategori_id',
        'satuan_id',
        'created_by',
        'last_edited_by',
        'is_deleted'
    ];

    protected $useTimestamps = false;

    protected $returnType = 'array';
}