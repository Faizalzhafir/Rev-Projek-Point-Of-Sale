<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('setting')->insert([
            'id_setting' => 1,
            'nama_perusahaan' => 'DIFFY',
            'alamat' => 'Jl. Talagasri No. 35 Kawali',
            'telepon' => '08123456789',
            'tipe_nota' => 1, //kecil
            'diskon' => 5,
            'path_logo' => 'img/logo.png',
            'path_kartu_member' => 'img/member.png',
        ]);
    }
}
