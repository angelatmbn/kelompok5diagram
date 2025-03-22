<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kategoriMenu extends Model
{
    use HasFactory;
    protected $table = 'kategori_menu';
    protected $primaryKey = 'id_kategori';
    public $incrementing = false; // Matikan auto-increment!
    protected $keyType = 'string';
    protected $guarded = [];
}
