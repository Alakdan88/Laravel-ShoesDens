<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    protected $table = 'pengiriman';

    public $timestamps = false;

    protected $fillable = [
        'pesanan_id',
        'no_resi',
        'ekspedisi',
        'status_kirim'
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
