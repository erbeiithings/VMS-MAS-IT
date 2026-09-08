<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';

    // Kolom apa aja yang boleh diisi lewat form
    protected $fillable = [
        'id_kunjungan',
        'jenis_biaya',
        'nominal',
        'keterangan',
        'bukti_nota'
    ];

    // Relasi balik ke tabel kunjungan
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'id_kunjungan', 'id_kunjungan');
    }
}