<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // Fungsi untuk melihat barang yang mendekati expired
    public function expired()
    {
        $data = StokMasuk::with('barang')
            ->whereNotNull('tgl_kadaluwarsa')
            ->whereDate('tgl_kadaluwarsa', '<=', now()->addDays(30))
            ->orderBy('tgl_kadaluwarsa', 'asc')
            ->get();

        return view('admin.laporan.expired', compact('data'));
    }

    // Fungsi untuk melihat barang yang jarang dibeli (Slow Moving)
    public function analitik()
    {
        // Mengambil 10 barang dengan jumlah transaksi paling sedikit
        $slowMoving = Barang::withCount('penjualan_detail')
            ->orderBy('penjualan_detail_count', 'asc')
            ->take(10)
            ->get();

        return view('admin.laporan.analitik', compact('slowMoving'));
    }

    public function stokRendah()
    {
        // Mengambil barang yang stok_total-nya sudah kurang dari atau sama dengan stok_minimal
        $barangKritis = \App\Models\Barang::with('kategori')
            ->whereRaw('stok_total <= stok_minimal')
            ->orderBy('stok_total', 'asc')
            ->get();

        return view('admin.laporan.stok_rendah', compact('barangKritis'));
    }
}