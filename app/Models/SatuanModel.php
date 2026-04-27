<?php

namespace App\Models;

use CodeIgniter\Model;

class SatuanModel extends Model
{
    protected $table      = 'satuan_barang';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama'
    ];

    protected $returnType = 'array';
}