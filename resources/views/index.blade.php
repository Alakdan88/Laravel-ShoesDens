@extends('layouts.app')

@section('title', 'ShoesDens - Toko Penjualan Sepatu Online')

@section('content')
    <section class="hero" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color: white; padding: 5rem 1rem; overflow: hidden;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 3rem;">
            
            <div style="flex: 1; min-width: 300px;">
                <h1 style="font-size: 3rem; font-weight: 800; line-height: 1.2; margin-bottom: 1.5rem;">
                    Tampil Percaya Diri <br>Bersama <span style="color: #fbbf24;">ShoesDens</span>
                </h1>
                <p style="font-size: 1.1rem; margin-bottom: 2.5rem; opacity: 0.85; line-height: 1.6;">
                    Pilihan sepatu berkualitas untuk menyempurnakan gaya harianmu. Berani melangkah, berani beda!
                </p>
                <a href="{{ url('/produk') }}" class="btn" style="background-color: #D97706; color: white; font-weight: bold; padding: 1rem 2rem; transform: translateY(-2px) box-shadow: var(--shadow-md);;">
                    Jelajahi Produk
                </a>
            </div>

            <div style="flex: 1; min-width: 300px; display: flex; justify-content: center;">
                <div style="width: 100%; max-width: 400px; height: 450px; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
                    <img src="{{ asset('assets/images/sneakers-NB.jpg') }}" alt="ShoesDens Lifestyle" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>

        </div>
    </section>

    <section class="section" style="padding: 3rem 1rem;">
        <div class="container">
            <h2 class="section-title" style="text-align: center; margin-bottom: 2rem;">Belanja Berdasarkan Kategori</h2>
            <div class="grid grid-2" style="max-width: 1000px; margin: 0 auto; gap: 1.5rem;">
                
                <!-- Running / Olahraga Category -->
                <a href="{{ url('/produk?kategori=Olahraga') }}" class="category-banner-card" style="text-decoration: none; display: block; position: relative; border-radius: 12px; overflow: hidden; aspect-ratio: 1 / 1; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="width: 100%; height: 100%;">
                        <img src="{{ asset('assets/images/Sepatu_lari.jpg') }}" alt="Olahraga" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                    </div>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0) 100%); transition: background 0.3s;"></div>
                    <div style="position: absolute; bottom: 10%; left: 50%; transform: translateX(-50%); width: 90%; text-align: center;">
                        <h3 style="margin: 0; font-size: 2rem; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 2px; text-shadow: 2px 2px 8px rgba(0,0,0,0.5);">
                            RUNNING
                        </h3>
                    </div>
                </a>

                <!-- Sneakers / Casual Category -->
                <a href="{{ url('/produk?kategori=Casual') }}" class="category-banner-card" style="text-decoration: none; display: block; position: relative; border-radius: 12px; overflow: hidden; aspect-ratio: 1 / 1; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="width: 100%; height: 100%;">
                        <img src="{{ asset('assets/images/sneakers.jpg') }}" alt="Casual" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                    </div>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0) 100%); transition: background 0.3s;"></div>
                    <div style="position: absolute; bottom: 10%; left: 50%; transform: translateX(-50%); width: 90%; text-align: center;">
                        <h3 style="margin: 0; font-size: 2rem; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 2px; text-shadow: 2px 2px 8px rgba(0,0,0,0.5);">
                            SNEAKERS
                        </h3>
                    </div>
                </a>

            </div>
        </div>
    </section>

    <section class="section" style="background-color: var(--gray-light); padding: 3rem 1rem;">
        <div class="container">
            <h2 class="section-title">Produk Terbaru</h2>
            
            @if ($produk_terbaru->isEmpty())
                <div style="text-align: center; padding: 2rem;">
                    <p>Belum ada produk. Silakan cek kembali nanti.</p>
                </div>
            @else
                <div class="grid grid-4">
                    @foreach ($produk_terbaru as $produk)
                        <div class="product-card">
                            <div class="product-image">
                                @if ($produk->foto)
                                    <img src="{{ asset('assets/uploads/produk/' . $produk->foto) }}" alt="{{ $produk->nama }}">
                                @else
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect fill='%23e5e7eb' width='100' height='100'/%3E%3Ctext x='50' y='50' font-size='14' fill='%236b7280' text-anchor='middle' dy='.3em'%3ENo Image%3C/text%3E%3C/svg%3E" alt="No image">
                                @endif
                                @if ($produk->stok_total <= 0)
                                    <div class="product-badge stok-habis">HABIS</div>
                                @else
                                    <div class="product-badge">TERBARU</div>
                                @endif
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">{{ $produk->nama }}</h3>
                                
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
                                    <a href="{{ url('/detail/' . $produk->id) }}" class="btn-detail">Detail</a>
                                    @if ($produk->stok_total > 0)
                                        <button class="btn-cart" onclick="buyProduct({{ $produk->id }})">Beli</button>
                                    @else
                                        <button class="btn-cart" disabled style="opacity: 0.5; cursor: not-allowed;">HABIS</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="text-align: center; margin-top: 2rem;">
                    <a href="{{ url('/produk') }}" class="btn">Lihat Semua Produk →</a>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function buyProduct(produkId) {
            @auth
                window.location.href = '{{ url("/detail") }}/' + produkId;
            @else
                alert('Anda harus login terlebih dahulu');
                window.location.href = '{{ url("/login") }}';
            @endauth
        }
    </script>
@endsection
