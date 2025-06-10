<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurnal extends Model
{
    use HasFactory;

    protected $table = 'jurnal'; // Nama tabel eksplisit

    protected $fillable = [
        'tgl',
        'deskripsi',
        'no_referensi'
    ];

    protected $casts = [
        'tgl' => 'date',
    ];

    // relasi ke jurnal detail
    public function jurnaldetail(): HasMany
    {
        return $this->hasMany(JurnalDetail::class);
    }

    // Optional: cek apakah seimbang
    public function validateBalance()
    {
        $totalDebit = $this->jurnaldetail()->sum('debit');
        $totalCredit = $this->jurnaldetail()->sum('credit');
        
        return $totalDebit === $totalCredit;
    }
}