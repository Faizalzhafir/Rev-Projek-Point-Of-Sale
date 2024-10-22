<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Member;
use App\Models\Setting;
use App\Models\PenjualanDetail;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    public function index() {
        $product = Product::orderBy('nama_produk')->get();
        $member = Member::orderBy('nama')->get();
        $diskon = Setting::first()->diskon ?? 0;

        
        // Cek apakah ada transaksi yang sedang berjalan
       if ($id_penjualan = session('id_penjualan' )) {
            $penjualan = Penjualan::find($id_penjualan);
            $memberSelected = $penjualan->member ?? new Member();
            
            return view('penjualan_detail.index', compact('product', 'member', 'diskon', 'id_penjualan', 'memberSelected', 'penjualan')); 
       } else {    
            if (auth()->user()->level == 0) {
                return redirect()->route('transaksi.baru'); 
            } else {
                return redirect()->route('dashboard');
            }
       }
       
    }

    public function data($id)
{
    $detail = PenjualanDetail::with('produk')
        ->where('id_penjualan', $id)
        ->get();

    // Ambil member terkait dengan penjualan
    $penjualan = Penjualan::find($id);
    $memberSelected = $penjualan->member ?? new Member();

    $data = array();
    $total = 0;
    $total_item = 0;
    // $total_diskon_produk = 0;
    // $total_diskon_member = 0;

    foreach ($detail as $item) {
        $row = array();
        $row['kode_produk'] = '<span class="label label-success">' . $item->produk['kode_produk'] . '</span>';
        $row['nama_produk'] = $item->produk['nama_produk'];
        $row['harga_jual']  = 'Rp. ' . format_uang($item->harga_jual);
        $row['stok']        = '<span class="stok">'.$item->produk->stok.'</span>';
        $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_penjualan_detail .'"  value="' . $item->jumlah .'">';
        $row['diskon']      = $item->produk->diskon . '%';
        $row['subtotal']    = 'Rp. ' . format_uang($item->subtotal);
        $row['action']      = '<div class="btn-group">
                                        <button onclick="deleteForm(`'. route('transaksi.destroy', $item->id_penjualan_detail).'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                   </div>';
        $data[] = $row;

        // $total += $item->harga_jual * $item->jumlah;
        $total += $item->harga_jual * $item->jumlah - (($item->diskon * $item->jumlah) / 100 * $item->harga_jual);
        $total_item += $item->jumlah;
    }

    $data[] = [
        'kode_produk' => '
            <div class="total hide">'. $total .'</div> 
            <div class="total_item hide">'. $total_item .'</div>',
        'nama_produk' => '',
        'harga_jual'  => '',
        'stok'        => '',
        'jumlah'      => '',
        'diskon'      => '',
        'subtotal'    => '',
        'action'      => '',
    ];

    return datatables()
        ->of($data)
        ->addIndexColumn()
        ->rawColumns(['action', 'kode_produk', 'jumlah', 'stok'])
        ->make(true);
}
    
    public function store(Request $request) {
        $product = Product::where('kode_produk', $request->kode_produk)->first();
        //kondisi untuk mengecek produk,jika variabel product bernilai false,maka kondisi bernilai true,karena menggunakan logika not
        if (! $product) {
            return response()->json('Data gagal disimpan', 404);
        }
        //kondisi jika stok yang ada diproduk kurang dari jumlah maka kondisi dijalankan
        if ($product->stok < $request->jumlah) {
            return response()->json(['message' => 'Stok produk tidak mencukupi!'], 400); 
        }
        $penjualanId = $request->id_penjualan ?? session('id_penjualan'); //variabel diambil dari dari fitur request dan sessio,dan menggunakan logika null coalescing operator,untuk mengeek apakah nilai disebelah kiri ada (tidak null),jika tidak maka akan menggunakan nilai yang disebelah kanan
        //Session adalah mekanisme penyimpanan data sementara di server, yang memungkinkan aplikasi web untuk melacak informasi pengguna dari satu permintaan ke permintaan lainnya.Jadi bisa dibilang sebagai tempat penyimpanan dari sisi server,jika suatu aksi telah dilakuan (penyimpanan session),maka fungsi tersebut akan melacakinformasi pengguna antar permintaan 
        //kondisi untuk mengecek nilai ID jika variabel penjualan Id bernilai false,maka kondisi bernilai true,karena menggunakana logika not
        if (!$penjualanId) {
         return response()->json(['message' => 'ID Penjualan tidak ditemukan'], 400);
        }

        // Cek apakah produk sudah ada dalam detail penjualan
        $detail = PenjualanDetail::where('id_penjualan', $penjualanId)
                    ->where('id_produk', $product->id_produk)
                    ->first();
    
        if ($detail) {
            // Jika produk sudah ada, perbarui jumlah dan subtotal
            $detail->jumlah += 1;
            $detail->subtotal = ($detail->harga_jual - ($detail->harga_jual * $detail->diskon / 100)) * $detail->jumlah;
            $detail->save();
        } else {
            // Jika produk belum ada, buat detail baru
            
            $detail = new PenjualanDetail();
            $detail->id_penjualan = $request->id_penjualan;
            $detail->id_produk = $product->id_produk;
            $detail->harga_jual = $product->harga_jual;
            $detail->jumlah = 1; 
            $detail->diskon = $product->diskon; //mengambil data diskon yang ada di produk,dari variabel detail
            $detail->subtotal = ($product->harga_jual * $detail->jumlah) - (($product->diskon / 100) * ($product->harga_jual * $detail->jumlah)) ; //mengambil subtotal hasil operasi harga jual dikurangi diskon yang dibagi 100,dikalikan dengan harga jualnya,ditambah dengan perhitungan dari setiap produknya
            $detail->save();
            
        }
        return response()->json( 'Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id) {
        $detail = PenjualanDetail::find($id);
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_jual * $request->jumlah - (($detail->diskon * $request->jumlah) / 100 * $detail->harga_jual); 
        $detail->save();
    }

    public function destroy($id)
    {
        $detail = PenjualanDetail::find($id);
        $detail->delete();
       
        return response(null, 204);
    }

    public function loadForm($diskon = 0, $total, $diterima) {
        $id_penjualan = session('id_penjualan'); // Ambil ID penjualan dari session
        $detailPenjualan = PenjualanDetail::where('id_penjualan', $id_penjualan)->get();

        $totalDiskon = 0;

        foreach ($detailPenjualan as $item) {
            //Hitung diskon per item
            $diskonItem = ($item->diskon / 100) * $item->harga_jual * $item->jumlah;
            $totalDiskon += $diskonItem; //Tambahkan ke total diskon
        }

        $diskonMember = $total * ($diskon / 100);
        $totalDiskon += $diskonMember;
        $bayar = $total - ($diskon / 100 * $total);
        $kembali = ($diterima != 0) ? $diterima - $bayar : 0;
        $data = [
            'totalrp' => format_uang($total),
            'diskonrp' => format_uang($totalDiskon),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar). ' Rupiah'),
            'kembalirp' => format_uang($kembali),
            'kembali_terbilang' => ucwords(terbilang($kembali). ' Rupiah')
        ];

        return response()->json($data);
    }

    public function checkStok($id_produk, $jumlah)
    {
        $product = Product::find($id_produk);

        if ($product->stok < $jumlah) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jumlah yang diminta melebihi stok yang tersedia!',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Stok tersedia!',
        ]);
    }
    //disini jumlah merupakan parameter yang dideklarasikan sebelumnya di halaman index penjualan detail,menggunakan ajax request
}
