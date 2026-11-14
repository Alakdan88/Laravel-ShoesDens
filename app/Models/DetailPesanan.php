<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    public $timestamps = false;

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'varian_id',
        'nama_produk',
        'ukuran',
        'harga_satuan',
        'jumlah',
        'subtotal'
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function varian()
    {
        return $this->belongsTo(ProdukVarian::class, 'varian_id');
    }
}
