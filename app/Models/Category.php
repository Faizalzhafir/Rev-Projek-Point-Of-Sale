<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'kategori'; //untuk menghubunkan ke tabel kategori
    protected $primaryKey = 'id_kategori'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_kategori
    protected $guarded = []; //protected untuk ada yang perlu dijaga atau tidak,karena tidak ada jadi null aja
}
