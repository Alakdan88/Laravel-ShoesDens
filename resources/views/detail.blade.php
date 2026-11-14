@extends('layouts.app')

@section('title')
    {{ $produk->nama }} - ShoesDens
@endsection

@section('content')
    <div class="container" style="padding: 1rem 1rem; font-size: 0.9rem;">
        <a href="{{ url('/') }}" style="color: var(--secondary-color); text-decoration: none;">Beranda</a>
        <span> / </span>
        <a href="{{ url('/produk') }}" style="color: var(--secondary-color); text-decoration: none;">Katalog</a>
        <span> / </span>
        <span>{{ $produk->nama }}</span>
    </div>

    <div class="container" style="padding: 2rem 1rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
            <!-- Left Panel: Photo -->
            <div>
                <div class="card">
                    @if ($produk->foto)
                        <img src="{{ asset('assets/uploads/produk/' . $produk->foto) }}" 
                             alt="{{ $produk->nama }}"
                             style="width: 100%; height: auto; display: block;">
                    @else
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400'%3E%3Crect fill='%23e5e7eb' width='400' height='400'/%3E%3Ctext x='50%' y='50%' font-size='24' fill='%236b7280' text-anchor='middle' dy='.3em'%3ENo Image%3C/text%3E%3C/svg%3E" 
                             alt="No image"
                             style="width: 100%; height: auto; display: block;">
                    @endif
                </div>
            </div>

            <!-- Right Panel: Order configuration -->
            <div>
                <h1 style="margin-bottom: 0.5rem; font-size: 2rem;">{{ $produk->nama }}</h1>

                <div class="product-rating" style="margin-bottom: 1rem; font-size: 1rem;">
                   @php
                        $rating = $produk->ulasan_avg_rating
                            ? round($produk->ulasan_avg_rating, 1)
                            : 0;
                    @endphp

                    @for ($i = 0; $i < 5; $i++)
                        {{ $i < floor($rating) ? '⭐' : '☆' }}
                    @endfor

                    {{ $rating }} ({{ $produk->ulasan_count }} ulasan)
                </div>

                <p style="color: var(--gray-dark); margin-bottom: 1rem;">
                    <strong>Kategori:</strong> {{ $produk->kategori }}<br>
                    <strong>Penjual:</strong> {{ $produk->penjual->nama ?? 'Penjual' }}
                </p>

                <div style="background-color: var(--gray-light); padding: 1rem; border-radius: var(--border-radius); margin-bottom: 1.5rem;">
                    <div style="color: var(--gray-dark); font-size: 0.9rem;">Harga</div>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                        Rp {{ number_format($produk->harga, 0, ',', '.') }}
                    </div>
                </div>

                <p style="margin-bottom: 1.5rem;">
                    <strong>Stok Tersedia:</strong> 
                    @if ($produk->stok_total > 0)
                        <span style="color: var(--success-color);">{{ $produk->stok_total }} unit</span>
                    @else
                        <span style="color: var(--danger-color);">HABIS</span>
                    @endif
                </p>

                @if ($produk->stok_total > 0)
                    <form method="POST" action="{{ url('/pembeli/keranjang/add') }}" style="display: grid; gap: 1rem;">
                        @csrf
                        <input type="hidden" name="produk_id" value="{{ $produk->id }}">

                        @if ($varian->isNotEmpty())
                            <div class="form-group">
                                <label for="varian_id" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">Pilih Ukuran & Warna *</label>
                                <select id="varian_id" name="varian_id" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach ($varian as $v)
                                        <option value="{{ $v->id }}" {{ $v->stok > 0 ? '' : 'disabled' }}>
                                            {{ $v->ukuran }} - {{ $v->warna }}
                                            {{ $v->stok > 0 ? '(' . $v->stok . ' stok)' : '(HABIS)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="ukuran_pilihan" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">Pilih Ukuran Sepatu *</label>
                                @php
                                    $opsi_ukuran = !empty($produk->ukuran) ? explode(', ', $produk->ukuran) : [];
                                @endphp
                                @if (!empty($opsi_ukuran))
                                    <select id="ukuran_pilihan" name="ukuran_pilihan" required>
                                        <option value="">-- Pilih Ukuran --</option>
                                        @foreach ($opsi_ukuran as $size)
                                            <option value="{{ $size }}">Ukuran {{ $size }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <p style="color: var(--danger-color); font-style: italic;">Ukuran belum diatur oleh penjual.</p>
                                @endif
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="jumlah" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">Jumlah</label>
                            <input type="number" id="jumlah" name="jumlah" value="1" min="1" max="{{ $produk->stok_total }}" required>
                        </div>

                        @auth
                            @if (Auth::user()->isPembeli())
                                <button type="submit" class="btn btn-block" style="background-color: var(--success-color); padding: 1rem; font-size: 1.1rem;">
                                     Tambah ke Keranjang 
                                </button>
                            @else
                                <div class="alert alert-warning">Hanya pembeli yang dapat membeli produk</div>
                            @endif
                        @else
                            <a href="{{ url('/login') }}" class="btn btn-block" style="background-color: var(--success-color); padding: 1rem; font-size: 1.1rem; text-decoration: none; text-align: center; color: white;">
                                Login untuk Membeli
                            </a>
                        @endauth
                    </form>
                @else
                    <div class="alert alert-danger">Produk ini sedang tidak tersedia</div>
                @endif
            </div>
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Deskripsi Produk</h3>
            </div>
            <div class="card-body">
                @if ($produk->deskripsi)
                    <div style="line-height: 1.8; color: var(--text-dark);">
                        {!! nl2br(e($produk->deskripsi)) !!}
                    </div>
                @else
                    <p style="color: var(--gray-dark);">Deskripsi produk tidak tersedia</p>
                @endif
            </div>
        </div>

        <!-- Ulasan Section -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;">Ulasan Pembeli ({{ $produk->ulasan_count }})</h3>
            </div>
            <div class="card-body">
                @if ($ulasan->isEmpty())
                    <p style="color: var(--gray-dark);">Belum ada ulasan untuk produk ini</p>
                @else
                    <div style="display: grid; gap: 1.5rem;">
                        @foreach ($ulasan as $u)
                            <div style="padding-bottom: 1.5rem; border-bottom: 1px solid var(--gray-light);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <strong>{{ $u->user->nama ?? 'Pembeli' }}</strong>
                                    <small style="color: var(--gray-dark);">{{ \Carbon\Carbon::parse($u->created_at)->format('d M Y') }}</small>
                                </div>
                                
                                <div style="margin-bottom: 0.5rem;">
                                    @for ($i = 0; $i < 5; $i++)
                                        {{ $i < $u->rating ? '⭐' : '☆' }}
                                    @endfor
                                </div>

                                <p style="margin: 0; color: var(--text-dark);">{{ $u->komentar }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
