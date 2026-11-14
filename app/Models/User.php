<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'alamat',
        'kota',
        'kode_pos',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Helper functions for roles
    public function isPembeli()
    {
        return $this->role === 'pembeli';
    }

    public function isPenjual()
    {
        return $this->role === 'penjual';
    }

    // Relationships
    public function produk()
    {
        return $this->hasMany(Produk::class, 'penjual_id');
    }

    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'user_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'user_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'user_id');
    }

    public function alamatPembeli()
    {
        return $this->hasMany(AlamatPembeli::class, 'user_id');
    }
}
