@extends('layouts.app')

@section('title', 'Katalog Produk - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem;">
        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            
            <!-- Filter Sidebar -->
            <aside>
                <div class="card">
                    <div class="card-header">
                        <h3 style="margin: 0;">Filter</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ url('/produk') }}">
                            <div class="form-group">
                                <label for="search">Cari Produk</label>
                                <input 
                                    type="text" 
                                    id="search" 
                                    name="search" 
                                    value="{{ $search }}"
                                    placeholder="Nama sepatu..."
                                >
                            </div>

                            <div class="form-group">
                                <label for="kategori">Kategori</label>
                                <select id="kategori" name="kategori">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategori_list as $kat)
                                        <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>
                                            {{ $kat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="min_price">Harga Minimum</label>
                                <input 
                                    type="number" 
                                    id="min_price" 
                                    name="min_price" 
                                    value="{{ $min_price > 0 ? $min_price : '' }}"
                                    placeholder="0"
                                >
                            </div>

                            <div class="form-group">
                                <label for="max_price">Harga Maksimum</label>
                                <input 
                                    type="number" 
                                    id="max_price" 
                                    name="max_price" 
                                    value="{{ $max_price < PHP_INT_MAX ? $max_price : '' }}"
                                    placeholder="Maksimum"
                                >
                            </div>

                            <button type="submit" class="btn btn-block">Terapkan Filter</button>
                            <a href="{{ url('/produk') }}" class="btn btn-block" style="background-color: var(--gray-medium); color: var(--text-dark); margin-top: 0.5rem; text-align: center; display: block; text-decoration: none;">Reset</a>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main>
                <h1 style="margin-bottom: 0.5rem;">Katalog Produk</h1>
                <p style="color: var(--gray-dark); margin-bottom: 2rem;">
                    Menampilkan {{ $total }} produk
                    @if ($kategori || $search || $min_price > 0 || $max_price < PHP_INT_MAX)
                        <span style="color: var(--primary-color); font-weight: bold;">(Hasil Filter)</span>
                    @endif
                </p>

                @if ($produk->isEmpty())
                    <div style="text-align: center; padding: 3rem 1rem; background-color: var(--gray-light); border-radius: var(--border-radius);">
                        <p style="font-size: 1.1rem; color: var(--gray-dark);">Produk tidak ditemukan. Coba ubah atau reset filter Anda.</p>
                    </div>
                @else
                    <div class="grid grid-4">
                        @foreach ($produk as $item)
                            <div class="product-card">
                                <div class="product-image">
                                    @if ($item->foto)
                                        <img src="{{ asset('assets/uploads/produk/' . $item->foto) }}" alt="{{ $item->nama }}">
                                    @else
                                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect fill='%23e5e7eb' width='100' height='100'/%3E%3Ctext x='50' y='50' font-size='14' fill='%236b7280' text-anchor='middle' dy='.3em'%3ENo Image%3C/text%3E%3C/svg%3E" alt="No image">
                                    @endif
                                    
                                    @if (($item->stok_total ?? 0) <= 0)
                                        <div class="product-badge stok-habis">HABIS</div>
                                    @elseif ($item->stok_total < 5)
                                        <div class="product-badge" style="background-color: var(--warning-color);">STOK TERBATAS</div>
                                    @endif
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">{{ $item->nama }}</h3>
                                    
                                    <div class="product-rating">
                                        @php
                                            $rating = $item->rating_avg ? round($item->rating_avg, 1) : 0;
                                        @endphp
                                        @for ($i = 0; $i < 5; $i++)
                                            {{ $i < floor($rating) ? '⭐' : '☆' }}
                                        @endfor
                                        ({{ $item->rating_count }})
                                    </div>

                                    <small style="color: var(--gray-dark); display: block; margin: 0.2rem 0;">{{ $item->kategori }}</small>

                                    <div class="product-price">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>

                                    <div class="product-actions">
                                        <a href="{{ url('/detail/' . $item->id) }}" class="btn-detail">Detail</a>
                                        @if (($item->stok_total ?? 0) > 0)
                                           <button class="btn-cart" onclick="buyProduct({{ Js::from($item->id) }})">Beli</button>
                                        @else
                                            <button class="btn-cart" disabled style="opacity: 0.5; cursor: not-allowed;">HABIS</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Custom pagination matching layout.css style -->
                    @if ($produk->lastPage() > 1)
                        <div class="pagination">
                            @if ($produk->currentPage() > 1)
                                <a href="{{ $produk->appends(request()->query())->previousPageUrl() }}">← Sebelumnya</a>
                            @endif

                            @for ($i = 1; $i <= $produk->lastPage(); $i++)
                                @if ($i === $produk->currentPage())
                                    <span class="current">{{ $i }}</span>
                                @else
                                    <a href="{{ $produk->appends(request()->query())->url($i) }}">{{ $i }}</a>
                                @endif
                            @endfor

                            @if ($produk->hasMorePages())
                                <a href="{{ $produk->appends(request()->query())->nextPageUrl() }}">Selanjutnya →</a>
                            @endif
                        </div>
                    @endif
                @endif
            </main>
        </div>
    </div>
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
