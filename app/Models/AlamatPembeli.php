<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlamatPembeli extends Model
{
    protected $table = 'alamat_pembeli';

    protected $fillable = [
        'user_id',
        'label',
        'nama_penerima',
        'no_hp',
        'alamat',
        'kota',
        'kode_pos',
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
