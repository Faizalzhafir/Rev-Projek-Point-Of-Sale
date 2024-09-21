<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class UserController extends Controller
{
    public function index() {
        return view('User.index');
    }

    public function data() {
        $user = User::isNotAdmin()->orderBy('id', 'desc')->get();

        return datatables()
            ->of($user)
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('user.update', $user->id) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-pencil"></i></button>                
                    <button  type="button" onclick="deleteForm(`'. route('user.destroy', $user->id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->level = 2;
        $user->foto = '/img/profile.jpg';
        $user->save();


        return response()->json('Data berhasil disimpan', 200);
         //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harusberapa karakter dll.
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        return response()->json($user);

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
    public function update(Request $request, $id)
    {
       $user =  User::find($id);
       $user->name = $request->name;
       $user->email = $request->email;
       if ($request->has('password') && $request->password != "") 
            $user->password = bcrypt($request->password); 
       $user->update();

        return response()->json('Data berhasil disimpan', 200);
        //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harusberapa karakter dll.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();

        return response(null, 204);
    }

    public function profil()
    {
        $profil = auth()->user();
        return view('User.profil', compact ('profil'));
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user();

        $user->name = $request->name;
        if ($request->has('password') && $request->password != "") {
            if (Hash::check($request->old_password, $user->password)) {
                if ($request->password == $request->password_confirmation) {
                    $user->password = bcrypt($request->password);
                } else {
                    return response()->json('Konirmasi password tidak sesuai', 422);
                } //jika password sama,maka update,jika tidak mka tidak usah di update
            } else {
                return response()->json('Password lama tidak sesuai', 422);
            } //jika ada,maka maka cek,menggunakan Hash,untuk meminta password yang lama,lalu mengambil ari password aktif di tabel user 
        } //kondisi jika ada perimntaan untuk mengganti password,dengan pernyataan,jika perminttan tersebut memliki password,maka jalankan kondisi yang dibawah

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama = 'logo-' .date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);

            $user->foto = "/img/$nama";
        }

        $user->update();
        
        return response()->json($user, 200);
    }
}
