<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            // Status persetujuan (udah ketambahan 'Revisi')
            $table->enum('status_approval', ['Menunggu Persetujuan', 'Disetujui', 'Revisi', 'Ditolak'])
                  ->default('Menunggu Persetujuan')
                  ->after('status_laporan');
            
            // Siapa atasan yang nge-ACC
            $table->unsignedBigInteger('disetujui_oleh')->nullable()->after('status_approval');
            
            // Catatan kalau laporannya direvisi/ditolak
            $table->text('catatan_approval')->nullable()->after('disetujui_oleh');

            // Bikin relasi foreign key ke tabel pengguna
            $table->foreign('disetujui_oleh')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            // Hapus relasi dan kolom kalau di-rollback
            $table->dropForeign(['disetujui_oleh']);
            $table->dropColumn(['status_approval', 'disetujui_oleh', 'catatan_approval']);
        });
    }
};