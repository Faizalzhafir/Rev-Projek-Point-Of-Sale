@extends('layouts.master')

@section('title')
    Transaksi Penjualan
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
                <div class="box-body">
                    <div class="alert alert-success alert-dissmissible">
                        <i class="fa fa-check icon"></i>
                        Transaksi berhasil !
                    </div>
                </div>
                <div class="box-footer">
                    @if ($setting->tipe_nota == 1)
                    <button class="btn btn-warning btn-flat" onclick="notaKecil('{{ route('transaksi.nota_kecil') }}', 'Nota Kecil')">Cetak Ulang Nota</button>
                    @else
                    <button class="btn btn-warning btn-flat" onclick="notaBesar('{{ route('transaksi.nota_besar') }}', 'Nota PDF')">Cetak Ulang Nota</button>
                    @endif
                    <a href="{{ route('transaksi.baru') }}" class="btn btn-primary btn-flat">Transaksi Baru</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function notaKecil(url, title) {
            popupCenter(url, title, 625, 500);
        } //atur width dan height kertas

        function notaBesar(url, title) {
            popupCenter(url, title, 900, 675);
        } //atur width dan height kertas

        function popupCenter(url, title, w, h) {
            const dualScreenLeft = window.screenLeft !==  undefined   ? window.screenLeft : window.screenX;
            const dualScreenTop  = window.screenTop  !==  undefined   ? window.screenTop  : window.screenY;

            const width  = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            const systemZoom = width / window.screen.availWidth;
            const left       = (width - w) / 2 / systemZoom + dualScreenLeft
            const top        = (height - h) / 2 / systemZoom + dualScreenTop
            const newWindow  = window.open(url, title, 
            `
                scrollbars=yes,
                width  =    ${w / systemZoom}, 
                height =    ${h / systemZoom}, 
                top    =    ${top}, 
                left   =    ${left}
            `
            );

            if (window.focus) newWindow.focus();
        } //fungsi di javasciprt untuk menampilkan pop-up untuk pembuatan nota
    </script>
@endpush


