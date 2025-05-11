<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pegawaii extends Model
{
    protected $table = 'pegawaii'; // Nama tabel eksplisit

    protected $guarded = [];
    public static function getIdPegawai()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(id_pegawai), 'PGW000') as id_pegawai
                FROM pegawaii ";
        $idpegawai = DB::select($sql);

        // cacah hasilnya
        foreach ($idpegawai as $idpgw) {
            $id = $idpgw->id_pegawai;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($id,-3);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'PGW-'.str_pad($noakhir,3,"0",STR_PAD_LEFT); //menyambung dengan string PR-001
        return $noakhir;

    }
}