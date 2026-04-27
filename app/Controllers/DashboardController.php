<?php

namespace App\Controllers;

use Config\Database;

class DashboardController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
    {
        $bulan = date('Y-m');

        $totalBarang = $this->db->table('barang')
            ->where('is_deleted', 0)
            ->countAllResults();

        $totalStok = $this->db->table('stok_barang')
            ->selectSum('qty')
            ->get()->getRow()->qty ?? 0;

        $totalMasuk = $this->db->table('mutasi_stok')
            ->selectSum('selisih')
            ->where('tipe', 'masuk')
            ->where("DATE_FORMAT(created_at,'%Y-%m')", $bulan)
            ->get()->getRow()->selisih ?? 0;

        $totalKeluar = $this->db->table('mutasi_stok')
            ->selectSum('selisih')
            ->where('tipe', 'keluar')
            ->where("DATE_FORMAT(created_at,'%Y-%m')", $bulan)
            ->get()->getRow()->selisih ?? 0;

        $stokMenipis = $this->db->table('stok_barang sb')
            ->select('b.nama, sb.qty')
            ->join('barang b', 'b.id = sb.barang_id')
            ->where('sb.qty <=', 10)
            ->get()->getResultArray();

        $recent = $this->db->table('mutasi_stok m')
            ->select('m.created_at, b.nama, m.tipe, m.selisih')
            ->join('barang b', 'b.id = m.barang_id')
            ->orderBy('m.created_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        return view('admin/dashboard', [
            'totalBarang' => $totalBarang,
            'totalStok' => $totalStok,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => abs($totalKeluar),
            'stokMenipis' => $stokMenipis,
            'recent' => $recent
        ]);
    }

    public function chartStok()
    {
        $data = $this->db->query("
            SELECT 
                DATE(created_at) as tanggal,
                SUM(CASE WHEN tipe='masuk' THEN selisih ELSE 0 END) as masuk,
                SUM(CASE WHEN tipe='keluar' THEN ABS(selisih) ELSE 0 END) as keluar
            FROM mutasi_stok
            GROUP BY DATE(created_at)
            ORDER BY tanggal ASC
        ")->getResultArray();

        return $this->response->setJSON($data);
    }

    public function transaksi()
    {
        $data = $this->db->table('mutasi_stok m')
            ->select('m.created_at, b.nama, m.tipe, m.selisih')
            ->join('barang b', 'b.id = m.barang_id')
            ->orderBy('m.created_at', 'DESC')
            ->get()->getResultArray();

        return $this->response->setJSON(['data' => $data]);
    }
}
