<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class coa extends Model
{
    use HasFactory;
    protected $table = 'coa'; // Nama tabel eksplisit

    protected $guarded = [];

    public function JournalDetail()
    {
        return $this->hasMany(JurnalDetail::class);
    }
}
