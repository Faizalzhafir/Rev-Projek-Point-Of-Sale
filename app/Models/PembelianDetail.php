<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_detail'; //untuk menghubungkan ke tabel pembelian_detail
    protected $primaryKey = 'id_pembelian_detail'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_pembelian_detail
    protected $guarded = [];

    public function produk() {
        return $this->hasOne(Product::class, 'id_produk', 'id_produk');
    }
    //fungsi untuk membuat relasi dengan model produk,agar nantinya data produk dapat tampil di modal pembelian detail
}
