<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_menu'; // Pastikan ini sesuai dengan database
    public $incrementing = false; // Jika id_menu bukan auto-increment
    protected $keyType = 'string'; // Jika id_menu adalah string
    protected $table = 'menu'; // Nama tabel eksplisit

    protected $guarded = [];

    public static function getIdMenu()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(id_menu), 'MN000') as id_menu 
                FROM menu ";
        $idmenu = DB::select($sql);

        // cacah hasilnya
        foreach ($idmenu as $idmn) {
            $id = $idmn->id_menu;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($id,-3);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'MN'.str_pad($noakhir,3,"0",STR_PAD_LEFT); //menyambung dengan string PR-001
        return $noakhir;

    }

    // Dengan mutator ini, setiap kali data harga_barang dikirim ke database, koma akan otomatis dihapus.
    public function setHargaMenuAttribute($value)
    {
        // Hapus koma (,) dari nilai sebelum menyimpannya ke database
        $this->attributes['harga'] = str_replace('.', '', $value);
    }

    //public function kategori()
    //{
    //    return $this->belongsTo(kategoriMenu::class, 'id_kategori', 'id_kategori');
    //}
}
