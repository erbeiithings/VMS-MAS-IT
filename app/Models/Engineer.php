<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Engineer extends Model
{
    protected $table = 'engineers';
    protected $primaryKey = 'id_engineer';
    protected $guarded = [];

    // Relasi ke User / Pengguna
    public function user()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi ke Kunjungan
    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'id_engineer', 'id_engineer');
    }
}