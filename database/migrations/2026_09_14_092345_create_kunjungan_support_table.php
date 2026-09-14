<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan_support', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kunjungan');
            $table->unsignedBigInteger('id_engineer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan_support');
    }
};