@extends('layouts.master')

@section('title')
    Daftar Pembelian
@endsection

@section('breadcrumb')
    @parent
    <li class="active"> Pembelian</li>
@endsection
    
@section('content')
    <!-- Main row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <button onclick="addForm()" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Transaksi Baru</button>
                    @empty(! session ('id_pembelian'))
                    <a href="{{ route('pembelian_detail.index') }}" class="btn btn-info btn-xs btn-flat"><i class="fa fa-pencil"></i> Transaksi Aktif</a>
                    @endempty
                    <!-- empty berfungsi agar pada saat input di transaksi,jika ada,maka tombol aktif akan menampilkan transaksi yang baru saja dilakukanmenggunakan session -->
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-striped table-bordered table-pembelian">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Total Item</th>
                                <th>Total Harga</th>
                                <th>Diskon</th>
                                <th>Total Bayar</th>
                                <th width="15%"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @includeIf('Pembelian.supplier')
    @includeIf('Pembelian.detail')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let table, table1;

        $(function () {
            table = $('.table-pembelian').DataTable({
                processing: true,
                autoWidth: false,
                // Uncomment and set URL if you're using AJAX
                ajax: {
                    url: '{{ route('pembelian.data') }}',
                },
                columns: [
                    {data: 'DT_RowIndex', searchable: false, sortable: false},
                    {data: 'tanggal'},
                    {data: 'supplier'},
                    {data: 'total_item'},
                    {data: 'total_harga'},
                    {data: 'diskon'},
                    {data: 'bayar'},
                    {data: 'action', searchable: false, sortable: false},
                ] 
                //columns diatas berfungsi untuk menampilkan isi dari data yang diinputkan,dengan posisi dibawah tabel, thead diatas
            });

            $('.table-supplier').DataTable();

            table1 = $('.table-detail').DataTable({
                processing: true,
                bsort: false,
                dom: 'Brt',
                columns: [
                    {data: 'DT_RowIndex', searchable: false, sortable: false},
                    {data: 'kode_produk'},
                    {data: 'nama_produk'},
                    {data: 'harga_beli'},
                    {data: 'jumlah'},
                    {data: 'subtotal'},
                ] 
            })
        });

        function addForm() {
            $('#modal-supplier').modal('show');
  
        }

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
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Hapus"
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
