@extends('layouts.app')

@section('title', 'Tambah Produk - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem; max-width: 800px;">
        <div style="margin-bottom: 2rem;">
            <a href="{{ url('/penjual/produk') }}" style="color: var(--secondary-color); text-decoration: none;">← Kembali</a>
        </div>

        <h1 style="margin-bottom: 2rem;">➕ Tambah Produk Baru</h1>

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <span class="alert-close" onclick="this.parentElement.remove();">&times;</span>
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/penjual/produk/tambah') }}" enctype="multipart/form-data" class="card">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nama">Nama Produk *</label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        value="{{ old('nama') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea 
                        id="deskripsi" 
                        name="deskripsi"
                        placeholder="Jelaskan detail produk Anda..."
                    >{{ old('deskripsi') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori *</label>
                    <select id="kategori" name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Olahraga" {{ old('kategori') === 'Olahraga' ? 'selected' : '' }}>Olahraga</option>
                        <option value="Casual" {{ old('kategori') === 'Casual' ? 'selected' : '' }}>Casual</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Ukuran Sepatu Tersedia *</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; background: #f9fafb; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
                        @php
                            $daftar_ukuran = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
                            $ukuran_terpilih = old('ukuran', []);
                        @endphp
                        @foreach ($daftar_ukuran as $size)
                            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; background: white; padding: 6px 12px; border-radius: 4px; border: 1px solid #d1d5db; user-select: none;">
                                <input type="checkbox" name="ukuran[]" value="{{ $size }}" {{ in_array($size, $ukuran_terpilih) ? 'checked' : '' }}>
                                <span style="font-size: 0.95rem; font-weight: 500;">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label for="harga">Harga (Rp) *</label>
                    <input 
                        type="number" 
                        id="harga" 
                        name="harga" 
                        value="{{ old('harga') }}"
                        min="0"
                        step="1000"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="stok">Stok Awal *</label>
                    <input 
                        type="number" 
                        id="stok" 
                        name="stok" 
                        value="{{ old('stok') }}"
                        min="0"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="foto">Foto Produk</label>
                    <input 
                        type="file" 
                        id="foto" 
                        name="foto" 
                        accept="image/*"
                    >
                    <small style="color: var(--gray-dark); display: block; margin-top: 0.5rem;">
                        Format: JPG, PNG, GIF (Max 5MB)
                    </small>
                </div>

                <button type="submit" class="btn btn-block" style="background-color: var(--success-color); padding: 1rem; font-size: 1.1rem;">
                    ✓ Tambah Produk
                </button>
            </div>
        </form>
    </div>
@endsection
