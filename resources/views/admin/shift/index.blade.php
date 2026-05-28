@extends('layouts.master')

@section('isi')
<div class="container-fluid pt-4">
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="background-color: #EF4444; color: white;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="background-color: #22C55E; color: white;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-6">
            @if(!$shiftAktif)
                <!-- TAMPILAN JIKA BELUM BUKA SHIFT -->
                <div class="card border-0 shadow-sm bg-white">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-cash-register fa-4x text-muted"></i>
                        </div>
                        <h4 class="font-weight-bold" style="color: #334155;">Mulai Shift Baru</h4>
                        <p class="text-muted small mb-4">Masukkan nominal uang tunai yang ada di laci kas saat ini sebagai modal awal transaksi.</p>
                        
                        <form action="{{ route('shift.buka') }}" method="POST" class="text-left">
                            @csrf
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted">Saldo Awal Laci (Rp)</label>
                                <input type="number" name="saldo_awal" class="form-control form-control-lg font-weight-bold text-center" placeholder="0" required style="border-radius: 8px; height: 55px; font-size: 1.4rem; border-color: #B69377;">
                            </div>
                            <button type="submit" class="btn btn-block btn-lg text-white" style="background-color: #B69377; py: 12px; border-radius: 8px;">
                                <i class="fas fa-play-circle mr-2"></i> Buka Laci Kasir
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- TAMPILAN JIKA SHIFT SEDANG AKTIF -->
                <div class="card border-0 shadow-sm bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="font-weight-bold mb-0" style="color: #334155;">🟢 Status Shift: AKTIF</h5>
                            <span class="badge text-white px-3 py-2" style="background-color: #B69377;">
                                {{ Auth::user()->name }}
                            </span>
                        </div>
                        
                        <div class="bg-light p-3 rounded-lg mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Waktu Mulai</span>
                                <!-- Optimasi menggunakan format Carbon native Laravel -->
                                <span class="font-weight-bold">{{ $shiftAktif->waktu_buka->format('d M Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Modal Awal</span>
                                <span class="font-weight-bold">Rp {{ number_format($shiftAktif->saldo_awal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-success font-weight-bold">
                                <span>Penjualan Tunai (Sistem)</span>
                                <span>+ Rp {{ number_format($totalPenjualanCash, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between font-weight-bold h5 mb-0 text-dark">
                                <span>Ekspektasi Kas di Laci</span>
                                <span>Rp {{ number_format($shiftAktif->saldo_awal + $totalPenjualanCash, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <form action="{{ route('shift.tutup') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-uppercase text-muted">Uang Fisik Aktual di Laci (Rp)</label>
                                <input type="number" name="saldo_akhir" class="form-control form-control-lg font-weight-bold" placeholder="Hitung uang fisik di laci..." required style="border-radius: 8px;">
                            </div>
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted">Catatan Selisih (Jika ada)</label>
                                <!-- Perbaikan tag penutup dari </radius> menjadi </textarea> -->
                                <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Kurang 500 rupiah karena tidak ada kembalian pecahan kecil..." style="border-radius: 8px;"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block btn-lg" style="background-color: #EF4444; border: none; border-radius: 8px;" onclick="return confirm('Apakah Anda yakin ingin menutup shift saat ini?')">
                                <i class="fas fa-stop-circle mr-2"></i> Tutup Shift & Setor Uang
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection