@extends('layouts.app')

@section('title', 'Detail Kunjungan - ' . $kunjungan->nomor)
@section('header_title', 'Lembar Pelaksanaan Kunjungan')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Summary Card -->
    <div class="p-5 md:p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-[#0a1533] to-[#040817] border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="font-mono text-xs md:text-sm font-bold text-blue-400">{{ $kunjungan->nomor }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $kunjungan->status == 'Selesai' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($kunjungan->status == 'Dikerjakan' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20') }}">
                    {{ $kunjungan->status }}
                </span>
            </div>
            <h2 class="text-lg md:text-xl font-bold text-white mt-1">{{ $kunjungan->pekerjaan }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ $kunjungan->customer->nama_perusahaan ?? '-' }} • {{ $kunjungan->lokasi }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($kunjungan->status == 'Selesai' || $kunjungan->laporan)
                <a href="{{ route('laporan.pdf', $kunjungan->id_kunjungan) }}" target="_blank" 
                   class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold flex items-center gap-2 shadow-lg shadow-rose-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Cetak PDF Laporan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Stepper Status Kunjungan -->
    <div class="p-4 md:p-6 rounded-2xl bg-white/[0.03] border border-slate-800/80">
        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Status Alur Kunjungan</h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
            <div class="p-2.5 rounded-xl bg-blue-600/10 border border-blue-500/30 text-blue-400 font-medium">
                1. Terjadwal
            </div>
            <div class="p-2.5 rounded-xl {{ in_array($kunjungan->status, ['Dikerjakan', 'Selesai']) ? 'bg-blue-600/10 border border-blue-500/30 text-blue-400 font-medium' : 'bg-slate-900/50 text-slate-600 border border-slate-800' }}">
                2. Check-in (GPS)
            </div>
            <div class="p-2.5 rounded-xl {{ in_array($kunjungan->status, ['Dikerjakan', 'Selesai']) ? 'bg-blue-600/10 border border-blue-500/30 text-blue-400 font-medium' : 'bg-slate-900/50 text-slate-600 border border-slate-800' }}">
                3. On-Site & Foto
            </div>
            <div class="p-2.5 rounded-xl {{ $kunjungan->status == 'Selesai' ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-medium' : 'bg-slate-900/50 text-slate-600 border border-slate-800' }}">
                4. TTD Customer
            </div>
        </div>
    </div>

    <!-- SECTION 1: Check-in GPS (Hanya muncul jika status Terjadwal & user adalah Engineer) -->
    @if(Auth::user()->id_role == 3 && $kunjungan->status == 'Terjadwal')
        <div class="p-6 rounded-2xl bg-gradient-to-r from-blue-950/40 to-slate-900 border border-blue-500/30 text-center space-y-3">
            <h4 class="text-sm font-bold text-white">Anda Sudah Tiba di Lokasi Klien?</h4>
            <p class="text-xs text-slate-400 max-w-md mx-auto">Klik tombol di bawah ini untuk mencatat koordinat GPS dan memulai pengerjaan on-site.</p>
            
            <form id="formCheckIn" action="{{ route('kunjungan.checkin', $kunjungan->id_kunjungan) }}" method="POST">
                @csrf
                <input type="hidden" name="lokasi_gps" id="lokasi_gps">
                <button type="button" onclick="getGPSLocation()" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-600/40 transition">
                    📍 Ambil Lokasi GPS & Check-In Sekarang
                </button>
            </form>
        </div>
    @endif

    <!-- SECTION 2: Pelaksanaan & Upload Dokumentasi Foto Lapangan -->
    @if(Auth::user()->id_role == 3 && $kunjungan->status == 'Dikerjakan')
        <div class="p-5 md:p-6 rounded-2xl bg-white/[0.03] border border-slate-800/80 space-y-4">
            <h4 class="text-sm font-bold text-white flex items-center gap-2">
                📷 Unggah Dokumentasi Lapangan (On-Site)
            </h4>
            <form action="{{ route('kunjungan.dokumentasi', $kunjungan->id_kunjungan) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Kategori Foto</label>
                        <select name="kategori_foto" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white">
                            <option value="Sebelum">Foto Sebelum Pengerjaan</option>
                            <option value="Proses">Foto Saat Pengerjaan</option>
                            <option value="Sesudah">Foto Setelah Selesai</option>
                            <option value="Lainnya">Foto Lainnya / Kondisi Khusus</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Pilih File Foto (Bisa dari Kamera HP)</label>
                        <input type="file" name="foto" accept="image/*" capture="environment" required class="w-full text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:bg-blue-600 file:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Keterangan Foto</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Kondisi port switch sebelum pergantian modul" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium">Unggah Foto Dokumentasi</button>
            </form>
        </div>
    @endif

    <!-- SECTION 3: Form Pembuatan Laporan Siap Pakai (Engineer Input Deskripsi) -->
    @if(Auth::user()->id_role == 3 && $kunjungan->status == 'Dikerjakan')
        <div class="p-5 md:p-6 rounded-2xl bg-white/[0.03] border border-blue-500/40 space-y-4">
            <h4 class="text-sm font-bold text-white">📝 Input Catatan & Hasil Pekerjaan Kunjungan</h4>
            <p class="text-xs text-slate-400">Tuliskan ringkasan hasil pengerjaan di bawah ini. Sistem otomatis memasukkannya ke dalam template resmi laporan MAS-IT.</p>
            
            <form id="formCheckOut" action="{{ route('kunjungan.checkout', $kunjungan->id_kunjungan) }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Deskripsi / Hasil Pekerjaan Lapangan:</label>
                    <textarea name="catatan" rows="4" required placeholder="Contoh: Pemeliharaan berkala server dan perapihan cabling rack selesai 100%. Uji konektivitas normal tanpa packet loss." class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>
                <button type="button" onclick="showConfirmModal(this.form, 'Terbitkan Template Laporan', 'Apakah catatan hasil pekerjaan sudah lengkap dan siap untuk ditandatangani oleh customer?')" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition">
                    ✔ Buat Laporan & Buka Kolom TTD Customer
                </button>
            </form>
        </div>
    @endif

    <!-- SECTION 4: Kotak Tanda Tangan Digital Khusus Customer -->
    @if(($kunjungan->status == 'Dikerjakan' && $kunjungan->laporan) || ($kunjungan->laporan && !$kunjungan->laporan->buktiPenyelesaian))
        <div class="p-5 md:p-6 rounded-2xl bg-white/[0.04] border border-amber-500/40 space-y-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-400 animate-ping"></span>
                <h4 class="text-sm font-bold text-amber-300">✍ Verifikasi Tanda Tangan Customer</h4>
            </div>
            <p class="text-xs text-slate-300">Silakan sodorkan HP ke Customer / PIC <strong>({{ $kunjungan->customer->pic ?? 'PIC Perusahaan' }})</strong> untuk membubuhkan tanda tangan langsung pada kotak putih di bawah:</p>
            
            <form id="signatureForm" action="{{ route('kunjungan.signature', $kunjungan->id_kunjungan) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="signature" id="signatureInput">
                
                <!-- Kotak Canvas TTD -->
                <div class="border-2 border-slate-600 bg-white rounded-xl overflow-hidden shadow-inner touch-none">
                    <canvas id="signaturePad" class="w-full h-56 block cursor-crosshair"></canvas>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <button type="button" onclick="clearSignature()" class="w-full sm:w-auto px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs hover:bg-slate-700">
                        Hapus & Ulangi TTD
                    </button>
                    <button type="button" onclick="submitSignature()" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-blue-600/30">
                        Selesaikan & Kunci Laporan Resmi
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- SECTION 5: Bukti Dokumen Terverifikasi & Tanda Tangan Tersimpan -->
    @if($kunjungan->laporan && $kunjungan->laporan->buktiPenyelesaian)
        <div class="p-5 md:p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="text-sm font-bold text-emerald-400">Pekerjaan Selesai & Dokumen Terverifikasi Resmi</h4>
                <p class="text-xs text-slate-400 mt-1">Ditandatangani oleh PIC pada: {{ $kunjungan->laporan->buktiPenyelesaian->tanggal_tanda_tangan }}</p>
            </div>
            <div class="bg-white p-2 rounded-xl border border-slate-700">
                <img src="{{ $kunjungan->laporan->buktiPenyelesaian->tanda_tangan_customer }}" alt="Customer Signature" class="h-14 object-contain">
            </div>
        </div>
    @endif

    <!-- SECTION 6: Detail Informasi Tiket, Tools & Galeri Foto (Dilihat oleh Semua Role) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Kunjungan & Alat -->
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80 space-y-3 text-xs">
            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Detail Informasi Tiket</h4>
            <div class="space-y-1.5 text-slate-400">
                <p><strong class="text-white">Customer:</strong> {{ $kunjungan->customer->nama_perusahaan ?? '-' }}</p>
                <p><strong class="text-white">PIC:</strong> {{ $kunjungan->customer->pic ?? '-' }} ({{ $kunjungan->customer->telepon ?? '-' }})</p>
                <p><strong class="text-white">Engineer Bertugas:</strong> {{ $kunjungan->engineer->user->nama ?? 'Belum Ditugaskan' }}</p>
                <p><strong class="text-white">Alat Kerja Terbawa:</strong></p>
                <ul class="list-disc list-inside text-slate-300 pl-2">
                    @forelse($kunjungan->tools as $tool)
                        <li>{{ $tool->nama_alat }} ({{ $tool->kode }})</li>
                    @empty
                        <li class="italic text-slate-500">Tidak ada tools khusus</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Log Aktivitas GPS & Waktu -->
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80 space-y-3 text-xs">
            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Log Waktu & Lokasi GPS</h4>
            @php $act = $kunjungan->aktivitas->last(); @endphp
            <div class="space-y-1.5 text-slate-400">
                <p><strong class="text-white">Koordinat Check-in:</strong> <span class="font-mono text-blue-400">{{ $act->lokasi ?? 'Belum check-in' }}</span></p>
                <p><strong class="text-white">Waktu Check-in:</strong> {{ $act->waktu_mulai ?? '-' }}</p>
                <p><strong class="text-white">Waktu Check-out:</strong> {{ $act->waktu_selesai ?? '-' }}</p>
                <p><strong class="text-white">Catatan Engineer:</strong> {{ $act->catatan ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Galeri Foto Lapangan -->
    <div class="p-5 md:p-6 rounded-2xl bg-white/[0.03] border border-slate-800/80">
        <h4 class="text-sm font-semibold text-slate-200 mb-4">Galeri Dokumentasi On-Site</h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @forelse($kunjungan->dokumentasi as $doc)
                <div class="rounded-xl overflow-hidden bg-slate-900 border border-slate-800">
                    <img src="{{ asset($doc->file_foto) }}" alt="Dokumentasi" class="w-full h-32 object-cover">
                    <div class="p-2 text-[10px]">
                        <span class="px-2 py-0.5 rounded bg-blue-600/20 text-blue-400 font-semibold uppercase">{{ $doc->kategori_foto }}</span>
                        <p class="text-slate-400 mt-1 truncate">{{ $doc->keterangan ?? '-' }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-6 text-slate-500 text-xs italic">Belum ada foto dokumentasi diunggah.</div>
            @endforelse
        </div>
    </div>

</div>

<!-- Script Signature Pad & GPS -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    function getGPSLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const coords = `${position.coords.latitude}, ${position.coords.longitude}`;
                    document.getElementById('lokasi_gps').value = coords;
                    showConfirmModal(
                        document.getElementById('formCheckIn'),
                        'Konfirmasi Check-in',
                        `Koordinat GPS terdeteksi: ${coords}. Mulai pengerjaan sekarang?`
                    );
                },
                (error) => {
                    alert('Gagal mengambil lokasi GPS. Pastikan izin lokasi di browser HP Anda telah diizinkan.');
                },
                { enableHighAccuracy: true }
            );
        } else {
            alert('Perangkat Anda tidak mendukung geolokasi.');
        }
    }

    let signaturePad;
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('signaturePad');
        if (canvas) {
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }
            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();
        }
    });

    function clearSignature() {
        if (signaturePad) signaturePad.clear();
    }

    function submitSignature() {
        if (signaturePad && signaturePad.isEmpty()) {
            alert('Customer belum membubuhkan tanda tangan.');
            return;
        }
        document.getElementById('signatureInput').value = signaturePad.toDataURL();
        showConfirmModal(
            document.getElementById('signatureForm'),
            'Kunci Laporan',
            'Dengan menandatangani dokumen ini, pekerjaan dinyatakan selesai secara resmi. Lanjutkan?'
        );
    }
</script>
@endsection