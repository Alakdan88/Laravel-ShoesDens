@extends('layouts.app')

@section('title', 'Pembayaran - ShoesDens')

@section('content')
<div class="container" style="padding: 2rem 1rem;">

    <h1 style="margin-bottom: 2rem;" class="icon-align">
        <span class="material-icons" style="font-size: 2.5rem; color: var(--primary-color);">payment</span>
        Pembayaran Pesanan
    </h1>

    @if(session('message'))
        <div class="alert alert-{{ session('message')['type'] === 'success' ? 'success' : 'info' }}">
            {{ session('message')['message'] }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1.2rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 350px; gap: 2rem;">

        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0;">Detail Pesanan</h3>
                </div>

                <div class="card-body">

                    <div style="display: grid; grid-template-columns: 170px 1fr; gap: 1rem; margin-bottom: 1.5rem;">

                        <strong>Kode Pesanan:</strong>
                        <span>{{ $pesanan->kode_pesanan }}</span>

                        <strong>Status Pesanan:</strong>
                        <span>{{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}</span>

                        <strong>Status Pembayaran:</strong>
                        <span>{{ ucfirst(str_replace('_', ' ', $pesanan->status_pembayaran)) }}</span>

                        <strong>Tanggal Pesanan:</strong>
                        <span>{{ $pesanan->created_at->format('d M Y H:i') }}</span>

                        <strong>Alamat Pengiriman:</strong>
                        <span>{{ $pesanan->alamat_kirim }}</span>

                        <strong>Ekspedisi:</strong>
                        <span>{{ $pesanan->ekspedisi }}</span>

                    </div>

                    <div style="border-top: 1px solid var(--gray-light); padding-top: 1.5rem;">

                        <h4 style="margin-bottom: 1rem;">Item Pesanan</h4>

                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($detail as $d)
                                        <tr>
                                            <td>
                                                {{ $d->nama_produk }}

                                                @if($d->ukuran)
                                                    <br>
                                                    <small>Ukuran: {{ $d->ukuran }}</small>
                                                @endif
                                            </td>

                                            <td>Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                            <td>{{ $d->jumlah }}</td>
                                            <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Informasi Transfer Bank</h3>
                </div>

                <div class="card-body">

                    <p>Silakan lakukan transfer sesuai total pembayaran ke rekening penjual berikut.</p>

                    <div style="background: var(--gray-light); padding: 1.2rem; border-radius: 8px; margin: 1rem 0;">

                        <p><strong>Bank:</strong> Bank BRI </p>

                        <p><strong>Nomor Rekening:</strong> 0876765455341927</p>

                        <p><strong>Atas Nama:</strong>Muhammad Helmi</p>

                        <p style="margin-bottom: 0;">
                            <strong>Jumlah Transfer:</strong>
                            <span style="color: var(--primary-color); font-weight: bold;">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </span>
                        </p>

                    </div>

                    <ol style="padding-left: 1.2rem; line-height: 1.8;">
                        <li>Transfer sesuai jumlah yang tertera.</li>
                        <li>Simpan bukti transfer.</li>
                        <li>Unggah bukti transfer melalui formulir pembayaran.</li>
                        <li>Tunggu penjual memverifikasi pembayaran.</li>
                    </ol>

                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Ringkasan Pembayaran</h3>
                </div>

                <div class="card-body">

                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span>Subtotal</span>
                        <strong>Rp {{ number_format($pesanan->total_harga - $pesanan->ongkir, 0, ',', '.') }}</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
                        <span>Ongkos Kirim</span>
                        <strong>Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.1rem;">
                        <strong>Total</strong>
                        <strong style="color: var(--primary-color);">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </strong>
                    </div>

                    @if($pesanan->status_pembayaran === 'menunggu_verifikasi')

                        <div class="alert alert-info">
                            <strong>Bukti pembayaran sedang diperiksa.</strong>
                            <p style="margin: 0.5rem 0 0;">
                                Silakan tunggu verifikasi dari penjual.
                            </p>
                        </div>

                    @elseif($pesanan->status_pembayaran === 'terverifikasi')

                        <div class="alert alert-success">
                            <strong>Pembayaran berhasil diverifikasi.</strong>
                            <p style="margin: 0.5rem 0 0;">
                                Pesanan Anda akan segera diproses.
                            </p>
                        </div>

                    @elseif($pesanan->status === 'menunggu_pembayaran' && in_array($pesanan->status_pembayaran, ['menunggu_pembayaran', 'ditolak']))

                        @if($pesanan->status_pembayaran === 'ditolak')
                            <div class="alert alert-danger">
                                <strong>Bukti pembayaran ditolak.</strong>

                                @if($pesanan->catatan_verifikasi)
                                    <p style="margin: 0.5rem 0 0;">
                                        Alasan: {{ $pesanan->catatan_verifikasi }}
                                    </p>
                                @endif

                                <p style="margin: 0.5rem 0 0;">
                                    Silakan periksa kembali dan unggah bukti pembayaran yang benar.
                                </p>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('pembeli.pembayaran.proses') }}"
                              enctype="multipart/form-data">

                            @csrf

                            <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">

                            <div class="form-group" style="margin-bottom: 1.5rem;">

                                <label for="bukti_pembayaran">
                                    <strong>Upload Bukti Transfer</strong>
                                </label>

                                <input
                                    type="file"
                                    id="bukti_pembayaran"
                                    name="bukti_pembayaran"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    required
                                    class="form-control"
                                    style="margin-top: 0.5rem;"
                                >

                                <small style="color: var(--gray-dark);">
                                    Format JPG, JPEG, PNG, WEBP. Maksimal 2 MB.
                                </small>

                            </div>

                            <button
                                type="submit"
                                class="btn btn-block btn-icon"
                                style="background-color: var(--success-color); padding: 1rem; border: none; cursor: pointer; color: white; border-radius: 6px;"
                            >
                                <span class="material-icons">upload</span>
                                Kirim Bukti Pembayaran
                            </button>

                        </form>

                    @else

                        <div class="alert alert-info">
                            Pesanan ini tidak sedang menunggu pembayaran.
                        </div>

                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
