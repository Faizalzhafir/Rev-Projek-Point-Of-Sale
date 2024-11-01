<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function scopeIsNotAdmin($query) {
        return $query->where('level', '!=', 1);
    }
    //metode yang digunakn dengan memanfaatkan fitur query scope,dengan fungsi untuk memfilter data yang akan ditampilkan scope disini berfungsi agar tidak menampilkan level 1,yaitu admin pada halaman data user
    //query scope merupakan salah satu fitur untuk memungkinkan membuat query bawaan,yang nantinya dapat didefiisikan di kondisi tertentu di tempat yang lain sesuai kebutuhan
    //untuk setiap method yang dibuat harus diawali dengan kata scope disatukan dengan nama (kondisional) yang nantinya dapat memanggil nama tersebut untuk penggunaannya,jadi dengan scope maka akan membuat penagturan kode dengan fungsi yang sama dapat terkelola
    //scope bisa ditambahkan dengan parameter (untuk parameter tidah harus berada dalam satu file,bisa dari luar),dengan tambahkan paramternya di argumen fungsi kodenya,lalu jika akan di panggil (scope),maka tambahkan parameter tadi sebagai argumen setelah nama scopenya

    public function penjualan() {
        return $this->hasMany(Penjualan::class, 'id_user');
    }
    //fungsi untuk membuat relasi dengan Model Pembelian,agar dapat melihat User sudah digunakan atau tidak
}
