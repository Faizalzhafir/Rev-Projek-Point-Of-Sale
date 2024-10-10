<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    
    protected $table = 'penjualan'; //untuk menghubunkan ke tabel penjualan
    protected $primaryKey = 'id_penjualan'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_penjualan
    protected $guarded = [];

    public function member() {
        return $this->hasOne(Member::class, 'id_member', 'id_member');
    }
    //fungsi untuk membuat relasi dengan model member,agae nantinya data member dapat tampil di halaman penjualan detail,melalui model

    public function user() {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
    //fungsi untuk membuat relasi dengan model user.agar nantinya data user dapat tampil di halaman penjualan,melalui model

    public function produk() {
        return $this->hasOne(Product::class, 'id_produk', 'id_produk');
    }
    //fungsi untuk membuat relasi dengan model produk,agar nantinya data produk dapat tampil di halaman penjualan,melalui model
}
