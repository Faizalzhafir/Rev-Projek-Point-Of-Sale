<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'kategori'; //untuk menghubungkan ke tabel kategori
    protected $primaryKey = 'id_kategori'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_kategori
    protected $guarded = []; //protected untuk ada yang perlu dijaga atau tidak,penggunaan mass assignment (pengisian data masal), karena tidak ada jadi null saja,maksudnya kode ini menunjukan bahwa setiap inputan dari penguna bisa masuk secaa bebas,jika semisalnya ada inputan yang tidak boleh diisi bisa saja terisi (kolom sensitif yang hanya bisa diii oleh admin semisalnya),sehingga memiliki makna bahwa kode ini tidak ada atribut/kolom untuk dilindungi ([])
}
