<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->text('draft_hasil_pekerjaan')->nullable()->after('draft_ttd_engineer');
            $table->text('draft_catatan_tambahan')->nullable()->after('draft_hasil_pekerjaan');
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropColumn(['draft_hasil_pekerjaan', 'draft_catatan_tambahan']);
        });
    }
};
