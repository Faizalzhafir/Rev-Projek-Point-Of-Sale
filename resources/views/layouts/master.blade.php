<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ $setting->nama_perusahaan }} | @yield('title')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="icon" href="{{ url($setting->path_logo) }}" type="image/png">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-2/bower_components/font-awesome/css/font-awesome.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-2/dist/css/AdminLTE.min.css')}}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-2/dist/css/skins/_all-skins.min.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-2/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  @stack('css')
</head>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

 @includeIf('layouts.header')

 @includeIf('layouts.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       @yield('title')
      </h1>
        <ol class="breadcrumb">
          @section('breadcrumb')
          <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
          @show
        </ol>
        <!-- show digunakan sebagai tag penutup untuk section yang telah dideklarasikan sebelumnya,berbeda dengan endsection yang dimana hanya pendeklarasian section nya saja,karena harus ditampilkan di halaman lain (jika ingin digunakan) dengan yield -->
    </section>

    <!-- Main content -->
    <section class="content">
    
    @yield('content')

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 @includeIf('layouts.footer')

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{ asset('AdminLTE-2/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('AdminLTE-2/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- Moment -->
<script src="{{ asset('AdminLTE-2/bower_components/moment/min/moment.min.js')}}"></script>
<!-- DataTables -->
<script src="{{ asset('AdminLTE-2/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('AdminLTE-2/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('AdminLTE-2/dist/js/adminlte.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<!-- <script src="dist/js/demo.js"></script> -->
<!-- validator -->
<script src="{{ asset('AdminLTE-2/validator.min_files/validator.min.js.download') }}"></script>

<script>
  function preview(selector, temporaryFile, width = 200) {
    $(selector).empty();
    $(selector).append(`<img src="${window.URL.createObjectURL(temporaryFile)}" width="${width}">`);
  }
</script>
@stack('scripts')
<!-- otomatis laravel akan membaca ketika ada view push dari js,nanti otomatis akan disimpan di stack diatas,stack berisi berbagai kumpulan konten (yang dinamis) yang telah didefinisikan di push sebelumnya-->
</body>
</html>

<!-- section dan yield: Digunakan untuk menentukan bagian-bagian konten tetap yang akan diisi di child template.
stack dan push: Lebih cocok untuk menambahkan konten (seperti file JavaScript, CSS) dari berbagai template tanpa menentukan posisi dari awal. -->
