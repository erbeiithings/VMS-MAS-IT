<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * QA #11: normalisasi data peminjaman kunjungan yang sudah terlanjur
     * tercatat per engineer (masing-masing jumlah 1, sehingga total terlihat
     * dobel). Untuk tiap grup (kunjungan, tool) yang masih Dipinjam, jumlah
     * hanya dipertahankan di catatan lead engineer kunjungan tersebut;
     * catatan engineer lain di-nol-kan. Stok tidak berubah.
     */
    public function up(): void
    {
        $grup = DB::table('peminjaman_tools')
            ->select('id_kunjungan', 'id_tool')
            ->whereNotNull('id_kunjungan')
            ->where('status', 'Dipinjam')
            ->groupBy('id_kunjungan', 'id_tool')
            ->get();

        foreach ($grup as $g) {
            $leadId = DB::table('kunjungan')
                ->where('id_kunjungan', $g->id_kunjungan)
                ->value('id_engineer');

            $rows = DB::table('peminjaman_tools')
                ->where('id_kunjungan', $g->id_kunjungan)
                ->where('id_tool', $g->id_tool)
                ->where('status', 'Dipinjam')
                ->orderBy('id_peminjaman')
                ->get();

            if ($rows->count() < 2) {
                continue;
            }

            // Jumlah fisik yang benar = jumlah di pivot kunjungan_tool
            // (catatan per engineer selama ini menduplikasi angka yang sama).
            $total = (int) (DB::table('kunjungan_tool')
                ->where('id_kunjungan', $g->id_kunjungan)
                ->where('id_tool', $g->id_tool)
                ->value('jumlah') ?? 0);
            if ($total <= 0) {
                $total = 1;
            }

            // Catatan yang mempertahankan jumlah: milik lead bila ada,
            // kalau tidak ada pakai catatan pertama.
            $keeper = $rows->firstWhere('id_engineer', $leadId) ?? $rows->first();

            foreach ($rows as $r) {
                DB::table('peminjaman_tools')
                    ->where('id_peminjaman', $r->id_peminjaman)
                    ->update(['jumlah' => $r->id_peminjaman == $keeper->id_peminjaman ? $total : 0]);
            }
        }
    }

    public function down(): void
    {
        // Tidak bisa dikembalikan: distribusi jumlah awal per engineer
        // tidak tercatat. No-op.
    }
};
