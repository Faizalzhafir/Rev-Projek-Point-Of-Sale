<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'penjualan_detail'; //untuk menghubungkan ke tabel penjualan_detail
    protected $primaryKey = 'id_penjualan_detail'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_penjualan_detail
    protected $guarded = [];

    
    public function produk() {
        return $this->hasOne(Product::class, 'id_produk', 'id_produk');
    }
    //fungsi untuk membuat relasi dengan model produk,agar nantinya data produk dapat tampil di halaman penjualan detail,melalui model

}
