<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Revisi alur: TTD customer boleh dilewati saat check-out (dengan alasan),
     * kunjungan ditandai untuk follow-up pimpinan agar tidak nyangkut.
     */
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->boolean('ttd_dilewati')->default(false)->after('catatan_approval');
            $table->text('alasan_tanpa_ttd')->nullable()->after('ttd_dilewati');
        });
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->text('catatan_force_selesai')->nullable()->after('alasan_reschedule');
        });
    }

    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropColumn(['ttd_dilewati', 'alasan_tanpa_ttd']);
        });
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropColumn('catatan_force_selesai');
        });
    }
};
