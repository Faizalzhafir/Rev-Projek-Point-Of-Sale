@extends('layouts.master')

@section('title')
    Produk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Produk</li>
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

            //Fungsi untuk mengubah format dari nilai numerik agar sesuai dengan format uang,khusunya dengan menambahkan ('.') sebagai pemisah ribuan
            //Fungsi tersebut menerima satu parameter bernama value,yang diharapkan dapat memiliki nilai string yang mengandung angka (string bisa saja angka,huruf,simbol) yang nantinya akan dihapus dalam format.Dengan fugsi akhir yaitu mengembalikan string yang hanya terdiri dari angka dan menamhkan titik sebaai pemisah ribuan
            function formatCurrency(value) {
                return value
                    .replace(/\D/g, '') // Hanya angka,menggunakan metode replace dengan artian mengganti value dari parameter dengan mempertahankan angka saja (/D = (0-9)) yang diterapkan untuk seluruh string yang diinput (g),yang mana selain string yang diharapkan (angka) akan diganti ('')
                    .replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Tambahkan titik sebagai pemisah ribuan,sama halnya dengan metode diatas untuk mengganti format string,dengan hanya menambahkan titik hanya diantara angka saja dengan boundary (batas-non-kata (/B))
                    //lalu melanjutkan dengan mencari fungsi yang ada didalam kurung (?=) lookahead asertion,dengan mencari posisi dimana ada kelompok 3 angka yang diikuti oleh banyak angka (tidak ada angka tambahan dibelakangnya)
                    //lalu mengelompokkan inputan dari parameter dengan mengelompokkannya menjadi kelompok 3 angka (dari kanan) (/d{3}), + berfungsi menyatakan bahwa akan ada lebih banyak kelompok 3 angka ( >999 )
                    //(?!\d) berfungsi untuk memastikan bahwa setelah string dikelompokkan tidak ada lagi angka (untuk menghindari pemisah ribuan di akhir string)
            };

            // Event keyup untuk otomatis memformat saat mengetik,event ini akan otomatis dijalankan pada saat pengguna menekan dan melepas tombol keyboard (proses input),maka kode yang ada di blok ini akan dijalankan.Selector "on" akan dijalnakn pada elemen input yang memiliki nama (name yang dimaksud),untuk menjalankan event keyup
            $('#modal-form').on('keyup', '[name=harga_beli], [name=harga_jual]', function () {
                let value = $(this).val(); //kode untuk mengambil nilai input field,yang dimaksud dari this yaitu nama (name yang dimaksud),dengan mengambil value nya menggnakan  val,lalu disimoan di value
                $(this).val(formatCurrency(value)); // Format nilai saat mengetik,this menyatakan baha nilai nama (name yang dimaksud) telah diambil,maka format nilai (val) tersebut dengan fungsi FormatCurrency yang telah dideklarasikan sebelumnya dari value yag tadi diambil
            });

            // Event submit untuk memastikan titik dihapus sebelum data dikirim ke server,event yang diambil dari id modal-form,yang mana pada saat tombol submit di klik di form
            //lalu memilih dua elemen input yang memiliki nama (name yang dimaksud),yang kemudian menggunakan fngsi each,yang mana setiap elemen tersebut akan diiterasi untuk diproses
            $('#modal-form').on('submit', function() {
                $('[name=harga_beli], [name=harga_jual]').each(function() {
                    let value = $(this).val().replace(/\./g, ''); // Hapus titik sebelum submit,lalu nilai yang merujuk pada this,akan diambil oleh val (yang mana berupa string yang memformat string yang tadi diinputkan),lalu gunakan fungsi replace untuk mengganti seluruh titik yang ada pada string,sebelum dikirim ke server,semuanya,alu disimpan ke value
                    $(this).val(value); // Set nilai tanpa titik,lalu nilai input di set ulang dengan meuliskan kembali this yang merujuk pada inputan tadi,dengan mengambil nilai dengan val,dengan isian nilai value sebelumnya
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
                            text: 'Produk berhasil dihapus',
                            confirmButtonText: 'OK'
                        });
                    })
                    .fail((errors) => {
                        Swal.fire({
                            icon: "error",
                            title: "Perhatian!",
                            text: 'Produk tidak dapat dihapus karena sudah digunakan di penjualan dan pembelian'
                        });
                    });
            }
        });
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
