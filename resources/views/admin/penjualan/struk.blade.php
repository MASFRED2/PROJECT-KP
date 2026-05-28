<!DOCTYPE html>
<html>
<head>
    <title>Cetak Struk</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 58mm; font-size: 12px; }
        .text-center { text-align: center; }
        hr { border-top: 1px dashed #000; }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); }">
    <div class="text-center">
        <h3>TOKO FREDY</h3>
        <p>Univ Pamulang<br>Inv: {{ $penjualan->no_invoice }}</p>
    </div>
    <hr>
    @foreach($penjualan->detail_penjualan as $item)
        <div>{{ $item->barang->nama_barang }}</div>
        <div>{{ $item->qty }} x {{ number_format($item->harga_satuan) }} = {{ number_format($item->subtotal) }}</div>
    @endforeach
    <hr>
    <div>Total: Rp {{ number_format($penjualan->total_harga) }}</div>
    <div>Bayar: Rp {{ number_format($penjualan->total_bayar) }}</div>
    <div>Kembali: Rp {{ number_format($penjualan->kembalian) }}</div>
    <hr>
    <div class="text-center">Terima Kasih</div>
</body>
</html>