@extends('layouts.app')

@section('title', 'Produk Saya - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="margin: 0;">Produk Saya</h1>
            <a href="{{ url('/penjual/produk/tambah') }}" class="btn" style="background-color: var(--success-color); text-decoration: none;">
                ➕ Tambah Produk
            </a>
        </div>

        @if ($produk_list->isEmpty())
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; color: var(--gray-dark); margin-bottom: 1rem;">Anda belum memiliki produk</p>
                <a href="{{ url('/penjual/produk/tambah') }}" class="btn" style="background-color: var(--success-color); text-decoration: none;">
                    Tambah Produk Pertama
                </a>
            </div>
        @else
            <div class="grid grid-4">
                @foreach ($produk_list as $produk)
                    <div class="product-card">
                        <div class="product-image">
                            @if ($produk->foto)
                                <img src="{{ asset('assets/uploads/produk/' . $produk->foto) }}" alt="{{ $produk->nama }}">
                            @else
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect fill='%23e5e7eb' width='100' height='100'/%3E%3Ctext x='50' y='50' font-size='14' fill='%236b7280' text-anchor='middle' dy='.3em'%3ENo Image%3C/text%3E%3C/svg%3E" alt="No image">
                            @endif
                            <div class="product-badge" style="background-color: var(--info-color); top: auto; bottom: 10px; right: 10px;">
                                Stok: {{ $produk->stok_total }}
                            </div>
                        </div>
                        <div class="product-info">
                            <div style="margin-bottom: 0.3rem;">
                                <span style="
                                    background-color: {{ ($produk->kategori === 'Olahraga') ? '#e0f2fe' : '#fef3c7' }}; 
                                    color: {{ ($produk->kategori === 'Olahraga') ? '#0369a1' : '#b45309' }}; 
                                    font-size: 0.75rem; 
                                    font-weight: 700; 
                                    padding: 0.25rem 0.6rem; 
                                    border-radius: 50px; 
                                    display: inline-block;
                                    text-transform: uppercase;
                                ">
                                    {{ $produk->kategori }}
                                </span>
                            </div>

                            <h3 class="product-name" style="margin-top: 0.2rem;">{{ $produk->nama }}</h3>
                            
                            <div class="product-rating">
                                @php
                                    $rating = $produk->rating_avg ? round($produk->rating_avg, 1) : 0;
                                @endphp
                                @for ($i = 0; $i < 5; $i++)
                                    {{ $i < floor($rating) ? '⭐' : '☆' }}
                                @endfor
                                ({{ $produk->rating_count }})
                            </div>

                            <div class="product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</div>

                            <div class="product-actions">
                                <a href="{{ url('/penjual/produk/' . $produk->id . '/edit') }}" class="btn-detail" style="text-decoration: none; text-align: center;">Edit</a>
                                <form method="POST" action="{{ url('/penjual/produk/delete') }}" style="display: contents;">
                                    @csrf
                                    <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                    <button type="submit" class="btn-cart" style="background-color: var(--danger-color);" onclick="return confirm('Hapus produk ini?');">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
