@extends('layouts.master') 
<!-- extends disini berfungsi bahwa halaman ini menunjukan halaman pewarisan dari halaman yang diextends yaitu,layouts.master,posisi di halaman ini diatur oleh yield di halaman yang diextends tersebut -->
@section('title')
    Dashboard
@endsection
  <!-- pada saat akan mengambil bagian dari suatu kode,buat terlebih dahulu section,pada bagian yang nantinya akan dipanggil,lalu pada saat pemanggilan gunakan yield -->

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection
<!-- parent disini berfungsi agar section breadcrumb sebelumnya,di halaman induk tidak terlewtkan,tetapi bisa ditambahkan dengan hal lain yang akan ditambahkan di section breadcrumb -->
    
@section('content')
       <!-- Small boxes (Stat box) -->
       <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{{ $category }}</h3>

              <p>Total Kategori</p>
            </div>
            <div class="icon">
              <i class="fa fa-cube"></i>
            </div>
            <a href="{{ route('category.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{{ $product }}</h3>

              <p>Total Produk</p>
            </div>
            <div class="icon">
              <i class="fa fa-cubes"></i>
            </div>
            <a href="{{ route('product.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{{ $supplier }}</h3>

              <p>Total Supplier</p>
            </div>
            <div class="icon">
              <i class="fa fa-truck"></i>
            </div>
            <a href="{{ route('supplier.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>{{ $member }}</h3>

              <p>Total Member</p>
            </div>
            <div class="icon">
              <i class="fa fa-id-card"></i>
            </div>
            <a href="{{ route('member.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->
      <!-- Main row -->
      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Grafik Pendapatan {{ tanggal_indonesia($tanggal_awal_asli, false) }} s/d {{ tanggal_indonesia($tanggal_akhir_asli, false) }}</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-lg-12">

                  <div class="chart">
                    <!-- Sales Chart Canvas -->
                    <canvas id="salesChart" style="height: 180px;"></canvas>
                  </div>
                  <!-- /.chart-responsive -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
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
      <!-- /.row (main row) -->
@endsection
<!-- sehingga pada saat pemanggilan sudah dinamis,sudah mengambil dari kelas turunannya,bukan dari kelas parent(induk) nya -->

@push('scripts')
  <!-- ChartJS -->
<script src="{{ asset('AdminLTE-2/bower_components/chart.js/Chart.js') }}"></script>
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('product.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, orderable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'nama_kategori'},
                {data: 'merk'},
                {data: 'stok'},
                {data: 'keterangan'},
            ]
        });

    })

$(function() {
    // Get context with jQuery - using jQuery's .get() method.
    var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
    // This will get the first returned node in the jQuery collection.
    var salesChart       = new Chart(salesChartCanvas);

    var salesChartData = {
      labels  : {{ json_encode($data_tanggal) }},
      datasets: [
        {
          label               : 'Pendapatan',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : {{ json_encode($data_pendapatan) }}
        },
      ]
    };

    var salesChartOptions = {
    // Boolean - Whether to show a dot for each point
    pointDot                : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive              : true,
    scaleLabel: function(label) {
      return 'Rp ' + label.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,"."); // Format Rupiah
    },
    //Mengatur konfigurai untuk mengubah format menjadi format rupiah
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
          //Ini memastikan bahwa sumbu Y dimulai dari angka nol, sehingga semua nilai positif akan terlihat jelas.
          userCallback: function(value) {
            value = value.toString(); //memanggil parameter value dan mengubahnya menjadi type string
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Menambahkan titik sebagai pemisah ribuan,mengambil parameter value,untuk diganti (string) dengan menambahkan titik,berada pada diantara angka (B),dan string yang diambil,dikelompokkn menjadi 3 angka
            //dan akan lebih dari ratusan,dan memastikan bahwa titik tidak digunakan di akhir string,dan penggantian ini diterapkan di seluruh string yang akan diformat
            return 'Rp ' + value; // Menambahkan "Rp" di depan nilai
          } // Memastikan bahwa sumbu Y dimulai dari nilai 0, meskipun nilai terkecil dalam data grafik lebih besar dari 0.
        } // Bagian ini berisi pengaturan untuk ticks (nilai yang ditampilkan di sepanjang sumbu Y).
      }] //Ini menunjukkan bahwa pengaturan dilakukan pada sumbu Y (y-axis) dalam bentuk array. Array ini berisi objek yang menentukan pengaturan untuk sumbu Y.
    } // bagian konfigurasi untuk mengatur skala pada sumbu di grafik Chart.js (baik sumbu X maupun Y)
  };

  // Create the line chart
  salesChart.Line(salesChartData, salesChartOptions);

})
</script>
@endpush