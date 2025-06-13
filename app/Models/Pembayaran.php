<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayaran'; // Nama tabel eksplisit

    protected $guarded = [];

    protected $fillable = [
    'penjualan_id',
    'tgl_bayar',
    'jenis_pembayaran',
    'transaction_time',
    'gross_amount',
    'order_id',
    'payment_type',
    'status_code',
    'transaction_id',
    'settlement_time',
    'status_message',
    'merchant_id'
];

    //relasi ke tabel penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
