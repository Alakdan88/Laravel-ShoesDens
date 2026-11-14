@extends('layouts.app')

@section('title', 'Keranjang - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem;">
        <h1 style="margin-bottom: 2rem;">Keranjang</h1>

        @if ($keranjang->isEmpty())
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; color: var(--gray-dark); margin-bottom: 1rem;">Keranjang Anda kosong</p>
                <a href="{{ url('/produk') }}" class="btn">Belanja Sekarang</a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
                <!-- Daftar Item -->
                <div>
                    <div class="card">
                        <table class="table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($keranjang as $item)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $item->produk->nama }}</strong>
                                                @if ($item->varian)
                                                    <br>
                                                    <small style="color: var(--gray-dark);">
                                                        {{ $item->varian->ukuran }} - {{ $item->varian->warna }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</td>
                                        <td>
                                            <form method="POST" action="{{ url('/pembeli/keranjang/update') }}" style="display: flex; gap: 0.25rem;">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                <input 
                                                    type="number" 
                                                    name="jumlah" 
                                                    value="{{ $item->jumlah }}"
                                                    min="1"
                                                    style="width: 60px; padding: 0.5rem;"
                                                >
                                                <button type="submit" class="btn btn-small" style="background-color: var(--secondary-color);">✓</button>
                                            </form>
                                        </td>
                                        <td>Rp {{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}</td>
                                        <td>
                                            <form method="POST" action="{{ url('/pembeli/keranjang/delete') }}">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                <button type="submit" class="btn btn-small btn-danger" style="width: 100%;">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ringkasan -->
                <div class="card">
                    <div class="card-header">
                        <h3 style="margin: 0;">Ringkasan</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
                            <span>Subtotal</span>
                            <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
                            <span>Ongkos Kirim</span>
                            <strong>Dihitung di checkout</strong>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.1rem;">
                            <strong>Total</strong>
                            <strong style="color: var(--primary-color);">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </strong>
                        </div>

                        <a href="{{ url('/pembeli/checkout') }}" class="btn btn-block" style="background-color: var(--success-color); padding: 1rem; font-size: 1.1rem; text-decoration: none; display: block; text-align: center; color: white;">
                            Lanjut ke Checkout
                        </a>

                        <a href="{{ url('/produk') }}" class="btn btn-block" style="background-color: var(--gray-medium); color: var(--text-dark); margin-top: 0.5rem; text-decoration: none; display: block; text-align: center;">
                            Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
