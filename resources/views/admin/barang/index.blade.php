@extends('layouts.master')

@section('judul', 'Manajemen Data Barang')

@section('isi')
<div class="row">
    <div class="col-12">
        {{-- Notifikasi Sukses --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahBarang">
                    <i class="fas fa-plus"></i> Tambah Barang Baru
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang as $b)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge badge-secondary">{{ $b->barcode }}</span></td>
                            <td>{{ $b->nama_barang }}</td>
                            <td>{{ $b->kategori->nama_kategori }}</td>
                            <td>Rp {{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                            <td>
                                @if($b->stok_total <= $b->stok_minimal)
                                    <span class="text-danger font-weight-bold">{{ $b->stok_total }} (Stok Menipis!)</span>
                                @else
                                    {{ $b->stok_total }}
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Barang --}}
<div class="modal fade" id="modalTambahBarang">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('barang.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Input Data Barang</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Barcode (Scan di sini)</label>
                                {{-- Autofocus agar saat modal buka, scanner langsung bisa jalan --}}
                                <input type="text" name="barcode" class="form-control" autofocus required>
                            </div>
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori_id" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategori as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga Jual (Rp)</label>
                                <input type="number" name="harga_jual" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Stok Minimal (Untuk Peringatan)</label>
                                <input type="number" name="stok_minimal" class="form-control" value="5" required>
                            </div>
                            {{-- Stok total awal biasanya 0, bertambah lewat fitur Stok Masuk --}}
                            <input type="hidden" name="stok_total" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection