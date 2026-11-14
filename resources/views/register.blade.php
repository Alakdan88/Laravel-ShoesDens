@extends('layouts.app')

@section('title', 'Daftar Akun - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem; max-width: 500px; margin-top: 2rem; margin-bottom: 2rem;">
        <div class="card">
            <div class="card-body">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">Daftar Akun</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <span class="alert-close" onclick="this.parentElement.remove();">&times;</span>
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('/register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="nama">Nama Lengkap *</label>
                        <input 
                            type="text" 
                            id="nama" 
                            name="nama" 
                            value="{{ old('nama') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                        >
                        <small style="color: var(--gray-dark); display: block; margin-top: 0.5rem;">
                            Minimal 8 karakter dengan huruf besar, huruf kecil, dan angka
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Konfirmasi Password *</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="no_hp">Nomor Telepon</label>
                        <input 
                            type="tel" 
                            id="no_hp" 
                            name="no_hp"
                            value="{{ old('no_hp') }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea 
                            id="alamat" 
                            name="alamat"
                        >{{ old('alamat') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="kota">Kota</label>
                        <input 
                            type="text" 
                            id="kota" 
                            name="kota"
                            value="{{ old('kota') }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="kode_pos">Kode Pos</label>
                        <input 
                            type="text" 
                            id="kode_pos" 
                            name="kode_pos"
                            value="{{ old('kode_pos') }}"
                        >
                    </div>

                    <button type="submit" class="btn btn-block" style="margin-bottom: 1rem;">Daftar</button>

                    <p style="text-align: center; margin-bottom: 0;">
                        Sudah punya akun? <a href="{{ url('/login') }}" style="color: var(--primary-color); text-decoration: none;">Masuk di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection
