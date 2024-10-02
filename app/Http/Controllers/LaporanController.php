<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends Controller
{
    public function exportExcel($awal, $akhir)
{
    // Ambil data
    $data = $this->getData($awal, $akhir);

    // Buat Spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set judul kolom
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Tanggal');
    $sheet->setCellValue('C1', 'Penjualan');
    $sheet->setCellValue('D1', 'Pembelian');
    $sheet->setCellValue('E1', 'Pengeluaran');
    $sheet->setCellValue('F1', 'Pendapatan');

    // Menambahkan Style untuk Header (Tebal, Tengah, dan Berwarna)
    $headerStyleArray = [
        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF4CAF50'], // Warna latar belakang hijau
        ],
    ];
    $sheet->getStyle('A1:F1')->applyFromArray($headerStyleArray);
    $sheet->getRowDimension('1')->setRowHeight(20); // Set tinggi baris header

    // Set lebar kolom secara otomatis
    foreach (range('A', 'F') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Isi data ke dalam sheet
    $rowIndex = 2;
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rowIndex, $row['DT_RowIndex']);
        $sheet->setCellValue('B' . $rowIndex, $row['tanggal']);
        $sheet->setCellValue('C' . $rowIndex, $row['penjualan']);
        $sheet->setCellValue('D' . $rowIndex, $row['pembelian']);
        $sheet->setCellValue('E' . $rowIndex, $row['pengeluaran']);
        $sheet->setCellValue('F' . $rowIndex, $row['pendapatan']);

        // Menambahkan border untuk setiap baris data
        $sheet->getStyle('A' . $rowIndex . ':F' . $rowIndex)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Alternatif warna latar belakang untuk setiap baris
        if ($rowIndex % 2 == 0) {
            $sheet->getStyle('A' . $rowIndex . ':F' . $rowIndex)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE8F5E9'], // Warna hijau muda
                ],
            ]);
        }

        $rowIndex++;
    }

    // Buat nama file
    $fileName = 'Laporan-Pendapatan-' . $awal . '-sd-' . $akhir . '.xlsx';

    // Set header agar file bisa diunduh
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    // Simpan file dalam format Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $total_pendapatan += $pendapatan;

            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($tanggal, false);
            $row['penjualan'] = format_uang($total_penjualan);
            $row['pembelian'] = format_uang($total_pembelian);
            $row['pengeluaran'] = format_uang($total_pengeluaran);
            $row['pendapatan'] = format_uang($pendapatan);

            $data[] = $row;
        }

        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'penjualan' => '',
            'pembelian' => '',
            'pengeluaran' => 'Total Pendapatan',
            'pendapatan' => format_uang($total_pendapatan),
        ];

        return $data;
    }

    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }
}
