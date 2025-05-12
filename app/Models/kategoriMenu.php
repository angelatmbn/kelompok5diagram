<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class kategoriMenu extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_kategori';
    public $incrementing = false;
    protected $table = 'kategori_menu'; // Nama tabel eksplisit

    protected $guarded = [];

    public static function getKategoriMenu()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(id_kategori), 'KAT000') as id_kategori
                FROM kategori_menu ";
        $idkategori = DB::select($sql);

        // cacah hasilnya
        foreach ($idkategori as $idkat) {
            $kd = $idkat->id_kategori;
        }
        // Mengambil substring tiga digit akhir dari string KAT-000
        $noawal = substr($kd,-3);
        $noakhir = intval($noawal) +1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'KAT'.str_pad($noakhir,3,"0",STR_PAD_LEFT); //menyambung dengan string KAT-001
        return $noakhir;

    }
}
