<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Engineer;
use App\Models\Tool;
use App\Models\Kunjungan;
use App\Models\AktivitasPekerjaan;
use App\Models\Dokumentasi;
use App\Models\Laporan;
use App\Models\BuktiPenyelesaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KunjunganController extends Controller
{
    // 1. Tampilkan List Kunjungan
    public function index()
    {
        $user = Auth::user();
        $query = Kunjungan::with(['customer', 'engineer.user', 'tools'])->latest();

        // Jika engineer, filter hanya kunjungan miliknya
        if ($user->id_role == 3) {
            $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
            if ($engineer) {
                $query->where('id_engineer', $engineer->id_engineer);
            }
        }

        $kunjunganList = $query->paginate(10);
        $customers = Customer::all();
        $engineers = Engineer::with('user')->where('status_ketersediaan', 'Tersedia')->get();
        $tools = Tool::where('status_ketersediaan', 'Tersedia')->get();

        return view('kunjungan.index', compact('kunjunganList', 'customers', 'engineers', 'tools'));
    }

    // 2. Buat Kunjungan Baru (Pimpinan)
    public function store(Request $request)
    {
        $request->validate([
            'id_customer' => 'required|exists:customers,id_customer',
            'id_engineer' => 'nullable|exists:engineers,id_engineer',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'lokasi' => 'required|string',
            'pekerjaan' => 'required|string|max:150',
            'tools' => 'nullable|array',
            'tools.*' => 'exists:tools,id_tool',
        ]);

        DB::transaction(function () use ($request) {
            // Generate nomor kunjungan otomatis
            $nomorKunjungan = 'VMS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $kunjungan = Kunjungan::create([
                'nomor' => $nomorKunjungan,
                'id_customer' => $request->id_customer,
                'id_engineer' => $request->id_engineer,
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'lokasi' => $request->lokasi,
                'pekerjaan' => $request->pekerjaan,
                'status' => 'Terjadwal',
            ]);

            // Hubungkan tools ke kunjungan
            if ($request->filled('tools')) {
                foreach ($request->tools as $toolId) {
                    $kunjungan->tools()->attach($toolId, ['jumlah' => 1]);
                }
            }
        });

        return redirect()->back()->with('success', 'Jadwal Kunjungan berhasil dibuat!');
    }

    // 3. Detail Kunjungan
    public function show($id)
    {
        $kunjungan = Kunjungan::with([
            'customer', 
            'engineer.user', 
            'tools', 
            'aktivitas', 
            'dokumentasi', 
            'laporan.buktiPenyelesaian'
        ])->findOrFail($id);

        return view('kunjungan.show', compact('kunjungan'));
    }

    // 4. Engineer Check-in (Mencatat GPS & Waktu Mulai)
    public function checkIn(Request $request, $id)
    {
        $kunjungan = Kunjungan::findOrFail($id);

        $request->validate([
            'lokasi_gps' => 'required|string',
        ]);

        $kunjungan->update(['status' => 'Dikerjakan']);

        AktivitasPekerjaan::create([
            'id_kunjungan' => $kunjungan->id_kunjungan,
            'waktu_mulai' => now(),
            'lokasi' => $request->lokasi_gps,
            'deskripsi' => 'Engineer tiba di lokasi dan memulai pengerjaan.',
        ]);

        return redirect()->back()->with('success', 'Check-in berhasil tercatat dengan koordinat GPS!');
    }

    // 5. Engineer Upload Dokumentasi Foto Lapangan
    public function uploadDokumentasi(Request $request, $id)
    {
        $request->validate([
            'kategori_foto' => 'required|in:Sebelum,Proses,Sesudah,Lainnya',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'keterangan' => 'nullable|string',
        ]);

        $file = $request->file('foto');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/dokumentasi'), $filename);

        Dokumentasi::create([
            'id_kunjungan' => $id,
            'kategori_foto' => $request->kategori_foto,
            'file_foto' => 'uploads/dokumentasi/' . $filename,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Foto dokumentasi berhasil diunggah!');
    }

    // 6. Engineer Check-out & Selesaikan Tugas
    public function checkOut(Request $request, $id)
    {
        $kunjungan = Kunjungan::findOrFail($id);

        $request->validate([
            'catatan' => 'required|string',
        ]);

        $aktivitas = AktivitasPekerjaan::where('id_kunjungan', $id)->latest()->first();
        if ($aktivitas) {
            $aktivitas->update([
                'waktu_selesai' => now(),
                'catatan' => $request->catatan,
            ]);
        }

        // Generate Record Laporan Otomatis
        Laporan::firstOrCreate(
            ['id_kunjungan' => $id],
            [
                'tanggal_dibuat' => now(),
                'status_laporan' => 'Terbuat Otomatis'
            ]
        );

        return redirect()->back()->with('success', 'Pekerjaan selesai! Menunggu verifikasi tanda tangan customer.');
    }

    // 7. Customer Digital Signature & Selesai
    public function verifySignature(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required|string', // Base64 Canvas data
        ]);

        $kunjungan = Kunjungan::findOrFail($id);
        $laporan = Laporan::where('id_kunjungan', $id)->firstOrFail();

        BuktiPenyelesaian::updateOrCreate(
            ['id_laporan' => $laporan->id_laporan],
            [
                'tanda_tangan_customer' => $request->signature,
                'tanggal_tanda_tangan' => now(),
                'status' => 'Ditandatangani',
            ]
        );

        $kunjungan->update(['status' => 'Selesai']);

        return redirect()->route('kunjungan.show', $id)->with('success', 'Kunjungan kerja selesai secara resmi dan dokumen telah ditandatangani!');
    }
}