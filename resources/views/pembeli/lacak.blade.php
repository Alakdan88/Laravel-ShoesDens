@extends('layouts.app')

@section('title', 'Lacak Pesanan - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem;">
        <div style="margin-bottom: 2rem;">
            <a href="{{ url('/pembeli/pesanan') }}" style="color: var(--secondary-color); text-decoration: none;">← Kembali ke Pesanan</a>
        </div>

        <h1 style="margin-bottom: 2rem;">Lacak Pesanan</h1>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Status Pengiriman</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; gap: 2rem;">
                        <!-- Step 1: Pesanan Dibuat -->
                        <div style="display: flex; gap: 1rem;">
                            <div style="text-align: center; min-width: 60px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: 
                                    {{ $pesanan->status !== 'menunggu_pembayaran' ? 'var(--success-color)' : 'var(--warning-color)' }};
                                    display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    {{ $pesanan->status !== 'menunggu_pembayaran' ? '✓' : '1' }}
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <strong>Pesanan Dibuat</strong>
                                <p style="color: var(--gray-dark); margin: 0.25rem 0 0;">
                                    {{ \Carbon\Carbon::parse($pesanan->created_at)->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        <!-- Step 2: Pembayaran -->
                        <div style="display: flex; gap: 1rem;">
                            <div style="text-align: center; min-width: 60px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: 
                                    {{ in_array($pesanan->status, ['pembayaran_sukses', 'diproses', 'dikirim', 'selesai']) ? 'var(--success-color)' : (in_array($pesanan->status, ['menunggu_pembayaran']) ? 'var(--gray-medium)' : 'var(--info-color)') }};
                                    display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    {{ in_array($pesanan->status, ['pembayaran_sukses', 'diproses', 'dikirim', 'selesai']) ? '✓' : '2' }}
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <strong>Pembayaran Dikonfirmasi</strong>
                                <p style="color: var(--gray-dark); margin: 0.25rem 0 0;">
                                    {{ in_array($pesanan->status, ['pembayaran_sukses', 'diproses', 'dikirim', 'selesai']) ? 'Pembayaran berhasil' : 'Menunggu...' }}
                                </p>
                            </div>
                        </div>

                        <!-- Step 3: Diproses -->
                        <div style="display: flex; gap: 1rem;">
                            <div style="text-align: center; min-width: 60px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: 
                                    {{ in_array($pesanan->status, ['diproses', 'dikirim', 'selesai']) ? 'var(--success-color)' : (in_array($pesanan->status, ['pembayaran_sukses', 'diproses', 'dikirim', 'selesai']) ? 'var(--info-color)' : 'var(--gray-medium)') }};
                                    display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    {{ in_array($pesanan->status, ['diproses', 'dikirim', 'selesai']) ? '✓' : '3' }}
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <strong>Pesanan Diproses</strong>
                                <p style="color: var(--gray-dark); margin: 0.25rem 0 0;">
                                    Penjual sedang mempersiapkan pesanan
                                </p>
                            </div>
                        </div>

                        <!-- Step 4: Dikirim -->
                        <div style="display: flex; gap: 1rem;">
                            <div style="text-align: center; min-width: 60px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: 
                                    {{ in_array($pesanan->status, ['dikirim', 'selesai']) ? 'var(--success-color)' : (in_array($pesanan->status, ['diproses', 'dikirim', 'selesai']) ? 'var(--info-color)' : 'var(--gray-medium)') }};
                                    display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    {{ in_array($pesanan->status, ['dikirim', 'selesai']) ? '✓' : '4' }}
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <strong>Dalam Perjalanan</strong>
                                <p style="color: var(--gray-dark); margin: 0.25rem 0 0;">
                                    {{ $pengiriman ? 'Nomor Resi: ' . $pengiriman->no_resi : 'Menunggu...' }}
                                </p>
                            </div>
                        </div>

                        <!-- Step 5: Selesai -->
                        <div style="display: flex; gap: 1rem;">
                            <div style="text-align: center; min-width: 60px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: 
                                    {{ $pesanan->status === 'selesai' ? 'var(--success-color)' : 'var(--gray-medium)' }};
                                    display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    {{ $pesanan->status === 'selesai' ? '✓' : '5' }}
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <strong>Pesanan Tiba</strong>
                                <p style="color: var(--gray-dark); margin: 0.25rem 0 0;">
                                    Pesanan sudah diterima
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Info Pesanan -->
            <div>
                <div class="card" style="margin-bottom: 1rem;">
                    <div class="card-header">
                        <h3 style="margin: 0;">Info Pesanan</h3>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
                            <small style="color: var(--gray-dark);">Kode Pesanan</small>
                            <br>
                            <strong>{{ $pesanan->kode_pesanan }}</strong>
                        </div>

                        <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
                            <small style="color: var(--gray-dark);">Total Harga</small>
                            <br>
                            <strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong>
                        </div>

                        <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
                            <small style="color: var(--gray-dark);">Ekspedisi</small>
                            <br>
                            <strong>{{ $pesanan->ekspedisi }}</strong>
                        </div>

                        <div>
                            <small style="color: var(--gray-dark);">Alamat Kirim</small>
                            <br>
                            <small>{{ $pesanan->alamat_kirim }}</small>
                        </div>
                    </div>
                </div>

                @if ($pesanan->status === 'selesai')
                    <a href="{{ url('/pembeli/ulasan?pesanan_id=' . $pesanan->id) }}" class="btn btn-block" style="background-color: var(--secondary-color); text-decoration: none; display: block; text-align: center; color: white;">
                        ⭐ Beri Ulasan
                    </a>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Item Pesanan</h3>
            </div>
            <div class="card-body">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detail as $d)
                            <tr>
                                <td>
                                    {{ $d->nama_produk }}
                                    @if ($d->ukuran)
                                        <br><small style="color: var(--gray-dark);">Ukuran: {{ $d->ukuran }}</small>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                <td>{{ $d->jumlah }}</td>
                                <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
