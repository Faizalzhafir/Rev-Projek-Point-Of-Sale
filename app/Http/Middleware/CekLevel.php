<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $level): Response
    {
        if (auth()->user() && $level == auth()->user()->level) {
            return $next($request);
        }
        //kondisi jika $level sama dengan auth-user,maka tampilkann levelnya,sesuai yang di atur di web.php
        //pada kode auth user pertama,merupakan fungsi untuk mengecek user,melalui login 

        return redirect()->route('dashboard');
    }
    //membuat middleware untuk akses sistem berdasarkn level,dengan nama middleware CekLevel
    //$level (nama vairabel) ditambahkan di Kernel.php,dn nantinya dipanggil di route middleware
}
