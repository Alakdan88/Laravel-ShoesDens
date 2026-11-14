@extends('layouts.app')

@section('title', 'Profil - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem; max-width: 600px;">
        <h1 style="margin-bottom: 2rem;">Profil Saya</h1>

        <form method="POST" action="{{ url('/pembeli/profil') }}" class="card">
            @csrf
            <div class="card-body">
                <!-- Email (Read Only) -->
                <div class="form-group">
                    <label>Email</label>
                    <input 
                        type="email" 
                        value="{{ $user->email }}"
                        disabled
                        style="background-color: var(--gray-light); cursor: not-allowed;"
                    >
                    <small style="color: var(--gray-dark); display: block; margin-top: 0.25rem;">Email tidak dapat diubah</small>
                </div>

                <!-- Nama -->
                <div class="form-group">
                    <label for="nama">Nama Lengkap *</label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        value="{{ old('nama', $user->nama) }}"
                        required
                    >
                </div>

                <!-- No HP -->
                <div class="form-group">
                    <label for="no_hp">Nomor Telepon</label>
                    <input 
                        type="tel" 
                        id="no_hp" 
                        name="no_hp"
                        value="{{ old('no_hp', $user->no_hp) }}"
                    >
                </div>

                <!-- Alamat -->
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea 
                        id="alamat" 
                        name="alamat"
                    >{{ old('alamat', $user->alamat) }}</textarea>
                </div>

                <!-- Kota -->
                <div class="form-group">
                    <label for="kota">Kota</label>
                    <input 
                        type="text" 
                        id="kota" 
                        name="kota"
                        value="{{ old('kota', $user->kota) }}"
                    >
                </div>

                <!-- Kode Pos -->
                <div class="form-group">
                    <label for="kode_pos">Kode Pos</label>
                    <input 
                        type="text" 
                        id="kode_pos" 
                        name="kode_pos"
                        value="{{ old('kode_pos', $user->kode_pos) }}"
                    >
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-block" style="margin-bottom: 1rem;">
                    Simpan Perubahan
                </button>

                <a href="{{ url('/pembeli/pesanan') }}" class="btn btn-block" style="background-color: var(--gray-medium); color: var(--text-dark); text-decoration: none; display: block; text-align: center;">
                    Lihat Pesanan
                </a>
            </div>
        </form>

        <!-- Akun & Keamanan -->
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Keamanan Akun</h3>
            </div>
            <div class="card-body">
                <p style="margin-bottom: 1rem;">Untuk mengubah password, hubungi admin atau gunakan fitur reset password.</p>
                <a href="#" class="btn" style="background-color: var(--secondary-color); text-decoration: none;">Ubah Password</a>
            </div>
        </div>
    </div>
@endsection
