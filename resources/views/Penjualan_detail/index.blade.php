@extends('layouts.master')

@section('title')
    Transaksi Penjualan
@endsection

@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
    }

    .table-penjualan tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Transaksi Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                    
                <form class="form-produk">
                    @csrf
                    <div class="form-group row">
                        <label for="kode_produk" class="col-lg-2">Kode Produk</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_penjualan" id="id_penjualan" value="{{ $id_penjualan }}">
                                <input type="hidden" name="id_produk" id="id_produk">
                                <input type="text" class="form-control" name="kode_produk" id="kode_produk">
                                 <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th width="15%">Jumlah</th>
                        <th>Diskon</th>
                        <th>Subtotal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-bayar bg-primary"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('transaksi.simpan') }}" class="form-penjualan" method="post">
                            @csrf
                            <input type="hidden" name="id_penjualan" value="{{ $id_penjualan }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="bayar" id="bayar">
                            <input type="hidden" name="id_member" id="id_member" value="{{ $memberSelected->id_member }}">

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kode_member" class="col-lg-2 control-label">Member</label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="kode_member" value="{{ $memberSelected->kode_member }}">
                                        <span class="input-group-btn">
                                            <button onclick="tampilMember()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diskon" class="col-lg-2 control-label">Diskon Member</label>
                                <div class="col-lg-8">
                                    <input type="number" name="diskon" id="diskon" class="form-control" value="{{ ! empty($memberSelected->id_member) ? $diskon : 0}}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diskonrp" class="col-lg-2 control-label">Total Diskon</label>
                                <div class="col-lg-8">
                                    <input type="text" name="diskonrp" id="diskonrp" class="form-control"  readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bayar" class="col-lg-2 control-label">Bayar</label>
                                <div class="col-lg-8">
                                    <input type="text" id="bayarrp"  class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diterima" class="col-lg-2 control-label">Diterima</label>
                                <div class="col-lg-8">
                                    <input type="text" id="diterima"  class="form-control" name="diterima" 
                                        value="{{ $penjualan->diterima ?? 0 }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kembali" class="col-lg-2 control-label">Kembali</label>
                                <div class="col-lg-8">
                                    <input type="text" id="kembali"  name="kembali" class="form-control" value="0" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i class="fa fa-floppy-o"></i> Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>

@includeIf('Penjualan_detail.produk')
@includeIf('Penjualan_detail.member')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let table, table2;
    let isStokValid = true; // Variabel untuk validasi stok,pendeklarasian nilai awal variabel,yang nantinya digunakan untuk validasi stok dibawah

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('transaksi.data', $id_penjualan) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'stok'},
                {data: 'jumlah'},
                {data: 'diskon'},
                {data: 'subtotal'},
                {data: 'action', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            loadForm($('#diskon').val());
            setTimeout(() => {
                $('#diterima').trigger('input');
            }, 300);
        });

        // Fungsi untuk memeriksa stok
        function checkStok() {
        isStokValid = true; // Reset nilai sebelum pengecekan stok,pemanggilan variabel untuk mengasumsikan bahwa stok cukup (true) sebelum pemeriksaan dimulai,tetapi nilai ini akan bernilai false,jika ditemukan produk yang memiliki stok tidak cukup atau habis

        // Cek stok untuk setiap produk dalam tabel,dengan memanggil setiap baris (tr) yang ada pada table-penjualan,yang nantinya akan menggunakan fungsi loop di jquery (each) yang akan mengiterasi atau memutar pada tabel yang dipanggil,dimana setiap kali loop dijalankan,maka fungsi anonim akan berjalan untuk setiap baris tabel 
        $('.table-penjualan tbody tr').each(function () {
            let stokTersedia = parseInt($(this).find('.stok').text()); //Stok yang tersedia,this merujuk pada tr (baris tabel saat ini(yang dipanggil pertama kali)),lalu kan mencarikan class stok dalam tabel tersebut menggunakan (find),lalu ambil teks nya (isian) yaitu jumlah stoknya menggunakan text,lalu konversi teks yang diambil ke dalam bentuk angak bulat integer,dan memasukkannya ke dalam variabel  stokTersedia
            let jumlah = parseInt($(this).find('.quantity').val()) || 0; // Jumlah yang diinput,sama halnya dengan kode sebelumnya,yang membedakan yaitu kelas yang dicari yaitu quantity (di tabel),lalu mengambil nilai input (val.) yang diisi oleh pengguna di elemen input tersebut (jumah stok),dan disimpan ke variabel jumlah,jika pengguna tidak menginputkan apapun,(|| 0) maka gunakan "0",sebagai nilai default,untuk disimpan ke variabel jumlah 

            //buat pengkondisian yang menyatakan,jika variabel jumlah > variabel stokTersedia atau (||) variabel stokTersedia sama dengan (strict equal one type,one value) 0,dan jika salah satu kondisi terpenuhi,makajalankan kode yang ada di blok
            if (jumlah > stokTersedia || stokTersedia === 0) {
                isStokValid = false; // Stok tidak cukup atau habis,maka variabel stokTersedia diubah menjadi false,dan menyatakan bahwa stok tidak cukup (untuk satu produk yang dimaksud)
                return false; // Hentikan pengecekan jika ada stok yang habis,hentikan iterasi loop jika stok tidak cukup,dan jika ditemukan (stok yang tidak mencukupi),maka tidak perlu memeriksa baris lainnya
                //dapat disimpulkan fungsi ini untuk memeriksa stok produk,pada saat transaksi dilakukan,dengan menggunkan variabel stokTersedia dengan type nya yaitu boolean untuk memeriksa stok
            }
        });
    }
        //Event untuk menangani pada saat proses input pada kelas quantity,dan pada saat proses tersebut berlangsung,maka function akan dijalankan
        $(document).on('input', '.quantity', function () {
            //event input akan dipantau pada saat pengguna sedang mlakukan proses input,pada saat proses berlangsung event input akan dipacu untuk mealkukan fungsi callback yang didefinisikan di dalam blok
            let id = $(this).data('id'); //this nerujuk kepada elemen input pada class quantity yang sedang diinput,lalu mengambil data id dengan mengambil nilai data-id dari elemen input tersebut. Ini mengakses atribut data-id yang ada pada elemen input. Biasanya, atribut data-id digunakan untuk menyimpan informasi tambahan, seperti ID produk atau item yang terkait dengan input tersebut,lalu nilai tersebut akan disimpan ke dalam variabel id
            let jumlah = parseInt($(this).val()); //this.val memiliki arti bahwa this merujuk pada nilai yang diinputkan pada input dengan class quantity,lalu diambil nilai tersebut menggunakan val,dan dikonversi ke dalam bentuk integer (bilangan bulat),dan disimpan ke dalam variabel jumlah
            let stok = parseInt($(this).parent().parent().find('.stok').text()); //this.parent.parent mengandung arti bahwa this merujuk pada quantitiy untuk mencari elemen parent (induk) ,lalu naik satu lagi ke elemen induk (dua kali parent),setelah naik dua level di dalam DOM,maka jalankan untuk mencari stok (find.stok),pada elemen stok di dalam induk tersebut,lalu mengambil teks dari elemen stok,dan mengonversi teks tersebut ke dalam integer (bilangan bulat),dan disimpan ke dalam variabel stok
            //jadi,variabel stok akan berisi angka yang merupakan nilai stok yang diambil dari elemen dengan kelas .stok, yang terletak dalam hierarki DOM yang lebih tinggi dari elemen input .quantity.

            console.log(stok); //digunakan untuk mencetak nilai stok di didalam konsol browser,sebagai langkah untuk melihat nilai dari kelas .quantity,ketika pengguna menginput jumlah

            //validasi untuk pengaturan jumlah dan stok pada saat input dilakukan oleh pengguna,jika stok kurang,muncul notifikasi ini
            if (jumlah < 1) {
                $(this).val(1);
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah Tidak Valid',
                    text: 'Jumlah tidak boleh kurang dari 1',
                    confirmButtonText: 'OK'
                });
                return;
            }
            //validasi untuk pengaturan jumlah,tidak boleh lebih dari 10000,muncul notifikasi ini
            if (jumlah > 10000) {
                $(this).val(10000);
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah Tidak Valid',
                    text: 'Jumlah tidak boleh lebih dari 10000',
                    confirmButtonText: 'OK'
                });
                return;
            }
            //validasi jika stok kurang dari jumlah yang diinputkan sebelumnya,maka muncul notifikasi ini
            if (stok < jumlah) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Valid',
                    text: 'Jumlah stok melebihi stok yang tersedia',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $.post(`{{ url('/transaksi') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah
                })
                .done(response => {
                    table.ajax.reload(() => loadForm($('#diskon').val()));
                })
                .fail(errors => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat menyimpan data',
                        confirmButtonText: 'OK'
                    });
                    return;
                });
        });


        $(document).on('input', '#diskon', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }
            loadForm($(this).val());
        });

        //Event listener untuk kolom diterima,jquery mengambil id diterima dari kolom diatas,lalu buat event pada saat (on) pengguna menginputkan dikolom ini,maka jalankan fungsi callback nya 
        $('#diterima').on('input', function () {
            let value = $(this).val().replace(/\D/g, ''); //Hapus karakter non digit,this merujuk pada id yang dipaggil (diterima),lalu ambil nilai nya meggunakan val,dan gati (replace) dengan mempertahankan angka ((/D) mewakili karakter non digit),secara menyeluruh (g),pada nilai string yang diambil (''),lalu simpan nilai nya ke variabel value
            //pengkondisian jika,variabel value (nilai dan tipe nya (strict equal)) sama dengan "" (null)
            if (value === "") {
                $(this).val(0); //maka this (nilai diterima),isi nilai nya (val) dengan  angka nol
                return; //hentikan fungsi,sehingga tidak ada operasi lebih lanjut
            }
            $(this).val(new Intl.NumberFormat('id-ID').format(value)); // Format Kembali,this merujuk pada diterima (nilai),dan atur value nya (val) dengan membuat value yang baru (new),dan panggil fungsi bawaan ari javascript,untuk memformat angka berdasarkan lokal tertentu (id - ID (Indonesia)),untuk membuat format variabel value (format.val)
            loadForm($('#diskon').val(), value); //Fungsi ini mungkin digunakan untuk memperbarui form atau menghitung sesuatu berdasarkan input yang diterima dan nilai diskon,bisa jadi fungsi loadform ini digunakan untuk,menghitung nilai diskon setelah menerima dari parametee kedua (value)
        }).focus(function () {
            $(this).select();
            //event focus digunakan untuk memfokuskan pada saat pengguna memasukkan data ke dalam kolom,dan pada saat itu dilakukan,maka jalankan fungsi (function),dengan merujuk pada input yang sedang dimasukan (this),dan pilih semua teks yang tadi diinputkan di kolom (select),ini mengkibatkan pengguna mengklik input atau menggunakan tab untuk berpindah ke input ini, semua teks di dalam input akan disorot secara otomatis,terutama pada saat pengguna ingin menytel ulang untuk jumlah yang diinputkan
        });

        //Event untuk aksi pada saat tombol class yang dipanggil di klik,jquery memanngil class btn-simpan (tombol pada saat klik di transaksi),lalu buat event pada saat (on) pengguna menekan tombol dengan class tadi (click),maka jalnkan fungsi yang ada di blok ini
        $('.btn-simpan').on('click', function (e) {
            e.preventDefault(); //fungsi ini digunakan untuk mencegah aksi 'default',pada saat menekan tombol (mengirimkan form secara otomatis),sehingga akan mencegah itu terjadi dengan melakukan fungsinya terlebih dahulu (pengecekan stok)

            checkStok(); // Cek validasi stok sebelum menyimpan,fungsi ini dipanggil untuk mengecek jumlah stok apakah mencukupi atau tidak,dan mengambil (nilai) variabel isstokValid 

            //Dikarenakan isstokValid merupakan boolean,maka jika bernilai false (stok kurang atau 0),maka kondisi ini akan bernilai true (penggunaan not !) dan memunculkan notifikasi alertnya
            if (!isStokValid) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Tidak Cukup!',
                text: 'Tidak dapat menyimpan transaksi karena stok habis atau tidak mencukupi.',
                confirmButtonText: 'OK'
            });
            return; // Hentikan proses simpan jika stok tidak cukup,sehingga tidak akan ada transaksi apapun
        }

            // Ambil nilai total dan uang diterima
            let totalBayar = parseFloat($('#bayar').val().replace(/[^0-9.-]+/g, "")); //jquery akan mencari id bayar,lalu diambil nilainya (val),dan digantikan (replace) semua nilai yang diambil tersebut dengan menghapus bukan angka,titik dan minus ([^0-9.-),lalu terapkan fungsi replace ini secara global (g) seluruh inputan pada string (""),dan megubahnya menjadi angka desimal (parseFloat), lalu ditambahkan ke variabel totalBayar
            let uangDiterima = parseFloat($('#diterima').val().replace(/\./g, '').replace(',', '.')); //jquery akan mencari id ditriema,lalu diambil nlai nya (val),dan digantikan setiap titik yang ada pada nilai terebut untuk dihilangkan (/\./g, ''),lalu ganti nilai tadi jika koma ada menggunakan titik,karena di JS (.) digunakan sebagai pemisah desimal,dan mengubahnya menjadi angka desimal (parseFloat), lalu ditambahkan ke variabel uangDiterima
            let totalDiskon = ($('#diskonrp').val()); //jquery akan mencari id diskonrp,lalu mengambil nilai tersebut (val),dan ditambahkan ke variabel totalDiskon
            // Debugging 
            console.log('Total Bayar:', totalBayar); //console.log merupakan fungsi bawaan JS untuk mencetak output ke console,dan menampilkan nilai dari variabel totalBayar,dengan menampilkan string sebelumnya (Total Bayar: )
            console.log('Uang Diterima:', uangDiterima);
            console.log('Total Diskon: ', totalDiskon);

            //kondisi untuk mengecek apakah variabel bukan suatu angka,isNaN merupakan fungsi bawaan javascript isNaN(Not A Number),jika nilai variabel bukan angka maka fungsi ini (isNaN) akan bernilai true, || (operasi or) digunakan untuk operasi logika jika salh satu atau keduanya bernilai true maka hasilnya,akan bernilai true (kode akan dijalankan)
            if (isNaN(uangDiterima) || isNaN(totalBayar)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid',
                    text: 'Silakan masukkan angka yang valid untuk bayar dan diterima.',
                    confirmButtonText: 'OK'
                });
                return; // Hentikan proses simpan jika stok tidak cukup,sehingga tidak akan ada transaksi apapun
                //kondisi masih perlu dicek
            }

            //kondisi jika pada saat nilai uangDiterima lebih kecil dari totalBayar,maka jalankan kode berikut
            if (uangDiterima < totalBayar) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Uang diterima tidak mencukupi untuk membayar total transaksi!',
                    confirmButtonText: 'OK'
                });
                return; // Hentikan proses simpan jika stok tidak cukup,sehingga tidak akan ada transaksi apapun
            }

            //Validasi sebelum kasir/admin mengklik untuk menyimpan transaksi yang sedang dilaksanakan
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyimpan transaksi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Tidak, batalkan'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.form-penjualan').submit();
                }
                //kondisi jika kasir/admin mengklik tombol simpan,maka kondisi akan dijalankan (result.isConfirmed),jquery akan memanggil class form pejualan untuk menjalankan fungsi submit
            });
        });
    });

    function tampilProduk() {
        $('#modal-produk').modal('show');
    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function pilihProduk(id, kode) {
        $('#id_produk').val(id);
        $('#kode_produk').val(kode);
        hideProduk();
        tambahProduk();
    }

    function tambahProduk() {
        $.post('{{ route('transaksi.store') }}', $('.form-produk').serialize())
            .done(response => {
                $('#kode_produk').focus();
                table.ajax.reload(() => loadForm($('#diskon').val()));
            })
            .fail(errors => {
                alert('Tidak dapat menyimpan data');
                return;
            });
    }

    function tampilMember() {
        $('#modal-member').modal('show');
    }

    function pilihMember(id, kode) {
        $('#id_member').val(id);
        $('#kode_member').val(kode);
        $('#diskon').val('{{ $diskon }}');
        loadForm($('#diskon').val());
        $('#diterima').val(0).focus().select();
        hideMember();
    }

    function hideMember() {
        $('#modal-member').modal('hide');
    }

    function deleteForm(url) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload(() => loadForm($('#diskon').val()));
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
    }


    function loadForm(diskon = 0, diterima = 0) {
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());

        $.get(`{{ url('/transaksi/loadform') }}/${diskon}/${$('.total').text()}/${diterima}`)
            .done(response => {
                $('#diskonrp').val('Rp. ' + response.diskonrp);
                console.log($('#diskonrp').val());
                $('#totalrp').val('Rp. ' + response.totalrp);
                $('#bayarrp').val('Rp. ' + response.bayarrp);
                $('#bayar').val(response.bayar);
                $('.tampil-bayar').text('Bayar: Rp. ' + response.bayarrp);
                $('.tampil-terbilang').text(response.terbilang);

                $('#kembali').val('Rp.' + response.kembalirp);
                if ($('#diterima').val() != 0) {
                    $('.tampil-bayar').text('Kembali: Rp. ' + response.kembalirp);
                    $('.tampil-terbilang').text(response.kembali_terbilang);
                }
            })
            .fail(errors => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }
</script>
@endpush