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
    Schema::create('customer_sites', function (Blueprint $table) {
        $table->id('id_site');
        $table->unsignedBigInteger('id_customer'); // Relasi ke PT Induk
        $table->string('nama_cabang', 100); // Misal: "Cabang Depok"
        $table->text('alamat_lengkap');
        $table->string('latitude', 50)->nullable();
        $table->string('longitude', 50)->nullable();
        $table->timestamps();

        // Foreign key mengarah ke tabel customers
        $table->foreign('id_customer')->references('id_customer')->on('customers')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sites');
    }
};
