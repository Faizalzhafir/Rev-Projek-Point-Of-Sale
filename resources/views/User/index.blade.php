@extends('layouts.master')

@section('title')
    Daftar User
@endsection

@section('breadcrumb')
    @parent
    <li class="active"> User</li>
@endsection
    
@section('content')
    <!-- Main row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <button onclick="addForm('{{ route('user.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th width="15%"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @includeIf('User.form')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let table;

        $(function () {
            table = $('.table').DataTable({
                processing: true,
                autoWidth: false,
                // Uncomment and set URL if you're using AJAX
                ajax: {
                    url: '{{ route('user.data') }}',
                },
                columns: [
                    {data: 'DT_RowIndex', searchable: false, sortable: false},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'action', searchable: false, sortable: false},
                ] 
                //columns diatas berfungsi untuk menampilkan isi dari data yang diinputkan,dengan posisi dibawah tabel, thead diatas
            });

            $('#modal-form').validator().on('submit', function (e) {
                if (! e.preventDefault()) {
                    $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                        icon: "success",
                        title: "Data User berhasil disimpan!",
                        showConfirmButton: false,
                        timer: 1500
                        });
                    })
                    .fail((errors) => {
                        Swal.fire({
                            icon: "error",
                            title: "Email sudah ada!",
                            text: "Silakan periksa kembali daftar User.",
                            confirmButtonText: 'OK'
                        })
                    });
                }
            });
            //pertama kita akan menlakukan aksi melalui url,mengguakan ajax,dengan type post,dan mengirimkan data melalui form,lalu buat manipulasi di controller utuk method store
        });

        function addForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Tambah User');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('post');
            $('#modal-form [name=name]').focus();

            $('#password, #password_confirmation').attr('required', true);
        }

        function editForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Edit User');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('put');
            $('#modal-form [name=name]').focus();
            
            $('#password, #password_confirmation').attr('required', false);

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=name]').val(response.name);
                    $('#modal-form [name=email]').val(response.email);
                    //digunakan untuk bagian field mana saja yang nantinya dapat diedit dan diatmpilkan difield,seperti value diphp
                })
                .fail((errors) => {
                    alert('Tidak dapat menyimpan data');
                    return;
                })
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
                        '_token': $('[name=csrf-token]').attr('content'),
                        '_method': 'delete'
                    })
                    .done((response) => {
                       
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil dihapus',
                            confirmButtonText: 'OK'
                        });
                    })
                    .fail((errors) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Tidak dapat menghapus User karena User sedang digunakan di Halaman Penjualan!',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    }
    </script>
@endpush
