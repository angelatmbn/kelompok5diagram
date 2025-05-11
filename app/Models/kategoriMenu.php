<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class KategoriMenu extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_kategori';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'kategori_menu';

    protected $guarded = [];

    public static function getKategoriMenu()
    {
        $sql = "SELECT IFNULL(MAX(id_kategori), 'KTG000') as id_kategori FROM kategori_menu";
        $idkategori = DB::select($sql);

        foreach ($idkategori as $idktg) {
            $id = $idktg->id_kategori;
        }

        $noawal = substr($id, -3);
        $noakhir = 'KTG' . str_pad($noawal + 1, 3, "0", STR_PAD_LEFT);
        return $noakhir;
    }

    public function menu()
    {
        return $this->hasMany(Menu::class, 'id_kategori', 'id_kategori');
    }

    public function setHargaMenuAttribute($value)
    {
        $this->attributes['harga'] = str_replace('.', '', $value);
    }
    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriMenu::class, 'id_kategori', 'id_kategori');
    }
}