<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah kolomnya sudah ada di database, kalau belum baru ditambahkan
        if (!Schema::hasColumn('bukti_penyelesaian', 'tanda_tangan_engineer')) {
            Schema::table('bukti_penyelesaian', function (Blueprint $table) {
                $table->text('tanda_tangan_engineer')->nullable()->after('tanda_tangan_customer');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bukti_penyelesaian', 'tanda_tangan_engineer')) {
            Schema::table('bukti_penyelesaian', function (Blueprint $table) {
                $table->dropColumn('tanda_tangan_engineer');
            });
        }
    }
};