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
<!-- ENGINE KASIR JAVASCRIPT (DIOPTIMALKAN) -->
<script>
let keranjang = [];

document.addEventListener("DOMContentLoaded", function() {
    const inputBarcode = document.getElementById('scan_barcode');
    const nominalBayar = document.getElementById('nominal_bayar');
    const btnProses = document.getElementById('btn_proses');

    // 1. Tangkap ketukan dari Barcode Scanner
    inputBarcode.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let barcode = this.value.trim();
            if (barcode !== '') {
                ambilDataBarang(barcode);
            }
            this.value = ''; 
        }
    });

    // 2. Ambil data barang dari server Laravel
    // 2. Ambil data barang dari server Laravel
    function ambilDataBarang(barcode) {
        // Trik jitu: Biarkan Laravel yang merakit URL-nya secara otomatis
        let url = "{{ route('kasir.get-barang', 'KODE_BARCODE') }}";
        url = url.replace('KODE_BARCODE', barcode);

        fetch(url)
            .then(response => {
                // Tangkap jika terjadi eror HTTP sebelum diproses ke JSON
                if (!response.ok) {
                    throw new Error(`Terjadi kesalahan jaringan (Status HTTP: ${response.status})`);
                }
                return response.json();
            })
            .then(res => {
                if (res.status === 'success') {
                    tambahKeKeranjang(res.data);
                } else {
                    alert(res.message);
                }
            })
            .catch(err => {
                console.error("Gagal memuat barang:", err);
                alert("Sistem gagal menghubungi database. Pastikan barcode benar dan rute aktif.");
            });
    }

    // 3. Masukkan item ke memori keranjang
    function tambahKeKeranjang(barang) {
        let index = keranjang.findIndex(item => item.id === barang.id);
        
        // Memastikan nama kolom harga sesuai dengan database (harga_jual / harga)
        let hargaItem = parseFloat(barang.harga_jual || barang.harga);

        if (index !== -1) {
            if (keranjang[index].qty >= barang.stok_total) {
                alert(`Stok produk ${barang.nama_barang} terbatas! Batas sisa: ${barang.stok_total}`);
                return;
            }
            keranjang[index].qty++;
        } else {
            if (barang.stok_total < 1) {
                alert(`Produk ${barang.nama_barang} habis total!`);
                return;
            }
            keranjang.push({
                id: barang.id,
                nama_barang: barang.nama_barang,
                harga: hargaItem,
                stok_maksimal: barang.stok_total,
                qty: 1
            });
        }
        renderKeranjang();
    }

    // 4. Render Tabel (Hanya digambar ulang saat ada barang baru/dihapus)
    window.renderKeranjang = function() {
        const body = document.getElementById('keranjang_body');
        body.innerHTML = '';

        keranjang.forEach((item, index) => {
            let subtotal = item.harga * item.qty;

            body.innerHTML += `
                <tr style="border-bottom: 1px solid #E2E8F0;">
                    <td class="font-weight-bold text-dark py-3">${item.nama_barang}</td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(item.harga)}</td>
                    <td>
                        <!-- Menggunakan oninput agar merespons seketika tanpa perlu klik luar -->
                        <input type="number" class="form-control form-control-sm text-center font-weight-bold" 
                               value="${item.qty}" min="1" max="${item.stok_maksimal}" 
                               oninput="updateQty(${index}, this)" style="width: 70px; border-radius: 6px;">
                    </td>
                    <td class="font-weight-bold text-dark" id="subtotal_${index}">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-light text-danger" onclick="hapusItem(${index})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        hitungTotalKeseluruhan();
    }

    // 5. Fitur Update Real-time Tanpa Menghilangkan Fokus Kursor
    window.updateQty = function(index, element) {
        let qtyBaru = parseInt(element.value);
        
        // Mencegah input kosong atau huruf
        if(isNaN(qtyBaru) || qtyBaru < 1) return;

        // Proteksi ketat agar qty tidak melebihi stok fisik rak
        if(qtyBaru > keranjang[index].stok_maksimal) {
            alert(`Maksimal stok tersedia hanya ${keranjang[index].stok_maksimal} pcs!`);
            element.value = keranjang[index].stok_maksimal;
            qtyBaru = keranjang[index].stok_maksimal;
        }

        keranjang[index].qty = qtyBaru;
        
        // Update Teks Subtotal hanya di baris tersebut
        let subtotalBaru = keranjang[index].harga * qtyBaru;
        document.getElementById(`subtotal_${index}`).innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotalBaru);
        
        hitungTotalKeseluruhan();
    }

    window.hapusItem = function(index) {
        keranjang.splice(index, 1);
        renderKeranjang();
    }

    // 6. Hitung Total Pembayaran & Kembalian
    window.hitungTotalKeseluruhan = function() {
        let totalHarga = 0;
        keranjang.forEach(item => {
            totalHarga += (item.harga * item.qty);
        });
        
        // Simpan ke atribut memori elemen agar akurat
        document.getElementById('total_tampilan').setAttribute('data-total', totalHarga);
        document.getElementById('total_tampilan').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalHarga);
        
        hitungKembalian();
    }

    nominalBayar.addEventListener('input', hitungKembalian);
    
    function hitungKembalian() {
        let totalHarga = parseFloat(document.getElementById('total_tampilan').getAttribute('data-total')) || 0;
        let bayar = parseFloat(nominalBayar.value) || 0;
        let kembalian = bayar - totalHarga;

        if (kembalian >= 0 && totalHarga > 0) {
            document.getElementById('kembalian_tampilan').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(kembalian);
        } else {
            document.getElementById('kembalian_tampilan').innerText = 'Rp 0';
        }
    }


    // 7. Simpan Transaksi POS via AJAX

    btnProses.addEventListener('click', function() {
        if (keranjang.length === 0) return alert('Keranjang belanja masih kosong!');
        
        let totalHarga = parseFloat(document.getElementById('total_tampilan').getAttribute('data-total')) || 0;
        let bayar = parseFloat(nominalBayar.value) || 0;
        let metode = document.getElementById('metode_bayar').value;

        if (metode === 'cash' && bayar < totalHarga) {
            return alert('Uang tunai yang diterima kurang dari nilai total tagihan!');
        }

        // Ubah teks tombol menjadi loading agar kasir tahu sistem sedang bekerja
        let originalText = btnProses.innerHTML;
        btnProses.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sedang Memproses...';
        btnProses.disabled = true;

        let dataTransaksi = {
            _token: "{{ csrf_token() }}",
            keranjang: keranjang,
            metode_pembayaran: metode,
            pelanggan_id: document.getElementById('pelanggan_id').value || null,
            total_harga: totalHarga,
            total_bayar: metode === 'cash' ? bayar : totalHarga,
            kembalian: metode === 'cash' ? (bayar - totalHarga) : 0
        };

        fetch("{{ route('penjualan.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(dataTransaksi)
        })
        .then(response => response.json())
        .then(res => {
            btnProses.innerHTML = originalText;
            btnProses.disabled = false;

            if (res.status === 'success') {
                // Alihkan (Redirect) tab yang sama ke halaman struk secara paksa
                window.location.href = `/transaksi/cetak/${res.id_penjualan}`;
            } else {
                alert('Gagal menyimpan transaksi: ' + res.message);
            }
        })
        .catch(err => {
            btnProses.innerHTML = originalText;
            btnProses.disabled = false;
            console.error("Eror POST transaksi:", err);
            alert("Terjadi kesalahan fatal pada server. Cek log console!");
        });
    });
});
</script>
@endpush