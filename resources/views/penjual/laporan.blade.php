@extends('layouts.app')

@section('title', 'Laporan Penjualan - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem;">
        <h1 style="margin-bottom: 2rem;">Laporan Penjualan</h1>

        <!-- Filter -->
        <form method="GET" action="{{ url('/penjual/laporan') }}" class="card" style="margin-bottom: 2rem;">
            <div class="card-body" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="start_date">Tanggal Mulai</label>
                    <input 
                        type="date" 
                        id="start_date" 
                        name="start_date"
                        value="{{ $start_date }}"
                    >
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="end_date">Tanggal Akhir</label>
                    <input 
                        type="date" 
                        id="end_date" 
                        name="end_date"
                        value="{{ $end_date }}"
                    >
                </div>

                <button type="submit" class="btn">Filter</button>
            </div>
        </form>

        <!-- Statistik -->
        <div class="grid grid-4" style="margin-bottom: 2rem;">
            <div class="card">
                <div class="card-body">
                    <h4 style="margin-top: 0; color: var(--gray-dark);">Total Pesanan</h4>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                        {{ $total_pesanan }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 style="margin-top: 0; color: var(--gray-dark);">Total Item</h4>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--secondary-color);">
                        {{ $total_qty }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 style="margin-top: 0; color: var(--gray-dark);">Total Pendapatan</h4>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--success-color);">
                        Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 style="margin-top: 0; color: var(--gray-dark);">Rata-rata/Pesanan</h4>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--info-color);">
                        Rp {{ number_format($rata_rata, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Pesanan -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;">Rincian Pesanan</h3>
            </div>
            <div class="card-body">
                @if ($pesanan_list->isEmpty())
                    <p style="text-align: center; color: var(--gray-dark);">Tidak ada pesanan dalam periode ini</p>
                @else
                    <table class="table" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Kode Pesanan</th>
                                <th>Tanggal</th>
                                <th>Item</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pesanan_list as $p)
                                <tr>
                                    <td>{{ $p->kode_pesanan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                                    <td>{{ $p->jumlah_item }} item</td>
                                    <td>Rp {{ number_format($p->subtotal_harga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="product-badge" style="position: static; display: inline-block; background-color: 
                                            @switch($p->status)
                                                @case('pembayaran_sukses') @case('diproses') var(--info-color) @break
                                                @case('dikirim') var(--secondary-color) @break
                                                @case('selesai') var(--success-color) @break
                                                @default var(--gray-dark)
                                            @endswitch
                                        ">
                                            @switch($p->status)
                                                @case('pembayaran_sukses')
                                                    Pembayaran Sukses
                                                    @break
                                                @case('diproses')
                                                    Diproses
                                                    @break
                                                @case('dikirim')
                                                    Dikirim
                                                    @break
                                                @case('selesai')
                                                    Selesai
                                                    @break
                                                @default
                                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                            @endswitch
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
