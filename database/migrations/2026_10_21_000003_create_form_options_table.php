<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * QA #15: daftar jenis biaya & kategori foto yang bisa dikelola admin
     * (ditambah/dihapus dari halaman Master Data > Opsi Formulir).
     */
    public function up(): void
    {
        Schema::create('form_options', function (Blueprint $table) {
            $table->id();
            $table->string('grup', 50); // jenis_biaya | kategori_foto
            $table->string('nama', 100);
            $table->timestamps();
            $table->unique(['grup', 'nama']);
        });

        $defaults = [
            'jenis_biaya' => ['Bensin', 'Tol', 'Parkir', 'Makan', 'Pembelian Alat'],
            'kategori_foto' => ['Sebelum', 'Proses', 'Sesudah'],
        ];
        foreach ($defaults as $grup => $list) {
            foreach ($list as $nama) {
                DB::table('form_options')->insert([
                    'grup' => $grup,
                    'nama' => $nama,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('form_options');
    }
};
