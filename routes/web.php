<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\StokMasukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ReturPenjualanController;

// Halaman utama (Welcome)
Route::get('/', function () {
    return view('welcome');
});

// Semua rute di bawah ini harus LOGIN dulu
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rute Profil (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
        // Route untuk Kategori
        Route::resource('kategori', KategoriController::class);
        
        // Route untuk Barang
        Route::resource('barang', BarangController::class);

        // Route untuk Stok Masuk
        Route::resource('stok-masuk', StokMasukController::class);

        Route::resource('retur', ReturPenjualanController::class);
        Route::get('/ambil-invoice/{no_invoice}', [ReturPenjualanController::class, 'getInvoice']);
    });

    Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    // Halaman utama kasir
    Route::get('/transaksi', [PenjualanController::class, 'index'])->name('penjualan.index');
    // API untuk ambil data barang saat di-scan (untuk kebutuhan JavaScript)
    Route::get('/kasir/get-barang/{barcode}', [PenjualanController::class, 'getBarang'])->name('kasir.get-barang');
    // Simpan transaksi
    Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('penjualan.store');
    });

    Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift/buka', [ShiftController::class, 'bukaShift'])->name('shift.buka');
    Route::post('/shift/tutup', [ShiftController::class, 'tutupShift'])->name('shift.tutup');

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/laporan/expired', [LaporanController::class, 'expired'])->name('laporan.expired');
        Route::get('/laporan/analitik', [LaporanController::class, 'analitik'])->name('laporan.analitik');
        Route::get('/laporan/stok-rendah', [LaporanController::class, 'stokRendah'])->name('laporan.stok-rendah');
    });

    // Rute untuk Admin
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::resource('diskon', DiskonController::class);
        Route::resource('cabang', CabangController::class);
        Route::resource('pemasok', PemasokController::class);
        Route::resource('pelanggan', PelangganController::class);
        Route::resource('pengeluaran', PengeluaranController::class);
        Route::resource('stock-opname', StockOpnameController::class);
    });

    // Rute untuk Pelanggan (Hanya Lihat)
    Route::middleware(['auth', 'role:pelanggan'])->group(function () {
        Route::get('/promo-diskon', [DiskonController::class, 'daftarPromo'])->name('pelanggan.promo');
    });

    Route::get('/transaksi/cetak/{id}', [PenjualanController::class, 'cetakStruk'])->name('penjualan.cetak');

    
});

require __DIR__.'/auth.php';