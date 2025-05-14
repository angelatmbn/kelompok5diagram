<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan
    protected $table = 'presensi';

    // Kolom-kolom yang dapat diisi melalui mass assignment
    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan',
    ];

    // Menyatakan relasi dengan model Pegawai
    public function pegawaii()
    {
        return $this->belongsTo(Pegawaii::class, 'id_pegawai');
    }

    // Menyatakan relasi dengan model Penggajian (bisa diubah jika diperlukan)
    public function penggajian()
    {
        return $this->hasMany(Penggajian::class, 'id_pegawai');
    }

    // Jika tidak ingin menggunakan timestamps 'created_at' dan 'updated_at', set ke false
    public $timestamps = true;
}

