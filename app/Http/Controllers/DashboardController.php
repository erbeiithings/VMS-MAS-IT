<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Engineer;
use App\Models\Kunjungan;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function kepalaDashboard()
    {
        $totalKunjungan = Kunjungan::count();
        $totalCustomer = Customer::count();
        $totalEngineer = Engineer::count();
        $totalTool = Tool::count();

        // Kunjungan per Status
        $kunjunganTerjadwal = Kunjungan::where('status', 'Terjadwal')->count();
        $kunjunganDikerjakan = Kunjungan::where('status', 'Dikerjakan')->count();
        $kunjunganSelesai = Kunjungan::where('status', 'Selesai')->count();

        // Kunjungan Terbaru
        $recentKunjungan = Kunjungan::with(['customer', 'engineer.user'])
            ->latest()
            ->take(5)
            ->get();

        // Analisis Jenis Pekerjaan Terbanyak
        $pekerjaanTerbanyak = Kunjungan::select('pekerjaan', DB::raw('count(*) as total'))
            ->groupBy('pekerjaan')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('dashboard.kepala', compact(
            'totalKunjungan', 'totalCustomer', 'totalEngineer', 'totalTool',
            'kunjunganTerjadwal', 'kunjunganDikerjakan', 'kunjunganSelesai',
            'recentKunjungan', 'pekerjaanTerbanyak'
        ));
    }

    public function pimpinanDashboard()
    {
        $totalKunjungan = Kunjungan::count();
        $toolTersedia = Tool::where('status_ketersediaan', 'Tersedia')->count();
        $engineerTersedia = Engineer::where('status_ketersediaan', 'Tersedia')->count();
        $kunjunganHariIni = Kunjungan::whereDate('tanggal', now()->toDateString())->count();

        $kunjunganList = Kunjungan::with(['customer', 'engineer.user', 'tools'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.pimpinan', compact(
            'totalKunjungan', 'toolTersedia', 'engineerTersedia', 'kunjunganHariIni', 'kunjunganList'
        ));
    }

    public function engineerDashboard()
    {
        $user = Auth::user();
        $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();

        $kunjunganAktif = null;
        $riwayatKunjungan = collect();
        $alertHariIni = collect(); // Variabel penampung tugas hari H

        if ($engineer) {
            $engineerId = $engineer->id_engineer;

            // 1. Kunjungan Aktif (Sebagai Lead ATAU Support)
            $kunjunganAktif = Kunjungan::with(['customer', 'tools'])
                ->whereIn('status', ['Terjadwal', 'Dikerjakan'])
                ->where(function($q) use ($engineerId) {
                    $q->where('id_engineer', $engineerId)
                      ->orWhereHas('supportEngineers', function($sq) use ($engineerId) {
                          $sq->where('engineers.id_engineer', $engineerId);
                      });
                })
                ->latest()
                ->first();

            // 2. Riwayat Kunjungan Selesai (Sebagai Lead ATAU Support)
            $riwayatKunjungan = Kunjungan::with('customer')
                ->where('status', 'Selesai')
                ->where(function($q) use ($engineerId) {
                    $q->where('id_engineer', $engineerId)
                      ->orWhereHas('supportEngineers', function($sq) use ($engineerId) {
                          $sq->where('engineers.id_engineer', $engineerId);
                      });
                })
                ->latest()
                ->take(5)
                ->get();

            // 3. LOGIC D-DAY ALERT: Cari jadwal yang tanggalnya HARI INI
            $alertHariIni = Kunjungan::with('customer')
                ->whereDate('tanggal', now()->toDateString())
                ->where('status', 'Terjadwal')
                ->where(function($q) use ($engineerId) {
                    $q->where('id_engineer', $engineerId)
                      ->orWhereHas('supportEngineers', function($sq) use ($engineerId) {
                          $sq->where('engineers.id_engineer', $engineerId);
                      });
                })
                ->get();
        }

        return view('dashboard.engineer', compact('engineer', 'kunjunganAktif', 'riwayatKunjungan', 'alertHariIni'));
    }
}