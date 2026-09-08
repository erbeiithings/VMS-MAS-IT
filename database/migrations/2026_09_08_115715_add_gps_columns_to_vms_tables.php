<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Nambahin target lokasi (Geofencing) di tabel customers
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        // 2. Nambahin rekam jejak (Geotagging) di tabel kunjungan
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->decimal('check_in_latitude', 10, 8)->nullable()->after('status');
            $table->decimal('check_in_longitude', 11, 8)->nullable()->after('check_in_latitude');
            $table->decimal('check_out_latitude', 10, 8)->nullable()->after('check_in_longitude');
            $table->decimal('check_out_longitude', 11, 8)->nullable()->after('check_out_latitude');
        });
    }

    public function down(): void
    {
        // Fitur rollback kalau terjadi kesalahan
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_latitude', 
                'check_in_longitude', 
                'check_out_latitude', 
                'check_out_longitude'
            ]);
        });
    }
};