<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota PDF</title>

    <style>
        table td {
            font-size: 14px;
        }
        table.data td {
            border: 1px solid #ccc;
            padding: 5px;
        }
        table.data th {
            padding-top: 8px;
            padding-bottom: 8px;
            background-color: #008d4c;
            color: white;
        }
        table.data (
            border-collapse: collapse;
        )
        .text-center {
          text-align: center;  
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
   <h2 style="text-align: center; color: #008d4c; font-family: Helvetica;"><b>Nota Penjualan {{ $setting->nama_perusahaan }}</b></h2>
   <table width="100%">
    <img src="{{ public_path($setting->path_logo) }}" alt="{{ $setting->path_logo }}" width="90">
        <tr>
            <td rowspan="4" width="60%">
                {{ $setting->alamat }}
                <br>
                <br>
            </td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ tanggal_indonesia(date('Y-m-d'), false) }}</td>
        </tr>
        <tr>
            <td>Kode Member</td>
            <td>: {{ $penjualan->member->kode_member ?? '' }}</td>
        </tr>
   </table>

   <table class="data" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Diskon</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $key => $item)
                    <tr>
                        <td class="text-center">{{ $key+1 }}</td>
                        <td>{{ $item->produk->kode_produk }}</td>
                        <td>{{ $item->produk->nama_produk }}</td>
                        <td class="text-right">{{ format_uang($item->harga_jual) }}</td>
                        <td class="text-right">{{ format_uang($item->jumlah) }}</td>
                        <td class="text-right">{{ $item->diskon }}</td>
                        <td class="text-right">{{ format_uang($item->subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <!-- dikarenakan dihalaman ini tidak menggunakan php,maka minta laravel untuk memanggil php,untuk pendeklarasian variabel untuk menimpan nilai total harga (hanya untuk di tampilan nota,tdak dimasukan ke database) -->
                @php
                    $total = 0;
                @endphp
                
                <!-- perulangan untuk mengambil item dari produk,untuk pengakumulasian total harga  -->
                @foreach ($detail as $item)
                    @php
                        $total += $item->jumlah * $item->harga_jual
                    @endphp
                    <!-- panggil variabel yang sudah dideklarasikan,untuk nantinya disimpan sebagai total harga -->
                @endforeach
                <tr>
                    <td colspan="6" class="text-right">Total Harga</td>
                    <td class="text-right"><b>{{ format_uang($total) }}</b></td>
                </tr>
                <tr>
                    <td colspan="6" class="text-right">Diskon</td>
                    <td class="text-right"><b>{{ format_uang($penjualan->diskon) }}</b></td>
                </tr>
                <tr>
                    <td colspan="6" class="text-right">Total Diskon</td>
                    <td class="text-right"><b>{{ format_uang($penjualan->total_diskon)  }}</b></td>
                </tr>
                <tr>
                    <td colspan="6" class="text-right">Total Bayar</td>
                    <td class="text-right"><b>{{ format_uang($penjualan->bayar) }}</b></td>
                </tr>
                <tr>
                    <td colspan="6" class="text-right">Diterima</td>
                    <td class="text-right"><b>{{ format_uang($penjualan->diterima) }}</b></td>
                </tr>
                <tr>
                    <td colspan="6" class="text-right">Kembali</td>
                    <td class="text-right"><b>{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</b></td>
                </tr>
            </tfoot>
   </table>

   <table width="100%">
        <tr>
            <td><b>Terimakasih telah berbelanja dan sampai jumpa</b><br><br></td>
            <td class="text-center"  style="background-color: #008d4c; color: white; font-family: Helvetica;"><b>
                Kasir
                <br>
                <br>
                {{ auth()->user()->name }}
                </b>
            </td>
        </tr>
   </table>
</body>
</html>