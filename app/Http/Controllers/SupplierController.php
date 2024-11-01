<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index() {
        return view('Supplier.index');
    }

    public function data() {
        $supplier = Supplier::orderBy('id_supplier', 'desc')->get();

        return datatables()
            ->of($supplier)
            ->addIndexColumn()
            ->addColumn('action', function ($supplier) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('supplier.update', $supplier->id_supplier) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-pencil"></i></button>                
                    <button  type="button" onclick="deleteForm(`'. route('supplier.destroy', $supplier->id_supplier) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'telepon' => 'required|unique:supplier,telepon', // Validasi telepon unik tanpa format tertentu
        ]); 
        
        $supplier = Supplier::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
         //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harusberapa karakter dll.
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::find($id);

        return response()->json($supplier);

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
       $supplier =  Supplier::find($id)->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
        //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harusberapa karakter dll.
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     $supplier = Supplier::find($id);
    //     $supplier->delete();

    //     return response(null, 204);
    // }

    public function delete($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);

             // Kondisi untuk mengecek apakah supplier sudah digunakan di pembelian
        if ($supplier->pembelian()->exists()) {
            return response()->json(['message' => 'Supplier tidak dapat dihapus karena sudah digunakan di pembelian'], 400);
        }

            $supplier->delete();
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Tidak dapat menghapus data'], 500);
        }
    }
}
