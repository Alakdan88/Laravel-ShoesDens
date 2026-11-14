@extends('layouts.app')

@section('title', 'Pesanan Masuk - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem;">
        <h1 style="margin-bottom: 2rem;">Pesanan Masuk</h1>

        @if ($pesanan_list->isEmpty())
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; color: var(--gray-dark); margin-bottom: 1rem;">Belum ada pesanan masuk</p>
            </div>
        @else
            <div class="grid" style="gap: 1.5rem;">
                @foreach ($pesanan_list as $p)
                    <div class="card">
                        <div class="card-body" style="display: grid; grid-template-columns: 1fr auto; gap: 2rem; align-items: center;">
                            <div>
                                <h3 style="margin-top: 0; margin-bottom: 0.5rem;">{{ $p->kode_pesanan }}</h3>
                                <p style="margin: 0; color: var(--gray-dark); font-size: 0.9rem;">
                                    {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') }}
                                </p>
                                <div style="margin: 1rem 0; display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                                    <div>
                                        <small style="color: var(--gray-dark);">Item</small>
                                        <br>
                                        <strong>{{ $p->total_qty }} unit</strong>
                                    </div>
                                    <div>
                                        <small style="color: var(--gray-dark);">Total Pendapatan</small>
                                        <br>
                                        <strong>Rp {{ number_format($p->subtotal_harga, 0, ',', '.') }}</strong>
                                    </div>
                                    <div>
                                        <small style="color: var(--gray-dark);">Status</small>
                                        <br>
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
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <a href="{{ url('/penjual/pesanan/' . $p->id) }}" class="btn" style="text-decoration: none;">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
