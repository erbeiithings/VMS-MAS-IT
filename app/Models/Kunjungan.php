<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';
    protected $primaryKey = 'id_kunjungan';
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
    }

    public function engineer()
    {
        return $this->belongsTo(Engineer::class, 'id_engineer', 'id_engineer');
    }

    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'kunjungan_tool', 'id_kunjungan', 'id_tool')
                    ->withPivot('jumlah', 'keterangan')
                    ->withTimestamps();
    }

    public function aktivitas()
    {
        return $this->hasMany(AktivitasPekerjaan::class, 'id_kunjungan', 'id_kunjungan');
    }

    public function dokumentasi()
    {
        return $this->hasMany(Dokumentasi::class, 'id_kunjungan', 'id_kunjungan');
    }

    public function laporan()
    {
        return $this->hasOne(Laporan::class, 'id_kunjungan', 'id_kunjungan');
    }
}