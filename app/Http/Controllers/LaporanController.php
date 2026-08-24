<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    // Halaman daftar semua laporan yang sudah selesai / terverifikasi
    public function index()
    {
        $user = Auth::user();
        $query = Laporan::with(['kunjungan.customer', 'kunjungan.engineer.user', 'buktiPenyelesaian'])->latest();

        // Jika login sebagai Engineer, filter hanya laporannya sendiri
        if ($user->id_role == 3) {
            $query->whereHas('kunjungan.engineer', function ($q) use ($user) {
                $q->where('id_pengguna', $user->id_pengguna);
            });
        }

        $laporanList = $query->paginate(10);

        return view('laporan.index', compact('laporanList'));
    }

    // Generate dan Download / Stream PDF Service Completion Receipt
    public function downloadPdf($id_kunjungan)
    {
        $kunjungan = Kunjungan::with([
            'customer',
            'engineer.user',
            'tools',
            'aktivitas',
            'dokumentasi',
            'laporan.buktiPenyelesaian'
        ])->findOrFail($id_kunjungan);

        // Ambil aktivitas terakhir untuk waktu check-in/check-out dan catatan
        $aktivitas = $kunjungan->aktivitas->last();
        $bukti = $kunjungan->laporan->buktiPenyelesaian ?? null;

        $pdf = Pdf::loadView('laporan.pdf_template', compact('kunjungan', 'aktivitas', 'bukti'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Service_Completion_Receipt_' . $kunjungan->nomor . '.pdf');
    }
}