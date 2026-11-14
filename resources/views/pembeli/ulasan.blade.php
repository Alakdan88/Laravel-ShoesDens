@extends('layouts.app')

@section('title', 'Beri Ulasan - ShoesDens')

@section('content')
    <div class="container" style="padding: 2rem 1rem; max-width: 800px;">
        <div style="margin-bottom: 2rem;">
            <a href="{{ url('/pembeli/pesanan') }}" style="color: var(--secondary-color); text-decoration: none;">← Kembali</a>
        </div>

        <h1 style="margin-bottom: 2rem;">⭐ Beri Ulasan</h1>

        @if ($detail->isEmpty())
            <div class="card">
                <div class="card-body">
                    <p>Tidak ada produk yang dapat diulas pada pesanan ini.</p>
                </div>
            </div>
        @else
            <form method="POST" action="{{ url('/pembeli/ulasan') }}">
                @csrf

                <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">

                <div style="display: grid; gap: 2rem;">
                    @foreach ($detail as $d)
                        <div class="card">
                            <div class="card-body">
                                <h3 style="margin-top: 0; margin-bottom: 1rem;">
                                    {{ $d->nama_produk }}
                                </h3>

                                <div class="form-group">
                                    <label for="rating_{{ $d->id }}">Rating *</label>

                                    <div style="display: flex; gap: 1rem; align-items: center;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; margin-bottom: 0;">
                                                <input
                                                    type="radio"
                                                    name="rating_{{ $d->id }}"
                                                    value="{{ $i }}"
                                                    id="rating_{{ $d->id }}_{{ $i }}"
                                                    required
                                                >

                                                <span style="font-size: 1.25rem;">
                                                    @for ($j = 1; $j <= $i; $j++)
                                                        ⭐
                                                    @endfor
                                                </span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="komentar_{{ $d->id }}">Komentar</label>

                                    <textarea
                                        id="komentar_{{ $d->id }}"
                                        name="komentar_{{ $d->id }}"
                                        maxlength="1000"
                                        placeholder="Bagikan pengalaman Anda dengan produk ini..."
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button
                        type="submit"
                        class="btn btn-block"
                        style="background-color: var(--success-color); padding: 1rem; font-size: 1.1rem;"
                    >
                        ✓ Kirim Ulasan
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection
