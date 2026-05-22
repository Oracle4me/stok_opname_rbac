<?php

namespace App\Controllers;

use App\Models\StokBarangModel;
use App\Models\MutasiStokModel;

class DistribusiBarangController extends BaseController
{
    protected $db;
    protected $stokModel;
    protected $mutasiModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->stokModel = new StokBarangModel();
        $this->mutasiModel = new MutasiStokModel();
    }

    public function index()
    {
        return view('admin/distribusi_barang');
    }

    public function getData()
    {
        $data = $this->db->table('distribusi_barang d')
            ->select('
                d.id,
                d.tanggal,
                b.nama,
                d.qty,
                d.team_leader,
                d.area,
                d.note,
                u.nama as user
            ')
            ->join('barang b', 'b.id = d.barang_id')
            ->join('users u', 'u.id = d.created_by', 'left')
            ->orderBy('d.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function save()
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();

        $barang_id   = $json['barang_id'] ?? null;
        $qty         = (int)($json['qty'] ?? 0);
        $team_leader = trim($json['team_leader'] ?? '');
        $area        = trim($json['area'] ?? '');
        $note        = trim($json['note'] ?? '');
        $tanggal     = $json['tanggal'] ?? date('Y-m-d');

        $userId = session()->get('user_id');

        if (!$barang_id || $qty <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barang dan qty wajib diisi'
            ]);
        }

        if (!$team_leader) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Team Leader wajib diisi'
            ]);
        }

        $draftBarang = $this->db->table('stok_opname_detail sod')
            ->join('stok_opname so', 'so.id = sod.stok_opname_id') // 🔥 samakan field!
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

        $stok = $this->stokModel->where('barang_id', $barang_id)->first();

        if (!$stok || $stok['qty'] < $qty) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Stok tidak cukup'
            ]);
        }

        $before = (int)$stok['qty'];
        $after  = $before - $qty;

        $this->db->table('distribusi_barang')->insert([
            'barang_id'   => $barang_id,
            'qty'         => $qty,
            'team_leader' => $team_leader,
            'area'        => $area,
            'note'        => $note,
            'tanggal'     => $tanggal,
            'created_by'  => $userId
        ]);

        $distribusiId = $this->db->insertID();

        $this->stokModel
            ->where('barang_id', $barang_id)
            ->set('qty', 'qty - ' . $qty, false)
            ->update();

        $this->mutasiModel->insert([
            'barang_id'      => $barang_id,
            'tipe'           => 'keluar',
            'qty_before'     => $before,
            'qty_after'      => $after,
            'selisih'        => -$qty,
            'referensi_id'   => $distribusiId,
            'referensi_tipe' => 'distribusi',
            'keterangan'     => "Distribusi ke {$team_leader}" . ($area ? " - {$area}" : ''),
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
            'message' => 'Distribusi berhasil'
        ]);
    }

    public function detail($id)
    {
        $data = $this->db->table('distribusi_barang d')
            ->select('
            d.*,
            b.code,
            b.nama,
            u.nama as user
        ')
            ->join('barang b', 'b.id = d.barang_id')
            ->join('users u', 'u.id = d.created_by', 'left')
            ->where('d.id', $id)
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
        $trx = $this->db->table('distribusi_barang')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$trx) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $json = $this->request->getJSON(true) ?? $this->request->getPost();

        $qtyBaru = (int)$json['qty'];
        $teamLeader = trim($json['team_leader']);
        $area = trim($json['area']);
        $note = trim($json['note']);

        if ($qtyBaru <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Qty tidak valid'
            ]);
        }

        $qtyLama = (int)$trx['qty'];
        $selisih = $qtyBaru - $qtyLama;

        $this->db->transStart();

        $stok = $this->stokModel
            ->where('barang_id', $trx['barang_id'])
            ->first();

        $stokBaru = (int)$stok['qty'] - $selisih;

        if ($stokBaru < 0) {
            $this->db->transRollback();

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Stok tidak cukup'
            ]);
        }

        $this->stokModel
            ->where('barang_id', $trx['barang_id'])
            ->update(null, [
                'qty' => $stokBaru
            ]);

        $this->db->table('distribusi_barang')
            ->where('id', $id)
            ->update([
                'qty' => $qtyBaru,
                'team_leader' => $teamLeader,
                'area' => $area,
                'note' => $note
            ]);

        $this->mutasiModel
            ->where('referensi_id', $id)
            ->where('referensi_tipe', 'distribusi')
            ->set([
                'selisih' => -$qtyBaru,
                'qty_after' => $stokBaru,
                'keterangan' => "Distribusi ke {$teamLeader}" . ($area ? " - {$area}" : '')
            ])
            ->update();

        $this->db->transComplete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Distribusi berhasil diupdate'
        ]);
    }

    public function delete($id)
    {
        $trx = $this->db->table('distribusi_barang')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$trx) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $this->db->transStart();

        $stok = $this->stokModel
            ->where('barang_id', $trx['barang_id'])
            ->first();

        $stokBaru = (int)$stok['qty'] + (int)$trx['qty'];

        $this->stokModel
            ->where('barang_id', $trx['barang_id'])
            ->update(null, [
                'qty' => $stokBaru
            ]);

        $this->mutasiModel
            ->where('referensi_id', $id)
            ->where('referensi_tipe', 'distribusi')
            ->delete();

        $this->db->table('distribusi_barang')
            ->where('id', $id)
            ->delete();

        $this->db->transComplete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Distribusi berhasil dihapus'
        ]);
    }
}
