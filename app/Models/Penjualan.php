<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'no_invoice', 
        'kasir_id', 
        'cabang_id', 
        'total_harga', 
        'total_bayar', 
        'kembalian', 
        'metode_pembayaran', 
        'status_pembayaran'
    ];

    // Relasi ke detail item yang dibeli dalam invoice ini
    public function detail_penjualan(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'penjualan_id');
    }

    // Relasi ke cabang tempat transaksi ini dicetak
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    // Relasi ke user kasir yang melayani transaksi
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}