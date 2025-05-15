<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pelanggan extends Model
{
    protected $table = 'pelanggan'; // Nama tabel eksplisit
    protected $guarded = [];

    use HasFactory;

    public static function getCustomerID()
    {
        $sql = "SELECT IFNULL(MAX(id_pelanggan), '0000') as id_pelanggan FROM pelanggan";
        $customerid = DB::select($sql);

        foreach ($customerid as $cstrid) {
            $cid = $cstrid->id_pelanggan;
        }

        $noawal = substr($cid, -4);
        $noakhir = $noawal + 1;
        $noakhir = str_pad($noakhir, 4, "0", STR_PAD_LEFT);
        return $noakhir;
    }
    // relasi ke tabel pembeli
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
        // pastikan 'user_id' adalah nama kolom foreign key
    }

    // relasi ke tabel penjualan
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'pelanggan_id');
    }
}
