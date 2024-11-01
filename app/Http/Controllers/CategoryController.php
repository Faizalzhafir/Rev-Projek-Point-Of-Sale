<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //method yang berfungsi untuk menampilkan halaman
    public function index()
    {
        return view('Category.index');
    } 

    //method yang berfungsi untuk menampilkan data di di Halaman Index
    public function data() {
        $category = Category::orderBy('id_kategori', 'desc')->get(); //variabel yang berfungsi untuk menampilkan data,yang diambil dari Model, berdasarkan (orderBy) id (id_kategori) dari yang terbesar ke yang terkecil (desc),get berfungsi untuk mendapatkan data tersebut dari Model

        //fungsi untuk mengembalikan penampilan data dari kategori menggunakan datatables
        return datatables()
            ->of($category) //data yang ditampilkan,diambil dari $category
            ->addIndexColumn() //menambahkan kolom index secara otomatis,biasanya berisikan nomor dari setiap baris
            ->addColumn('action', function ($category) {
                return '
                <div class="btn-group">
                    <button onclick="editForm(`'. route('category.update', $category->id_kategori) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-pencil"></i></button> 
                    <button onclick="deleteForm(`'. route('category.destroy', $category->id_kategori) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })  //menambahkan kolom aksi yang mana difungsikan untuk $category,dengan mengembalikan suatu aksi pada setiap barisnya
                // terdapat fungsi javascript untuk melakukan aksi (fungsi di index),fungsi route() menghasilkan URL menuju metode update berdasarkan id_kategori
            ->rawColumns(['action']) // memugkinkan kolom 'action' untuk untuk menerima dan menampilkan HTML mentah,untuk metode edit dan delete,sehingga Laravel tidak akan meng-escape pada tabel
            ->make(true); //Mengubah data menjadi format JSON (sebuah format untuk menyimpan dan mengangkt data,yang biasanya digunakan pada saat data dikirimkan dari server ke halaman web) yang dapat diproses oleh DataTables di sisi klien (JavaScript). JSON ini akan diterima oleh DataTables untuk menampilkan data secara interaktif di halaman web.
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
     * @return \Iluminate\Http\Response
     */
    public function store(Request $request)
    {
        //validasi untuk nama kategori agar unik
        $request->validate([
            'nama_kategori' => 'required|unique:kategori,nama_kategori',
        ]);
        
        $category = new Category(); //buat objek baru dari model Category,dengan membuat variabel yang nantinya bisa untuk menambahkan  kolom data yang sesuai
        $category->nama_kategori = $request->nama_kategori; //isi objek tersebut pada kolom (nama_kategori),berdasarkan inputan dari pengguna (request) pada kolom nama_kategori
        $category->save(); //panggil metode save,setelah penginputan selesai (pada $category) agar disimpan didatabase 

        return response()->json('Data berhasil disimpan', 200);
         //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harus berapa karakter dll.
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id); // Mencari data berdasarkan ID pengguna menggunakan model Category,Jika ditemukan,data tersebut disimpan ke dalam variabel category
        //$id berasal dari route di web,yang bisa diakses oleh pengguna,laravel akan mengiriman ke controller sebagai parameter,yang nantinya disesuaikan dengan id yang diakses oleh pengguna 

        return response()->json($category);

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
        $category = Category::find($id); // Mencari data kategori berdasarkan ID menggunakan model Category. Jika ditemukan, data tersebut disimpan ke dalam variabel $category.
        $category->nama_kategori = $request->nama_kategori; // Mengambil input 'nama_kategori' dari pengguna melalui request dan menyimpannya ke properti 'nama_kategori' pada objek $category.
        $category->update(); // Menyimpan perubahan data pada database berdasarkan input pengguna. Metode update() akan memperbarui record sesuai dengan properti pada objek $category.

        return response()->json('Data berhasil disimpan', 200); //200 menandakan status 'OK',yang emnunjukan bahwa operasi berhasil dilakukan tidak ada masalah
        //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harus berapa karakter dll.
        // Penting: Tambahkan validasi pada setiap field yang diambil dari request. Misalnya, pastikan nama_kategori memiliki jumlah karakter yang sesuai, bukan kosong, dll.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id); // Mencari data berdasarkan ID menggunakan model Category,jika ditemukan lalu menyimpannya ke variabel category
        $category->delete(); // Lalu simpan perubahnnya berdasrkan ID yang ditemukan,dan perbarui record (di database) dengan mengapus data tersebut,berdasarkan objek $category 

        return response(null, 204); //kode untuk mengembalikan response dari helper function Laravel membuat reponse HTTP, dengan tidak mengandung data atau konten apapun (null),204 kode HTTP yang berisikan no content,menunjukan bahwa permintaan berhasil diproses namun tidak ada konten untuk dikirim kembali ke klien
    }
}
