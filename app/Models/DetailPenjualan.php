<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Menu; // Add this import
use App\Models\StokLog; // Add this import if you're using StokLog

class DetailPenjualan extends Model
{
    use HasFactory;
    
    protected $table = 'detail_penjualan';
    
    // Add fillable fields to prevent mass assignment issues
    protected $fillable = [
        'penjualan_id',
        'menu_id',
        'harga_satuan',
        'subtotal',
        'jumlah', // Changed from qty to match your form
        'penjualan.tgl'
    ];

    protected static function booted()
    {
        static::created(function ($detail) {
            // Decrement menu stock
            $menu = Menu::find($detail->menu_id);
            if ($menu) {
                $menu->decrement('stok', $detail->jumlah); // Changed from qty to jumlah
                
                // Create stock log
                StokLog::create([
                    'menu_id' => $detail->menu_id,
                    'jumlah_berkurang' => $detail->jumlah, // Changed from qty to jumlah
                    'keterangan' => 'Penjualan ID: ' . $detail->penjualan_id
                ]);
            }
        });
    }

    // Relationship to Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    // Relationship to Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id_menu'); // Adjusted if your primary key is different
    }
}