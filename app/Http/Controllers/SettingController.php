<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index() {
        return view('Setting.index');
    }

    public function show() {
        return Setting::first();
    }

    public function update(Request $request) {
        $setting = Setting::first(); //mengambil data yang pertama dari tabel setting
        $setting->nama_perusahaan = $request->nama_perusahaan;
        $setting->telepon = $request->telepon;
        $setting->email = $request->email;
        $setting->youtube = $request->youtube;
        $setting->instagram = $request->instagram;
        $setting->twitter = $request->twitter;
        $setting->facebook = $request->facebook;
        $setting->alamat = $request->alamat;
        $setting->diskon = $request->diskon;
        $setting->tipe_nota = $request->tipe_nota;

        //Kondisi untuk mengecek jika ada logo yang diupload/uodate
        if ($request->hasFile('path_logo')) {
            $file = $request->file('path_logo');
            $nama = 'logo-' .date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);

            $setting->path_logo = "/img/$nama";
        }

        //Kondisi untuk mengecek jika ada kartu member yang diupload/update
        if ($request->hasFile('path_kartu_member')) {
            $file = $request->file('path_kartu_member');
            $nama = 'member-' .date('Y-m-dHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);

            $setting->path_kartu_member = "/img/$nama";
        }

        $setting->update();

        return response()->json('Data berhasil disimpan', 200);
    }

    //Kenapa untuk setting tidak ada method untuk store?karena,untuk setting menggunakan seeder,sehingga hanya perlu untuk metho updatenya saja
}
