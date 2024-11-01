@extends('layouts.master')

@section('title')
    Daftar Member
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Member</li>
@endsection
    
@section('content')
    <!-- Main row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <button onclick="addForm('{{ route('member.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                    <button onclick="cetakMember('{{ route('member.cetak_member') }}')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-id-card"></i> Cetak Member</button>
                </div>
                <div class="box-body table-responsive">
                    <form action="" method="post" class="form-member">
                    @csrf
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" name="select_all" id="select_all">
                                </th>
                                <th width="5%">No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th width="15%"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @includeIf('Member.form')
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
                    url: '{{ route('member.data') }}',
                },
                columns: [
                    {data: 'select_all', searchable: false, sortable: false},
                    {data: 'DT_RowIndex', searchable: false, sortable: false},
                    {data: 'kode_member'},
                    {data: 'nama'},
                    {data: 'telepon'},
                    {data: 'alamat'},
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
                        title: "Suppplier berhasil disimpan!",
                        showConfirmButton: false,
                        timer: 1500
                        });
                    })
                    .fail((errors) => {
                        table.ajax.reload();
                        Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: "Tidak bisa menambahkan,telepon sudah ada di daftar!",
                        confirmButtonText: 'OK'
                        });
                        return;
                    });
                }
            });

            $('[name=select_all]').on('click', function () {
                $(':checkbox').prop('checked', this.checked);
            });

            //pertama kita akan menlakukan aksi melalui url,mengguakan ajax,dengan type post,dan mengirimkan data melalui form,lalu buat manipulasi di controller utuk method store
        });

        function addForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Add Member');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('post');
            $('#modal-form [name=nama]').focus();
        }

        function editForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Edit Member');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('put');
            $('#modal-form [name=nama]').focus();

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=nama]').val(response.nama);
                    $('#modal-form [name=telepon]').val(response.telepon);
                    $('#modal-form [name=alamat]').val(response.alamat);
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
                            text: 'Member berhasil dihapus',
                            confirmButtonText: 'OK'
                        });
                    })
                    .fail((errors) => {
                        Swal.fire({
                            icon: "error",
                            title: "Perhatian!",
                            text: 'Member tidak dapat dihapus karena sudah digunakan di Daftar Penjualan'
                        });
                    });
            }
        });
    }


        function cetakMember (url) {
            if ($('input:checked').length < 1) {
                Swal.fire({
                        icon: "warning",
                        title: "Perhatian!",
                        text: "Pilih data yang akan dicetak!",
                    });
                return; //kondisi jika menekan tombol cetak,tapi belum memilih
            } else {
                $('.form-member')
                    .attr('target', '_blank')
                    .attr('action', url)
                    .submit();
            } //kondisi jika sudah memilih 3 opsi untuk diceak,mak akan beralhi ke halaman lain khusus untuk menampilkan barcode
        }
    </script>
@endpush
