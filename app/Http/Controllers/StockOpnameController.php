<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        // Ambil riwayat audit stok beserta data barangnya
        $opname = StockOpname::with('barang')->latest()->get();
        // Ambil data barang untuk pilihan di modal formulir
        $barang = Barang::all();

        return view('admin.stock_opname.index', compact('opname', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $barang = Barang::find($request->barang_id);
            
            $stokSistem = $barang->stok_total;
            $stokFisik = $request->stok_fisik;
            $selisih = $stokFisik - $stokSistem;

            // 1. Catat riwayat audit ke tabel stock_opname
            StockOpname::create([
                'barang_id' => $request->barang_id,
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $stokFisik,
                'selisih' => $selisih,
                'keterangan' => $request->keterangan,
            ]);

            // 2. Sinkronkan stok_total di tabel barang sesuai jumlah fisik riil
            $barang->update([
                'stok_total' => $stokFisik
            ]);
        });

        return redirect()->back()->with('success', 'Stock Opname berhasil diproses. Stok sistem telah disinkronkan!');
    }
}