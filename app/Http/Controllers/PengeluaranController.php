<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;

class PengeluaranController extends Controller
{
    public function index() {
        return view('Pengeluaran.index');
    }

    public function data() {
        $pengeluaran = Pengeluaran::orderBy('id_pengeluaran', 'desc')->get();

        return datatables()
            ->of($pengeluaran)
            ->addIndexColumn()
            ->addColumn('created_at', function ($pengeluaran) {
                return tanggal_indonesia($pengeluaran->created_at, false);
            })
            ->addColumn('nominal', function ($pengeluaran) {
                return 'Rp. ' . format_uang($pengeluaran->nominal);
            })
            ->addColumn('action', function ($pengeluaran) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('pengeluaran.update', $pengeluaran->id_pengeluaran) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-pencil"></i></button>                
                    <button  type="button" onclick="deleteForm(`'. route('pengeluaran.destroy', $pengeluaran->id_pengeluaran) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // Hilangkan titik pada harga_beli dan harga_jual sebelum disimpan
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal)
            //merge berfungsi untuk mengganti atau memodifikasi data yang ada didalam objek request,dimana request menyimpan data yang sebelumnya telah diinputkan (dari form),dalam hal ini merge menggantikan nilai harga_beli dan harga_jual setelah dimodifikasi
            //str_replace berfungsi untuk mengganti seluruh kemunculan karakter dalam nilai request dalam hal ini,titik diganti menjadi tidak ada pada variabel request yang diminta untuk diganti
            //kenapa fungsi ini dijalnakan?karena untuk membuat format string yang dikirimkan ke database sesuai dengan format numerik,tanpa ada titik (pemisah ribuan dari index)
        ]);
        $pengeluaran = Pengeluaran::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
         //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harusberapa karakter dll.
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengeluaran = Pengeluaran::find($id);

        return response()->json($pengeluaran);

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
       $pengeluaran =  Pengeluaran::find($id)->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
        //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harusberapa karakter dll.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengeluaran = Pengeluaran::find($id);
        $pengeluaran->delete();

        return response(null, 204);
    }
}
