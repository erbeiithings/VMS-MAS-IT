<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah pilihan status ENUM di database pakai Raw SQL biar aman
        DB::statement("ALTER TABLE kunjungan MODIFY COLUMN status ENUM('Request', 'Terjadwal', 'Reschedule', 'Dikerjakan', 'Selesai') NOT NULL DEFAULT 'Terjadwal'");

        // 2. Tambahin kolom buat nyimpen alasan penolakan dari Engineer
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->text('alasan_reschedule')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        // Fitur rollback
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropColumn('alasan_reschedule');
        });
        
        DB::statement("ALTER TABLE kunjungan MODIFY COLUMN status ENUM('Terjadwal', 'Dikerjakan', 'Selesai') NOT NULL DEFAULT 'Terjadwal'");
    }
};