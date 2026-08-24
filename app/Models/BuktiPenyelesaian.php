<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiPenyelesaian extends Model
{
    protected $table = 'bukti_penyelesaian';
    protected $primaryKey = 'id_bukti';
    protected $guarded = [];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan', 'id_laporan');
    }
}