<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barang';
    protected $fillable = ['kategori_id', 'barcode', 'nama_barang', 'stok_total', 'stok_minimal', 'harga_jual'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function stok_masuk(): HasMany
    {
        return $this->hasMany(StokMasuk::class, 'barang_id');
    }

    public function diskon(): HasMany
    {
        return $this->hasMany(Diskon::class, 'barang_id');
    }

    // --- TAMBAHKAN KODE DI BAWAH INI ---
    public function penjualan_detail(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'barang_id');
    }
    // ----------------------------------

    public function diskon_aktif()
    {
        return $this->diskon()
            ->where('status_aktif', true)
            ->whereDate('tgl_mulai', '<=', now())
            ->whereDate('tgl_selesai', '>=', now())
            ->first();
    }
}