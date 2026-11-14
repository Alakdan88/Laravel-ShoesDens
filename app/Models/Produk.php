<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'kategori',
        'foto',
        'stok_total',
        'penjual_id'
    ];

    public function penjual()
    {
        return $this->belongsTo(User::class, 'penjual_id');
    }

    public function varian()
    {
        return $this->hasMany(ProdukVarian::class, 'produk_id');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'produk_id');
    }
    
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'produk_id');
    }

    public function getRatingAvgAttribute()
    {
        return $this->ulasan()->avg('rating') ?: 0;
    }

    public function getRatingCountAttribute()
    {
        return $this->ulasan()->count();
    }
}
