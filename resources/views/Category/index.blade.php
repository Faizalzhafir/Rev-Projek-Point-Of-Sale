@extends('layouts.master')

@section('title')
    Kategori
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Kategori</li>
@endsection
    
@section('content')
    <!-- Main row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <!-- oncilck merupakan fungsi dijavascript,untuk membuat aksi apabila tombol yang diberi fungsi ini,akan otomatis menjalanan fungsi yang telah dideklarasikan,dan menuju ke url yang ditujukan -->
                    <button onclick="addForm('{{ route('category.store') }}')" class="btn btn-success btn-xs btn-flat">
                        <i class="fa fa-plus-circle"></i> Tambah
                    </button> 
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kategori</th>
                                <th width="15%"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @includeIf('Category.form')
    <!-- includeIf digunakan untuk menyertakan view yang dipanggil,jika view yang dipanggil tidak ada,maka laravel tidak mengembalikan pesan error -->
    <!-- berbeda dengan include,laravel akan mengembalikan pesan error jika view yang dipanggil tidak ada -->
    <!-- includewhen digunakan untuk menyertakan sebuah view tergantung kondisi boolean,jika bernilai true maka tampilkan view includeWhen($user->isAdmin(), 'kiko.page') -->
    <!-- sementara includeUnless(is->Admin(), 'kiko.page') akan disertakan (ditampilkan) kecuali kondisi mengembalikan true (tidak ditampilkan) -->
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let table; //pendeklarasian variabel yab=ng bersifat global,untuk bisa diakses di kode lain jika diperlukan

        $(function () {
            table = $('.table').DataTable({
                processing: true, //menampilkan indikator 'processing ' dengan nilai true,memiliki arti menampilkan animasi lading saat data sedang diproses dan diambil dari serve
                autoWidth: false, //menonaktifkan pengaturan lebar kolom otomatis,dengan nilai false,dimana kita dapat mengatur lebar kolom sesuai keinginan di css tanpa dipengaruhi oleh datatables
                // Uncomment and set URL if you're using AJAX
                ajax: {
                    url: '{{ route('category.data') }}', // URL endpoint untuk mengambil data dari server. Di sini, {{ route('category.data') }} akan menghasilkan URL dari rute Laravel (method) yang bernama category.data, yang mengirimkan data kategori dalam format JSON untuk diisi di tabel.
                }, //mengonfigurasi datatable untuk mengambil data dari server menggunakan ajax
                columns: [
                    {data: 'DT_RowIndex', searchable: false, sortable: false}, //mengatur kolom pertama untuk menampilkan nomor baris (DT_RowIndex) yang diberiikan oleh server,dengan opsi pencarian dan pengurutan bernilai false (tidak berfungsi)
                    {data: 'nama_kategori'}, //menampilkan data berdasarkan elemen nama_kategori dari server,dengan opsi pencarian dan pengurutan bernilai true,meskipun tidak ditampilkan (bawaan dari datatable)
                    {data: 'action', searchable: false, sortable: false}, //menampilkan kolom aksi untuk metode edit dan delete yang dideklarasikan di controller dengan opsi pencarian dan pengurutan bernilai false (tidak berfungsi)
                ] 
                //columns diatas berfungsi untuk menampilkan isi dari data yang diinputkan,dengan posisi dibawah tabel,bagian tbody
            }); //Datatable dinisialisasi dengan elemen HTML class table,dengan memanggil (class) menggunakan jquery,yang nantinya disimpan ke variabel tabel

            $('#modal-form').validator().on('submit', function (e) {
                //jquery akan mengambil id (modal-form) di halaman form,lalu menginisialisasi plugin validasi (kemungkinan menggunakan Bootstrap Validator) pada elemen formulir, sehingga saat formulir ini dikirim, validasi akan dijalankan terlebih dahulu,lalu menggunakan (on) event submit,dimana saat formulir dikirim (klik submit),maka fungsi akan dijalankan 
                if (! e.preventDefault()) {
                    //kondisi yang menangani,jika preventdefault (mencegah perilaku default yang biasanya memuat ulang halaman) memeriksa apakah preventDefault tidak mengembalikan false, sehingga AJAX hanya berjalan jika validasi berhasil.
                    $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize()) //mengirim data dari secara asinkron,dengan metode http post (metode untuk mengirim data),dengan jquery
                    //jquery akan mengambil data dari id (modal form pada atah form),dengan mendapatkan url tujuan dari atribut action (category.data),lalu jquery akan (serialize) mengubah seluruh data dari input form (modal-form) menjadi format URL-encoded string yang dapat dikirim ke server.
                    .done((response) => {
                        $('#modal-form').modal('hide'); //jquery akan mengambil id(modal-form),lalu menutup kembali modal yang ditampilkan (modal.hide)
                        table.ajax.reload(); ////memperbarui data pada tabel secara otomatis,tanpa memuat ulang halaman
                        Swal.fire({
                        icon: "success",
                        title: "Kategori berhasil disimpan!",
                        showConfirmButton: false,
                        timer: 1500
                        }); 
                    }) //jika permintaan berhasil,maka callback done aka dijalankan
                    .fail((errors) => {
                        Swal.fire({
                                icon: "error",
                                title: "Kategori sudah ada!",
                                text: "Silakan periksa kembali daftar kategori Anda.",
                                confirmButtonText: 'OK'
                        })
                    });//jika permintaan gagal,maka callback fail aka dijalankan
                }
            }); //salah satu fungsi JavaScript yang digunakan untuk menangani proses pengiriman data dari formulir modal dengan jQuery dan AJAX

            //pertama kita akan menlakukan aksi melalui url,mengguakan ajax,dengan type post,dan mengirimkan data melalui form,lalu buat manipulasi di controller utuk method store
        }); //$(function() {...}) adalah shorthand dari $(document).ready(), yang berarti kode di dalamnya akan dijalankan setelah seluruh dokumen HTML (yang dideklarasikan sebelumnya diatas) selesai dimuat.

        function addForm(url) {
            $('#modal-form').modal('show'); //jquery mengabil id (modal-form) dari halaman form,lalu menggunakan fungsi show dari bootstrap untuk menampilkan modal
            $('#modal-form .modal-title').text('Tambah Kategori'); //jquery mengambil id (modal-form) dengan class modal-title,dengan memanipulasi text yang ditampilkan (text())

            $('#modal-form form')[0].reset(); //jquery mengambil id (modal-form),dibagian tag form untuk mengatur ulang (reset) kembali isian pada form tersebut,jadi nol (0) jika ada yang tertinggal,yang mungkin dari pengeditan sebelumnya
            $('#modal-form form').attr('action', url); //jquery mengambil id (modal-form),dibagian tag form,agar mengatur atribut 'action' pada form agar mengarah ke url yang diterima sebagai parameter fungsi 
            $('#modal-form [name=_method]').val('post'); //jquery mengambil id (modal-form),lalu menetapkan nilai post,pada elemen method di form,agar mengarahkan form untuk melkuakan http post (umumnya digunakan untuk mengerimkan data ke server)
            $('#modal-form [name=nama_kategori]').focus(); //jquery mengambil id (modal-form),lalu memberikan fokus (focus) pada elemen dengan name nama_kategori,agar pengguna bisa langsung menambahkan data
            
        }

        //parameter URL berasal dari route fungsi yang ada di controller
        function editForm(url) {
            $('#modal-form').modal('show'); //jquery mengabil id (modal-form) dari halaman form,lalu menggunakan fungsi show dari bootstrap untuk menampilkan modal
            $('#modal-form .modal-title').text('Edit Kategori'); //jquery mengambil id (modal-form) dengan class modal-title,dengan memanipulasi text yang ditampilkan (text())

            $('#modal-form form')[0].reset(); //jquery mengambil id (modal-form),dibagian tag form untuk mengatur ulang (reset) kembali isian pada form tersebut,jadi nol (0) jika ada yang tertinggal,yang mungkin dari pengeditan sebelumnya
            $('#modal-form form').attr('action', url); //jquery mengambil id (modal-form),dibagian tag form,agar mengatur atribut 'action' pada form agar mengarah ke url yang diterima sebagai parameter fungsi 
            $('#modal-form [name=_method]').val('put'); //jquery mengambil id (modal-form),lalu menetapkan nilai put,pada elemen method di form,agar mengarahkan form untuk melkuakan http put (umumnya digunakan untuk memperbarui data di server)
            $('#modal-form [name=nama_kategori]').focus(); //jquery mengambil id (modal-form),lalu memberikan fokus (focus) pada elemen dengan name nama_kategori,agar pengguna bisa langsung mengedit

            //jquery akan melakukan request get,berdasarkan parameter (url),sehingga mengambil data nya dari url yang berhubungan dengan server di controller
            $.get(url)
                .done((response) => {
                    $('#modal-form [name=nama_kategori]').val(response.nama_kategori); //jquery akan mengambil id (modal-form) bagian elmen dengan name nya nama_kategori,dengan menetapkan nilai dari nama_kategori dari response dari server (mengambil data sebelumnya)
                }) //done akan dieksekusi jika permintaan berhasil,response berisikan data yang dikembalikan dari server
                .fail((errors) => {
                    alert('Tidak dapat menyimpan data'); //memunculkan alert default dengan text
                    return; //keluaratau hentikan dari fungsi yang dijalankan 
                }) //fail akan dieksekusi jika prmintaan gagal
        }

        function deleteForm(url) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'), //token yang diperlukan disetap proses POST,PUT,DELETE untuk keamanan,yang mengambil nilai dari tag csrf di file HTML 
                    '_method': 'delete' //cara untuk mengkonfirmasi ke laravel,walaupun permintaan POST,tetapi perlkuan yang akan dijalnkan adalah delete,dimana kode ini menjalankan penghapusan tanpa memuat ulang halaman
                }) //jquery akan mengirikan permintaan (post,secara asinkron) AJAX  ke url yang dituju (untuk penghapusan menggunakan route (di web juga),sehingga tertuju ke satu method),yang dimana berisi perintah untuk penghapusan,lalu data yang dipilih akan dikirim dengan permintaan diatas 
                .done((response) => {
                    table.ajax.reload(); //memperbarui tabel secara otomatis,tanpa memuat ulang halaman
                    Swal.fire({
                        icon: "success",
                        title: "Data berhasil dihapus",
                        showConfirmButton: false,
                        timer: 1500
                    });
                })
                .fail((errors) => {
                    Swal.fire({
                        icon: "error",
                        title: "Perhatian!",
                        text: "Kategori tidak dapat dihapus, karena kategori telah digunakan",
                    });
                });
            }
        });
    }

    </script>
@endpush
