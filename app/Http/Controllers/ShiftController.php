<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index()
    {
        // Cek apakah ada shift yang masih aktif untuk user ini di cabangnya
        $shiftAktif = Shift::where('user_id', Auth::id())
            ->whereNull('waktu_tutup')
            ->first();

        // Jika ada shift aktif, hitung total penjualan cash selama shift ini berjalan
        $totalPenjualanCash = 0;
        if ($shiftAktif) {
            $totalPenjualanCash = Penjualan::where('kasir_id', Auth::id())
                ->where('metode_pembayaran', 'cash')
                ->where('created_at', '>=', $shiftAktif->waktu_buka)
                ->sum('total_harga');
        }

        return view('admin.shift.index', compact('shiftAktif', 'totalPenjualanCash'));
    }

    public function bukaShift(Request $request)
    {
        $request->validate([
            'saldo_awal' => 'required|numeric|min:0',
        ]);

        // Proteksi ganda agar tidak bisa double open shift
        $cek = Shift::where('user_id', Auth::id())->whereNull('waktu_tutup')->count();
        if ($cek > 0) {
            return redirect()->back()->with('error', 'Anda masih memiliki shift yang aktif!');
        }

        Shift::create([
            'user_id' => Auth::id(),
            'cabang_id' => Auth::user()->cabang_id, // Mengunci otomatis sesuai cabang kasir
            'waktu_buka' => now(),
            'saldo_awal' => $request->saldo_awal,
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Shift berhasil dibuka. Selamat bertugas!');
    }

    public function tutupShift(Request $request)
    {
        $request->validate([
            'saldo_akhir' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $shiftAktif = Shift::where('user_id', Auth::id())->whereNull('waktu_tutup')->first();
        
        if (!$shiftAktif) {
            return redirect()->back()->with('error', 'Tidak ada shift aktif yang ditemukan.');
        }

        $shiftAktif->update([
            'waktu_tutup' => now(),
            'saldo_akhir' => $request->saldo_akhir,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('shift.index')->with('success', 'Shift berhasil ditutup. Terima kasih!');
    }
}