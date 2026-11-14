@extends('layouts.app')

@section('title', 'Checkout - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem;">
        <h1 style="margin-bottom: 2rem;">Checkout Pesanan</h1>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
            <!-- Form -->
            <div>
                <form method="POST" action="{{ url('/pembeli/checkout') }}">
                    @csrf

                    <!-- Informasi Penerima -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3 style="margin: 0;">Informasi Penerima</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nama_penerima">Nama Penerima *</label>
                                <input 
                                    type="text" 
                                    id="nama_penerima"
                                    name="nama_penerima" 
                                    value="{{ old('nama_penerima', $user->nama) }}"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="no_hp">Nomor HP *</label>
                                <input 
                                    type="tel" 
                                    id="no_hp"
                                    name="no_hp" 
                                    value="{{ old('no_hp', $user->no_hp) }}"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat *</label>
                                <textarea 
                                    id="alamat"
                                    name="alamat" 
                                    required
                                >{{ old('alamat', $user->alamat) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="kota">Kota *</label>
                                <input 
                                    type="text" 
                                    id="kota"
                                    name="kota" 
                                    value="{{ old('kota', $user->kota) }}"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="kode_pos">Kode Pos *</label>
                                <input 
                                    type="text" 
                                    id="kode_pos"
                                    name="kode_pos" 
                                    value="{{ old('kode_pos', $user->kode_pos) }}"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Pengiriman -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3 style="margin: 0;">Pilih Ekspedisi Pengiriman</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="ekspedisi">Ekspedisi *</label>
                                <select id="ekspedisi" name="ekspedisi" required>
                                    <option value="JNE" {{ old('ekspedisi') === 'JNE' ? 'selected' : '' }}>JNE - Rp 25.000</option>
                                    <option value="Tiki" {{ old('ekspedisi') === 'Tiki' ? 'selected' : '' }}>TIKI - Rp 25.000</option>
                                    <option value="Pos Indonesia" {{ old('ekspedisi') === 'Pos Indonesia' ? 'selected' : '' }}>Pos Indonesia - Rp 20.000</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3 style="margin: 0;">Catatan (Opsional)</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="catatan">Catatan untuk penjual</label>
                                <textarea 
                                    id="catatan"
                                    name="catatan" 
                                    placeholder="Misalnya: packaging rapih, jangan dibanting, dll"
                                >{{ old('catatan') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-block" style="background-color: var(--success-color); padding: 1rem; font-size: 1.1rem;">
                        Lanjut ke Pembayaran
                    </button>
                </form>
            </div>

            <!-- Ringkasan -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Ringkasan Pesanan</h3>
                </div>
                <div class="card-body">
                    <!-- Items -->
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
                        @foreach ($keranjang as $item)
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.9rem;">
                                <span>{{ Str::limit($item->produk->nama, 20) }} x{{ $item->jumlah }}</span>
                                <strong>Rp {{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                    </div>

                    <!-- Total -->
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span>Subtotal</span>
                        <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
                        <span>Ongkos Kirim</span>
                        <strong>Rp {{ number_format($ongkir, 0, ',', '.') }}</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 1.1rem;">
                        <strong>Total</strong>
                        <strong style="color: var(--primary-color);">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
