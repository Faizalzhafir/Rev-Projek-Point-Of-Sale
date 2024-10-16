<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index() {
        $supplier = Supplier::orderBy('nama')->get();

        return view('Pembelian.index', compact('supplier'));
    }

    public function data() {
        $pembelian = Pembelian::where('bayar', '>', '0')->orderBy('id_pembelian', 'desc')->get();
         //untuk where,maka akan menampilkan bayar yang lebih dari 0,jika tidak,transaksi yang belum diinputkan,muncul di data,dan bernilai null,tetapi perlu dilihat kembali didatabase,apakah terhapus atau tidak,untuk field nya kosong

        return datatables()
            ->of($pembelian)
            ->addIndexColumn()
            ->addColumn('total_item', function ($pembelian) {
                return format_uang($pembelian->total_item);
            })
            ->addColumn('total_harga', function ($pembelian) {
                return 'Rp. ' . format_uang($pembelian->total_harga);
            })
            ->addColumn('bayar', function ($pembelian) {
                return 'Rp. ' . format_uang($pembelian->bayar);
            })
            ->addColumn('tanggal', function ($pembelian) {
                return tanggal_indonesia($pembelian->created_at, false);
            })
            ->addColumn('supplier', function ($pembelian) {
                return $pembelian->supplier->nama;
            })
            ->editColumn('diskon', function ($pembelian) {
                return $pembelian->diskon . '%';
            })
            ->addColumn('action', function ($pembelian) {
                return '
                <div class="btn-group">                
                    <button onclick="showDetail(`'. route('pembelian.show', $pembelian->id_pembelian) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteForm(`'. route('pembelian.destroy', $pembelian->id_pembelian) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })
            ->rawColumns(['action'])
            ->make(true);

        //fungsi untuk menampilkan data yang tadinya sudah diiputkan,yang diurutkan berdasarkan data terbaru,menggunakan datatbles
        //apabila ingin memberikan spesifikasi pada kolom tertentu,maka tambahkan addColumn,lalu return dengan aksi yang akan dipakai nantinya dikolom tersebut
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

    public function store (Request $request) {
        $pembelian = Pembelian::findOrFail($request->id_pembelian);
        $pembelian->total_item = $request->total_item; 
        $pembelian->total_harga = $request->total; 
        $pembelian->diskon = $request->diskon; 
        $pembelian->bayar = $request->bayar; 
        $pembelian->update();
        //fungsi untuk mengrimkan data ke halaman index pembelian,pada daftar pembelian

       
        $detail = PembelianDetail::where('id_pembelian', $pembelian->id_pembelian)->get();
        foreach ($detail as $item) {
            $product = Product::find($item->id_produk);
            $product->stok += $item->jumlah;
            $product->update();
        }
        //fungsi untuk memperbarui stok produk yang ada di halaman produk

        return redirect()->route('pembelian.index');
    } //findporfail,jika senaja memasukan data pada saat penginputan,maka akan muncul 404

    public function show($id) {
        $detail = PembelianDetail::with('produk')->where('id_pembelian', $id)->get();

        return datatables()
        ->of($detail)
        ->addIndexColumn()
        ->addColumn('kode_produk', function ($detail) {
            return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
        })
        ->addColumn('nama_produk', function ($detail) {
            return  $detail->produk->nama_produk;
        })
        ->addColumn('harga_beli', function ($detail) {
            return 'Rp. ' . format_uang($detail->harga_beli);
        })
        ->addColumn('jumlah', function ($detail) {
            return  format_uang($detail->jumlah);
        })
        ->addColumn('subtotal', function ($detail) {
            return 'Rp. ' . format_uang($detail->subtotal);
        })
        ->rawColumns(['kode_produk'])
        ->make(true);
    }

    public function destroy($id) {
        $pembelian = Pembelian::find($id); 
        $detail = PembelianDetail::where('id_pembelian', $pembelian->id_pembelian)->get();
        //Perulangan yang digunakan apabila jumlah produk di data daftar pembelian dihapus,kembali menjadi jumlah awal
        foreach ($detail as $item) {
            $product = Product::find($item->id_produk);
            if ($product) {
                $product->stok -= $item->jumlah;
                $product->update();
            }
            $item->delete();
        } //pembelian pada detail pembelian

        $pembelian->delete(); //delete pada daftar pembelian

        return response(null, 204);
    }
}

