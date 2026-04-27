<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\SatuanModel;

class MasterBarangController extends BaseController
{
    protected $barangModel;
    protected $kategoriModel;
    protected $satuanModel;

    public function __construct()
    {
        $this->barangModel  = new BarangModel();
        $this->kategoriModel = new KategoriModel();
        $this->satuanModel   = new SatuanModel();
    }

    public function index()
    {
        return view('admin/master_barang');
    }

    public function get($id)
    {
        $data = $this->barangModel
            ->select('barang.*, sb.qty as stok')
            ->join('stok_barang sb', 'sb.barang_id = barang.id', 'left')
            ->where('barang.id', $id)
            ->first();

        return $this->response->setJSON($data);
    }

    public function getData()
    {
        $request = service('request');

        $draw   = (int) $request->getGet('draw');
        $start  = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');
        $search = $request->getGet('search')['value'] ?? '';

        $orderColumnIndex = $request->getGet('order')[0]['column'] ?? 0;
        $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

        $columns = [
            'barang.id',
            'barang.tanggal',
            'barang.code',
            'barang.nama',
            'kategori_barang.nama',
            'satuan_barang.nama'
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'barang.id';

        $builder = $this->barangModel
            ->select('
                barang.id,
                barang.tanggal,
                barang.code,
                barang.nama,
                kategori_barang.nama as kategori,
                satuan_barang.nama as satuan,
                sb.qty as stok
            ')
            ->join('kategori_barang', 'kategori_barang.id = barang.kategori_id', 'left')
            ->join('satuan_barang', 'satuan_barang.id = barang.satuan_id', 'left')
            ->join('stok_barang sb', 'sb.barang_id = barang.id', 'left')
            ->where('barang.is_deleted', 0);

        if ($search) {
            $builder->groupStart()
                ->like('barang.code', $search)
                ->orLike('barang.nama', $search)
                ->orLike('kategori_barang.nama', $search)
                ->orLike('satuan_barang.nama', $search)
                ->groupEnd();
        }

        $totalData = $builder->countAllResults(false);

        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);

        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            "draw" => intval($draw),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalData,
            "data" => $data 
        ]);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        $userId = session()->get('user_id');

        $tanggal     = $this->request->getPost('tanggal');
        $code        = trim($this->request->getPost('code'));
        $nama        = trim($this->request->getPost('nama'));
        $kategori_id = $this->request->getPost('kategori_id');
        $satuan_id   = $this->request->getPost('satuan_id');
        $minimal_stock = $this->request->getPost('minimal_stock') ?? 0;

        if (!$code || !$nama || !$kategori_id || !$satuan_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua field wajib diisi'
            ]);
        }

        $existCode = $this->barangModel
            ->where('LOWER(code)', strtolower($code))
            ->where('is_deleted', 0)
            ->where('id !=', $id ?? 0)
            ->first();

        if ($existCode) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kode barang sudah digunakan'
            ]);
        }

        $existNama = $this->barangModel
            ->where('LOWER(nama)', strtolower($nama))
            ->where('id !=', $id ?? 0)
            ->first();

        if ($existNama) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nama barang sudah digunakan'
            ]);
        }

        $data = [
            'tanggal'     => $tanggal,
            'code'        => $code,
            'nama'        => $nama,
            'kategori_id' => $kategori_id,
            'satuan_id'   => $satuan_id,
            'minimal_stock' => $minimal_stock,
        ];

        if ($id) {
            // Hak akses edit
            if (!can('barang_edit')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak punya akses edit'
                ]);
            }

            $data['last_edited_by'] = $userId;
            $this->barangModel->update($id, $data);
        } else {
            // Hak akses tambah
            if (!can('barang_create')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak punya akses tambah'
                ]);
            }

            $data['created_by'] = $userId;
            $this->barangModel->insert($data);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data barang berhasil disimpan'
        ]);
    }

    public function delete($id)
    {
        $this->barangModel->update($id, [
            'is_deleted' => 1,
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Barang berhasil dihapus'
        ]);
    }


    public function getKategori()
    {
        $data = $this->kategoriModel->findAll();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function getSatuan()
    {
        $data = $this->satuanModel->findAll();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }
}