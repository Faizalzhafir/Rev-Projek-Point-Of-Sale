<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan</title>
    <style>
        .text-center {
            text-align: center;
            color: white;
            padding: 5px;
            background-color: #008d4c;
        }
        .text-left {
            text-align: left;
        }
        table.line th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: center;
            background-color: #008d4c;
            color: white;
        }
        table.line tr {
            border: 1px solid #ddd;
            padding: 8px;
            background-color: #c0bdbd;
        }
        table.line td {
            border: 1px solid #ddd;
            padding: 8px;
            background-color: #fff;
        }
        .text-right {
            text-align: right;
            border-color: green;
        }
      
    </style>
</head>
<body>
<h3>
        <h2 style="text-align: center; font-family: Helvetica; color: #008d4c"><b>Laporan Pendapatan</b></h2>
        <h4 class="text-center">
            Tanggal {{ tanggal_indonesia($awal, false) }}
            s/d
            Tanggal {{ tanggal_indonesia($akhir, false) }}
        </h4>

        <table class="line" width="100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Tanggal</th>
                    <th>Penjualan</th>
                    <th>Pembelian</th>
                    <th>Pengeluaran</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr class="text-left">
                        @foreach ($row as $col)
                            <td>{{ $col }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </h3>
</body>
</html>