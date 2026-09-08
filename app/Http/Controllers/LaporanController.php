<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\LaporanCustomerMail;
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

    // =====================================================================
    // FUNGSI BARU UNTUK INTERNAL APPROVAL
    // =====================================================================
    public function updateApproval(Request $request, $id_laporan)
    {
        // 1. LOGIC SAKTI: Tendang Engineer kalau nyoba akses fitur ini
        if (Auth::user()->id_role == 3) {
            return back()->with('error', 'Akses ditolak! Engineer tidak berhak melakukan persetujuan laporan.');
        }

        // 2. Validasi inputan dari form UI
        $request->validate([
            'status_approval' => 'required|in:Disetujui,Revisi,Ditolak',
            'catatan_approval' => 'nullable|string'
        ]);

        // 3. Cari laporan berdasarkan ID, lalu update datanya
        $laporan = Laporan::findOrFail($id_laporan);
        
        $laporan->status_approval = $request->status_approval;
        $laporan->catatan_approval = $request->catatan_approval;
        
        // Catat siapa atasan (id_pengguna) yang nge-klik tombol approve/revisi
        $laporan->disetujui_oleh = Auth::user()->id_pengguna;
        
        $laporan->save();

        // Kembalikan ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Status persetujuan laporan berhasil diperbarui!');
    }

    // =====================================================================
    // FUNGSI BARU: Kirim Email PDF ke Customer
    // =====================================================================
    public function sendEmail($id_kunjungan)
    {
        $kunjungan = Kunjungan::with([
            'customer', 'engineer.user', 'tools', 'aktivitas', 'dokumentasi', 'laporan.buktiPenyelesaian'
        ])->findOrFail($id_kunjungan);

        // Pastikan laporan sudah di-ACC sebelum dikirim
        if ($kunjungan->laporan->status_approval != 'Disetujui') {
            return back()->with('error', 'Gagal! Laporan harus disetujui (ACC) oleh atasan sebelum dikirim ke klien.');
        }

        // Opsional: Cek apakah customer punya email di database (asumsi kolomnya 'email')
        $emailTujuan = $kunjungan->customer->email ?? 'dummyclient@mailinator.com'; // Ganti fallback-nya kalau kolom email nggak ada

        // Ambil data untuk PDF
        $aktivitas = $kunjungan->aktivitas->last();
        $bukti = $kunjungan->laporan->buktiPenyelesaian ?? null;

        // Render PDF ke dalam memory (tanpa di-download ke browser)
        $pdf = Pdf::loadView('laporan.pdf_template', compact('kunjungan', 'aktivitas', 'bukti'))
                  ->setPaper('a4', 'portrait');
        
        $pdfData = $pdf->output();

        // Eksekusi pengiriman email
        Mail::to($emailTujuan)->send(new LaporanCustomerMail($kunjungan, $pdfData));

        return back()->with('success', 'Berhasil! Email Berita Acara beserta PDF terkirim ke: ' . $emailTujuan);
    }
}