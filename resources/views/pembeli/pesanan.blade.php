
@extends('layouts.app')

@section('title', 'Pesanan Saya - ShoesDens')

@section('content')
<div class="container" style="padding: 2rem 1rem;">
    <h1 style="margin-bottom: 2rem;">Pesanan Saya</h1>

    @if ($pesanan_list->isEmpty())
        <div class="card" style="text-align: center; padding: 3rem;">
            <p style="font-size: 1.2rem; color: var(--gray-dark); margin-bottom: 1rem;">
                Anda belum memiliki pesanan
            </p>
            <a href="{{ url('/produk') }}" class="btn">Mulai Belanja</a>
        </div>
    @else
        <div style="display: grid; gap: 1.5rem;">
            @foreach ($pesanan_list as $p)
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <strong>{{ $p->kode_pesanan }}</strong>
                            <br>
                            <small style="color: var(--gray-dark);">
                                {{ $p->created_at->format('d M Y H:i') }}
                            </small>
                        </div>

                        <span class="product-badge" style="position: static; display: inline-block; background-color:
                            @switch($p->status)
                                @case('menunggu_pembayaran') var(--warning-color) @break
                                @case('pembayaran_sukses')
                                @case('diproses') var(--info-color) @break
                                @case('dikirim') var(--secondary-color) @break
                                @case('selesai') var(--success-color) @break
                                @case('dibatalkan') var(--danger-color) @break
                                @default var(--gray-dark)
                            @endswitch
                        ">
                            @switch($p->status)
                                @case('menunggu_pembayaran') ⏳ Menunggu Pembayaran @break
                                @case('pembayaran_sukses') ✓ Pembayaran Sukses @break
                                @case('diproses') 🔄 Diproses @break
                                @case('dikirim') 🚚 Dikirim @break
                                @case('selesai') ✓ Selesai @break
                                @case('dibatalkan') ✗ Dibatalkan @break
                                @default {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                            @endswitch
                        </span>
                    </div>

                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <small style="color: var(--gray-dark);">Total Harga</small>
                                <br>
                                <strong>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                            </div>

                            <div>
                                <small style="color: var(--gray-dark);">Ekspedisi</small>
                                <br>
                                <strong>{{ $p->ekspedisi }}</strong>
                            </div>

                            <div>
                                <small style="color: var(--gray-dark);">Alamat Kirim</small>
                                <br>
                                <small>{{ \Illuminate\Support\Str::limit($p->alamat_kirim, 30) }}</small>
                            </div>
                        </div>

                        {{-- Status pembayaran --}}
                        <div style="padding: 1rem; background: var(--background-color, #f5f5f5); border-radius: 8px; margin-bottom: 1rem;">
                            <strong>Status Pembayaran:</strong>

                            @switch($p->status_pembayaran)
                                @case('menunggu_pembayaran')
                                    <span>Belum melakukan pembayaran</span>
                                    @break

                                @case('menunggu_verifikasi')
                                    <span style="color: var(--warning-color);">
                                        Menunggu verifikasi penjual
                                    </span>
                                    @break

                                @case('terverifikasi')
                                    <span style="color: var(--success-color);">
                                        Pembayaran telah diverifikasi
                                    </span>
                                    @break

                                @case('ditolak')
                                    <span style="color: var(--danger-color);">
                                        Bukti pembayaran ditolak
                                    </span>

                                    @if ($p->catatan_verifikasi)
                                        <p style="margin-top: 0.5rem;">
                                            <strong>Alasan penolakan:</strong>
                                            {{ $p->catatan_verifikasi }}
                                        </p>
                                    @endif
                                    @break

                                @default
                                    <span>{{ $p->status_pembayaran }}</span>
                            @endswitch
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; flex-wrap: wrap;">
                            @if (
                                $p->status === 'menunggu_pembayaran' &&
                                in_array($p->status_pembayaran, ['menunggu_pembayaran', 'ditolak'])
                            )
                                <a href="{{ url('/pembeli/pembayaran?pesanan_id=' . $p->id) }}"
                                   class="btn btn-small"
                                   style="background-color: var(--accent-color);">
                                    {{ $p->status_pembayaran === 'ditolak' ? 'Upload Ulang Bukti' : 'Bayar Sekarang' }}
                                </a>
                            @endif

                            @if ($p->status !== 'dibatalkan')
                                <a href="{{ url('/pembeli/lacak?pesanan_id=' . $p->id) }}"
                                   class="btn btn-small">
                                    Lacak Pesanan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
