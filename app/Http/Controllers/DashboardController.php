<?php

namespace App\Http\Controllers;

use App\Models\Pendapatan;
use App\Models\Kategori;
use App\Models\Member;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        
        // Hitung jumlah kategori, produk, member, dan supplier
        $kategori = Kategori::count();
        $produk = Produk::count();
        $supplier = Supplier::count();
        $member = Member::count();

        // Atur tanggal awal dan akhir
        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');
        $total_pengeluaran = 0;

        // Hitung data pendapatan berdasarkan tanggal
        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            // Hitung total penjualan, pembelian, dan pengeluaran
            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            // Hitung pendapatan dan simpan dalam array
            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] = $pendapatan; // Gunakan = bukan += untuk menambahkan elemen baru

            // Ubah tanggal untuk iterasi selanjutnya
            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        // Hitung total pendapatan dari data yang telah dikumpulkan
        $total_pendapatan = array_sum($data_pendapatan);

        // Render view berdasarkan level user
        if (auth()->user()->level == 1) {
            return view('admin.dashboard', compact('kategori', 'produk', 'supplier', 'member', 'tanggal_awal', 'tanggal_akhir', 'data_tanggal', 'data_pendapatan', 'total_pendapatan', 'total_pengeluaran'));
        } else {
            return view('kasir.dashboard');
        }
    }
} 
