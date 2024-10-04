<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Member;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        // Hitung jumlah model yang relevan
        $category = Category::count();
        $product = Product::count();
        $supplier = Supplier::count();
        $member = Member::count();
    
        // Set tanggal awal dan akhir
        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $tanggal_awal_asli = date('Y-m-01');
        $tanggal_akhir_asli = date('Y-m-d');

        $tanggal_awal = $tanggal_awal_asli;
        $tanggal_akhir = $tanggal_akhir_asli;
    
        // Inisialisasi array untuk menyimpan data tanggal dan pendapatan
        $data_tanggal = array();
        $data_pendapatan = array();
    
        // Loop melalui setiap hari dalam rentang tanggal
        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            // Simpan hari dalam bulan
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);
            
            // Hitung total penjualan, pembelian, dan pengeluaran
            $total_penjualan = Penjualan::whereDate('created_at', $tanggal_awal)->sum('bayar');
            $total_pembelian = Pembelian::whereDate('created_at', $tanggal_awal)->sum('bayar');
            $total_pengeluaran = Pengeluaran::whereDate('created_at', $tanggal_awal)->sum('nominal');
    
            // Hitung pendapatan
            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] = $pendapatan; // Memperbaiki penugasan nilai
    
            // Pindah ke hari berikutnya
            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }
    
        // Cek jika pengguna sudah login
        if (!auth()->check()) {
            return redirect()->route('login'); // Alihkan ke halaman login jika belum login
        }
    
        // Muat tampilan yang sesuai berdasarkan level pengguna
        if (auth()->user()->level == 1) {
            return view('Admin.dashboard', compact('category', 'product', 'supplier', 'member', 'data_tanggal', 'tanggal_awal_asli', 'tanggal_akhir_asli', 'data_pendapatan'));
        } else {
            return view('Kasir.dashboard');
        }
    }
    
    
}
