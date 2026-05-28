<?php

namespace App\Http\Controllers;

use App\Models\StokMasuk;
use App\Models\Barang;
use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokMasukController extends Controller
{
    public function index()
    {
        // Ambil data stok masuk beserta relasi barang dan pemasoknya
        $stok_masuk = StokMasuk::with(['barang', 'pemasok'])->latest()->get();
        $barang = Barang::all();
        $pemasok = Pemasok::all(); // Mengambil semua data supplier untuk dropdown
        
        return view('admin.stok_masuk.index', compact('stok_masuk', 'barang', 'pemasok'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required',
            'pemasok_id' => 'required', // Validasi wajib memilih pemasok
            'jumlah_masuk' => 'required|numeric|min:1',
            'harga_beli' => 'required|numeric',
            'tgl_kadaluwarsa' => 'nullable|date'
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan ke tabel stok_masuk dengan menyertakan pemasok_id
            StokMasuk::create([
                'barang_id' => $request->barang_id,
                'pemasok_id' => $request->pemasok_id,
                'jumlah_masuk' => $request->jumlah_masuk,
                'jumlah_sisa' => $request->jumlah_masuk, 
                'harga_beli' => $request->harga_beli,
                'tgl_kadaluwarsa' => $request->tgl_kadaluwarsa,
            ]);

            // 2. Update stok_total di tabel barang
            $barang = Barang::find($request->barang_id);
            $barang->increment('stok_total', $request->jumlah_masuk);
        });

        return redirect()->back()->with('success', 'Stok masuk dari supplier berhasil dicatat!');
    }
}