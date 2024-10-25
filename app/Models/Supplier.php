<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    
    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';
    protected $guarded = [];
    
    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'id_supplier');
    }
    //fungsi untuk membuat relasi dengan model Pembelian,agar dapat meliihat jika supplier telah digunakan atau tidak
}
