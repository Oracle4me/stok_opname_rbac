<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return view('admin/laporan');
    }

    public function rekap()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $start = $bulan . '-01';
        $end   = date('Y-m-t', strtotime($start));



        $data = $this->db->table('barang b')
            ->select("
                b.id,
                b.nama,

                COALESCE(SUM(CASE 
                    WHEN m.tipe = 'keluar' 
                    AND DATE(m.created_at) BETWEEN '$start' AND '$end'
                    THEN ABS(m.selisih)
                END),0) as penggunaan,

                COALESCE(SUM(CASE 
                    WHEN m.tipe = 'masuk'
                    AND DATE(m.created_at) BETWEEN '$start' AND '$end'
                    THEN m.selisih
                END),0) as tambahan,

                COALESCE(sb.qty,0) as sisa
            ")
            ->join('mutasi_stok m', 'm.barang_id = b.id', 'left')
            ->join('stok_barang sb', 'sb.barang_id = b.id', 'left')
            ->groupBy('b.id')
            ->orderBy('b.nama', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($data as &$row) {
            $row['stok_awal'] = $row['sisa']
                + $row['penggunaan']
                - $row['tambahan'];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function detail()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $start = $bulan . '-01';
        $end   = date('Y-m-t', strtotime($start));

        $data = $this->db->table('distribusi_barang d')
            ->select('
                d.tanggal,
                b.nama,
                d.team_leader,
                d.qty,
                d.area,
                d.note
            ')
            ->join('barang b', 'b.id = d.barang_id')
            ->where("DATE(d.tanggal) >=", $start)
            ->where("DATE(d.tanggal) <=", $end)
            ->orderBy('d.tanggal', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['data' => $data]);
    }

    public function mutasi()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $start = $bulan . '-01';
        $end   = date('Y-m-t', strtotime($start));

        $data = $this->db->table('mutasi_stok m')
            ->select('
            m.*,
            b.code,
            b.nama,
            u.nama as user,
            CASE
                WHEN m.tipe = "opname" THEN sod.keterangan
                ELSE m.keterangan 
            END as keterangan_display
        ', false)
            ->join('barang b', 'b.id = m.barang_id')
            ->join('users u', 'u.id = m.created_by', 'left')

            ->join(
                'stok_opname_detail sod',
                'sod.stok_opname_id = m.referensi_id
                AND sod.barang_id = m.barang_id',
                'left'
            )

            ->where("DATE(m.created_at) >=", $start)
            ->where("DATE(m.created_at) <=", $end)
            ->orderBy('m.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function exportRekap()
    {
        if (!can('laporan_export')) {
            return $this->response->setStatusCode(403);
        }

        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $start = $bulan . '-01 00:00:00';
        $end   = date('Y-m-t 23:59:59', strtotime($bulan));

        $bulanIndo = [
            '01' => 'JANUARI',
            '02' => 'FEBRUARI',
            '03' => 'MARET',
            '04' => 'APRIL',
            '05' => 'MEI',
            '06' => 'JUNI',
            '07' => 'JULI',
            '08' => 'AGUSTUS',
            '09' => 'SEPTEMBER',
            '10' => 'OKTOBER',
            '11' => 'NOVEMBER',
            '12' => 'DESEMBER'
        ];
        $nama_bulan = $bulanIndo[date('m', strtotime($bulan))] . ' ' . date('Y', strtotime($bulan));

        // Query 
        $data = $this->db->table('barang b')
            ->select("
            b.nama,
            k.nama as kategori,
            COALESCE(SUM(CASE WHEN m.tipe='keluar' AND m.created_at BETWEEN '$start' AND '$end' THEN ABS(m.selisih) END),0) as penggunaan,
            COALESCE(SUM(CASE WHEN m.tipe='masuk' AND m.created_at BETWEEN '$start' AND '$end' THEN m.selisih END),0) as tambahan,
            COALESCE(sb.qty,0) as sisa
        ")
            ->join('kategori_barang k', 'k.id=b.kategori_id', 'left')
            ->join('mutasi_stok m', 'm.barang_id=b.id', 'left')
            ->join('stok_barang sb', 'sb.barang_id=b.id', 'left')
            ->groupBy('b.id')
            ->orderBy('k.nama', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($data as &$row) {
            $row['stok_awal'] = $row['sisa'] + $row['penggunaan'] - $row['tambahan'];
        }

        $grouped = [];
        foreach ($data as $row) {
            $grouped[$row['kategori'] ?? 'Tanpa Kategori'][] = $row;
        }

        // Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', "REPORT PENGGUNAAN MATERIAL PROMOSI $nama_bulan");

        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A2', "BRANCH SEMARANG");

        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $rowNum = 4;

        $headers = ['Jenis Material Promosi', 'STOCK AWAL', 'PENGGUNAAN', 'TAMBAHAN STOCK', 'SISA STOCK'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $rowNum, $header);
            $col++;
        }

        // Header style
        $sheet->getStyle("A$rowNum:E$rowNum")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(4)->setRowHeight(35);
        $sheet->getStyle("A4:E4")->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        // Border bawah header lebih tegas
        $sheet->getStyle("A$rowNum:E$rowNum")->applyFromArray([
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM]
            ]
        ]);

        $sheet->setAutoFilter("A4:E4");

        $rowNum++;

        $total_awal = 0;
        $total_penggunaan = 0;
        $total_tambahan = 0;
        $total_sisa = 0;

        $noKategori = 1;

        foreach ($grouped as $kategori => $items) {

            $sheet->mergeCells("A$rowNum:E$rowNum");
            $sheet->setCellValue("A$rowNum", chr(64 + $noKategori) . ". " . strtoupper($kategori));

            $sheet->getStyle("A$rowNum:E$rowNum")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8EAADB']
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]);

            $rowNum++;

            foreach ($items as $item) {

                $sheet->setCellValue("A$rowNum", $item['nama']);
                $sheet->setCellValue("B$rowNum", $item['stok_awal']);
                $sheet->setCellValue("C$rowNum", $item['penggunaan']);
                $sheet->setCellValue("D$rowNum", $item['tambahan']);
                $sheet->setCellValue("E$rowNum", $item['sisa']);

                // Highlight kolom
                $sheet->getStyle("B$rowNum")->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2E5C3');

                $sheet->getStyle("E$rowNum")->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2E5C3');

                $sheet->getStyle("B$rowNum:E$rowNum")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $total_awal += $item['stok_awal'];
                $total_penggunaan += $item['penggunaan'];
                $total_tambahan += $item['tambahan'];
                $total_sisa += $item['sisa'];

                $rowNum++;
            }

            $noKategori++;
        }

        // Total
        $sheet->setCellValue("A$rowNum", "TOTAL");
        $sheet->setCellValue("B$rowNum", $total_awal);
        $sheet->setCellValue("C$rowNum", $total_penggunaan);
        $sheet->setCellValue("D$rowNum", $total_tambahan);
        $sheet->setCellValue("E$rowNum", $total_sisa);

        $sheet->getStyle("A$rowNum:E$rowNum")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // Border Style
        $lastRow = $rowNum;

        $sheet->getStyle("A4:E$lastRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        $sheet->getStyle("B5:E$lastRow")
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $sheet->getStyle("B5:B$lastRow")->getFont()->setBold(true);
        $sheet->getStyle("E5:E$lastRow")->getFont()->setBold(true);

        $sheet->getStyle("A5:A$lastRow")
            ->getAlignment()
            ->setWrapText(true);

        $sheet->getStyle("A5:E$lastRow")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A5');

        $writer = new Xlsx($spreadsheet);
        $filename = "laporan_rekap_$bulan.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $writer->save('php://output');
        exit;
    }

    public function exportDetail()
    {
        if (!can('laporan_export')) {
            return $this->response->setStatusCode(403);
        }

        $bulan = $this->request->getGet('bulan') ?? date('Y-m');

        $start = $bulan . '-01 00:00:00';
        $end   = date('Y-m-t 23:59:59', strtotime($bulan));

        $data = $this->db->table('distribusi_barang d')
            ->select('d.tanggal, b.nama, d.team_leader, d.qty, d.area, d.note')
            ->join('barang b', 'b.id = d.barang_id')
            ->where('d.tanggal >=', $start)
            ->where('d.tanggal <=', $end)
            ->orderBy('d.team_leader', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];
        foreach ($data as $row) {
            $grouped[$row['team_leader']][] = $row;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', "DETAIL PENGGUNAAN MATERIAL " . strtoupper(date('F Y', strtotime($bulan))));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $rowNum = 3;
        $lastDataRow = 0;

        foreach ($grouped as $leader => $items) {

            $sheet->mergeCells("A$rowNum:A" . ($rowNum + 1));
            $sheet->setCellValue("A$rowNum", "TEAM LEADER");

            $sheet->mergeCells("B$rowNum:D$rowNum");
            $sheet->setCellValue("B$rowNum", "Banner Tiang");

            $sheet->mergeCells("E$rowNum:E" . ($rowNum + 1));
            $sheet->setCellValue("E$rowNum", "AREA");

            $sheet->mergeCells("F$rowNum:F" . ($rowNum + 1));
            $sheet->setCellValue("F$rowNum", "NOTE");

            $sheet->getStyle("A$rowNum:F$rowNum")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9C46A']
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);

            $sheet->getRowDimension($rowNum)->setRowHeight(22);

            $sheet->setCellValue("B" . ($rowNum + 1), "Stock Awal");
            $sheet->setCellValue("C" . ($rowNum + 1), "Penggunaan");
            $sheet->setCellValue("D" . ($rowNum + 1), "Stock Akhir");

            $sheet->getStyle("B" . ($rowNum + 1) . ":D" . ($rowNum + 1))->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9']
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            if ($rowNum === 3) {
                $sheet->setAutoFilter("A" . ($rowNum + 1) . ":F" . ($rowNum + 1));
            }

            $rowNum += 2;

            $startRow = $rowNum;
            $total = 0;

            foreach ($items as $item) {
                $sheet->setCellValue("B$rowNum", $item['qty']);
                $sheet->setCellValue("C$rowNum", $item['qty']);
                $sheet->setCellValue("D$rowNum", 0);
                $sheet->setCellValue("E$rowNum", $item['area']);
                $sheet->setCellValue("F$rowNum", $item['note']);

                $total += $item['qty'];
                $rowNum++;
            }

            $endRow = $rowNum - 1;

            $sheet->mergeCells("A$startRow:A$endRow");
            $sheet->setCellValue("A$startRow", $leader);
            $sheet->getStyle("A$startRow")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->setCellValue("A$rowNum", "TOTAL");
            $sheet->setCellValue("B$rowNum", $total);
            $sheet->setCellValue("C$rowNum", $total);
            $sheet->setCellValue("D$rowNum", 0);

            $sheet->getStyle("A$rowNum:F$rowNum")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFF00']
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);

            $lastDataRow = $rowNum;
            $rowNum += 2;
        }

        $sheet->getStyle("A3:F$lastDataRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ]);

        $sheet->getStyle("B5:D$lastDataRow")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle("E5:F$lastDataRow")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);

        for ($i = 3; $i <= $lastDataRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(24);
        }

        $sheet->freezePane('A6');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=detail_$bulan.xlsx");

        $writer->save('php://output');
        exit;
    }

    public function exportMutasi()
    {
        if (!can('laporan_export')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak punya akses export'
            ]);
        }

        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $start = $bulan . '-01 00:00:00';
        $end   = date('Y-m-t 23:59:59', strtotime($bulan));

        $data = $this->db->table('mutasi_stok m')
            ->select('
            m.created_at,
            b.code,
            b.nama,
            m.tipe,
            m.qty_before,
            m.selisih,
            m.qty_after,
            u.nama as user
        ')
            ->join('barang b', 'b.id = m.barang_id')
            ->join('users u', 'u.id = m.created_by', 'left')
            ->where('m.created_at >=', $start)
            ->where('m.created_at <=', $end)
            ->orderBy('m.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', "LAPORAN MUTASI STOK " . strtoupper(date('F Y', strtotime($bulan))));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headers = ['Tanggal', 'Barang', 'Tipe', 'Stok Sebelum', 'Selisih', 'Stok Sesudah', 'User'];
        $row = 3;
        $col = 'A';

        foreach ($headers as $h) {
            $sheet->setCellValue($col . $row, $h);
            $col++;
        }

        $sheet->getStyle("A$row:G$row")->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        $row++;

        $isAlt = false;

        foreach ($data as $item) {

            if ($isAlt) {
                $sheet->getStyle("A$row:G$row")->getFill()->setFillType(
                    \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
                )->getStartColor()->setRGB('F9F9F9');
            }
            $isAlt = !$isAlt;

            $sheet->setCellValue("A$row", $item['created_at']);

            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $bold = $richText->createTextRun($item['code']);
            $bold->getFont()->setBold(true);
            $richText->createTextRun(" - " . $item['nama']);

            $sheet->setCellValue("B$row", $richText);

            $sheet->getStyle("B$row")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setWrapText(true)
                ->setIndent(1);

            $tipe = strtolower($item['tipe']);
            $sheet->setCellValue("C$row", ucfirst($tipe));

            $color = 'CCCCCC';
            if ($tipe === 'masuk') $color = '5B9BD5';
            if ($tipe === 'keluar') $color = 'E74C3C';
            if ($tipe === 'opname') $color = 'F5B041';

            $sheet->getStyle("C$row")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color]
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);

            $sheet->setCellValue("D$row", $item['qty_before']);
            $sheet->setCellValue("E$row", $item['selisih']);
            $sheet->setCellValue("F$row", $item['qty_after']);
            $sheet->setCellValue("G$row", $item['user']);

            $sheet->getStyle("A$row:G$row")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->getStyle("B$row")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            if ($item['selisih'] > 0) {
                $sheet->getStyle("E$row")->getFont()->getColor()->setRGB('008000');
            } elseif ($item['selisih'] < 0) {
                $sheet->getStyle("E$row")->getFont()->getColor()->setRGB('FF0000');
            }

            $row++;
        }

        $lastRow = $row - 1;

        $sheet->getStyle("A3:G$lastRow")->applyFromArray([
            'borders' => [
                'inside' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ]
        ]);

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(20);

        for ($i = 3; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(25);
        }

        $sheet->setAutoFilter("A3:G3");

        $sheet->freezePane('A4');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=mutasi_$bulan.xlsx");

        $writer->save('php://output');
        exit;
    }
}
