@extends('layouts.master')

@section('title')
    Daftar Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active"> Penjualan</li>
@endsection
    
@section('content')
    <!-- Main row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <table class="table table-striped table-bordered table-penjualan">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Kode Member</th>
                                <th>Total Item</th>
                                <th>Total Harga</th>
                                <th>Total Diskon</th>
                                <th>Total Bayar</th>
                                <th>Kasir</th>
                                <th width="15%"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    @includeIf('Penjualan.detail')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let table, table1;

        $(function () {
            table = $('.table-penjualan').DataTable({
                processing: true,
                autoWidth: false,
                // Uncomment and set URL if you're using AJAX
                ajax: {
                    url: '{{ route('penjualan.data') }}',
                },
                columns: [
                    {data: 'DT_RowIndex', searchable: false, sortable: false},
                    {data: 'tanggal'},
                    {data: 'kode_member'},
                    {data: 'total_item'},
                    {data: 'total_harga'},
                    {data: 'total_diskon'},
                    {data: 'bayar'},
                    {data: 'kasir'},
                    {data: 'action', searchable: false, sortable: false},
                ] 
                //columns diatas berfungsi untuk menampilkan isi dari data yang diinputkan,dengan posisi dibawah tabel, thead diatas
            });

            table1 = $('.table-detail').DataTable({
                processing: true,
                bsort: false,
                dom: 'Brt',
                columns: [
                    {data: 'DT_RowIndex', searchable: false, sortable: false},
                    {data: 'kode_produk'},
                    {data: 'nama_produk'},
                    {data: 'harga_jual'},
                    {data: 'jumlah'},
                    {data: 'subtotal'},
                ] 
            })
        });

        function showDetail(url) {
            $('#modal-detail').modal('show');

            table1.ajax.url(url);
            table1.ajax.reload();
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
    </script>
@endpush
