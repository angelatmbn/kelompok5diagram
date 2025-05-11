<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_menu';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'menu';

    protected $guarded = [];

    public static function getIdMenu()
    {
        $sql = "SELECT IFNULL(MAX(id_menu), 'MN000') as id_menu FROM menu";
        $idmenu = DB::select($sql);

        foreach ($idmenu as $idmn) {
            $id = $idmn->id_menu;
        }

        $noawal = substr($id, -3);
        $noakhir = 'MN' . str_pad($noawal + 1, 3, "0", STR_PAD_LEFT);
        return $noakhir;
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