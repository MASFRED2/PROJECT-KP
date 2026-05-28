<?php

namespace App\Http\Controllers;

use App\Models\Diskon;
use App\Models\Barang;
use Illuminate\Http\Request;

class DiskonController extends Controller
{
    public function index()
    {
        $diskon = Diskon::with('barang')->latest()->get();
        $barang = Barang::all();
        return view('admin.diskon.index', compact('diskon', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required',
            'jenis_diskon' => 'required',
            'nilai_diskon' => 'required|numeric',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        Diskon::create($request->all());

        return redirect()->back()->with('success', 'Diskon berhasil dijadwalkan!');
    }

    // Fungsi khusus untuk dilihat oleh akun Customer
    public function daftarPromo()
    {
        $promo = Diskon::with('barang')
            ->where('status_aktif', true)
            ->whereDate('tgl_mulai', '<=', now())
            ->whereDate('tgl_selesai', '>=', now())
            ->get();

        return view('customer.promo', compact('promo'));
    }
}