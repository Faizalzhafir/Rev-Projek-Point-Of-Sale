<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan</title>
    <style>
        .text-center {
            text-align: center;
        }
        table.line th {
            border: 1px solid;
            border-color: green;
        }
        table.line tr {
            border: 1px solid;
            border-color: green;
        }
        table.line td {
            border: 1px solid;
            border-color: green;
        }
        .text-right {
            text-align: right;
            border-color: green;
        }
      
    </style>
</head>
<body>
<h3 class="text-center">
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
                    <tr class="text-center">
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