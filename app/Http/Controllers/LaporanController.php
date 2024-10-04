<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use PDF;

class LaporanController extends Controller
{
    public function index(Request $request) {
        $tanggalAwal = date('Y-m-d', mktime(0,0,0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('Laporan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir) {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $total_pendapatan += $pendapatan;

            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($tanggal, false);
            $row['penjualan'] = 'Rp. ' . format_uang($total_penjualan);
            $row['pembelian'] = 'Rp. ' . format_uang($total_pembelian);
            $row['pengeluaran'] = 'Rp. ' . format_uang($total_pengeluaran);
            $row['pendapatan'] = 'Rp. ' . format_uang($pendapatan);

            $data[] = $row;
        }

        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'penjualan' => '', 
            'pembelian' => '',
            'pengeluaran' => 'Total Pendapatan',
            'pendapatan' => 'Rp' . format_uang($total_pendapatan) . ',00',
        ];

        return $data;

    }

    public function data($awal, $akhir) {
       $data = $this->getData($awal, $akhir);

        return datatables()
        ->of($data)
        ->make(true);
    }

    public function exportPDF($awal, $akhir) {
        $data = $this->getData($awal, $akhir);
        $pdf = PDF::loadView('Laporan.pdf', compact ('awal', 'akhir', 'data'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('Laporan-pendapatan' . date('Y-m-d-his') .'.pdf');
    }
}
