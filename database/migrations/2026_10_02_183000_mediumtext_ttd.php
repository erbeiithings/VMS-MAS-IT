<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PNG tanda tangan dari HP (devicePixelRatio tinggi) bisa melebihi
     * 65KB batas kolom TEXT -> naikkan ke MEDIUMTEXT agar tidak error
     * "Data too long" saat simpan.
     */
    public function up(): void
    {
        Schema::table('bukti_penyelesaian', function (Blueprint $table) {
            $table->mediumText('tanda_tangan_customer')->nullable()->change();
            $table->mediumText('tanda_tangan_engineer')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bukti_penyelesaian', function (Blueprint $table) {
            $table->text('tanda_tangan_customer')->nullable()->change();
            $table->text('tanda_tangan_engineer')->nullable()->change();
        });
    }
};
