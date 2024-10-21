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

    // Set judul header aplikasi
    $sheet->mergeCells('A1:F1');
    $sheet->setCellValue('A1', 'Laporan Pendapatan SMKN 1 Kadipaten');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Set tanggal laporan
    $sheet->mergeCells('A2:F2');
    $sheet->setCellValue('A2', 'Periode: ' . tanggal_indonesia($awal, false) . ' s/d ' . tanggal_indonesia($akhir, false));
    $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Set judul kolom
    $sheet->setCellValue('A4', 'No');
    $sheet->setCellValue('B4', 'Tanggal');
    $sheet->setCellValue('C4', 'Penjualan');
    $sheet->setCellValue('D4', 'Pembelian');
    $sheet->setCellValue('E4', 'Pengeluaran');
    $sheet->setCellValue('F4', 'Pendapatan');

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
    $sheet->getStyle('A4:F4')->applyFromArray($headerStyleArray);
    $sheet->getRowDimension('4')->setRowHeight(20); // Set tinggi baris header

    // Set lebar kolom secara otomatis
    foreach (range('A', 'F') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Isi data ke dalam sheet
    $rowIndex = 5;
    $total_penjualan = 0;
    $total_pembelian = 0;
    $total_pengeluaran = 0;
    $total_pendapatan = 0;

    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rowIndex, $row['DT_RowIndex']);
        $sheet->setCellValue('B' . $rowIndex, $row['tanggal']);
        $sheet->setCellValue('C' . $rowIndex, $row['penjualan']);
        $sheet->setCellValue('D' . $rowIndex, $row['pembelian']);
        $sheet->setCellValue('E' . $rowIndex, $row['pengeluaran']);
        $sheet->setCellValue('F' . $rowIndex, $row['pendapatan']);

        // Konversi string menjadi float untuk penjumlahan
        $total_penjualan += (float)str_replace(',', '', $row['penjualan']);
        $total_pembelian += (float)str_replace(',', '', $row['pembelian']);
        $total_pengeluaran += (float)str_replace(',', '', $row['pengeluaran']);
        $total_pendapatan += (float)str_replace(',', '', $row['pendapatan']);

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

    // Tambahkan baris total di bawah data
    $sheet->setCellValue('A' . $rowIndex, '');
    $sheet->setCellValue('B' . $rowIndex, 'Total');
    $sheet->setCellValue('C' . $rowIndex, format_uang($total_penjualan));
    $sheet->setCellValue('D' . $rowIndex, format_uang($total_pembelian));
    $sheet->setCellValue('E' . $rowIndex, format_uang($total_pengeluaran));
    $sheet->setCellValue('F' . $rowIndex, format_uang($total_pendapatan));

    // Menambahkan style untuk baris total
    $sheet->getStyle('A' . $rowIndex . ':F' . $rowIndex)->applyFromArray([
        'font' => ['bold' => true],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ]);

    // Buat nama file
    $fileName = 'Laporan-Pendapatan-SMKN1-' . $awal . '-sd-' . $akhir . '.xlsx';

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
    $total_penjualan_all = 0;
    $total_pembelian_all = 0;
    $total_pengeluaran_all = 0;

    while (strtotime($awal) <= strtotime($akhir)) {
        $tanggal = $awal;
        $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

        $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
        $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
        $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

        $total_penjualan_all += $total_penjualan;
        $total_pembelian_all += $total_pembelian;
        $total_pengeluaran_all += $total_pengeluaran;

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
        'penjualan' => 'Total Penjualan : ' . format_uang($total_penjualan_all),
        'pembelian' => 'Total Pembelian : ' . format_uang($total_pembelian_all),
        'pengeluaran' => 'Total Pengeluaran : ' . format_uang($total_pengeluaran_all),
        'pendapatan' => 'Total Pendapatan : ' . format_uang($total_pendapatan),
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
