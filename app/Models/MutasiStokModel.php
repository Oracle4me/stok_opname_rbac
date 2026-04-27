<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiStokModel extends Model
{
    protected $table = 'mutasi_stok';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'barang_id',
        'tipe',
        'qty_before',
        'qty_after',
        'selisih',
        'referensi_id',
        'referensi_tipe',
        'keterangan',
        'created_at',
        'created_by',
    ];

    protected $useTimestamps = true;
}