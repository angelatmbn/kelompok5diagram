<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $penjualan_id
 * @property int $menu_id
 * @property int $jumlah
 * @property int $harga_satuan
 * @property int $subtotal
 * @property string $tgl
 */


class DetailPenjualan extends Model
{
    use HasFactory;
    protected $table = 'detail_penjualan';

    protected $fillable = [
        'penjualan_id',
        'menu_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'tgl'
    ];
    protected static function booted()
    {
        static::created(function ($detail) {
            $menu = Menu::find($detail->menu_id);
            $menu->stok -= $detail->jumlah;
            $menu->save();

            StokLog::create([
                'menu_id' => $detail->menu_id,
                'jumlah_berkurang' => $detail->jumlah,
                'tanggal_kejadian' => now(),
                'keterangan' => 'Penjualan ID: ' . $detail->penjualan_id
            ]);
        });
    }

    //relasi ke tabel penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    //realsi ke tabel menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
