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
        $id = DB::table('menu')->max('id_menu') ?? 'MN000';
        $noUrut = (int) substr($id, -3) + 1;
        return 'MN' . str_pad($noUrut, 3, '0', STR_PAD_LEFT);
    }

    public function setHargaAttribute($value)
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
