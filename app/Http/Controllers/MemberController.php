<?php

namespace App\Http\Controllers;
use App\Models\Member;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use PDF;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Member.index');
    }

    public function data() {
        $member = Member::orderBy('kode_member', 'asc')->get();

        return datatables()
            ->of($member)
            ->addIndexColumn()
            ->addColumn('select_all', function ($member) {
                return '
                    <input type="checkbox" name="id_member[]" value="'. $member->id_member .'">
                ';
            })
            ->addColumn('kode_member', function ($member) {
                return '<span class="label label-success">'. $member->kode_member .'<span>';
            })
            ->addColumn('action', function ($member) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('member.update', $member->id_member) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-pencil"></i></button>                
                    <button  type="button" onclick="deleteForm(`'. route('member.destroy', $member->id_member) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>                
                ';
            })
            ->rawColumns(['action','select_all', 'kode_member'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $member = Member::latest()->first(); //member akan mengambil data terakhir,jikalau ada maka ambil kode nya
        $kode_member = (int) $member->kode_member +1 ?? 1; //jika kode_member null,maka kode mmeber akan memanggil fungsi tambah nol didepan
        //apabila kita menemukan nullattempt to read property,itu karena pada saat pengisian memeberikan nilai null,sehingga menghasilkan error untuk mengatasinya tambahkan ?? new Model; agar langsung dpat diterima oleh sistem dan membuat data baru

        $member = new Member();
        $member->kode_member = tambah_nol_didepan($kode_member, 5); //00001
        $member->nama = $request->nama;
        $member->telepon = $request->telepon;
        $member->alamat = $request->alamat;
        $member->save();

        return response()->json('Data berhasil disimpan', 200);
         //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harusberapa karakter dll.
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = Member::find($id);

        return response()->json($member);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = Member::find($id)->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
        //jangan lupa untuk menambakan validasi pada setiap field nya,contohnya seperti,harusberapa karakter dll.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $member = Member::findOrFail($id);

        //Kondisi untuk mengecek member,apakah sudah digunakan di Penjualan atau tidak
        if ($member->penjualan()->exists()) {
            return response->json(['message' => 'Member tidak dihapus,karena sudah digunakan di Daftar Penjualan']);
        }
        
        $member->delete();

        return response(null, 204);
    }

    public function cetakMember(Request $request) 
    {
        $datamember = collect(array());
        foreach ($request->id_member as $id) {
            $member = Member::find($id);
            if ($member) {
                $datamember[] = $member;
            }
        }

        $datamember = $datamember->chunk(2);

        $setting = Setting::first();
        // Generate PDF dengan data yang dikumpulkan
        $no = 1;
        // return view('Member.cetak', compact('datamember','no'));
        $pdf = Pdf::loadView('Member.cetak', compact('datamember', 'no', 'setting'));
        $pdf->setPaper(array(0, 0, 566.93, 850.39), 'portrait');

        return $pdf->stream('member.pdf');
    }
}
