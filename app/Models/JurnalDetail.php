<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalDetail extends Model
{
    use HasFactory;

    protected $table = 'jurnal_detail'; // Nama tabel eksplisit

    protected $fillable = [
        'jurnal_id',
        'coa_id',
        'keterangan',
        'ref',
        'debit',
        'credit'
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    // relasi ke tabel jurnal
    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class);
    }

    // relasi ke tabel coa
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class);
    }
}