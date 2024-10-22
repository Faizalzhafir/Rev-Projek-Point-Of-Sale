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
        //untuk where,maka akan menampilkan bayar yang lebih dari 0,jika tidak,transaksi yang belum diinputkan,muncul di data,dan bernilai null,tetapi perlu dilihat kembali didatabase,apakah terhapus atau tidak,untuk field nya kosong


        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('no_transaksi', function ($penjualan) {
                return str_pad($penjualan->id_penjualan, 10, '0', STR_PAD_LEFT);
            })
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
            ->editColumn('total_diskon', function ($penjualan) {
                return 'Rp. ' . format_uang($penjualan->total_diskon);
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            //?? ''. apabila tidak ada datnya,maka rollback atau hasilkan string kosong
            ->addColumn('action', function ($penjualan) {
                return '
                <div class="btn-group">                
                    <button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="window.location.href=\''. route('penjualan.edit', $penjualan->id_penjualan) . '\'" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-pencil"></i></button>
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
        $penjualan->total_diskon = 0;
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
        $penjualan->diterima = str_replace('.', '', $request->diterima);
    
        // Menentukan total diskon
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $penjualan->id_penjualan)->get();
        $totalDiskon = 0;
        $total = 0;
    
        foreach ($detail as $item) {
            // Hitung diskon per item
            $diskonItem = ($item->diskon / 100) * $item->harga_jual * $item->jumlah;
            $totalDiskon += $diskonItem;
    
            $total += $item->harga_jual * $item->jumlah - (($item->diskon * $item->jumlah) / 100 * $item->harga_jual);
        }
    
        // Periksa apakah ada member yang diinput
        if ($penjualan->id_member) {
            // Ambil diskon member dari pengaturan
            $diskonMember = Setting::first()->diskon ?? 0;
            $diskonMember = $total * ($diskonMember / 100); // Hitung diskon member
        } else {
            // Jika tidak ada member, diskon member 0
            $diskonMember = 0;
        }
    
        $totalDiskon += $diskonMember; //Panggil diskonMember untuk ditambahkan ke variabel totalDiskon
    
        $penjualan->total_diskon = $totalDiskon; // Update kolom total diskon
        $penjualan->update();
    
        // Update stok produk setelah transaksi
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

    public function edit($id) {
        // Logika untuk mengambil data penjualan berdasarkan ID
        $penjualan = Penjualan::findOrFail($id);
        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        $product = Product::all();

        return view('Penjualan.edit', compact('penjualan', 'detail', 'product'));
    }

    public function update(Request $request, $id) {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        $penjualan->bayar = $request->bayar;
        $penjualan->diterima = str_replace(',','.', $request->diterima);

        // Menentukan total diskon
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $penjualan->id_penjualan)->get();
        $totalDiskon = 0;
        $total = 0;
    
        foreach ($detail as $item) {
            // Hitung diskon per item
            $diskonItem = ($item->diskon / 100) * $item->harga_jual * $item->jumlah;
            $totalDiskon += $diskonItem;
    
            $total += $item->harga_jual * $item->jumlah - (($item->diskon * $item->jumlah) / 100 * $item->harga_jual);
        }
    
        // Periksa apakah ada member yang diinput
        if ($penjualan->id_member) {
            // Ambil diskon member dari pengaturan
            $diskonMember = Setting::first()->diskon ?? 0;
            $diskonMember = $total * ($diskonMember / 100); // Hitung diskon member
        } else {
            // Jika tidak ada member, diskon member 0
            $diskonMember = 0;
        }
    
        $totalDiskon += $diskonMember; //Panggil diskonMember untuk ditambahkan ke variabel totalDiskon
    
        $penjualan->total_diskon = $totalDiskon; // Update kolom total diskon
        $penjualan->update();

        return redirect()->route('penjualan.index')->with('success', 'Transaksi berhasil disimpan');
        
    }

    public function destroy($id) {
        $penjualan = Penjualan::find($id); 
        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        //Perulangan yang digunakan untuk pengembalian stok produk,jika di daftar penjualan dihapus
        foreach ($detail as $item) {
            $product = Product::find($item->id_produk);
            if ($product) {
                $product->stok += $item->jumlah;
                $product->update();
            }

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
