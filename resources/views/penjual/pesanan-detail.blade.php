@extends('layouts.app')

@section('title', 'Detail Pesanan - ShoesDens')

@section('content')
<div class="container" style="padding: 2rem 1rem;">

    <div style="margin-bottom: 2rem;">
        <a href="{{ url('/penjual/pesanan') }}" style="color: var(--secondary-color); text-decoration: none;">
            ← Kembali
        </a>
    </div>

    <h1 style="margin-bottom: 2rem;">Detail Pesanan</h1>

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

    <div style="display: grid; grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr); gap: 2rem;">

        <div>

            <!-- Informasi Pesanan -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0;">Informasi Pesanan</h3>
                </div>

                <div class="card-body">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 150px; padding: 0.75rem 0;"><strong>Kode Pesanan</strong></td>
                            <td>{{ $pesanan->kode_pesanan }}</td>
                        </tr>

                        <tr>
                            <td style="padding: 0.75rem 0;"><strong>Tanggal Pesanan</strong></td>
                            <td>{{ \Carbon\Carbon::parse($pesanan->created_at)->format('d M Y H:i') }}</td>
                        </tr>

                        <tr>
                            <td style="padding: 0.75rem 0;"><strong>Status Pesanan</strong></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}</td>
                        </tr>

                        <tr>
                            <td style="padding: 0.75rem 0;"><strong>Status Pembayaran</strong></td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $pesanan->status_pembayaran)) }}
                            </td>
                        </tr>

                        <tr>
                            <td style="padding: 0.75rem 0;"><strong>Subtotal Produk</strong></td>
                            <td>Rp {{ number_format($pesanan->subtotal_penjual, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <td style="padding: 0.75rem 0;"><strong>Alamat Kirim</strong></td>
                            <td>{{ $pesanan->alamat_kirim }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Verifikasi Pembayaran -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0;">Verifikasi Pembayaran</h3>
                </div>

                <div class="card-body">

                    @if($pesanan->status_pembayaran === 'menunggu_verifikasi' && $pesanan->bukti_pembayaran)

                        <div class="alert alert-info">
                            Pembeli telah mengunggah bukti transfer. Silakan periksa bukti pembayaran sebelum mengambil keputusan.
                        </div>

                        <p><strong>Tanggal Upload:</strong>
                            {{ $pesanan->tanggal_upload_bukti
                                ? \Carbon\Carbon::parse($pesanan->tanggal_upload_bukti)->format('d M Y H:i')
                                : '-' }}
                        </p>

                        <div style="margin: 1.5rem 0;">
                            <a href="{{ url('/penjual/pesanan/' . $pesanan->id . '/bukti') }}"
                               target="_blank"
                               rel="noopener"
                               style="color: var(--primary-color);">
                                <span class="material-icons" style="vertical-align: middle;">image</span>
                                Lihat Bukti Transfer
                            </a>
                        </div>

                        <form method="POST"
                              action="{{ url('/penjual/pesanan/' . $pesanan->id . '/verifikasi-pembayaran') }}">
                            @csrf

                            <div class="form-group" style="margin-bottom: 1rem;">
                                <label for="catatan_verifikasi">
                                    Catatan Penolakan (wajib diisi jika menolak)
                                </label>

                                <textarea
                                    id="catatan_verifikasi"
                                    name="catatan_verifikasi"
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Contoh: Nominal transfer tidak sesuai."
                                    style="width: 100%;"
                                >{{ old('catatan_verifikasi') }}</textarea>
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">

                                <button
                                    type="submit"
                                    name="aksi"
                                    value="terima"
                                    class="btn"
                                    style="background-color: var(--success-color); color: white;"
                                    onclick="return confirm('Apakah Anda yakin pembayaran ini sudah benar?')"
                                >
                                    ✓ Terima Pembayaran
                                </button>

                                <button
                                    type="submit"
                                    name="aksi"
                                    value="tolak"
                                    class="btn"
                                    style="background-color: #dc2626; color: white;"
                                    onclick="return confirm('Apakah Anda yakin ingin menolak bukti pembayaran ini?')"
                                >
                                    ✗ Tolak Pembayaran
                                </button>

                            </div>
                        </form>

                    @elseif($pesanan->status_pembayaran === 'terverifikasi')

                        <div class="alert alert-success">
                            <strong>Pembayaran telah diverifikasi.</strong>
                            <p style="margin: 0.5rem 0 0;">
                                Pesanan sudah dapat dilanjutkan ke proses pengemasan dan pengiriman.
                            </p>
                        </div>

                        <p>
                            <strong>Tanggal Verifikasi:</strong>
                            {{ $pesanan->tanggal_verifikasi
                                ? \Carbon\Carbon::parse($pesanan->tanggal_verifikasi)->format('d M Y H:i')
                                : '-' }}
                        </p>

                    @elseif($pesanan->status_pembayaran === 'ditolak')

                        <div class="alert alert-danger">
                            <strong>Bukti pembayaran sebelumnya ditolak.</strong>
                            <p style="margin: 0.5rem 0 0;">
                                Menunggu pembeli mengunggah bukti pembayaran baru.
                            </p>

                            @if($pesanan->catatan_verifikasi)
                                <p style="margin: 0.5rem 0 0;">
                                    <strong>Alasan:</strong> {{ $pesanan->catatan_verifikasi }}
                                </p>
                            @endif
                        </div>

                    @elseif($pesanan->status_pembayaran === 'menunggu_pembayaran')

                        <div class="alert alert-info">
                            Pembeli belum mengunggah bukti pembayaran.
                        </div>

                    @else

                        <div class="alert alert-info">
                            Tidak ada pembayaran yang perlu diverifikasi.
                        </div>

                    @endif

                </div>
            </div>

            <!-- Item Pesanan -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Item Pesanan</h3>
                </div>

                <div class="card-body">
                    <div style="overflow-x: auto;">
                        <table class="table" style="margin-bottom: 0;">
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
                                                <small style="color: var(--gray-dark);">
                                                    Ukuran: {{ $d->ukuran }}
                                                </small>
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

        <!-- Panel Kanan -->
        <div>

            <!-- Update Status -->
            @if(in_array($pesanan->status, ['pembayaran_sukses', 'diproses', 'dikirim']))

                <form method="POST"
                      action="{{ url('/penjual/pesanan/' . $pesanan->id . '/status') }}"
                      class="card">

                    @csrf

                    <div class="card-header">
                        <h3 style="margin: 0;">Update Status Pesanan</h3>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="status">Status Berikutnya</label>

                            <select id="status" name="status" required style="width: 100%;">

                                <option value="">-- Pilih Status --</option>

                                @if($pesanan->status === 'pembayaran_sukses')
                                    <option value="diproses">Diproses</option>
                                    <option value="dibatalkan">Dibatalkan</option>

                                @elseif($pesanan->status === 'diproses')
                                    <option value="dikirim">Dikirim</option>
                                    <option value="dibatalkan">Dibatalkan</option>

                                @elseif($pesanan->status === 'dikirim')
                                    <option value="selesai">Selesai</option>
                                @endif

                            </select>
                        </div>

                        <div class="form-group" id="resi_group" style="display: none;">
                            <label for="no_resi">Nomor Resi</label>

                            <input
                                type="text"
                                id="no_resi"
                                name="no_resi"
                                maxlength="100"
                                value="{{ old('no_resi', $pengiriman->no_resi ?? '') }}"
                                style="width: 100%;"
                            >
                        </div>

                        <button type="submit"
                                class="btn btn-block"
                                style="background-color: var(--success-color); color: white;">
                            Update Status
                        </button>

                    </div>
                </form>

            @endif

            <!-- Informasi Pengiriman -->
            @if($pengiriman)
                <div class="card" style="margin-top: 1rem;">
                    <div class="card-header">
                        <h3 style="margin: 0;">Info Pengiriman</h3>
                    </div>

                    <div class="card-body">
                        <p><strong>Ekspedisi:</strong> {{ $pengiriman->ekspedisi }}</p>
                        <p><strong>No Resi:</strong> {{ $pengiriman->no_resi }}</p>
                        <p>
                            <strong>Status:</strong>
                            {{ ucfirst(str_replace('_', ' ', $pengiriman->status_kirim)) }}
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('status');
    const resiGroup = document.getElementById('resi_group');
    const noResi = document.getElementById('no_resi');

    if (statusSelect && resiGroup) {
        function updateResiField() {
            const isShipping = statusSelect.value === 'dikirim';

            resiGroup.style.display = isShipping ? 'block' : 'none';

            if (noResi) {
                noResi.required = isShipping;
            }
        }

        statusSelect.addEventListener('change', updateResiField);
        updateResiField();
    }
});
</script>
@endsection
