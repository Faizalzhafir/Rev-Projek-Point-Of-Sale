<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_detail'; //untuk menghubunkan ke tabel pembelian_detail
    protected $primaryKey = 'id_pembelian_detail'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_pembelian_detail
    protected $guarded = [];
}
