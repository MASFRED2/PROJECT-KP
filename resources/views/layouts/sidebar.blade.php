<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">FUZZA MART</span>
    </a>

    <div class="sidebar">
        <!-- Info User -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }} | <small>{{ strtoupper(Auth::user()->role) }}</small></a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                
                <!-- DASHBOARD (Semua Role) -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('shift.index') }}" class="nav-link {{ request()->is('shift*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>Shift Kasir</p>
                    </a>
                </li>

                {{-- MENU KHUSUS ADMIN --}}
                @if(Auth::user()->role == 'admin')
                <li class="nav-header">MANAJEMEN MASTER</li>

                <li class="nav-item">
                    <a href="{{ route('kategori.index') }}" class="nav-link {{ request()->is('kategori*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Kategori</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('barang.index') }}" class="nav-link {{ request()->is('barang*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Data Barang</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('stock-opname.index') }}" class="nav-link {{ request()->is('stock-opname*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>Stock Opname</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cabang.index') }}" class="nav-link {{ request()->is('cabang*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marked-alt"></i>
                        <p>Cabang Toko</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pemasok.index') }}" class="nav-link {{ request()->is('pemasok*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Data Pemasok</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('stok-masuk.index') }}" class="nav-link {{ request()->is('stok-masuk*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck-loading"></i>
                        <p>Stok Masuk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('diskon.index') }}" class="nav-link {{ request()->is('diskon*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-percent"></i>
                        <p>Set Diskon</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pelanggan.index') }}" class="nav-link {{ request()->is('pelanggan*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Member</p>
                    </a>
                </li>

                <li class="nav-header">LAPORAN & ANALITIK</li>
                @php
                    // Menghitung jumlah barang kritis secara instan untuk diletakkan pada badge sidebar
                    $jumlahKritis = \App\Models\Barang::whereRaw('stok_total <= stok_minimal')->count();
                @endphp

                <li class="nav-item">
                    <a href="{{ route('laporan.stok-rendah') }}" class="nav-link {{ request()->is('laporan/stok-rendah*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                        <p>
                            Stok Rendah
                            @if($jumlahKritis > 0)
                                <span class="badge badge-warning right px-2 font-weight-bold" style="background-color: #F59E0B; color: white; border-radius: 10px;">
                                    {{ $jumlahKritis }}
                                </span>
                            @endif
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.expired') }}" class="nav-link {{ request()->is('laporan/expired*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-times"></i>
                        <p>Kontrol Expired</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.analitik') }}" class="nav-link {{ request()->is('laporan/analitik*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Analitik Barang</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengeluaran.index') }}" class="nav-link {{ request()->is('pengeluaran*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>Biaya Operasional</p>
                    </a>
                </li>
                @endif

                {{-- MENU TRANSAKSI (Admin & Kasir) --}}
                @if(in_array(Auth::user()->role, ['admin', 'kasir']))
                <li class="nav-header">TRANSAKSI</li>
                <li class="nav-item">
                    <a href="{{ route('penjualan.index') }}" class="nav-link {{ request()->is('transaksi*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Kasir (POS)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('retur.index') }}" class="nav-link {{ request()->is('retur*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-undo"></i>
                        <p>Retur Penjualan</p>
                    </a>
                </li>
                @endif

                {{-- MENU KHUSUS PELANGGAN --}}
                @if(Auth::user()->role == 'pelanggan')
                <li class="nav-header">INFORMASI</li>
                <li class="nav-item">
                    <a href="{{ route('pelanggan.promo') }}" class="nav-link {{ request()->is('promo*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>Diskon Aktif</p>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>