<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualan'; // Nama tabel eksplisit

    protected $guarded = [];

    // relasi ke tabel detail penjualan
    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    // relasi ke tabel pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    //relasi ke tabel pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
