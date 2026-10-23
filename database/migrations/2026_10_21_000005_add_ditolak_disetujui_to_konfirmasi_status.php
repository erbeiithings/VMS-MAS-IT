<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * QA #5: status baru untuk penolakan engineer yang sudah di-ACC pimpinan
     * (engineer baru boleh diganti setelah status ini).
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE kunjungan_konfirmasi MODIFY COLUMN status ENUM('menunggu', 'diterima', 'ditolak', 'ditolak_disetujui') NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("UPDATE kunjungan_konfirmasi SET status = 'ditolak' WHERE status = 'ditolak_disetujui'");
        DB::statement("ALTER TABLE kunjungan_konfirmasi MODIFY COLUMN status ENUM('menunggu', 'diterima', 'ditolak') NOT NULL DEFAULT 'menunggu'");
    }
};
