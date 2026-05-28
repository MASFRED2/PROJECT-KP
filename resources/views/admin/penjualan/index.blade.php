@extends('layouts.master')

@section('judul', '') {{-- Kosongkan judul agar lebih clean --}}

@section('isi')
<div class="container-fluid pt-3">
    <div class="row">
        <!-- Area Input & Keranjang -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="input-group input-group-lg mb-4 shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0">
                                <i class="fas fa-barcode text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="scan_barcode" 
                               class="form-control border-left-0 pl-0" 
                               placeholder="Scan barcode atau ketik nama barang..." 
                               autofocus 
                               style="font-size: 1.1rem;">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless align-middle" id="tabel_keranjang">
                            <thead>
                                <tr>
                                    <th class="rounded-left">Produk</th>
                                    <th>Harga</th>
                                    <th width="120">Qty</th>
                                    <th>Subtotal</th>
                                    <th class="rounded-right text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="keranjang_body">
                                <!-- Data via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Area Ringkasan Pembayaran -->
        <div class="col-lg-4">
            <div class="card shadow-lg border-0" style="background-color: #FFFFFF;">
                <div class="card-body p-4">
                    <div class="text-right mb-4">
                        <p class="text-muted mb-0 font-weight-bold">Total Pembayaran</p>
                        <h1 id="total_tampilan" class="display-4 font-weight-bold text-dark" style="letter-spacing: -1px;">
                            Rp 0
                        </h1>
                    </div>

                    <hr class="my-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-uppercase">Pelanggan / Member</label>
                        <select id="pelanggan_id" class="form-control form-control-lg custom-select">
                            <option value="">-- Umum / Non-Member --</option>
                            @foreach(\App\Models\User::where('role', 'pelanggan')->get() as $cust)
                                <option value="{{ $cust->id }}">👤 {{ $cust->name }} ({{ $cust->telepon }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-uppercase">Metode Pembayaran</label>
                        <div class="d-flex flex-wrap gap-2">
                            {{-- Button Group untuk pilihan cepat --}}
                            <select id="metode_bayar" class="form-control form-control-lg custom-select">
                                <option value="cash">💵 Tunai</option>
                                <option value="qris">📱 QRIS / E-Wallet</option>
                                <option value="debit">💳 Debit BCA</option>
                            </select>
                        </div>
                    </div>

                    <div id="input_bayar_tunai">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-uppercase">Uang Diterima</label>
                            <input type="number" id="nominal_bayar" 
                                   class="form-control form-control-lg text-right font-weight-bold" 
                                   placeholder="0" 
                                   style="height: 60px; font-size: 1.5rem; border-color: #B69377;">
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-lg mb-4 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Kembalian</span>
                        <h4 id="kembalian_tampilan" class="mb-0 font-weight-bold text-dark">Rp 0</h4>
                    </div>

                    <button type="button" class="btn btn-warning btn-block btn-lg shadow-sm py-3" id="btn_proses">
                        <span class="h5 font-weight-bold mb-0">PROSES BAYAR</span>
                    </button>
                </div>
            </div>
            
            <!-- Info Notifikasi Kecil -->
            <div class="alert mt-3 border-0 bg-white shadow-sm" style="border-left: 4px solid #F59E0B !important;">
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Tekan <b>F9</b> untuk cari barang manual atau <b>F10</b> untuk bayar.</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let keranjang = [];

    // Fungsi saat barcode di-scan
    $('#scan_barcode').on('keypress', function(e) {
        if (e.which == 13) { // 13 adalah tombol Enter (otomatis dikirim scanner)
            let barcode = $(this).val();
            tambahBarang(barcode);
            $(this).val(''); // Kosongkan input lagi
        }
    });

    function tambahBarang(barcode) {
        $.get(`/api/barang/${barcode}`, function(res) {
            if (res.status == 'success') {
                let barang = res.data;
                let harga = barang.harga_jual;
                
                // Hitung diskon jika ada
                if (res.diskon > 0) {
                    if (res.jenis_diskon == 'persentase') {
                        harga = harga - (harga * (res.diskon / 100));
                    } else {
                        harga = harga - res.diskon;
                    }
                }

                // Masukkan ke array keranjang
                keranjang.push({
                    id: barang.id,
                    nama: barang.nama_barang,
                    harga: harga,
                    qty: 1
                });

                renderKeranjang();
            } else {
                alert('Barang tidak ditemukan!');
            }
        });
    }

    function renderKeranjang() {
        let html = '';
        let total = 0;
        
        keranjang.forEach((item, index) => {
            let subtotal = item.harga * item.qty;
            total += subtotal;
            html += `
                <tr>
                    <td>${item.nama}</td>
                    <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td><input type="number" value="${item.qty}" class="form-control form-control-sm"></td>
                    <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="hapusItem(${index})">&times;</button></td>
                </tr>
            `;
        });

        $('#keranjang_body').html(html);
        $('#total_tampilan').text('Rp ' + total.toLocaleString('id-ID'));
    }

    function hapusItem(index) {
        keranjang.splice(index, 1);
        renderKeranjang();
    }

    // Fungsi untuk memproses transaksi
$('#btn_proses').on('click', function() {
    if (keranjang.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }

    let metode = $('#metode_bayar').val();
    let total = totalBelanja(); // Fungsi pembantu hitung total
    let bayar = $('#nominal_bayar').val();
    let kembalian = bayar - total;

    if (metode === 'cash' && bayar < total) {
        alert('Uang bayar kurang!');
        return;
    }

    // Jika metode QRIS atau Debit, kita munculkan loading simulasi
    if (metode !== 'cash') {
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menunggu Pembayaran...');
        
        // Simulasi menunggu pembayaran otomatis selama 3 detik
        setTimeout(() => {
            kirimDataKeServer(metode, total, total, 0);
        }, 3000);
    } else {
        kirimDataKeServer(metode, total, bayar, kembalian);
    }
});

function totalBelanja() {
    return keranjang.reduce((sum, item) => sum + (item.harga * item.qty), 0);
}

function kirimDataKeServer(metode, total, bayar, kembalian) {
    $.ajax({
        url: "{{ route('penjualan.store') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            keranjang: keranjang,
            metode_pembayaran: metode,
            pelanggan_id: $('#pelanggan_id').val(),
            total_harga: total,
            total_bayar: bayar,
            kembalian: kembalian
        },
        success: function(res) {
            if (res.status === 'success') {
                alert('Transaksi Berhasil!');
                // Fungsi untuk cetak struk (akan kita buat di langkah berikutnya)
                cetakStruk(res.id_penjualan);
                location.reload(); // Refresh halaman setelah sukses
            }
        },
        error: function(err) {
            alert('Gagal simpan transaksi: ' + err.responseJSON.message);
            $('#btn_proses').prop('disabled', false).text('PROSES TRANSAKSI');
        }
    });
}

function cetakStruk(id) {
    let url = "{{ url('/transaksi/cetak') }}/" + id;
    window.open(url, '_blank', 'width=300,height=600');
}

</script>
@endpush