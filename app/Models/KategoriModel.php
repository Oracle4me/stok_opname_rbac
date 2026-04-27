<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table      = 'kategori_barang';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama'
    ];

    protected $returnType = 'array';
}