<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class PenjualanController extends Controller
{
    public function index()
    {
        // Cek apakah kasir sudah buka shift
        $shiftAktif = \App\Models\Shift::where('user_id', auth()->id())
            ->whereNull('waktu_tutup')
            ->first();

        // Jika belum buka shift, lempar ke halaman manajemen shift
        if (!$shiftAktif) {
            return redirect()->route('shift.index')->with('error', 'Anda harus membuka SHIFT (Input Saldo Awal) terlebih dahulu!');
        }

        return view('admin.penjualan.index');
    }

    public function cetakThermal($id)
    {
        $penjualan = Penjualan::with('cabang')->find($id);
        
        // Contoh menggunakan printer jaringan (IP Address)
        $connector = new NetworkPrintConnector("192.168.1.100", 9100);
        $printer = new Printer($connector);

        $printer->text("TOKO FREDY - CABANG " . $penjualan->cabang->nama_cabang . "\n");
        $printer->text("--------------------------------\n");
        // ... loop barang ...
        $printer->cut();
        $printer->close();
    }

    public function getBarang($barcode)
{
    // Cari barang berdasarkan kolom barcode
    $barang = Barang::where('barcode', $barcode)->first();

    if ($barang) {
        // Amankan response, kirim data mentah barangnya saja terlebih dahulu
        return response()->json([
            'status'  => 'success',
            'data'    => $barang
        ]);
    }

    return response()->json([
        'status' => 'error', 
        'message' => 'Barang dengan barcode ' . $barcode . ' tidak ditemukan di database!'
    ]);
}

    public function cetakStruk($id)
    {
        // Pastikan cabang dan pelanggan ikut dipanggil (Eager Loading)
        $penjualan = Penjualan::with(['detail_penjualan.barang', 'kasir', 'cabang', 'pelanggan'])->findOrFail($id);
        
        return view('admin.penjualan.struk', compact('penjualan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keranjang' => 'required|array',
            'metode_pembayaran' => 'required',
            'total_harga' => 'required|numeric',
            'pelanggan_id' => 'nullable|exists:users,id'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $tanggal = date('Ymd');
                $count = Penjualan::whereDate('created_at', date('Y-m-d'))->count() + 1;
                $no_invoice = 'POS-' . $tanggal . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                // 1. Simpan data ke tabel penjualan (Sudah ditambahkan cabang_id)
                $penjualan = Penjualan::create([
                    'no_invoice' => $no_invoice,
                    'kasir_id' => Auth::id(),
                    'cabang_id' => Auth::user()->cabang_id,
                    'pelanggan_id' => $request->pelanggan_id, // <-- Kunci ID pelanggan di sini
                    'total_harga' => $request->total_harga,
                    'total_bayar' => $request->total_bayar ?? $request->total_harga,
                    'kembalian' => $request->kembalian ?? 0,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'status_pembayaran' => $request->metode_pembayaran == 'cash' ? 'sukses' : 'pending',
                ]);

                // LOGIKA PENAMBAHAN POIN LOYALITAS
                if ($request->pelanggan_id) {
                    // Hitung poin: Kelipatan Rp 10.000 dapat 1 poin
                    $poinBaru = floor($request->total_harga / 10000);
                    
                    if ($poinBaru > 0) {
                        $customer = \App\Models\User::find($request->pelanggan_id);
                        $customer->increment('poin_loyalitas', $poinBaru);
                    }
                }

                // 2. Loop keranjang untuk simpan detail dan kurangi stok
                foreach ($request->keranjang as $item) {
                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'barang_id' => $item['id'],
                        'qty' => $item['qty'],
                        'harga_satuan' => $item['harga'],
                        'subtotal' => $item['harga'] * $item['qty'],
                    ]);

                    $barang = Barang::find($item['id']);
                    if ($barang->stok_total < $item['qty']) {
                        throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi!");
                    }
                    $barang->decrement('stok_total', $item['qty']);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaksi berhasil disimpan',
                    'id_penjualan' => $penjualan->id
                ]);
        });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}