@extends('layouts.app')

@section('title', 'Dashboard Penjual - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem;">
        <h1 style="margin-bottom: 2rem;">Dashboard Penjual</h1>

        <!-- Stats Cards -->
        <div class="grid grid-4" style="margin-bottom: 2rem;">
            <div class="card">
                <div class="card-body">
                    <h4 style="margin-top: 0; color: var(--gray-dark);">Pesanan Hari Ini</h4>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                        {{ $pesanan_hari_ini }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 style="margin-top: 0; color: var(--gray-dark);">Pendapatan Bulan Ini</h4>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--success-color);">
                        Rp {{ number_format($pendapatan_bulan, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 style="margin-top: 0; color: var(--gray-dark);">Total Produk</h4>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--secondary-color);">
                        {{ $total_produk }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 style="margin-top: 0; color: var(--gray-dark);">Stok Menipis</h4>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--warning-color);">
                        {{ $stok_terbatas->count() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick actions -->
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <a href="{{ url('/penjual/produk') }}" class="btn" style="text-decoration: none;">Kelola Produk</a>
            <a href="{{ url('/penjual/produk/tambah') }}" class="btn" style="background-color: var(--success-color); text-decoration: none;">Tambah Produk Baru</a>
            <a href="{{ url('/penjual/pesanan') }}" class="btn" style="background-color: var(--secondary-color); text-decoration: none;">Kelola Pesanan</a>
            <a href="{{ url('/penjual/laporan') }}" class="btn" style="background-color: var(--info-color); text-decoration: none;">Lihat Laporan</a>
        </div>

        <!-- Low Stock Products -->
        @if ($stok_terbatas->isNotEmpty())
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0;">Produk Stok Terbatas (&lt; 5)</h3>
                </div>
                <div class="card-body">
                    <table class="table" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stok_terbatas as $produk)
                                <tr>
                                    <td>{{ $produk->nama }}</td>
                                    <td>
                                        <span style="color: var(--danger-color); font-weight: bold;">
                                            {{ $produk->stok_total }} unit
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ url('/penjual/produk/' . $produk->id . '/edit') }}" class="btn btn-small" style="text-decoration: none;">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;">Pesanan Terbaru</h3>
            </div>
            <div class="card-body">
                @if (empty($pesanan_terbaru) || count($pesanan_terbaru) === 0)
                    <p style="text-align: center; color: var(--gray-dark);">Belum ada pesanan</p>
                @else
                    <table class="table" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Kode Pesanan</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pesanan_terbaru as $p)
                                <tr>
                                    <td>
                                        <a href="{{ url('/penjual/pesanan/' . $p->id) }}" style="color: var(--secondary-color); text-decoration: none; font-weight: bold;">
                                            {{ $p->kode_pesanan }}
                                        </a>
                                    </td>
                                    <td>{{ $p->total_qty }} item</td>
                                    <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="product-badge" style="position: static; display: inline-block; background-color: 
                                            @switch($p->status)
                                                @case('menunggu_pembayaran') var(--warning-color) @break
                                                @case('pembayaran_sukses') @case('diproses') var(--info-color) @break
                                                @case('dikirim') var(--secondary-color) @break
                                                @case('selesai') var(--success-color) @break
                                                @case('dibatalkan') var(--danger-color) @break
                                                @default var(--gray-dark)
                                            @endswitch
                                        ">
                                            @switch($p->status)
                                                @case('menunggu_pembayaran') Menunggu Pembayaran @break
                                                @case('pembayaran_sukses') Pembayaran Sukses @break
                                                @case('diproses') Diproses @break
                                                @case('dikirim') Dikirim @break
                                                @case('selesai') Selesai @break
                                                @case('dibatalkan') Dibatalkan @break
                                                @default {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection

