@extends('layouts.app')

@section('title', 'Edit Produk - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem; max-width: 800px;">
        <div style="margin-bottom: 2rem;">
            <a href="{{ url('/penjual/produk') }}" style="color: var(--secondary-color); text-decoration: none;">← Kembali</a>
        </div>

        <h1 style="margin-bottom: 2rem;">Edit Produk</h1>

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

        <!-- Form Info Produk Utama -->
        <form method="POST" action="{{ url('/penjual/produk/' . $produk->id . '/edit') }}" enctype="multipart/form-data" class="card" style="margin-bottom: 2rem;">
            @csrf
            <div class="card-header">
                <h3 style="margin: 0;">Informasi Produk</h3>
            </div>
            <div class="card-body">
                @if ($produk->foto)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ asset('assets/uploads/produk/' . $produk->foto) }}" 
                             alt="{{ $produk->nama }}"
                             style="max-width: 200px; max-height: 200px; border-radius: var(--border-radius);">
                    </div>
                @endif

                <div class="form-group">
                    <label for="foto">Ganti Foto Produk</label>
                    <input 
                        type="file" 
                        id="foto" 
                        name="foto" 
                        accept="image/*"
                    >
                </div>

                <div class="form-group">
                    <label for="nama">Nama Produk *</label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        value="{{ old('nama', $produk->nama) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea 
                        id="deskripsi" 
                        name="deskripsi"
                    >{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori *</label>
                    <select id="kategori" name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Pria" {{ old('kategori', $produk->kategori) === 'Pria' ? 'selected' : '' }}>Pria</option>
                        <option value="Wanita" {{ old('kategori', $produk->kategori) === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                        <option value="Anak-anak" {{ old('kategori', $produk->kategori) === 'Anak-anak' ? 'selected' : '' }}>Anak-anak</option>
                        <option value="Olahraga" {{ old('kategori', $produk->kategori) === 'Olahraga' ? 'selected' : '' }}>Olahraga</option>
                        <option value="Formal" {{ old('kategori', $produk->kategori) === 'Formal' ? 'selected' : '' }}>Formal</option>
                        <option value="Casual" {{ old('kategori', $produk->kategori) === 'Casual' ? 'selected' : '' }}>Casual</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Ukuran Sepatu Tersedia *</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; background: #f9fafb; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
                        @php
                            $ukuran_tersimpan = !empty($produk->ukuran) ? explode(', ', $produk->ukuran) : [];
                            $ukuran_terpilih = old('ukuran', $ukuran_tersimpan);
                        @endphp
                        @foreach ($ukuran_list as $size)
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
                        value="{{ old('harga', $produk->harga) }}"
                        min="0"
                        step="1000"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="stok">Stok Produk *</label>
                    <input 
                        type="number" 
                        id="stok" 
                        name="stok" 
                        value="{{ old('stok', $produk->stok_total) }}"
                        min="0"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-block" style="background-color: var(--success-color); padding: 1rem;">
                    ✓ Simpan Perubahan
                </button>
            </div>
        </form>

        <!-- Kelola Varian Ukuran & Warna -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;">Varian Produk (Ukuran & Warna)</h3>
            </div>
            <div class="card-body">
                @if ($varian_list->isEmpty())
                    <p style="color: var(--gray-dark); margin-bottom: 1.5rem;">Belum ada varian produk</p>
                @else
                    <table class="table" style="margin-bottom: 1rem;">
                        <thead>
                            <tr>
                                <th>Ukuran</th>
                                <th>Warna</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($varian_list as $v)
                                <tr>
                                    <td>{{ $v->ukuran }}</td>
                                    <td>{{ $v->warna }}</td>
                                    <td>{{ $v->stok }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <!-- Tambah Varian Baru -->
                <form method="POST" action="{{ url('/penjual/produk/' . $produk->id . '/varian') }}" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--gray-light);">
                    @csrf
                    <select name="ukuran" required>
                        <option value="">-- Ukuran --</option>
                        @foreach ($ukuran_list as $u)
                            <option value="{{ $u }}">{{ $u }}</option>
                        @endforeach
                    </select>

                    <select name="warna" required>
                        <option value="">-- Warna --</option>
                        @foreach ($warna_list as $w)
                            <option value="{{ $w }}">{{ $w }}</option>
                        @endforeach
                    </select>

                    <input 
                        type="number" 
                        name="stok_varian" 
                        placeholder="Stok"
                        min="1"
                        required
                    >

                    <button type="submit" class="btn" style="background-color: var(--success-color);">Tambah</button>
                </form>
            </div>
        </div>
    </div>
@endsection
