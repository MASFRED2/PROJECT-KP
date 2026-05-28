<?php

namespace App\Http\Controllers;

use App\Models\ReturPenjualan;
use App\Models\Penjualan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturPenjualanController extends Controller
{
    public function index()
    {
        // Menampilkan seluruh riwayat barang yang diretur pembeli
        $retur = ReturPenjualan::with('penjualan.kasir')->latest()->get();
        return view('admin.retur.index', compact('retur'));
    }

    // Fungsi AJAX untuk memeriksa validitas nomor invoice saat diketik kasir
    public function getInvoice($no_invoice)
    {
        $penjualan = Penjualan::with('detail_penjualan.barang')->where('no_invoice', $no_invoice)->first();

        if ($penjualan) {
            return response()->json([
                'status' => 'success',
                'data' => $penjualan
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Nomor Invoice tidak ditemukan']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id' => 'required|exists:penjualan,id',
            'alasan' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $penjualan = Penjualan::with('detail_penjualan')->find($request->penjualan_id);

                // 1. Catat data retur ke dalam tabel retur_penjualan
                ReturPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'alasan' => $request->alasan,
                    'total_refund' => $penjualan->total_harga // Refund penuh sesuai nilai transaksi
                ]);

                // 2. Kembalikan stok masing-masing barang ke tabel 'barang'
                foreach ($penjualan->detail_penjualan as $detail) {
                    $barang = Barang::find($detail->barang_id);
                    if ($barang) {
                        $barang->increment('stok_total', $detail->qty);
                    }
                }

                // 3. Update status pembayaran pada invoice asli menjadi 'retur'
                $penjualan->update([
                    'status_pembayaran' => 'retur'
                ]);
        });

            return redirect()->back()->with('success', 'Proses retur berhasil! Stok barang telah dikembalikan ke gudang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses retur: ' . $e->getMessage());
        }
    }
}