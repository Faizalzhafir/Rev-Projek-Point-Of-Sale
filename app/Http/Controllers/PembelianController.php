<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index() {
        $supplier = Supplier::orderBy('nama')->get();

        return view('Pembelian.index', compact('supplier'));
    }

    public function create($id) {
        $pembelian = new Pembelian();
        $pembelian->id_supplier = $id;
        $pembelian->total_item  = 0;
        $pembelian->total_harga = 0;
        $pembelian->diskon      = 0;
        $pembelian->bayar       = 0;
        $pembelian->save(); 
        //pembuatan variabel untuk membuat data pada daftar pembelian yang baru nanti,untuk diinputkan 

        session(['id_pembelian' => $pembelian->id_pembelian]);
        session(['id_supplier' => $pembelian->id_supplier]);
        //membuat session agar pada saat menekan tombol transaksi baru untuk memanggil id_pembelian dan id_supplier

        return redirect()->route('pembelian_detail.index');
        //route ke tampilan pembelian detail
    }
}
