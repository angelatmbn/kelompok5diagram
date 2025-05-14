<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajian';
    
    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'gaji_pokok',
        'potongan',
        'total_gaji',
        'status_pembayaran',
     ];

      public function pegawaii(): BelongsTo
    {
        return $this->belongsTo(Pegawaii::class, 'id_pegawai');
    }
}
