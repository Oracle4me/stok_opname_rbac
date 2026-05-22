<?php

namespace App\Controllers;

use App\Models\StokBarangModel;
use App\Models\MutasiStokModel;

class StokMasukController extends BaseController
{
    protected $db;
    protected $stokBarangModel;
    protected $mutasiModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->stokBarangModel = new StokBarangModel();
        $this->mutasiModel = new MutasiStokModel();
    }

    public function index()
    {
        return view('admin/stok_masuk');
    }

    public function getBarang()
    {
        $data = $this->db->table('barang b')
            ->select('
            b.id, 
            b.code, 
            b.nama,
            EXISTS(
                SELECT 1
                FROM stok_opname_detail sod
                JOIN stok_opname so ON so.id = sod.stok_opname_id
                WHERE sod.barang_id = b.id
                AND LOWER(so.status) = "draft"
            ) as is_locked
        ')
            ->where('b.is_deleted', 0)
            ->orderBy('b.nama', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }

    public function getData()
    {
        $data = $this->db->table('mutasi_stok m')
            ->select('
            m.id,
            m.created_at as tanggal,
            b.nama,
            ABS(m.selisih) as qty,
            m.keterangan,
            m.tipe,
        ')
            ->join('barang b', 'b.id = m.barang_id')
            ->join('users u', 'u.id = m.created_by', 'left')
            ->whereIn('m.tipe', ['masuk'])
            ->orderBy('m.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function save()
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();

        $barang_id = $json['barang_id'] ?? null;
        $qty       = (int)($json['qty'] ?? 0);
        $keterangan = $json['keterangan'] ?? null;

        $userId = session()->get('user_id');

        if (!$barang_id || $qty <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barang dan qty wajib diisi'
            ]);
        }

        $draftBarang = $this->db->table('stok_opname_detail sod')
            ->join('stok_opname so', 'so.id = sod.stok_opname_id')
            ->where('sod.barang_id', $barang_id)
            ->where('LOWER(so.status)', 'draft')
            ->countAllResults();

        if ($draftBarang > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barang ini sedang dalam proses opname (draft)'
            ]);
        }

        $this->db->transStart();

        $this->db->query(
            "SELECT qty FROM stok_barang WHERE barang_id = ? FOR UPDATE",
            [$barang_id]
        );

        $stok = $this->stokBarangModel
            ->where('barang_id', $barang_id)
            ->first();

        $before = $stok ? (int)$stok['qty'] : 0;
        $after  = $before + $qty;

        if ($stok) {
            $this->stokBarangModel
                ->where('barang_id', $barang_id)
                ->set('qty', 'qty + ' . $qty, false)
                ->update();
        } else {
            $this->stokBarangModel->insert([
                'barang_id' => $barang_id,
                'qty' => $qty
            ]);
        }

        $this->mutasiModel->insert([
            'barang_id'      => $barang_id,
            'tipe'           => 'masuk',
            'qty_before'     => $before,
            'qty_after'      => $after,
            'selisih'        => $qty,
            'referensi_id'   => null,
            'referensi_tipe' => 'stok_masuk',
            'keterangan'     => $keterangan ?? 'Stok masuk',
            'created_by'     => $userId
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Stok berhasil ditambahkan'
        ]);
    }


    public function detail($id)
    {
        $data = $this->db->table('mutasi_stok m')
            ->select('
            m.*,
            b.nama,
            b.code,
            ABS(m.selisih) as qty,
            u.nama as user
        ')
            ->join('barang b', 'b.id = m.barang_id')
            ->join('users u', 'u.id = m.created_by', 'left')
            ->where('m.id', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON($data);
    }

    public function update($id)
    {
        $trx = $this->mutasiModel->find($id);

        if (!$trx || $trx['tipe'] !== 'masuk') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid'
            ]);
        }

        $json = $this->request->getJSON(true) ?? $this->request->getPost();

        $qtyBaru = (int)($json['qty'] ?? 0);
        $keterangan = $json['keterangan'] ?? null;

        if ($qtyBaru <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Qty wajib diisi'
            ]);
        }

        $qtyLama = (int)$trx['selisih'];
        $selisih = $qtyBaru - $qtyLama;

        $this->db->transStart();

        $stok = $this->stokBarangModel
            ->where('barang_id', $trx['barang_id'])
            ->first();

        $stokBaru = (int)$stok['qty'] + $selisih;

        if ($stokBaru < 0) {
            $this->db->transRollback();

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Stok tidak valid'
            ]);
        }

        $this->stokBarangModel
            ->where('barang_id', $trx['barang_id'])
            ->update(null, [
                'qty' => $stokBaru
            ]);

        $this->mutasiModel->update($id, [
            'selisih' => $qtyBaru,
            'qty_after' => $stokBaru,
            'keterangan' => $keterangan
        ]);

        $this->db->transComplete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil diupdate'
        ]);
    }

    public function delete($id)
    {
        $trx = $this->mutasiModel->find($id);

        if (!$trx || $trx['tipe'] !== 'masuk') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid'
            ]);
        }

        $this->db->transStart();

        $stok = $this->stokBarangModel
            ->where('barang_id', $trx['barang_id'])
            ->first();

        $stokBaru = (int)$stok['qty'] - (int)$trx['selisih'];

        if ($stokBaru < 0) {
            $this->db->transRollback();

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Stok tidak cukup untuk rollback'
            ]);
        }

        $this->stokBarangModel
            ->where('barang_id', $trx['barang_id'])
            ->update(null, [
                'qty' => $stokBaru
            ]);

        $this->mutasiModel->delete($id);

        $this->db->transComplete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
