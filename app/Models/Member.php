<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member'; //untuk menghubunkan ke tabel member
    protected $primaryKey = 'id_member'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_member
    protected $guarded = [];

    public function penjualan() {
        return $this->hasMany(Penjualan::class, 'id_member');
    }
    //fungsi untuk membuat relasi dengan model Penjualan,apakah sudah dipakai atau tidak
}
