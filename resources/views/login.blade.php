@extends('layouts.app')

@section('title', 'Masuk - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem; max-width: 400px; margin-top: 3rem; margin-bottom: 3rem;">
        <div class="card">
            <div class="card-body">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">Masuk Akun</h2>

                <!-- Error Messages -->
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

                <form method="POST" action="{{ url('/login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                        >
                    </div>

                    <!-- Remember Me -->
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; width: 100%;">
                        <input type="checkbox" id="remember" name="remember" style="margin: 0; width: auto; cursor: pointer;">
                        <label for="remember" style="margin: 0; display: inline-block; white-space: nowrap; cursor: pointer;">Ingat saya</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-block" style="margin-bottom: 1rem;">Masuk</button>

                    <!-- Link -->
                    <p style="text-align: center; margin-bottom: 0;">
                        Belum punya akun? <a href="{{ url('/register') }}" style="color: var(--primary-color); text-decoration: none;">Daftar di sini</a>
                    </p>
                </form>

            </div>
        </div>
    </div>
@endsection
