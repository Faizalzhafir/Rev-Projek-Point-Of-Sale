<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'setting'; //untuk menghubunkan ke tabel setting
    protected $primaryKey = 'id_setting'; //berfungsi untuk melindungi atau menjadikan primary key nya yaitu id_setting
    protected $guarded = [];
}
