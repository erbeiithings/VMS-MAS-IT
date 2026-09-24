<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('kunjungan', function (Blueprint $table) {
        $table->unsignedBigInteger('id_site')->nullable()->after('id_customer');
        $table->foreign('id_site')->references('id_site')->on('customer_sites')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            //
        });
    }
};
