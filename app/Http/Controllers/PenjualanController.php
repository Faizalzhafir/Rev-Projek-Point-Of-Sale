<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use PDF;

class PenjualanController extends Controller
{
    public function index() {
        return view('Penjualan.index');
    }

    public function data() {
        $penjualan = Penjualan::with('member')->where('bayar', '>', '0')->orderBy('id_penjualan', 'desc')->get();
        //untuk where,maka akan menampilkan bayar yang lebih dari 0,jika tidak,transaksi yang belum diinputkan,muncul di data,dan bernilai null


        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. ' . format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. ' . format_uang($penjualan->bayar);
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->addColumn('kode_member', function ($penjualan) {
                $member = $penjualan->member->kode_member ?? '';
                return '<span class="label label-success">'. $member .'</span>';
            })
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            //?? ''. apabila tidak ada datnya,maka rollback atau hasilkan string kosong
            ->addColumn('action', function ($penjualan) {
                return '
                <div class="btn-group">                
                    <button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteForm(`'. route('penjualan.destroy', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })
            ->rawColumns(['action', 'kode_member'])
            ->make(true);

        //fungsi untuk menampilkan data yang tadinya sudah diiputkan,yang diurutkan berdasarkan data terbaru,menggunakan datatbles
        //apabila ingin memberikan spesifikasi pada kolom tertentu,maka tambahkan addColumn,lalu return dengan aksi yang akan dipakai nantinya dikolom tersebut
    }


    public function create() {
        $penjualan = new Penjualan();
        $penjualan->id_member = null;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan->bayar = 0;
        $penjualan->diterima = 0;
        $penjualan->id_user = auth()->id(); //id user ambil berdasrkan login,menggunakan auth,jadi mendapatkan id berdasrkan user yang aktif
        $penjualan->save();


        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');

    }

    public function store(Request $request) {
        $penjualan = Penjualan::findOrFail($request->id_penjualan);
        $penjualan->id_member = $request->id_member;
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        $penjualan->diskon = $request->diskon;
        $penjualan->bayar = $request->bayar;
        $penjualan->diterima = $request->diterima;
        $penjualan->update();
    
        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $product = Product::find($item->id_produk);
            $product->stok -= $item->jumlah;
            $product->update();
        }
    
        return redirect()->route('transaksi.selesai');
    }
    

    public function show($id) {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
        ->of($detail)
        ->addIndexColumn()
        ->addColumn('kode_produk', function ($detail) {
            return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
        })
        ->addColumn('nama_produk', function ($detail) {
            return  $detail->produk->nama_produk;
        })
        ->addColumn('harga_jual', function ($detail) {
            return 'Rp. ' . format_uang($detail->harga_jual);
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
        $penjualan = Penjualan::find($id); 
        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $item->delete();
        } //penjualan pada detail penjualan

        $penjualan->delete(); //delete pada daftar penjualan

        return response(null, 204);
    }

    public function selesai() {
        $setting = Setting::first(); //setting digunakan untuk membuat pengaturan perihal nota yang akan dicetak

        return view('Penjualan.selesai', compact('setting'));
    } 

    public function notaKecil() {
        $setting = Setting::first(); //membuat pengaturan agar mengambil data yang perlu ditampilkan di nota dari setting
        $penjualan = Penjualan::find(session('id_penjualan')); //variabel untuk mencarikan id,yang nantinya berfungsi untuk session
        if (! $penjualan) {
            abort(404);
        } //kondisi jika varibael penjualan habis sessionnya,atau ada kesalahan,maka tampilkan 404
        $detail = PenjualanDetail::with('produk') //membuat oengaturan agar data produk dapat diakses,dan ditampilakn di nota
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        return view('Penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    } 

    public function notaBesar() {
        $setting = Setting::first(); //membuat pengaturan agar mengambil data yang perlu ditampilkan di nota dari setting
        $penjualan = Penjualan::find(session('id_penjualan')); //variabel untuk mencarikan id,yang nantinya berfungsi untuk session
        if (! $penjualan) {
            abort(404);
        } //kondisi jika varibael penjualan habis sessionnya,atau ada kesalahan,maka tampilkan 404
        $detail = PenjualanDetail::with('produk') //membuat oengaturan agar data produk dapat diakses,dan ditampilakn di nota
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        $pdf = PDF::loadView('Penjualan.nota_besar', compact('setting', 'penjualan', 'detail')); //variabel yang digunakan untuk memuat halaman berbentuk pdf
        $pdf->setPaper(0,0,609,460, 'potrait'); //mengatur set ukuran kertas yang ditampilkan
        return $pdf->stream('Transaksi' . date('Y-m-d-his') .'.pdf'); //mengatur nama kertas,pada saat diunduh
    }
}
