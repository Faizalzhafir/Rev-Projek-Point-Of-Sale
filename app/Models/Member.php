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
}
