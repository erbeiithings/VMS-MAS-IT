<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * QA #21: pengeluaran dicatat per engineer (siapa yang mengeluarkan).
     * QA #22: status penggantian perusahaan (Belum Diganti / Sudah Diganti)
     * untuk ditampilkan di dashboard.
     */
    public function up(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->unsignedBigInteger('id_engineer')->nullable()->after('id_kunjungan');
            $table->string('status_ganti', 30)->default('Belum Diganti')->after('bukti_nota');
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropColumn(['id_engineer', 'status_ganti']);
        });
    }
};
