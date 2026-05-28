@extends('layouts.master')

@section('judul', 'Dashboard Utama')

@section('isi')
<div class="container-fluid">
    <div class="row">
        {{-- Tampilan untuk ADMIN --}}
        @if(Auth::user()->role == 'admin')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>150</h3>
                        <p>Total Barang</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>5</h3>
                        <p>Barang Expired</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tampilan untuk KASIR --}}
        @if(Auth::user()->role == 'kasir')
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Selamat Datang, Kasir!</h5>
                        <p>Siap melayani pelanggan hari ini? Klik menu Kasir di samping untuk mulai transaksi.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tampilan untuk PELANGGAN --}}
        @if(Auth::user()->role == 'pelanggan')
            <div class="col-12">
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-bullhorn"></i> Info Promo!</h5>
                    Ada diskon 20% untuk pembelian beras hari ini. Cek menu Diskon Aktif!
                </div>
            </div>
        @endif
    </div>
</div>
@endsection