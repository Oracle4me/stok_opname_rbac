<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\StokOpnameModel;
use App\Models\StokOpnameDetailModel;
use App\Models\StokBarangModel;
use App\Models\MutasiStokModel;

class StokOpnameController extends BaseController
{
    protected $barangModel;
    protected $opnameModel;
    protected $detailModel;
    protected $stokBarangModel;
    protected $mutasiModel;
    protected $db;

    public function __construct()
    {
        $this->barangModel   = new BarangModel();
        $this->opnameModel   = new StokOpnameModel();
        $this->detailModel   = new StokOpnameDetailModel();
        $this->stokBarangModel = new StokBarangModel();
        $this->mutasiModel = new MutasiStokModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return view('admin/stok_opname');
    }

    // Select2 search barang
    public function searchBarang()
    {
        $search = $this->request->getGet('search') ?? '';

        $data = $this->db->table('barang b')
            ->select('b.id, b.code, b.nama, COALESCE(sb.qty,0) as stok')
            ->join('stok_barang sb', 'sb.barang_id = b.id', 'left')
            ->like('b.nama', $search)
            ->orLike('b.code', $search)
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }

    // Ambil data barang
    public function getBarang()
    {
        $data = $this->db->table('barang b')
            ->select('b.id, b.code, b.nama, COALESCE(sb.qty,0) as stok')
            ->join('stok_barang sb', 'sb.barang_id = b.id', 'left')
            ->where('b.is_deleted', 0)
            ->orderBy('b.nama', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }

    // Datatables Stok Opname
    public function getData()
    {
        $request = service('request');

        $draw   = (int) $request->getGet('draw');
        $start  = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');
        $search = $request->getGet('search')['value'] ?? '';

        $orderColumnIndex = $request->getGet('order')[0]['column'] ?? 0;
        $orderDir = $request->getGet('order')[0]['dir'] ?? 'desc';

        $columns = [
            'stok_opname.tanggal',
            'stok_opname.keterangan',
            'stok_opname.status',
            'users.nama'
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'stok_opname.id';

        $builder = $this->db->table('stok_opname')
            ->select('stok_opname.*, users.nama as user')
            ->join('users', 'users.id = stok_opname.created_by', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('stok_opname.keterangan', $search)
                ->orLike('users.nama', $search)
                ->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        $builder->orderBy($orderColumn, $orderDir)
            ->limit($length, $start);

        $data = $builder->get()->getResultArray();

        $totalAll = $this->db->table('stok_opname')->countAll();

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalAll,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }

    public function detail($id)
    {
        $header = $this->opnameModel->find($id);

        $detail = $this->db->table('stok_opname_detail d')
            ->select('d.*, b.code, b.nama')
            ->join('barang b', 'b.id = d.barang_id')
            ->where('d.stok_opname_id', $id)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'header' => $header,
            'detail' => $detail
        ]);
    }

    // Save dan Draft 
    public function save()
    {
        $this->db->transStart();

        $json = $this->request->getJSON(true);

        if (!$json) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ]);
        }

        $id         = $json['id'] ?? null;
        $tanggal    = $json['tanggal'] ?? null;
        $nama_barang = $json['nama_barang'] ?? null;
        $status     = $json['status'] ?? 'draft';
        $items      = $json['items'] ?? [];

        $userId = session()->get('user_id');

        if (!$tanggal || empty($items)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap'
            ]);
        }

        if ($status === 'final' && !can('stok_final')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak punya hak akses finalisasi'
            ]);
        }

        if ($id) {
            $existing = $this->opnameModel->find($id);

            if ($existing) {

                if ($existing['status'] === 'final') {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Data sudah difinalisasi'
                    ]);
                }
            }
        }

        $header = [
            'tanggal'    => $tanggal,
            'nama_barang' => $nama_barang,
            'status'     => $status
        ];

        if ($status === 'final') {
            $header['finalized_at'] = date('Y-m-d H:i:s');
        }

        if ($id) {
            $this->opnameModel->update($id, $header);
            $opnameId = $id;

            $this->detailModel->where('stok_opname_id', $opnameId)->delete();
        } else {
            $header['created_by'] = $userId;
            $this->opnameModel->insert($header);
            $opnameId = $this->opnameModel->getInsertID();
        }

        foreach ($items as $item) {

            if (!isset($item['stok_fisik'])) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data stok tidak valid'
                ]);
            }

            $stokSistem = (int)$item['stok_sistem'];
            $stokFisik  = (int)$item['stok_fisik'];
            $selisih    = $stokFisik - $stokSistem;

            if ($stokFisik < 0) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Stok tidak boleh negatif'
                ]);
            }

            // Cek barang
            $barang = $this->barangModel->find($item['barang_id']);
            if (!$barang || $barang['is_deleted']) {
                continue;
            }

            // Simpan detail opname
            $this->detailModel->insert([
                'stok_opname_id' => $opnameId,
                'barang_id'      => $item['barang_id'],
                'stok_sistem'    => $stokSistem,
                'stok_fisik'     => $stokFisik,
                'selisih'        => $selisih,
                'keterangan'     => $item['keterangan'] ?? null
            ]);

            // Finalisasi Opname Logic
            if ($status === 'final') {

                $this->db->query("SELECT qty FROM stok_barang WHERE barang_id = ? FOR UPDATE", [$item['barang_id']]);

                $stokSekarang = $this->db->table('stok_barang')
                    ->where('barang_id', $item['barang_id'])
                    ->get()
                    ->getRow();

                $currentQty = $stokSekarang ? (int)$stokSekarang->qty : 0;

                if ($currentQty != $stokSistem) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Stok sudah berubah, refresh opname!'
                    ]);
                }

                if ($stokSekarang) {

                    if ($selisih != 0) {

                        $after = $currentQty + $selisih;
                        $this->mutasiModel->insert([
                            'barang_id' => $item['barang_id'],
                            'tipe' => 'opname',
                            'qty_before' => $currentQty,
                            'qty_after' => $after,
                            'selisih' => $selisih,
                            'referensi_id' => $opnameId,
                            'referensi_tipe' => 'opname',
                            'created_by' => $userId
                        ]);

                        $this->db->table('stok_barang')
                            ->where('barang_id', $item['barang_id'])
                            ->set('qty', 'qty + ' . $selisih, false)
                            ->update();
                    }
                } else {

                    $this->db->table('stok_barang')->insert([
                        'barang_id' => $item['barang_id'],
                        'qty' => $stokFisik
                    ]);

                    if ($stokFisik > 0) {
                        $this->mutasiModel->insert([
                            'barang_id' => $item['barang_id'],
                            'tipe' => 'opname',
                            'qty_before' => 0,
                            'qty_after' => $stokFisik,
                            'selisih' => $stokFisik,
                            'referensi_id' => $opnameId,
                            'referensi_tipe' => 'opname',
                            'created_by' => $userId
                        ]);
                    }
                }
            }
        }

        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $status === 'final'
                ? 'Opname berhasil difinalisasi & stok diperbarui'
                : 'Draft berhasil disimpan'
        ]);
    }

    public function delete($id)
    {
        $data = $this->opnameModel->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        if ($data['status'] !== 'draft') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya draft yang boleh dihapus'
            ]);
        }

        $this->db->transStart();

        $this->detailModel
            ->where('stok_opname_id', $id)
            ->delete();

        $this->opnameModel->delete($id);

        $this->db->transComplete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Draft berhasil dihapus'
        ]);
    }
}
