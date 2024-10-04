@extends('layouts.master')

@section('title')
    Produk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Produk</li>
@endsection

@section('content')
    <!-- Baris Utama -->
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <button onclick="addForm('{{ route('product.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                    <button onclick="deleteSelected('{{ route('product.delete_selected') }}')" class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button>
                    <button onclick="cetakBarcode('{{ route('product.cetak_barcode') }}')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-barcode"></i> Cetak Barcode</button>
                </div>
                <div class="box-body table-responsive">
                    <form action="" method="post" class="form-produk">
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
                                    <th>Kategori</th>
                                    <th>Merk</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Diskon</th>
                                    <th>Stok</th>
                                    <th>Keterangan</th>
                                    <th width="15%"><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @includeIf('Product.form')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    {data: 'select_all', orderable: false, searchable: false},
                    {data: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'kode_produk'},
                    {data: 'nama_produk'},
                    {data: 'nama_kategori'},
                    {data: 'merk'},
                    {data: 'harga_beli'},
                    {data: 'harga_jual'},
                    {data: 'diskon'},
                    {data: 'stok'},
                    {data: 'keterangan'},
                    {data: 'action', orderable: false, searchable: false},
                ]
            });

            
            $('#modal-form').validator().on('submit', function (e) {
                if (!e.preventDefault()) {
                    $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                        .done((response) => {
                            $('#modal-form').modal('hide');
                            table.ajax.reload();
                            Swal.fire({
                                icon: "success",
                                title: "Produk berhasil disimpan!",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        })
                        .fail((errors) => {
                            alert('Tidak dapat menyimpan data');
                        });
                }
            });

            $('[name=select_all]').on('click', function () {
                $(':checkbox').prop('checked', this.checked);
            });

            function formatCurrency(value) {
                return value
                    .replace(/\D/g, '') // Hanya angka
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Tambahkan titik sebagai pemisah ribuan
            };

            // Event keyup untuk otomatis memformat saat mengetik
            $('#modal-form').on('keyup', '[name=harga_beli], [name=harga_jual]', function () {
                let value = $(this).val();
                $(this).val(formatCurrency(value)); // Format nilai saat mengetik
            });

            // Event submit untuk memastikan titik dihapus sebelum data dikirim ke server
            $('#modal-form').on('submit', function() {
                $('[name=harga_beli], [name=harga_jual]').each(function() {
                    let value = $(this).val().replace(/\./g, ''); // Hapus titik sebelum submit
                    $(this).val(value); // Set nilai tanpa titik
                });
            });
        });

        function addForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Tambah Produk');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('post');
            $('#modal-form [name=nama_produk]').focus();
        }

        function editForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Edit Produk');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('put');
            $('#modal-form [name=nama_produk]').focus();

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=nama_produk]').val(response.nama_produk);
                    $('#modal-form [name=id_kategori]').val(response.id_kategori);
                    $('#modal-form [name=merk]').val(response.merk);
                    $('#modal-form [name=harga_beli]').val(response.harga_beli);
                    $('#modal-form [name=harga_jual]').val(response.harga_jual);
                    $('#modal-form [name=diskon]').val(response.diskon);
                    $('#modal-form [name=stok]').val(response.stok);
                })
                .fail((errors) => {
                    alert('Tidak dapat mengambil data');
                    return;
                })
        }

        async function deleteForm(url) {
            const result = await Swal.fire({
                title: "Yakin ingin menghapus data?",
                text: "Data yang dihapus tidak akan kembali!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Hapus",
                cancelButtonText: "Batal"
            });

            if (result.isConfirmed) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
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
                        text: "Tidak dapat menghapus data!",
                    });
                });
            }
        }

        async function deleteSelected(url) {
            const checkedInputs = $('input:checked');

            if (checkedInputs.length > 0) {
                const result = await Swal.fire({
                    title: "Yakin ingin menghapus data terpilih?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Hapus",
                    cancelButtonText: "Batal"
                });

                if (result.isConfirmed) {
                    $.post(url, $('.form-produk').serialize())
                        .done((response) => {
                            table.ajax.reload();
                            Swal.fire({
                                icon: "success",
                                text: "Data berhasil dihapus",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        })
                        .fail((errors) => {
                            Swal.fire({
                                icon: "error",
                                title: "Perhatian!",
                                text: "Tidak dapat menghapus data!",
                            });
                        });
                }
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Perhatian!",
                    text: "Pilih data yang akan dihapus!",
                });
            }
        }

    
        // async function deleteSelected(url) {
        //     if ($('input:checked').length > 1) {
        //         const result = await Swal.fire({
        //         title: "Apakah kamu yakin ingin menghapus data terpilih?",
        //         icon: "question",
        //         showCancelButton: true,
        //         confirmButtonText: 'Ok',
        //         cancelButtonText: 'Cancel'
        //         }).then((result) => {

        //         if (result.isConfirmed) {
        //             $.post(url, {
        //                 '_token': $('[name=csrf-token]').attr('content'),
        //                 '_method': 'delete'
        //             })
        //             .done((response) => {
        //                 table.ajax.reload();
        //                 Swal.fire({
        //                     icon: "success",
        //                     title: "Data berhasil dihapus",
        //                     showConfirmButton: false,
        //                     timer: 1500
        //                 });
        //             })
        //             .fail((errors) => {
        //                 Swal.fire({
        //                     icon: "error",
        //                     title: "Perhatian!",
        //                     text: "Tidak dapat menghapus data!",
        //                 });
        //             });
        //         }
        //     });
        //     } else {
        //         Swal.fire({
        //             icon: "warning",
        //             title: "Perhatian!",
        //             text: "Pilih data yang akan dihapus!",
        //         });
        //         return;
        //     }
            
        // }

        function cetakBarcode (url) {
            if ($('input:checked').length < 1) {
                Swal.fire({
                    icon: "warning",
                    title: "Perhatian!",
                    text: "Pilih data yang akan dicetak!",
                });
                return; //kondisi jika menekan tombol cetak,tapi belum memilih
            } else if ($('input:checked').length < 3) {
                Swal.fire({
                    icon: "warning",
                    title: "Perhatian!",
                    text: "Pilih minimal 3 data yang akan dicetak!",
                });
                return;  //kondisi jika memilih 1 point untuk dicetak,maka error lalu harus memilih 3 point
            } else {
                $('.form-produk')
                    .attr('target', '_blank')
                    .attr('action', url)
                    .submit();
            } //kondisi jika sudah memilih 3 opsi untuk diceak,mak akan beralhi ke halaman lain khusus untuk menampilkan barcode
        }
    </script>
@endpush
