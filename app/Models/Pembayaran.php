<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayaran'; // Nama tabel eksplisit

    protected $guarded = [];

    //relasi ke tabel penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
