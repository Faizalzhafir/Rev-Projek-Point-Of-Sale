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
            ->orderBy('id_produk', 'desc')
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
                return format_uang($product->harga_beli);
            })
            ->addColumn('harga_jual', function ($product) {
                return format_uang($product->harga_jual);
            })
            ->addColumn('stok', function ($product) {
                return format_uang($product->stok);
            })
            ->addColumn('action', function ($product) {
                return '
                <div class="btn-group">
                    <button  type="button" onclick="editForm(`'. route('product.update', $product->id_produk) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-pencil"></i></button>                
                    <button  type="button" onclick="deleteForm(`'. route('product.destroy', $product->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })
            ->rawColumns(['action', 'kode_produk', 'select_all'])
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
        $product = Product::latest()->first();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$product->id_produk + 1, 6); // Membuat kode produk unik
        $product = Product::create($request->all());
         //apabila kita menemukan nullattempt to read property,itu karena pada saat pengisian memeberikan nilai null,sehingga menghasilkan error untuk mengatasinya tambahkan ?? new Model; agar langsung dpat diterima oleh sistem dan membuat data baru

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
        }

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
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $product = Product::find($id);
            $product = delete();
        }

        return response(null, 204);
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
