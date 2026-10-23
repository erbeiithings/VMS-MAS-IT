<?php

namespace App\Http\Controllers;

use App\Models\Engineer;
use App\Models\Kunjungan;
use App\Models\User;

class MasterPenggunaController extends Controller
{
    /**
     * QA #1: satu halaman master untuk semua pengguna, disortir berdasarkan
     * role (Kepala Pimpinan, Pimpinan, lalu Engineer). Pengelolaan detail
     * tetap di halaman Data Pimpinan / Data Engineer masing-masing.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // QA ronde 2 #1: filter tampilan berdasarkan grup role
        // (semua | pimpinan | engineer) — satu menu manajemen akun terpusat.
        $filter = in_array($request->query('role'), ['pimpinan', 'engineer'], true)
            ? $request->query('role')
            : 'semua';

        $pimpinan = $filter === 'engineer'
            ? collect()
            : User::whereIn('id_role', [1, 2])->orderBy('id_role')->orderBy('nama')->get();
        $engineers = $filter === 'pimpinan'
            ? collect()
            : Engineer::with('user')->get()->sortBy(fn($e) => $e->user->nama ?? '')->values();
        $tugasAktif = Kunjungan::tugasAktifPerEngineer();

        return view('master.pengguna.index', compact('pimpinan', 'engineers', 'tugasAktif', 'filter'));
    }
}
