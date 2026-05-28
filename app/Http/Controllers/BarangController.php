<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::with('kategori')->get();
        $kategori = Kategori::all();
        return view('admin.barang.index', compact('barang', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required',
            'barcode' => 'required|unique:barang',
            'nama_barang' => 'required',
            'harga_jual' => 'required|numeric',
            'stok_minimal' => 'required|numeric',
        ]);

        Barang::create($request->all());

        return redirect()->back()->with('success', 'Barang berhasil disimpan');
    }
}