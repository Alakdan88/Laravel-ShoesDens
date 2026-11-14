<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Produk;
use App\Models\ProdukVarian;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pengiriman;

class PenjualController extends Controller
{
    public function dashboard()
    {
        $penjual_id = Auth::id();

        // 1. Pesanan Hari Ini
        $pesanan_hari_ini = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->join('produk', 'detail_pesanan.produk_id', '=', 'produk.id')
            ->where('produk.penjual_id', $penjual_id)
            ->whereDate('pesanan.created_at', today())
            ->distinct()
            ->count('pesanan.id');

        // 2. Pendapatan Bulan Ini
        $pendapatan_bulan = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->join('produk', 'detail_pesanan.produk_id', '=', 'produk.id')
            ->where('produk.penjual_id', $penjual_id)
            ->whereMonth('pesanan.created_at', now()->month)
            ->whereYear('pesanan.created_at', now()->year)
            ->whereIn('pesanan.status', ['pembayaran_sukses', 'diproses', 'dikirim', 'selesai'])
            ->sum('detail_pesanan.subtotal');

        // 3. Total Produk
        $total_produk = Produk::where('penjual_id', $penjual_id)->count();

        // 4. Stok Menipis (< 5)
        $stok_terbatas = Produk::where('penjual_id', $penjual_id)
            ->where('stok_total', '<', 5)
            ->orderBy('stok_total', 'asc')
            ->limit(5)
            ->get();

        // 5. Pesanan Terbaru
        $pesanan_terbaru = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->join('produk', 'detail_pesanan.produk_id', '=', 'produk.id')
            ->where('produk.penjual_id', $penjual_id)
            ->select('pesanan.*', DB::raw('SUM(detail_pesanan.jumlah) as total_qty'))
            ->groupBy('pesanan.id')
            ->orderBy('pesanan.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('penjual.dashboard', compact(
            'pesanan_hari_ini',
            'pendapatan_bulan',
            'total_produk',
            'stok_terbatas',
            'pesanan_terbaru'
        ));
    }


    public function laporan(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $penjual_id = Auth::id();

        $start_date = $validated['start_date'] ?? date('Y-m-01');
        $end_date = $validated['end_date'] ?? date('Y-m-d');

        $pesanan_list = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->join('produk', 'detail_pesanan.produk_id', '=', 'produk.id')
            ->where('produk.penjual_id', $penjual_id)
            ->whereBetween(DB::raw('DATE(pesanan.created_at)'), [$start_date, $end_date])
            ->whereIn('pesanan.status', [
                'pembayaran_sukses',
                'diproses',
                'dikirim',
                'selesai'
            ])
            ->select(
                'pesanan.*',
                DB::raw('SUM(detail_pesanan.jumlah) as jumlah_item'),
                DB::raw('SUM(detail_pesanan.subtotal) as subtotal_harga')
            )
            ->groupBy('pesanan.id')
            ->orderBy('pesanan.created_at', 'desc')
            ->get();

        $total_pesanan = $pesanan_list->count();
        $total_pendapatan = $pesanan_list->sum('subtotal_harga');
        $total_qty = $pesanan_list->sum('jumlah_item');

        $rata_rata = $total_pesanan > 0
            ? $total_pendapatan / $total_pesanan
            : 0;

        return view('penjual.laporan', compact(
            'pesanan_list',
            'start_date',
            'end_date',
            'total_pesanan',
            'total_pendapatan',
            'total_qty',
            'rata_rata'
        ));
    }


    
    public function exportLaporan(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $penjual_id = Auth::id();

        $start_date = $validated['start_date'] ?? date('Y-m-01');
        $end_date = $validated['end_date'] ?? date('Y-m-d');

        $pesanan_list = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->join('produk', 'detail_pesanan.produk_id', '=', 'produk.id')
            ->where('produk.penjual_id', $penjual_id)
            ->whereBetween(DB::raw('DATE(pesanan.created_at)'), [$start_date, $end_date])
            ->whereIn('pesanan.status', [
                'pembayaran_sukses',
                'diproses',
                'dikirim',
                'selesai'
            ])
            ->select(
                'pesanan.kode_pesanan',
                'pesanan.created_at',
                'pesanan.status',
                'pesanan.ekspedisi',
                DB::raw('SUM(detail_pesanan.jumlah) as jumlah_item'),
                DB::raw('SUM(detail_pesanan.subtotal) as subtotal_harga')
            )
            ->groupBy('pesanan.id')
            ->orderBy('pesanan.created_at', 'desc')
            ->get();

        $filename = "Laporan-Penjualan-{$start_date}-to-{$end_date}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $columns = [
            'Kode Pesanan',
            'Tanggal',
            'Item',
            'Total Harga',
            'Status',
            'Ekspedisi'
        ];

        
        $callback = function () use ($pesanan_list, $columns) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $columns);

            // Perlindungan CSV Formula Injection
        $safeText = function ($value) {
            $value = (string) ($value ?? '');

            if (preg_match('/^[\t\r ]*[=+\-@]/', $value)) {
                $value = "'" . $value;
            }

            return $value;
        };

        foreach ($pesanan_list as $row) {
            fputcsv($file, [
                $safeText($row->kode_pesanan),
                $safeText($row->created_at),
                $row->jumlah_item,
                $row->subtotal_harga,
                $safeText($row->status),
                $safeText($row->ekspedisi)
            ]);
        }

        fclose($file);
    };

        return response()->stream($callback, 200, $headers);
    }


    public function produk()
    {
        $penjual_id = Auth::id();
        $produk_list = Produk::with(['ulasan'])
            ->where('penjual_id', $penjual_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('penjual.produk', compact('produk_list'));
    }

    public function produkTambah()
    {
        return view('penjual.produk-tambah');
    }

    public function simpanProduk(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string|max:5000',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'ukuran' => 'required|array|min:1',
            'ukuran.*' => 'required|string|in:35,36,37,38,39,40,41,42,43,44,45,46',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $penjual_id = Auth::id();
        $foto_name = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $foto_name = $file->hashName();
            $file->move(public_path('assets/uploads/produk'), $foto_name);
        }

        Produk::create([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'stok_total' => $validated['stok'],
            'ukuran' => implode(', ', $validated['ukuran']),
            'foto' => $foto_name,
            'penjual_id' => $penjual_id
        ]);

        return redirect('/penjual/produk')->with('message', [
            'message' => 'Produk berhasil ditambahkan',
            'type' => 'success'
        ]);
    }

    public function produkEdit($id)
    {
        $penjual_id = Auth::id();
        $produk = Produk::where('id', $id)->where('penjual_id', $penjual_id)->firstOrFail();
        $varian_list = ProdukVarian::where('produk_id', $id)->get();

        $ukuran_list = ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'];
        $warna_list = ['Hitam', 'Putih', 'Merah', 'Biru', 'Hijau', 'Kuning', 'Coklat', 'Abu-abu', 'Orange', 'Pink', 'Ungu', 'Kombinasi'];

        return view('penjual.produk-edit', compact('produk', 'varian_list', 'ukuran_list', 'warna_list'));
    }

    public function updateProduk(Request $request, $id)
    {
        $penjual_id = Auth::id();
        $produk = Produk::where('id', $id)->where('penjual_id', $penjual_id)->firstOrFail();

        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string|max:5000',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'ukuran' => 'required|array|min:1',
            'ukuran.*' => 'required|string|in:35,36,37,38,39,40,41,42,43,44,45,46',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $foto_name = $produk->foto;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

          $foto_baru = $file->hashName();

          $file->move(  
                public_path('assets/uploads/produk'),
                $foto_baru
            );

            // Hapus foto lama setelah foto baru berhasil disimpan
           if ($produk->foto) {
                $foto_lama = public_path(
                    'assets/uploads/produk/' . basename($produk->foto)
                );

                if (file_exists($foto_lama)) {
                    unlink($foto_lama);
                }
            }

            $foto_name = $foto_baru;
        }

        $produk->update([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'stok_total' => $validated['stok'],
            'ukuran' => implode(', ', $validated['ukuran']),
            'foto' => $foto_name
        ]);

        return redirect('/penjual/produk')->with('message', [
            'message' => 'Produk berhasil diperbarui',
            'type' => 'success'
        ]);
    }

    public function simpanVarian(Request $request, $id)
    {
        $penjual_id = Auth::id();
        $produk = Produk::where('id', $id)->where('penjual_id', $penjual_id)->firstOrFail();

        $validated = $request->validate([
            'ukuran' => 'required|string|in:35,36,37,38,39,40,41,42,43,44,45,46',
            'warna' => 'required|string|in:Hitam,Putih,Merah,Biru,Hijau,Kuning,Coklat,Abu-abu,Orange,Pink,Ungu,Kombinasi',
            'stok_varian' => 'required|integer|min:1'
        ]);

        ProdukVarian::create([
            'produk_id' => $id,
            'ukuran' => $validated['ukuran'],
            'warna' => $validated['warna'],
            'stok' => $validated['stok_varian']
        ]);

        return redirect("/penjual/produk/{$id}/edit")->with('message', [
            'message' => 'Varian berhasil ditambahkan',
            'type' => 'success'
        ]);
    }

    public function hapusProduk(Request $request)
    {
        $penjual_id = Auth::id();
        $validated = $request->validate([
            'produk_id' => 'required|integer|min:1'
        ]);

        $produk_id = $validated['produk_id'];

        $produk = Produk::where('id', $produk_id)->where('penjual_id', $penjual_id)->firstOrFail();

        if ($produk->foto) {
            $fotoPath = public_path(
                'assets/uploads/produk/' . basename($produk->foto)
            );

            if (is_file($fotoPath)) {
                unlink($fotoPath);
            }
        }   

        $produk->delete();

        return redirect('/penjual/produk')->with('message', [
            'message' => 'Produk berhasil dihapus',
            'type' => 'success'
        ]);
    }

    public function pesanan()
    {
        $penjual_id = Auth::id();

        // Get orders that contain products of this seller
        $pesanan_list = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->join('produk', 'detail_pesanan.produk_id', '=', 'produk.id')
            ->where('produk.penjual_id', $penjual_id)
            ->select('pesanan.*', DB::raw('SUM(detail_pesanan.jumlah) as total_qty'), DB::raw('SUM(detail_pesanan.subtotal) as subtotal_harga'))
            ->groupBy('pesanan.id')
            ->orderBy('pesanan.created_at', 'desc')
            ->get();

        return view('penjual.pesanan-list', compact('pesanan_list'));
    }

    public function pesananDetail($id)
    {
        $penjual_id = Auth::id();

        // Verify order contains products from this seller
        $pesanan = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->join('produk', 'detail_pesanan.produk_id', '=', 'produk.id')
            ->where('pesanan.id', $id)
            ->where('produk.penjual_id', $penjual_id)
            ->select(
                'pesanan.*',
                DB::raw('SUM(detail_pesanan.subtotal) as subtotal_penjual')
            )
            ->groupBy('pesanan.id')
            ->first();

        if (!$pesanan) {
            return redirect('/penjual/pesanan')->with('message', [
                'message' => 'Pesanan tidak ditemukan',
                'type' => 'error'
            ]);
        }

        $detail = DetailPesanan::where('pesanan_id', $id)
        ->whereIn('produk_id', function ($query) use ($penjual_id) {
        $query->select('id')
            ->from('produk')
            ->where('penjual_id', $penjual_id);
    }) 
        ->get();
        $pengiriman = Pengiriman::where('pesanan_id', $id)->first();

        return view('penjual.pesanan-detail', compact('pesanan', 'detail', 'pengiriman'));
    }

    
    public function updatePesananStatus(Request $request, $id)
    {
        $penjual_id = Auth::id();

        $validated = $request->validate([
            'status' => 'required|in:diproses,dikirim,selesai,dibatalkan',
            'no_resi' => 'nullable|required_if:status,dikirim|string|max:100'
        ]);

        DB::transaction(function () use ($id, $penjual_id, $validated) {

            $pesanan = Pesanan::whereHas('detail.produk', function ($query) use ($penjual_id) {
                $query->where('penjual_id', $penjual_id);
            })
            ->lockForUpdate()
            ->findOrFail($id);

            $transisi = [
                'pembayaran_sukses' => ['diproses', 'dibatalkan'],
                'diproses' => ['dikirim', 'dibatalkan'],
                'dikirim' => ['selesai'],
                'selesai' => [],
                'dibatalkan' => [],
            ];

            if (!in_array($validated['status'], $transisi[$pesanan->status] ?? [])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                   'status' => 'Perubahan status pesanan tidak diperbolehkan.'
                ]);
            }

            // Pastikan pembayaran sudah diverifikasi
            if (
                in_array($validated['status'], ['diproses', 'dikirim', 'selesai']) &&
                $pesanan->status_pembayaran !== 'terverifikasi'
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => 'Pesanan tidak dapat diproses karena pembayaran belum diverifikasi.'
                ]);
            }

            // Kembalikan stok jika pesanan dibatalkan
            if ($validated['status'] === 'dibatalkan') {

                $detailPesanan = DetailPesanan::where('pesanan_id', $pesanan->id)
                    ->orderBy('produk_id')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                foreach ($detailPesanan as $detail) {

                    $produk = Produk::where('id', $detail->produk_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    // Kembalikan stok total produk
                    $produk->increment('stok_total', $detail->jumlah);

                    if ($detail->varian_id) {

                        $varian = ProdukVarian::where('id', $detail->varian_id)
                            ->where('produk_id', $detail->produk_id)
                            ->lockForUpdate()
                            ->firstOrFail();

                        // Kembalikan stok varian yang tepat
                        $varian->increment('stok', $detail->jumlah);

                    } else {

                        // Periksa produk yang memiliki varian
                        $memilikiVarian = ProdukVarian::where(
                            'produk_id',
                            $detail->produk_id
                        )->exists();

                        if ($memilikiVarian) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'status' => 'Pesanan lama tidak memiliki data varian. Pengembalian stok perlu diperiksa secara manual.'
                            ]);
                        }
                    }
                }
            }

            // Perbarui status pesanan
            $pesanan->status = $validated['status'];
            $pesanan->save();

            // Buat atau perbarui data pengiriman
            if ($validated['status'] === 'dikirim') {
                Pengiriman::updateOrCreate(
                    ['pesanan_id' => $pesanan->id],
                    [
                        'no_resi' => $validated['no_resi'],
                        'ekspedisi' => $pesanan->ekspedisi,
                        'status_kirim' => 'dalam_perjalanan'
                    ]
                );
            }
        });

        return redirect("/penjual/pesanan/{$id}")->with('message', [
            'message' => 'Pesanan berhasil diperbarui',
            'type' => 'success'
        ]);
    }


    public function verifikasiPembayaran(Request $request, $id)
    {
        $validated = $request->validate([
            'aksi' => 'required|in:terima,tolak',
            'catatan_verifikasi' => 'required_if:aksi,tolak|nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($id, $validated) {

            $penjual_id = Auth::id();

            $pesanan = Pesanan::whereHas('detail.produk', function ($query) use ($penjual_id) {
                $query->where('penjual_id', $penjual_id);
            })
            ->lockForUpdate()
            ->findOrFail($id);

            // Pastikan pesanan memang menunggu pembayaran
            if ($pesanan->status !== 'menunggu_pembayaran') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'aksi' => 'Status pesanan tidak memungkinkan verifikasi pembayaran.',
                ]);
            }

            // Pastikan bukti sudah diunggah
            if (
                $pesanan->status_pembayaran !== 'menunggu_verifikasi' ||
                !$pesanan->bukti_pembayaran
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'aksi' => 'Belum ada bukti pembayaran yang perlu diverifikasi.',
                ]);
            }

            if (!Storage::disk('local')->exists($pesanan->bukti_pembayaran)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'aksi' => 'File bukti pembayaran tidak ditemukan. Silakan periksa kembali.',
                ]);
            }

            if ($validated['aksi'] === 'terima') {

                $pesanan->status_pembayaran = 'terverifikasi';
                $pesanan->status = 'pembayaran_sukses';
                $pesanan->catatan_verifikasi = null;

            } else {

                $pesanan->status_pembayaran = 'ditolak';
                $pesanan->catatan_verifikasi = $validated['catatan_verifikasi'];

            }

            $pesanan->tanggal_verifikasi = now();
            $pesanan->diverifikasi_oleh = Auth::id();

            $pesanan->save();
        });

        return redirect("/penjual/pesanan/{$id}")->with('message', [
            'message' => $validated['aksi'] === 'terima'
                ? 'Pembayaran berhasil diverifikasi.'
                : 'Bukti pembayaran berhasil ditolak.',
            'type' => 'success'
        ]);
    }

    public function lihatBuktiPembayaran($id)
    {
        $penjual_id = Auth::id();

        $pesanan = Pesanan::whereHas('detail.produk', function ($query) use ($penjual_id) {
            $query->where('penjual_id', $penjual_id);
        })->findOrFail($id);

        if (!$pesanan->bukti_pembayaran) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        $path = $pesanan->bukti_pembayaran;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File bukti pembayaran tidak tersedia.');
        }

        $fullPath = Storage::disk('local')->path($path);

        return response()->file($fullPath, [
            'Content-Disposition' => 'inline; filename="bukti-pembayaran-' . $pesanan->kode_pesanan . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

}
