@extends('layouts.master') 
<!-- digunakan untuk memanggil semua isi dari halaman yang ada di dalam kurungnya -->

@section('title')
    Dashboard
@endsection
  <!-- pada saat akan mengambil bagian dari suatu kode,buat terlebih dahulu section,pada bagian yang nantinya akan dipanggil,lalu pada saat pemanggilan gunakan yield -->

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection
    
@section('content')
       <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-12">
            <div class="box">
              <div class="box-body text-center">
                <h1>Selamat Datang</h1>
                <h2>Anda login sebagai KASIR</h2>
                <br><br>
                <a href="{{ route('transaksi.baru') }}" class="btn btn-success btn-lg">Transaksi Baru</a>
                <br><br><br>
              </div>
            </div>
          </div>
       </div>
      <!-- /.row (main row) -->
@endsection
<!-- sehingga pada saat pemanggilan sudah dinamis,sudah mengambil adri kelas turunannya,bukan dari kelas parent(induk) nya -->