<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all()->pluck('nama_kategori', 'id_kategori'); //mengonversi jadi array

        return view('Product.index', compact('category'));
    } 

    public function data() {
        $product = Product::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->orderBy('stok', 'asc')
            ->get();

        return datatables()
            ->of($product)
            ->addIndexColumn()
            ->addColumn('select_all', function ($product) {
                return '
                    <input type="checkbox" name="id_produk[]" value="'. $product->id_produk .'">
                ';
            })
            ->addColumn('kode_produk', function ($product) {
                return '<span class="label label-success">'. $product->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($product) {
                //Format angka dengan pemisah ribuan
                return 'Rp. ' . number_format($product->harga_beli, 0, ',', '.');
            })
            ->addColumn('harga_jual', function ($product) {
                //Format angka dengan pemisah ribuan
                return 'Rp. ' . number_format($product->harga_jual, 0, ',', '.');
            })
            ->addColumn('diskon', function ($product) {
                return $product->diskon . '%';
            })
            ->addColumn('stok', function ($product) {
                return format_uang($product->stok);
            })
            ->addColumn('keterangan', function ($product) {
                if ($product->stok < 1) {
                    return '<span class="label label-danger">'. 'STOK HABIS' .'</span>';
                } elseif ($product->stok < 20) {
                    return '<span class="label label-warning">'. 'STOK MENIPIS' .'</span>';
                } elseif ($product->stok < 50 ) {
                    return '<span class="label label-success">'. 'STOK CUKUP' .'</span>';
                } else {
                    return  '<span class="label label-primary">'. 'STOK BANYAK' .'</span>';
                } //jika variabel produk bagian stok kurang dari 1,maka tampilkan codenya
                return '';  //Jika stok masih ada, sebaiknya mengembalikan nilai yang jelas (misalnya, string kosong).
            }) //fungsi untuk membuat keterangan stok habis, keterangan merupakan nama column dan nantinya ditampilkan di data,lalu fungsi produk menerima dari variabel produk,yang nantinya dapat menentukan data apa yang akan dimanipulasi dan dipakai
            ->addColumn('action', function ($product) {
                return '
                <div class="btn-group">
                    <button  type="button" onclick="editForm(`'. route('product.update', $product->id_produk) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-pencil"></i></button>                
                    <button  type="button" onclick="deleteForm(`'. route('product.destroy', $product->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })
            ->rawColumns(['action', 'kode_produk', 'select_all', 'keterangan', 'diskon' ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if a product with the same name already exists
        // $existingProduct = Product::where('nama_produk', $request->nama_produk)->first();

        // if ($existingProduct) {
        //     // Return a response indicating the product name already exists
        //     return response()->json(['error' => 'Nama produk sudah ada'], 400);
        // }

        // Hilangkan titik pada harga_beli dan harga_jual sebelum disimpan
        $request->merge([
            'harga_beli' => str_replace('.', '', $request->harga_beli),
            'harga_jual' => str_replace('.', '', $request->harga_jual),
            //merge berfungsi untuk mengganti atau memodifikasi data yang ada didalam objek request,dimana request menyimpan data yang sebelumnya telah diinputkan (dari form),dalam hal ini merge menggantikan nilai harga_beli dan harga_jual setelah dimodifikasi
            //str_replace berfungsi untuk mengganti seluruh kemunculan karakter dalam nilai request dalam hal ini,titik diganti menjadi tidak ada pada variabel request yang diminta untuk diganti
            //kenapa fungsi ini dijalnakan?karena untuk membuat format string yang dikirimkan ke database sesuai dengan format numerik,tanpa ada titik (pemisah ribuan dari index)
        ]);

        $product = Product::latest()->first() ?? new Product();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$product->id_produk + 1, 6); // Membuat kode produk unik
        $product = Product::create($request->all());
         //apabila kita menemukan null attempt to read property,itu karena pada saat pengisian memeberikan nilai null,sehingga menghasilkan error untuk mengatasinya tambahkan ?? new Model; agar langsung dpat diterima oleh sistem dan membuat data baru

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        } //kondisi apabila produk yan akan diedit tidak ditemukan,maka tampilkan ini

        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $product = Product::find($id);

        $product = Product::findOrFail($id);

        //Kondisi untuk mengecek apakah produk sudah digunakan di penjualan dan pembelian
        //variabel terhubung dengan metode yang ada pada Model,dengan membuat method untuk merelasikan dengan objek yang ingin dicek
        if ($product->penjualandetail()->exists() || $product->pembeliandetail()->exists()) {
            return response()->json(['message' => 'Produk tidak dapat dihapus karena sudah digunakan di penjualan dan pembelian'], 400);
        }

        // Jika tidak digunakan di penjualan, maka dapat melanjutkan penghapusan
        $product->delete();
        return response()->json(['message' => 'Produk berhasil dihapus'], 200);
    }


    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            if ($product->pembelian()->exists() || $product->penjualan()->exists()) {
                return response()->json(['message' => 'Produk tidak dapat dihapus karena sudah digunakan di pembelian atau penjualan'], 400);
            }

            $product->delete();
        }

        return response()->json('Data berhasil dihapus', 200);
    }

    public function cetakBarcode(Request $request) 
    {
        $dataproduk = [];

        foreach ($request->id_produk as $id) {
            $product = Product::find($id);
            if ($product) {
                $dataproduk[] = $product;
            }
        }

        // Generate PDF with the data
        $no  = 1;
        $pdf = FacadePdf::loadView('Product.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('produk.pdf');
    }

}
