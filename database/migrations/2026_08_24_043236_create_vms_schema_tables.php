<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_role');
            $table->enum('nama_role', ['Kepala Pimpinan', 'Pimpinan', 'Engineer']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Pengguna (Users)
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('kontak', 20)->nullable();
            $table->unsignedBigInteger('id_role');
            $table->enum('status_akun', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamps();

            $table->foreign('id_role')->references('id_role')->on('roles')->onDelete('cascade');
            $table->foreign('dibuat_oleh')->references('id_pengguna')->on('pengguna')->onDelete('set null');
        });

        // 3. Tabel Customer
        Schema::create('customers', function (Blueprint $table) {
            $table->id('id_customer');
            $table->string('nama_perusahaan', 100);
            $table->string('alamat', 255);
            $table->string('pic', 100);
            $table->string('telepon', 20);
            $table->string('email', 100);
            $table->timestamps();
        });

        // 4. Tabel Engineer
        Schema::create('engineers', function (Blueprint $table) {
            $table->id('id_engineer');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('kontak', 20);
            $table->enum('status_ketersediaan', ['Tersedia', 'Tidak Tersedia'])->default('Tersedia');
            $table->timestamps();

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });

        // 5. Tabel Tools (Alat Kerja)
        Schema::create('tools', function (Blueprint $table) {
            $table->id('id_tool');
            $table->string('nama_alat', 100);
            $table->string('kode', 50)->unique();
            $table->string('kategori', 100);
            $table->text('spesifikasi')->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status_ketersediaan', ['Tersedia', 'Tidak Tersedia'])->default('Tersedia');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 6. Tabel Kunjungan
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id('id_kunjungan');
            $table->string('nomor', 50)->unique();
            $table->unsignedBigInteger('id_customer');
            $table->unsignedBigInteger('id_engineer')->nullable();
            $table->date('tanggal');
            $table->time('waktu');
            $table->text('lokasi');
            $table->string('pekerjaan', 150);
            $table->enum('status', ['Terjadwal', 'Dikerjakan', 'Selesai'])->default('Terjadwal');
            $table->timestamps();

            $table->foreign('id_customer')->references('id_customer')->on('customers')->onDelete('cascade');
            $table->foreign('id_engineer')->references('id_engineer')->on('engineers')->onDelete('set null');
        });

        // 7. Tabel Pivot Kunjungan_Tool (Composite Primary Key)
        Schema::create('kunjungan_tool', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kunjungan');
            $table->unsignedBigInteger('id_tool');
            $table->integer('jumlah')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->primary(['id_kunjungan', 'id_tool']);
            $table->foreign('id_kunjungan')->references('id_kunjungan')->on('kunjungan')->onDelete('cascade');
            $table->foreign('id_tool')->references('id_tool')->on('tools')->onDelete('cascade');
        });

        // 8. Tabel Aktivitas Pekerjaan
        Schema::create('aktivitas_pekerjaan', function (Blueprint $table) {
            $table->id('id_aktivitas');
            $table->unsignedBigInteger('id_kunjungan');
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('lokasi')->nullable(); // GPS koordinat
            $table->text('foto')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_kunjungan')->references('id_kunjungan')->on('kunjungan')->onDelete('cascade');
        });

        // 9. Tabel Dokumentasi
        Schema::create('dokumentasi', function (Blueprint $table) {
            $table->id('id_dokumentasi');
            $table->unsignedBigInteger('id_kunjungan');
            $table->enum('kategori_foto', ['Sebelum', 'Proses', 'Sesudah', 'Lainnya']);
            $table->text('file_foto');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_kunjungan')->references('id_kunjungan')->on('kunjungan')->onDelete('cascade');
        });

        // 10. Tabel Laporan
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->unsignedBigInteger('id_kunjungan')->unique();
            $table->dateTime('tanggal_dibuat');
            $table->text('file_pdf')->nullable();
            $table->enum('status_laporan', ['Terbuat Otomatis'])->default('Terbuat Otomatis');
            $table->timestamps();

            $table->foreign('id_kunjungan')->references('id_kunjungan')->on('kunjungan')->onDelete('cascade');
        });

        // 11. Tabel Bukti Penyelesaian
        Schema::create('bukti_penyelesaian', function (Blueprint $table) {
            $table->id('id_bukti');
            $table->unsignedBigInteger('id_laporan')->unique();
            $table->text('tanda_tangan_customer'); // Base64 data signature
            $table->dateTime('tanggal_tanda_tangan');
            $table->enum('status', ['Ditandatangani'])->default('Ditandatangani');
            $table->timestamps();

            $table->foreign('id_laporan')->references('id_laporan')->on('laporan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_penyelesaian');
        Schema::dropIfExists('laporan');
        Schema::dropIfExists('dokumentasi');
        Schema::dropIfExists('aktivitas_pekerjaan');
        Schema::dropIfExists('kunjungan_tool');
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('tools');
        Schema::dropIfExists('engineers');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('pengguna');
        Schema::dropIfExists('roles');
    }
};