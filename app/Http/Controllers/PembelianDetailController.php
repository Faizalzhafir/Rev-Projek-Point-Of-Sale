<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PembelianDetailController extends Controller
{
    public function index() {

        $id_pembelian = session('id_pembelian'); //perlu untuk menampilkan id dari pembeliannya
        $product = Product::orderBy('nama_produk')->get(); //membuat varibael baru untuk mendapatkan daftar produk agar bisa diinputkan ke halaman detail pebelian
        $supplier = Supplier::find(session('id_supplier')); //dikarenakan tampilan detail pembelian memuat satu supplier pertransaksi makaharus dicari terlebih dahulu id dari supplier yang dipilh

        if (! $supplier) {
            abort(404);
        } //kondisi untuk menghasilkan 404,jika data supplier tidak dipilih

        return view('Pembelian_detail.index', compact('id_pembelian', 'product', 'supplier'));
    }
}
