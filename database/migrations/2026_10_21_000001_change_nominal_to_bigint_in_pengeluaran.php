<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * QA #17: nominal pengeluaran terlalu besar error karena kolom integer
     * (maks ~Rp 2,147 miliar). Ubah ke bigInteger.
     */
    public function up(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->bigInteger('nominal')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->integer('nominal')->change();
        });
    }
};
