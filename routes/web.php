<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PenjualController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest / Public routes
Route::get('/', [MainController::class, 'index']);
Route::get('/produk', [MainController::class, 'katalog']);
Route::get('/detail/{id}', [MainController::class, 'detail']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Pembeli (Buyer) routes
Route::middleware(['auth', 'pembeli'])->group(function () {
    Route::get('/pembeli/keranjang', [PembeliController::class, 'cart']);
    Route::post('/pembeli/keranjang/add', [PembeliController::class, 'addToCart']);
    Route::post('/pembeli/keranjang/update', [PembeliController::class, 'updateCart']);
    Route::post('/pembeli/keranjang/delete', [PembeliController::class, 'deleteCart']);
    Route::get('/pembeli/keranjang/count', [PembeliController::class, 'getCartCount']);

    Route::get('/pembeli/checkout', [PembeliController::class, 'checkout']);
    Route::post('/pembeli/checkout', [PembeliController::class, 'prosesCheckout']);

    Route::get('/pembeli/pembayaran', [PembeliController::class, 'pembayaran'])->name('pembeli.pembayaran');
    Route::post('/pembeli/pembayaran', [PembeliController::class, 'prosesPembayaran'])->name('pembeli.pembayaran.proses');

    Route::get('/pembeli/pesanan', [PembeliController::class, 'pesanan']);
    Route::get('/pembeli/lacak', [PembeliController::class, 'lacak']);

    Route::get('/pembeli/ulasan', [PembeliController::class, 'ulasan']);
    Route::post('/pembeli/ulasan', [PembeliController::class, 'simpanUlasan']);

    Route::get('/pembeli/profil', [PembeliController::class, 'profil']);
    Route::post('/pembeli/profil', [PembeliController::class, 'updateProfil']);
});

// Penjual (Seller) routes
Route::middleware(['auth', 'penjual'])->group(function () {
    Route::get('/penjual/dashboard', [PenjualController::class, 'dashboard']);
    Route::get('/penjual/produk', [PenjualController::class, 'produk']);
    Route::get('/penjual/produk/tambah', [PenjualController::class, 'produkTambah']);
    Route::post('/penjual/produk/tambah', [PenjualController::class, 'simpanProduk']);
    Route::get('/penjual/produk/{id}/edit', [PenjualController::class, 'produkEdit']);
    Route::post('/penjual/produk/{id}/edit', [PenjualController::class, 'updateProduk']);
    Route::post('/penjual/produk/{id}/varian', [PenjualController::class, 'simpanVarian']);
    Route::post('/penjual/produk/delete', [PenjualController::class, 'hapusProduk']);

    Route::get('/penjual/pesanan', [PenjualController::class, 'pesanan']);
    Route::get('/penjual/pesanan/{id}', [PenjualController::class, 'pesananDetail']);
    Route::get('/penjual/pesanan/{id}/bukti', [PenjualController::class, 'lihatBuktiPembayaran']);
    Route::post('/penjual/pesanan/{id}/status', [PenjualController::class, 'updatePesananStatus']);
    Route::post('/penjual/pesanan/{id}/verifikasi-pembayaran', [PenjualController::class, 'verifikasiPembayaran']);

    Route::get('/penjual/laporan', [PenjualController::class, 'laporan']);
    Route::get('/penjual/laporan/export', [PenjualController::class, 'exportLaporan']);
});
