<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\ProdukVarian;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Ulasan;
use App\Models\Pengiriman;

class PembeliController extends Controller
{
    public function cart()
    {
        $user_id = Auth::id();
        $keranjang = Keranjang::with(['produk', 'varian'])
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $total = 0;
        foreach ($keranjang as $item) {
            $total += $item->produk->harga * $item->jumlah;
        }

        return view('pembeli.keranjang', compact('keranjang', 'total'));
    }

    
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|integer|min:1',
            'varian_id' => 'nullable|integer|min:1',
            'jumlah' => 'required|integer|min:1',
        ]);

        $user_id = Auth::id();

        try {
            DB::transaction(function () use ($validated, $user_id) {
                $produk = Produk::where('id', $validated['produk_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$produk) {
                    throw ValidationException::withMessages([
                        'produk_id' => 'Produk tidak ditemukan.',
                    ]);
                }

                if ($produk->stok_total <= 0) {
                    throw ValidationException::withMessages([
                        'produk_id' => 'Produk sedang habis atau tidak memiliki stok.',
                    ]);
                }

                $varian_id = $validated['varian_id'] ?? null;
                $jumlah = $validated['jumlah'];
                $varian = null;

                if ($varian_id) {
                    $varian = ProdukVarian::where('id', $varian_id)
                        ->where('produk_id', $produk->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$varian) {
                        throw ValidationException::withMessages([
                            'varian_id' => 'Varian produk tidak valid.',
                        ]);
                    }
                }

                $existingItems = Keranjang::where('user_id', $user_id)
                    ->where('produk_id', $produk->id)
                    ->where('varian_id', $varian_id)
                    ->lockForUpdate()
                    ->get();

                $jumlahSebelumnya = $existingItems->sum('jumlah');
                $jumlahAkhir = $jumlah + $jumlahSebelumnya;

                $totalJumlahProduk = Keranjang::where('user_id', $user_id)
                    ->where('produk_id', $produk->id)
                    ->sum('jumlah') + $jumlah;

                if ($produk->stok_total < $totalJumlahProduk) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Stok produk tidak mencukupi.',
                    ]);
                }

                if ($varian && $varian->stok < $jumlahAkhir) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Stok varian tidak mencukupi.',
                    ]);
                }

                if ($existingItems->isNotEmpty()) {
                    $itemUtama = $existingItems->first();
                    $itemUtama->jumlah = $jumlahAkhir;
                    $itemUtama->save();

                    $existingItems->skip(1)->each(function ($item) {
                        $item->delete();
                    });
                } else {
                    Keranjang::create([
                        'user_id' => $user_id,
                        'produk_id' => $produk->id,
                        'varian_id' => $varian_id,
                        'jumlah' => $jumlah,
                    ]);
                }
            });

            return redirect('/pembeli/keranjang')->with('message', [
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'type' => 'success',
            ]);

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Throwable $e) {
            \Log::error('Gagal menambahkan produk ke keranjang: ' . $e->getMessage());

            return redirect('/produk')->with('message', [
                'message' => 'Terjadi kesalahan saat menambahkan produk.',
                'type' => 'error',
            ]);
        }
    }


    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
        ]);

        $user_id = Auth::id();

        try {
            DB::transaction(function () use ($validated, $user_id) {
                $item = Keranjang::where('id', $validated['item_id'])
                    ->where('user_id', $user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $produk = Produk::where('id', $item->produk_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $jumlah = $validated['jumlah'];

                $totalJumlahProduk = Keranjang::where('user_id', $user_id)
                    ->where('produk_id', $produk->id)
                    ->where('id', '!=', $item->id)
                    ->sum('jumlah') + $jumlah;
                
                if ($produk->stok_total < $totalJumlahProduk) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Stok produk tidak mencukupi.',
                    ]);
                }

                if ($item->varian_id) {
                    $varian = ProdukVarian::where('id', $item->varian_id)
                        ->where('produk_id', $produk->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$varian || $varian->stok < $jumlah) {
                        throw ValidationException::withMessages([
                            'jumlah' => 'Stok varian tidak mencukupi atau varian tidak ditemukan.',
                        ]);
                    }
                }   

                $item->jumlah = $jumlah;
                $item->save();
            });

            return redirect('/pembeli/keranjang')->with('message', [
                'message' => 'Jumlah produk berhasil diperbarui.',
                'type' => 'success',
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            \Log::error('Gagal memperbarui keranjang: ' . $e->getMessage());

            return redirect('/pembeli/keranjang')->with('message', [
                'message' => 'Terjadi kesalahan saat memperbarui keranjang.',
                'type' => 'error',
            ]);
        }
    }

    public function deleteCart(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer|min:1',
        ]);

        Keranjang::where('id', $validated['item_id'])
            ->where('user_id', Auth::id())
            ->delete();

        return redirect('/pembeli/keranjang')->with('message', [
            'message' => 'Produk berhasil dihapus dari keranjang.',
            'type' => 'success',
        ]);
    }

    public function getCartCount()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user && isset($user->role) && $user->role === 'pembeli') {
                $count = Keranjang::where('user_id', Auth::id())->count();
                return response()->json(['count' => $count]);
            }
        }

        return response()->json(['count' => 0]);
    }

    public function checkout()
    {
        $user_id = Auth::id();
        $keranjang = Keranjang::with(['produk'])->where('user_id', $user_id)->get();

        if ($keranjang->isEmpty()) {
            return redirect('/pembeli/keranjang')->with('message', [
                'message' => 'Keranjang kosong',
                'type' => 'warning'
            ]);
        }

        $user = Auth::user();

        $subtotal = 0;
        foreach ($keranjang as $item) {
            $subtotal += $item->produk->harga * $item->jumlah;
        }

        $ongkir = 25000; // default dummy
        $total = $subtotal + $ongkir;

        return view('pembeli.checkout', compact('keranjang', 'user', 'subtotal', 'ongkir', 'total'));
    }

    public function prosesCheckout(Request $request)
    {
        $validated = $request->validate([
            'nama_penerima' => 'required|string|max:100',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string|max:255',
            'kota' => 'required|string|max:50',
            'kode_pos' => 'required|string|max:10',
            'ekspedisi' => 'required|string|max:50',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user_id = Auth::id();

        try {
            $pesanan = DB::transaction(function () use ($validated, $user_id) {

                // Ambil dan kunci keranjang milik pengguna
                $keranjang = Keranjang::where('user_id', $user_id)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if ($keranjang->isEmpty()) {
                    return null;
                }

                $subtotal = 0;
                $dataProduk = [];

                // Periksa ulang stok dan harga terkini
                foreach ($keranjang as $item) {
                    $produk = Produk::where('id', $item->produk_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$produk) {
                        throw ValidationException::withMessages([
                            'stok' => 'Salah satu produk sudah tidak tersedia.',
                        ]);
                    }

                    $jumlahProdukDiKeranjang = $keranjang
                        ->where('produk_id', $produk->id)
                        ->sum('jumlah');

                    if ($produk->stok_total < $jumlahProdukDiKeranjang) {
                        throw ValidationException::withMessages([
                            'stok' => 'Total stok produk ' . $produk->nama . ' tidak mencukupi.',
                        ]);
                    }

                    $varian = null;

                    if ($item->varian_id) {
                        $varian = ProdukVarian::where('id', $item->varian_id)
                            ->where('produk_id', $produk->id)
                            ->lockForUpdate()
                            ->first();
                        
                        if (!$varian) {
                            throw ValidationException::withMessages([
                                'stok' => 'Varian produk tidak ditemukan.',
                            ]);
                        }

                        $jumlahVarianDiKeranjang = $keranjang
                            ->where('varian_id', $varian->id)
                            ->sum('jumlah');

                        if  ($varian->stok < $jumlahVarianDiKeranjang) {
                            throw ValidationException::withMessages([
                                'stok' => 'Stok varian produk ' . $produk->nama . ' tidak mencukupi.',
                            ]);
                        }
                    }

                    $harga = $produk->harga;
                    $jumlah = $item->jumlah;
                    $subtotalItem = $harga * $jumlah;

                    $subtotal += $subtotalItem;

                    $dataProduk[] = [
                        'produk' => $produk,
                        'varian' => $varian,
                        'jumlah' => $jumlah,
                        'harga' => $harga,
                        'subtotal' => $subtotalItem,
                    ];
                }

                $ongkir = 25000;
                $total = $subtotal + $ongkir;

                // Buat kode pesanan
                $kode_pesanan = 'PSN-' . now()->format('Ymd') . '-' .
                    str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                // Buat pesanan
                $pesanan = Pesanan::create([
                    'user_id' => $user_id,
                    'kode_pesanan' => $kode_pesanan,
                    'total_harga' => $total,
                    'ongkir' => $ongkir,
                    'ekspedisi' => $validated['ekspedisi'],
                    'alamat_kirim' =>
                        'Nama: ' . $validated['nama_penerima'] .
                        ', No. HP: ' . $validated['no_hp'] .
                        ', Alamat: ' . $validated['alamat'] .
                        ', Kota: ' . $validated['kota'] .
                        ', Kode Pos: ' . $validated['kode_pos'],
                    'catatan_pembeli' => $validated['catatan'] ?? null,
                    'status' => 'menunggu_pembayaran',
                ]);

                // Buat detail pesanan dan kurangi stok
                foreach ($dataProduk as $data) {
                    $produk = $data['produk'];
                    $varian = $data['varian'];
                    $jumlah = $data['jumlah'];

                    DetailPesanan::create([
                        'pesanan_id' => $pesanan->id,
                        'produk_id' => $produk->id,
                        'varian_id' => $varian ? $varian->id : null,
                        'nama_produk' => $produk->nama,
                        'ukuran' => $varian ? $varian->ukuran : null,
                        'harga_satuan' => $data['harga'],
                        'jumlah' => $jumlah,
                        'subtotal' => $data['subtotal'],
                    ]);

                    $produk->decrement('stok_total', $jumlah);

                    if ($varian) {
                        $varian->decrement('stok', $jumlah);
                    }
                }

                // Kosongkan keranjang setelah pesanan berhasil dibuat
                Keranjang::where('user_id', $user_id)->delete();

                return $pesanan;
            }, 3);

            if (!$pesanan) {
                return redirect('/pembeli/keranjang')->with('message', [
                    'message' => 'Keranjang kosong.',
                    'type' => 'warning',
                ]);
            }

            return redirect('/pembeli/pembayaran?pesanan_id=' . $pesanan->id);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Checkout gagal: ' . $e->getMessage());

            return redirect('/pembeli/checkout')->with('message', [
                'message' => 'Terjadi kesalahan saat memproses pesanan. Silakan coba kembali.',
                'type' => 'error',
            ]);
        }
    }

    public function pembayaran(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|integer|min:1',
        ]);

        $pesanan_id = $validated['pesanan_id'];
        $user_id = Auth::id();

        $pesanan = Pesanan::where('id', $pesanan_id)->where('user_id', $user_id)->firstOrFail();
        $detail = DetailPesanan::where('pesanan_id', $pesanan_id)->get();

        return view('pembeli.pembayaran', compact('pesanan', 'detail'));
    }

    
    
    public function prosesPembayaran(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|integer|min:1',
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = null;
        $buktiLama = null;

        try {
            DB::transaction(function () use ($request, $validated, &$path, &$buktiLama) {
                $pesanan = Pesanan::where('id', $validated['pesanan_id'])
                    ->where('user_id', Auth::id())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pesanan->status !== 'menunggu_pembayaran') {
                    throw ValidationException::withMessages([
                        'bukti_pembayaran' => 'Pesanan tidak dapat menerima pembayaran.',
                    ]);
                }

                if ($pesanan->status_pembayaran === 'terverifikasi') {
                    throw ValidationException::withMessages([
                        'bukti_pembayaran' => 'Pembayaran sudah diverifikasi.',
                    ]);
                }

                if ($pesanan->status_pembayaran === 'menunggu_verifikasi') {
                    throw ValidationException::withMessages([
                        'bukti_pembayaran' => 'Bukti pembayaran sedang diperiksa penjual.',
                    ]);
                }

                $buktiLama = $pesanan->bukti_pembayaran;

                $path = $request->file('bukti_pembayaran')
                    ->store('bukti-pembayaran', 'local');

                $pesanan->update([
                    'bukti_pembayaran' => $path,
                    'tanggal_upload_bukti' => now(),
                    'status_pembayaran' => 'menunggu_verifikasi',
                    'tanggal_verifikasi' => null,
                    'diverifikasi_oleh' => null,
                    'catatan_verifikasi' => null,
                ]);
            });

            if ($buktiLama && $buktiLama !== $path) {
                Storage::disk('local')->delete($buktiLama);
            }

        } catch (ValidationException $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }

            throw $e;

        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }

            \Log::error('Gagal mengunggah bukti pembayaran: ' . $e->getMessage());

            return back()->with('message', [
                'message' => 'Terjadi kesalahan saat mengunggah bukti pembayaran.',
                'type' => 'error',
            ]);
        }

        return redirect('/pembeli/pesanan')->with('message', [
            'message' => 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi penjual.',
            'type' => 'success',
        ]);
    }


        
    public function pesanan()
    {
        $user_id = Auth::id();

        $pesanan_list = Pesanan::with('detail')
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembeli.pesanan', compact('pesanan_list'));
    }


    public function lacak(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|integer|min:1',
        ]);

        $pesanan_id = $validated['pesanan_id'];
        $user_id = Auth::id();

        $pesanan = Pesanan::where('id', $pesanan_id)->where('user_id', $user_id)->firstOrFail();
        $detail = DetailPesanan::where('pesanan_id', $pesanan_id)->get();
        $pengiriman = Pengiriman::where('pesanan_id', $pesanan_id)->first();

        return view('pembeli.lacak', compact('pesanan', 'detail', 'pengiriman'));
    }

    public function ulasan(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|integer|min:1',
        ]);

        $pesanan_id = $validated['pesanan_id'];
        $user_id = Auth::id();

        $pesanan = Pesanan::where('id', $pesanan_id)->where('user_id', $user_id)->firstOrFail();

        if ($pesanan->status !== 'selesai') {
            return redirect('/pembeli/pesanan')->with('message', [
                'message' => 'Hanya pesanan yang selesai dapat diberi ulasan',
                'type' => 'error'
            ]);
        }

        $detail = DetailPesanan::where('pesanan_id', $pesanan_id)->get();

        return view('pembeli.ulasan', compact('pesanan', 'detail'));
    }


    public function simpanUlasan(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|integer|min:1',
        ]);

        $pesanan_id = $validated['pesanan_id'];
        $user_id = Auth::id();

        $pesanan = Pesanan::where('id', $pesanan_id)
            ->where('user_id', $user_id)
            ->firstOrFail();

        if ($pesanan->status !== 'selesai') {
            return redirect('/pembeli/pesanan')->with('message', [
                'message' => 'Hanya pesanan yang selesai dapat diberi ulasan.',
                'type' => 'error',
            ]);
        }

        $detail = DetailPesanan::where('pesanan_id', $pesanan_id)->get();

        if ($detail->isEmpty()) {
            return redirect('/pembeli/pesanan')->with('message', [
                'message' => 'Detail produk pesanan tidak ditemukan.',
                'type' => 'error',
            ]);
        }

        // Siapkan aturan validasi seluruh rating dan komentar
        $rules = [
            'pesanan_id' => 'required|integer|min:1',
        ];

        foreach ($detail as $d) {
            $rules['rating_' . $d->id] = 'required|integer|min:1|max:5';
            $rules['komentar_' . $d->id] = 'nullable|string|max:1000';
        }

        // Validasi seluruh input sebelum menyimpan ulasan
        $dataValidated = $request->validate($rules);

        try {
            $jumlahDisimpan = DB::transaction(function () use (
                $pesanan_id,
                $user_id,
                $detail,
                $dataValidated
            ) {
                // Kunci pesanan agar tidak diproses bersamaan
                $pesanan = Pesanan::where('id', $pesanan_id)
                    ->where('user_id', $user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pesanan->status !== 'selesai') {
                    throw ValidationException::withMessages([
                        'pesanan_id' => 'Pesanan belum selesai dan tidak dapat diberi ulasan.',
                    ]);
                }

                $jumlah = 0;

                foreach ($detail as $d) {
                    // Periksa apakah produk sudah pernah diulas
                    $existing = Ulasan::where('user_id', $user_id)
                        ->where('produk_id', $d->produk_id)
                        ->where('pesanan_id', $pesanan_id)
                        ->exists();

                    if ($existing) {
                        continue;
                    }

                    Ulasan::create([
                        'user_id' => $user_id,
                        'produk_id' => $d->produk_id,
                        'pesanan_id' => $pesanan_id,
                        'rating' => $dataValidated['rating_' . $d->id],
                        'komentar' => $dataValidated['komentar_' . $d->id] ?? null,
                    ]);

                    $jumlah++;
                }

                return $jumlah;
            });

            return redirect('/pembeli/pesanan')->with('message', [
                'message' => $jumlahDisimpan > 0
                    ? 'Ulasan berhasil dikirim.'
                    : 'Semua produk dalam pesanan ini sudah pernah diulas.',
                'type' => $jumlahDisimpan > 0 ? 'success' : 'warning',
            ]);

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Throwable $e) {
            \Log::error('Gagal menyimpan ulasan: ' . $e->getMessage());

            return redirect('/pembeli/pesanan')->with('message', [
                'message' => 'Terjadi kesalahan saat menyimpan ulasan. Silakan coba kembali.',
                    'type' => 'error',
            ]);
        }
    }


    public function profil()
    {
        $user = Auth::user();
        return view('pembeli.profil', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:50',
            'kode_pos' => 'nullable|string|max:10',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        $user->update($validated);

        return redirect('/pembeli/profil')->with('message', [
            'message' => 'Profil berhasil diperbarui.',
            'type' => 'success',
        ]);
    }
}
