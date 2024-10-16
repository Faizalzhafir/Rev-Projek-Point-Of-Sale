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
                <h1>Selamat Datang !</h1>
                <h2>Anda login sebagai <span class="label label-success">{{ auth()->user()->name }}</span> (KASIR)</h2>
                <br><br>
                <a href="{{ route('transaksi.baru') }}" class="btn btn-success btn-lg"><b>Transaksi Baru</b></a>
                <br><br><br>
              </div>
            </div>
          </div>
       </div>
      <!-- /.row (main row) -->
      <div class="row">
        <div class="col-lg-12">
          <div class="box">
            <div class="box-body table-responsive">
              <form action="" method="post" class="form-produk">
                @csrf
                  <table class="table table-striped table-bordered">
                    <thead>
                        <h3 style="text-align: center; font-weight: bold;">Informasi Produk</h3>
                        <br>
                        <tr>
                          <th width="5%">No</th>
                          <th>Kode</th>
                          <th>Nama</th>
                          <th>Kategori</th>
                          <th>Merk</th>
                          <th>Stok</th>
                          <th>Keterangan</th>
                        </tr>
                    </thead>
                  </table>
              </form>
            </div>
          </div>
        </div>
      </div>
@endsection
<!-- sehingga pada saat pemanggilan sudah dinamis,sudah mengambil adri kelas turunannya,bukan dari kelas parent(induk) nya -->

@stack('scripts')