<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian'; //untuk menghubunkan ke tabel pembelian
    protected $primaryKey = 'id_pembelian'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_pembelian
    protected $guarded = [];
}
