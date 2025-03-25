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
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(id_pelanggan), '0000') as id_pelanggan 
                FROM pelanggan ";
        $customerid = DB::select($sql);

        // cacah hasilnya
        foreach ($customerid as $cstrid) {
            $cid = $cstrid->id_pelanggan;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($cid,-4);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = str_pad($noakhir,4,"0",STR_PAD_LEFT); //menyambung dengan string PR-001
        return $noakhir;

    }
}
