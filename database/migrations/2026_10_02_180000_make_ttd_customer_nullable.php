<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TTD customer & engineer bersifat independen: laporan boleh dibuat
     * hanya dengan salah satu TTD, yang lain bisa ditambahkan/diubah
     * belakangan lewat revisi.
     */
    public function up(): void
    {
        Schema::table('bukti_penyelesaian', function (Blueprint $table) {
            $table->text('tanda_tangan_customer')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bukti_penyelesaian', function (Blueprint $table) {
            $table->text('tanda_tangan_customer')->nullable(false)->change();
        });
    }
};
