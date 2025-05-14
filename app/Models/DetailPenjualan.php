<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;
    protected $table = 'detail_penjualan';
    protected static function booted()
    {
    static::created(function ($detail) {
        $menu = Menu::find($detail->menu_id);
        $menu->stok -= $detail->qty;
        $menu->save();

        StokLog::create([
            'menu_id' => $detail->menu_id,
            'jumlah_berkurang' => $detail->qty,
            'keterangan' => 'Penjualan ID: ' . $detail->penjualan_id
        ]);
    });
    }

    //relasi ke tabel penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    //realsi ke tabel menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
