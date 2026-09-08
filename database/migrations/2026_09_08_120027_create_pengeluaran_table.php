<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            // Primary key kita samain formatnya pakai id_pengeluaran
            $table->id('id_pengeluaran'); 
            
            // Relasi ke tabel kunjungan
            $table->unsignedBigInteger('id_kunjungan');
            
            // Rincian biaya
            $table->string('jenis_biaya', 100); // Contoh: Bensin, Tol, Parkir, Makan
            $table->integer('nominal'); // Pakai integer karena Rupiah biasanya nggak pake koma
            $table->text('keterangan')->nullable();
            
            // Bukti foto nota/struk
            $table->text('bukti_nota')->nullable(); 
            
            $table->timestamps();

            // Bikin relasi foreign key ke tabel kunjungan (kalau kunjungan dihapus, pengeluaran ikut kehapus)
            $table->foreign('id_kunjungan')
                  ->references('id_kunjungan')->on('kunjungan')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};