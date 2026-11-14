<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Ulasan;
use App\Models\ProdukVarian;

class MainController extends Controller
{
    public function index()
    {
        $produk_terbaru = Produk::withCount('ulasan')
            ->withAvg('ulasan', 'rating')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('index', compact('produk_terbaru'));
    }

    public function katalog(Request $request)
    {
        
        $kategori = $request->input('kategori');
        $search = $request->input('search');
        $min_price = $request->filled('min_price') ? floatval($request->input('min_price')) : 0;
        $max_price = $request->filled('max_price') ? floatval($request->input('max_price')) : PHP_INT_MAX;

        $query = Produk::withCount('ulasan')->withAvg('ulasan', 'rating');

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($min_price > 0) {
            $query->where('harga', '>=', $min_price);
        }

        if ($max_price < PHP_INT_MAX) {
            $query->where('harga', '<=', $max_price);
        }

        $total = $query->count();
        $produk = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $kategori_list = ['Pria', 'Wanita', 'Anak-anak', 'Olahraga', 'Formal', 'Casual'];

        return view('produk', compact('produk', 'total', 'kategori_list', 'kategori', 'search', 'min_price', 'max_price'));
    }

    public function detail($id)
    {
        $produk = Produk::with(['penjual'])
            ->withCount('ulasan')
            ->withAvg('ulasan', 'rating')
            ->findOrFail($id);
        
        // Get variants
        $varian = ProdukVarian::where('produk_id', $id)
            ->orderBy('ukuran')
            ->orderBy('warna')
            ->get();

        // Get recent 5 reviews
        $ulasan = Ulasan::with(['user'])
            ->where('produk_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('detail', compact('produk', 'varian', 'ulasan'));
    }
}
