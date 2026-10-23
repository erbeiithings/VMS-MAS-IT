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

    // TAMBAHAN BARU: Relasi ke lokasi cabang/site
    public function site()
    {
        return $this->belongsTo(CustomerSite::class, 'id_site', 'id_site');
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

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_kunjungan', 'id_kunjungan');
    }

    public function supportEngineers()
    {
        return $this->belongsToMany(Engineer::class, 'kunjungan_support', 'id_kunjungan', 'id_engineer')
                    ->withTimestamps();
    }

    public function konfirmasi()
    {
        return $this->hasMany(KunjunganKonfirmasi::class, 'id_kunjungan', 'id_kunjungan');
    }

    /** QA ronde 2 #2: true bila minimal satu engineer sudah menerima (accept) tugas ini. */
    public function sudahAdaYangMenerima(): bool
    {
        return $this->konfirmasi()->where('status', 'diterima')->exists();
    }

    /**
     * Alamat kunjungan yang selalu sinkron dengan data site terkini.
     * Jika kunjungan terhubung ke site, pakai alamat site (live dari master).
     */
    public function getAlamatSinkronAttribute()
    {
        return $this->site->alamat_lengkap ?? $this->lokasi;
    }

    /**
     * QA #13: label status untuk tampilan. Nilai status internal tetap
     * 'Dikerjakan', yang tampil ke user 'Sedang Dikerjakan'.
     */
    public function statusLabel()
    {
        return $this->status === 'Dikerjakan' ? 'Sedang Dikerjakan' : $this->status;
    }

    /**
     * QA #9: peta id_engineer => kunjungan aktif terdekat (status Terjadwal /
     * Dikonfirmasi / Sedang Dikerjakan, sebagai lead atau support).
     * Dipakai untuk catatan "sedang ditugaskan" di master data & form assign.
     */
    public static function tugasAktifPerEngineer()
    {
        $aktif = self::with('supportEngineers')
            ->whereIn('status', ['Terjadwal', 'Dikonfirmasi', 'Dikerjakan'])
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        $map = [];
        foreach ($aktif as $k) {
            $ids = collect([$k->id_engineer])
                ->merge($k->supportEngineers->pluck('id_engineer'))
                ->filter()->unique();
            foreach ($ids as $eid) {
                if (!isset($map[$eid])) {
                    $map[$eid] = $k;
                }
            }
        }
        return $map;
    }

    /**
     * QA #10: cari kunjungan lain yang bentrok untuk seorang engineer pada
     * tanggal + jam yang sama (di luar kunjungan yang dikecualikan).
     */
    public static function cariBentrok($idEngineer, $tanggal, $waktu, $kecualiId = null)
    {
        $jam = substr((string) $waktu, 0, 5);
        return self::where('tanggal', $tanggal)
            ->whereIn('status', ['Terjadwal', 'Dikonfirmasi', 'Dikerjakan'])
            ->when($kecualiId, fn($q) => $q->where('id_kunjungan', '!=', $kecualiId))
            ->where(function ($q) use ($idEngineer) {
                $q->where('id_engineer', $idEngineer)
                  ->orWhereHas('supportEngineers', fn($sq) => $sq->where('engineers.id_engineer', $idEngineer));
            })
            ->get()
            ->first(fn($k) => substr((string) $k->waktu, 0, 5) === $jam);
    }
}