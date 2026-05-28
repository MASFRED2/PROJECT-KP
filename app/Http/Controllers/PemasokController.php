<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    // Menampilkan daftar vendor / supplier
    public function index()
    {
        $pemasok = Pemasok::all();
        return view('admin.pemasok.index', compact('pemasok'));
    }

    // Menyimpan data vendor baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pemasok' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
        ]);

        Pemasok::create($request->all());

        return redirect()->back()->with('success', 'Data pemasok berhasil disimpan');
    }

    // Menghapus data vendor
    public function destroy($id)
    {
        Pemasok::find($id)->delete();
        return redirect()->back()->with('success', 'Data pemasok berhasil dihapus');
    }
}