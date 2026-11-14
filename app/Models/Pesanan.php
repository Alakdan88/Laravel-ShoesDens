<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'user_id',
        'kode_pesanan',
        'total_harga',
        'ongkir',
        'ekspedisi',
        'status',
        'status_pembayaran',
        'bukti_pembayaran',
        'tanggal_upload_bukti',
        'tanggal_verifikasi',
        'diverifikasi_oleh',
        'catatan_verifikasi',
        'midtrans_order_id',
        'midtrans_status',
        'alamat_kirim',
        'catatan_pembeli'
    ];

    protected $casts = [
        'tanggal_upload_bukti' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'total_harga' => 'decimal:2',
        'ongkir' => 'decimal:2',
        'diverifikasi_oleh' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'pesanan_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'pesanan_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
