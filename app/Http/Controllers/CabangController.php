<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    // Menampilkan daftar cabang toko
    public function index()
    {
        $cabang = Cabang::all();
        return view('admin.cabang.index', compact('cabang'));
    }

    // Menyimpan data cabang baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'alamat' => 'required|string',
        ]);

        Cabang::create($request->all());

        return redirect()->back()->with('success', 'Cabang baru berhasil didaftarkan');
    }

    // Menghapus data cabang
    public function destroy($id)
    {
        Cabang::find($id)->delete();
        return redirect()->back()->with('success', 'Cabang berhasil dihapus');
    }
}