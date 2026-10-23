@extends('layouts.app')

@section('title', 'Detail Kunjungan - ' . $kunjungan->nomor)
@section('header_title', 'Lembar Pelaksanaan Kunjungan')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2 font-medium shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2 font-medium shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header Summary Card -->
    <div class="p-5 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-start gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </span>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-xs md:text-sm font-bold text-[#003399] bg-blue-50 border border-blue-100 px-2 py-1 rounded-lg">{{ $kunjungan->nomor }}</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $kunjungan->statusLabel() }}
                    </span>
                </div>
                <h2 class="text-lg md:text-xl font-bold text-slate-900 mt-2">{{ $kunjungan->pekerjaan }}</h2>
                <p class="text-xs text-slate-500 mt-1 font-medium">{{ $kunjungan->customer->nama_perusahaan ?? '-' }} • {{ $kunjungan->alamat_sinkron }}</p>
                @if($kunjungan->patokan)
                    <p class="text-xs text-amber-600 mt-1 font-medium">📎 Patokan: {{ $kunjungan->patokan }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($kunjungan->status == 'Selesai' || $kunjungan->laporan)
                <button type="button" onclick="bukaPreviewPdf()"
                   class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white rounded-lg text-[13px] font-semibold flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Cetak PDF Laporan</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Stepper Status Kunjungan -->
    <div class="p-4 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Status Alur Kunjungan</h4>
        <div class="flex items-start">
            <div class="flex-1 flex flex-col items-center text-center min-w-0">
                <span class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 {{ $kunjungan->status == 'Terjadwal' ? 'bg-[#002266] text-white border-[#002266] shadow-md shadow-blue-900/20' : 'bg-white text-slate-400 border-slate-200' }}">1</span>
                <span class="mt-2 text-[10px] sm:text-[11px] font-semibold leading-tight {{ $kunjungan->status == 'Terjadwal' ? 'text-[#003399]' : 'text-slate-400' }}">Terjadwal</span>
            </div>
            <div class="flex-none w-4 sm:flex-1 h-0.5 bg-slate-200 mt-[18px] rounded-full"></div>
            <div class="flex-1 flex flex-col items-center text-center min-w-0">
                <span class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 {{ $kunjungan->status == 'Dikonfirmasi' ? 'bg-[#002266] text-white border-[#002266] shadow-md shadow-blue-900/20' : 'bg-white text-slate-400 border-slate-200' }}">2</span>
                <span class="mt-2 text-[10px] sm:text-[11px] font-semibold leading-tight {{ $kunjungan->status == 'Dikonfirmasi' ? 'text-[#003399]' : 'text-slate-400' }}">Check-in (GPS)</span>
            </div>
            <div class="flex-none w-4 sm:flex-1 h-0.5 bg-slate-200 mt-[18px] rounded-full"></div>
            <div class="flex-1 flex flex-col items-center text-center min-w-0">
                <span class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 {{ ($kunjungan->status == 'Dikerjakan' && !$kunjungan->laporan) ? 'bg-[#002266] text-white border-[#002266] shadow-md shadow-blue-900/20' : 'bg-white text-slate-400 border-slate-200' }}">3</span>
                <span class="mt-2 text-[10px] sm:text-[11px] font-semibold leading-tight {{ ($kunjungan->status == 'Dikerjakan' && !$kunjungan->laporan) ? 'text-[#003399]' : 'text-slate-400' }}">On-Site &amp; Foto</span>
            </div>
            <div class="flex-none w-4 sm:flex-1 h-0.5 bg-slate-200 mt-[18px] rounded-full"></div>
            <div class="flex-1 flex flex-col items-center text-center min-w-0">
                <span class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 {{ ($kunjungan->status == 'Selesai' || ($kunjungan->status == 'Dikerjakan' && $kunjungan->laporan)) ? 'bg-[#002266] text-white border-[#002266] shadow-md shadow-blue-900/20' : 'bg-white text-slate-400 border-slate-200' }}">4</span>
                <span class="mt-2 text-[10px] sm:text-[11px] font-semibold leading-tight {{ ($kunjungan->status == 'Selesai' || ($kunjungan->status == 'Dikerjakan' && $kunjungan->laporan)) ? 'text-[#003399]' : 'text-slate-400' }}">TTD Customer</span>
            </div>
        </div>
    </div>

    <!-- MAPS & LOKASI LENGKAP -->
    @php
        $targetLat = $kunjungan->site->latitude ?? ($kunjungan->customer->latitude ?? null);
        $targetLng = $kunjungan->site->longitude ?? ($kunjungan->customer->longitude ?? null);
        $locationQuery = ($targetLat && $targetLng) ? "{$targetLat},{$targetLng}" : urlencode($kunjungan->lokasi);
    @endphp

    <div class="p-5 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h4 class="text-sm font-bold text-[#002266] flex items-center gap-2">
                    📍 Peta Lokasi & Koordinat Target
                </h4>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    {{ $kunjungan->site->nama_cabang ?? $kunjungan->customer->nama_perusahaan ?? 'Lokasi Tujuan' }} — {{ $kunjungan->alamat_sinkron }}
                    @if($kunjungan->patokan)
                        <span class="text-amber-600 font-semibold">(📎 {{ $kunjungan->patokan }})</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="https://www.google.com/maps/search/?api=1&query={{ $locationQuery }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                    🗺️ Buka Google Maps
                </a>
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $locationQuery }}" target="_blank" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    🧭 Navigasi Rute
                </a>
            </div>
        </div>

        <!-- Iframe Google Maps -->
        <div class="w-full h-64 rounded-xl overflow-hidden border border-slate-200 shadow-inner">
            <iframe 
                class="w-full h-full border-0"
                loading="lazy"
                allowfullscreen
                src="https://maps.google.com/maps?q={{ $locationQuery }}&z=15&output=embed">
            </iframe>
        </div>
    </div>

    <!-- REVISI ALUR: Selesaikan Paksa oleh Pimpinan (kunjungan nyangkut) -->
    @if(in_array(Auth::user()->id_role, [1, 2]) && $kunjungan->status == 'Dikerjakan')
        <div class="p-5 rounded-xl bg-rose-50 border border-rose-200 shadow-sm space-y-3">
            <h4 class="text-sm font-bold text-rose-700">⚠️ Kunjungan Nyangkut?</h4>
            <p class="text-xs text-slate-600 font-medium">Jika engineer lupa check-out atau ada kendala, Anda bisa memaksa selesaikan kunjungan ini. Tools yang dipinjam tetap tercatat sampai dikembalikan manual.</p>
            <form action="{{ route('kunjungan.force-selesai', $kunjungan->nomor) }}" method="POST" class="space-y-3" onsubmit="return confirm('Yakin ingin memaksa selesaikan kunjungan ini?');">
                @csrf
                <textarea name="alasan_force_selesai" rows="2" required placeholder="Alasan penyelesaian paksa (wajib diisi)..." class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-[13px] font-semibold rounded-lg shadow-sm transition">
                    Selesaikan Paksa Kunjungan
                </button>
            </form>
        </div>
    @endif

    <!-- SECTION 1: Konfirmasi Jadwal per Engineer -->
    @if($kunjungan->status == 'Terjadwal')
        @php
            $konfirmasiList = $kunjungan->konfirmasi()->with('engineer.user')->get();
            $myKonfirmasi = null;
            if (Auth::user()->id_role == 3) {
                $engLogin = \App\Models\Engineer::where('id_pengguna', Auth::user()->id_pengguna)->first();
                $myKonfirmasi = $engLogin ? $konfirmasiList->firstWhere('id_engineer', $engLogin->id_engineer) : null;
            }
            $adaDitolak = $konfirmasiList->where('status', 'ditolak')->count() > 0;
        @endphp
        <div class="p-6 rounded-xl bg-amber-50 border border-amber-200 space-y-4 shadow-sm">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-bold text-amber-900">⚡ Konfirmasi Penugasan Jadwal</h4>
                <span class="text-xs font-semibold bg-amber-200 text-amber-800 px-2.5 py-0.5 rounded-full">Menunggu Konfirmasi</span>
            </div>

            <!-- Daftar status konfirmasi tiap engineer -->
            <div class="space-y-2">
                @foreach($konfirmasiList as $kf)
                    @php
                        $isLeadKf = $kf->id_engineer == $kunjungan->id_engineer;
                        $namaKf = $kf->engineer->user->nama ?? '-';
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-amber-100 text-xs">
                        <div>
                            <p class="font-bold text-slate-800">{{ $namaKf }}
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold {{ $isLeadKf ? 'bg-[#002266] text-white' : 'bg-slate-200 text-slate-600' }}">{{ $isLeadKf ? 'LEAD' : 'SUPPORT' }}</span>
                            </p>
                            @if($kf->status == 'ditolak' && $kf->alasan_ditolak)
                                <p class="text-rose-600 font-medium mt-0.5">Alasan tolak: "{{ $kf->alasan_ditolak }}"</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($kf->status == 'diterima')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[11px]">✅ Diterima</span>
                            @elseif($kf->status == 'ditolak')
                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[11px]">❌ Ditolak — Menunggu Keputusan Pimpinan</span>
                                @if(in_array(Auth::user()->id_role, [1, 2]))
                                    <form action="{{ route('kunjungan.konfirmasi.acc-penolakan', [$kunjungan->nomor, $kf->id_engineer]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition">ACC Penolakan</button>
                                    </form>
                                    <form action="{{ route('kunjungan.konfirmasi.tolak-penolakan', [$kunjungan->nomor, $kf->id_engineer]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-white border border-rose-300 text-rose-600 hover:bg-rose-50 font-bold text-[11px] transition">Tolak Penolakan</button>
                                    </form>
                                @endif
                            @elseif($kf->status == 'ditolak_disetujui')
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-[11px]">Penolakan Disetujui</span>
                                @if(in_array(Auth::user()->id_role, [1, 2]))
                                    <button type="button" onclick="document.getElementById('ganti-{{ $kf->id_engineer }}').classList.toggle('hidden')" class="px-3 py-1.5 rounded-lg bg-[#003399] hover:bg-[#002266] text-white font-bold text-[11px] transition">🔄 Ganti</button>
                                @endif
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-bold text-[11px]">⏳ Menunggu</span>
                            @endif
                        </div>
                    </div>
                    @if($kf->status == 'ditolak_disetujui' && in_array(Auth::user()->id_role, [1, 2]))
                        <form id="ganti-{{ $kf->id_engineer }}" action="{{ route('kunjungan.ganti-engineer', $kunjungan->nomor) }}" method="POST" class="hidden p-3 bg-white rounded-xl border border-blue-200 space-y-2">
                            @csrf
                            <input type="hidden" name="id_engineer_lama" value="{{ $kf->id_engineer }}">
                            <label class="block text-xs font-bold text-slate-700">Ganti {{ $namaKf }} <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $isLeadKf ? 'bg-[#002266] text-white' : 'bg-slate-200 text-slate-600' }}">{{ $isLeadKf ? 'LEAD' : 'SUPPORT' }}</span> dengan:</label>
                            <p class="text-[11px] text-slate-500">Pengganti otomatis menjadi {{ $isLeadKf ? 'LEAD' : 'SUPPORT' }}.</p>
                            <div class="flex gap-2">
                                <select name="id_engineer_baru" required class="flex-1 h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm">
                                    <option value="">-- Pilih Engineer --</option>
                                    <optgroup label="Daftar Engineer">
@foreach(\App\Models\Engineer::with('user')->get() as $engOpt)
                                        @if(!$konfirmasiList->contains('id_engineer', $engOpt->id_engineer))
                                            <option value="{{ $engOpt->id_engineer }}">{{ $engOpt->user->nama ?? '-' }} ({{ $engOpt->kode ?? '-' }})</option>
                                        @endif
                                    @endforeach
                                    </optgroup>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">Ganti</button>
                            </div>
                        </form>
                    @endif
                @endforeach
            </div>

            @if($adaDitolak && Auth::user()->id_role == 3)
                <p class="text-xs text-rose-600 font-medium text-center">⚠️ Ada engineer yang menolak. Menunggu keputusan pimpinan atas penolakan tersebut.</p>
            @endif

            @if(Auth::user()->id_role == 3 && $myKonfirmasi && $myKonfirmasi->status == 'menunggu')
                <p class="text-xs text-amber-800 font-medium">Silakan konfirmasi penerimaan penugasan ini sebelum menuju lokasi kerja.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <!-- Form Terima -->
                    <form action="{{ route('kunjungan.terima', $kunjungan->nomor) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[13px] font-semibold shadow-sm transition flex items-center justify-center gap-2">
                            ✅ Terima & Konfirmasi Jadwal
                        </button>
                    </form>

                    <!-- Form Tolak -->
                    <form action="{{ route('kunjungan.reschedule', $kunjungan->nomor) }}" method="POST" class="space-y-2">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="alasan_reschedule" required placeholder="Alasan Tolak (Contoh: Jadwal Bentrok)" class="w-full h-11 px-3 bg-white border border-slate-200 focus:border-rose-400 focus:ring-rose-400 rounded-lg text-sm text-slate-800">
                            <button type="button" onclick="if(!this.form.checkValidity()) { this.form.reportValidity(); return; } showConfirmModal(this.form, 'Tolak Jadwal', 'Apakah Anda yakin ingin menolak jadwal ini?')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition whitespace-nowrap">
                                ❌ Tolak
                            </button>
                        </div>
                    </form>
                </div>
            @elseif(Auth::user()->id_role == 3 && $myKonfirmasi && $myKonfirmasi->status == 'diterima')
                <p class="text-xs text-emerald-700 font-bold text-center">✅ Anda sudah mengkonfirmasi. Menunggu engineer lainnya.</p>
            @endif
        </div>
    @endif
    @if(Auth::user()->id_role == 3)
        @if(in_array($kunjungan->status, ['Dikonfirmasi', 'Dikerjakan']))
            @php
                $sudahCheckinSaya = false;
                if (Auth::user()->id_role == 3) {
                    $engSaya = \App\Models\Engineer::where('id_pengguna', Auth::user()->id_pengguna)->first();
                    if ($engSaya) {
                        $sudahCheckinSaya = \App\Models\AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
                            ->where('id_engineer', $engSaya->id_engineer)
                            ->whereNotNull('waktu_mulai')->exists();
                    }
                }
            @endphp
            @if(!$sudahCheckinSaya)
            <!-- Kotak Check-In GPS -->
            <div class="p-6 rounded-xl bg-blue-50 border border-blue-200 text-center space-y-3 shadow-sm">
                <h4 class="text-sm font-bold text-[#002266]">Sudah Tiba di Lokasi Klien?</h4>
                <p class="text-xs text-slate-600 font-medium">Klik tombol di bawah ini untuk mencatat koordinat GPS dan memulai pengerjaan.</p>
                
                <form id="formCheckIn" action="{{ route('kunjungan.checkin', $kunjungan->nomor) }}" method="POST">
                    @csrf
                    <input type="hidden" name="lokasi_gps" id="lokasi_gps_checkin">
                    <button type="button" onclick="getGPSCheckIn()" class="w-full max-w-md mx-auto px-4 py-3 bg-[#002266] hover:bg-[#001a4d] text-white rounded-lg text-[13px] font-semibold shadow-sm transition">
                        📍 Ambil Lokasi GPS & Check-In
                    </button>
                </form>
            </div>
            @endif
        @endif
    @endif

    @if($kunjungan->status == 'Reschedule')
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex flex-col gap-1 shadow-sm">
            <span class="font-bold">⚠️ Status: Jadwal Perlu Dijadwalkan Ulang (Reschedule)</span>
            <p class="font-medium">Alasan penolakan dari Engineer: "{{ $kunjungan->alasan_reschedule }}"</p>
        </div>
    @endif

    <!-- SECTION 2: Pelaksanaan & Upload Dokumentasi Foto Lapangan -->
    @if(Auth::user()->id_role == 3 && $kunjungan->status == 'Dikerjakan')
        <div class="p-5 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-[#002266] flex items-center gap-2">
                📷 Unggah Dokumentasi Lapangan (On-Site)
            </h4>
            <form action="{{ route('kunjungan.dokumentasi', $kunjungan->nomor) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-medium">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 mb-1.5 font-bold">Kategori Foto</label>
                        <select name="kategori_foto" onchange="document.getElementById('keteranganFoto').required = (this.value === 'Lainnya')" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                            @php $labelKat = ['Sebelum' => 'Foto Sebelum Pengerjaan', 'Proses' => 'Foto Saat Pengerjaan', 'Sesudah' => 'Foto Setelah Selesai']; @endphp
                            @foreach($opsiKategoriFoto as $opKat)
                                <option value="{{ $opKat }}">{{ $labelKat[$opKat] ?? ('Foto ' . $opKat) }}</option>
                            @endforeach
                            <option value="Lainnya">Foto Lainnya / Kondisi Khusus</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1.5 font-bold">Pilih Foto/Video (Kamera atau Galeri HP)</label>
                        <input type="file" name="foto" accept="image/*,video/*" required class="w-full text-slate-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#002266] file:text-white hover:file:bg-[#001233]">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 mb-1.5 font-bold">Keterangan Foto <span class="text-rose-500 font-semibold">(wajib jika kategori Lainnya)</span></label>
                    <input type="text" name="keterangan" id="keteranganFoto" placeholder="Contoh: Kondisi port switch sebelum pergantian modul" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <button type="submit" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white rounded-lg text-[13px] font-semibold shadow-sm transition">Unggah Foto Dokumentasi</button>
            </form>
        </div>
    @endif

    <!-- SECTION 2.5: Form Pencatatan Pengeluaran (Expense) -->
    @if(Auth::user()->id_role == 3 && $kunjungan->status == 'Dikerjakan')
        <div class="p-5 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                💸 Catat Pengeluaran Operasional
            </h4>
            <form action="{{ route('kunjungan.pengeluaran', $kunjungan->nomor) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-medium">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 mb-1.5 font-bold">Jenis Biaya</label>
                        <select name="jenis_biaya" id="jenisBiayaSelect" onchange="document.getElementById('jenisBiayaLainnyaWrap').classList.toggle('hidden', this.value !== 'Lainnya')" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                            @foreach($opsiJenisBiaya as $opBiaya)
                                <option value="{{ $opBiaya }}">{{ $opBiaya }}</option>
                            @endforeach
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1.5 font-bold">Nominal (Rp)</label>
                        <input type="number" name="nominal" min="0" required placeholder="Contoh: 50000" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    </div>
                </div>
                <div id="jenisBiayaLainnyaWrap" class="hidden">
                    <label class="block text-slate-700 mb-1.5 font-bold">Tulis Jenis Biaya <span class="text-rose-500 font-semibold">(wajib jika Lainnya)</span></label>
                    <input type="text" name="jenis_biaya_lainnya" placeholder="Contoh: Sewa tangga" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-slate-700 mb-1.5 font-bold">Dikeluarkan Oleh</label>
                    <select name="id_engineer" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                        @php $engSaya = \App\Models\Engineer::where('id_pengguna', Auth::user()->id_pengguna)->first(); @endphp
                        @foreach($timKunjungan as $anggota)
                            <option value="{{ $anggota->id_engineer }}" {{ $engSaya && $engSaya->id_engineer == $anggota->id_engineer ? 'selected' : '' }}>{{ $anggota->user->nama ?? '-' }}{{ $anggota->id_engineer == $kunjungan->id_engineer ? ' (Lead)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 mb-1.5 font-bold">Bukti Foto/Video Nota / Struk (Opsional)</label>
                        <input type="file" name="bukti_nota" accept="image/*,video/*" class="w-full text-slate-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300">
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1.5 font-bold">Keterangan Tambahan</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Beli kabel LAN 5 meter" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    </div>
                </div>
                <button type="submit" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white rounded-lg text-[13px] font-semibold shadow-sm transition">Simpan Pengeluaran</button>
            </form>
        </div>
    @endif

    <!-- SECTION 3: Form Check-Out (muncul setelah laporan dibuat & TTD terkunci) -->
    @php
        $myEngineer = Auth::user()->id_role == 3 ? \App\Models\Engineer::where('id_pengguna', Auth::user()->id_pengguna)->first() : null;
        $myAktivitas = $myEngineer ? $kunjungan->aktivitas->where('id_engineer', $myEngineer->id_engineer)->sortByDesc('created_at')->first() : null;
        $sudahCheckin = $myAktivitas && $myAktivitas->waktu_mulai;
        $sudahCheckout = $myAktivitas && $myAktivitas->waktu_selesai;
    @endphp
    @if(Auth::user()->id_role == 3 && $kunjungan->status == 'Dikerjakan' && !$sudahCheckout && $kunjungan->laporan && ($kunjungan->laporan->buktiPenyelesaian || $kunjungan->laporan->ttd_dilewati))
        <div class="p-5 md:p-6 rounded-xl bg-blue-50 border border-blue-200 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-[#002266]">📝 Input Catatan & Check-Out</h4>
            <p class="text-xs text-slate-600 font-medium">Tuliskan ringkasan hasil pengerjaan. Sistem akan memverifikasi lokasi GPS Anda untuk proses Check-Out.</p>

            <form id="formCheckOut" action="{{ route('kunjungan.checkout', $kunjungan->nomor) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="lokasi_gps" id="lokasi_gps_checkout">

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Deskripsi / Hasil Pekerjaan Lapangan:</label>
                    <textarea name="catatan" id="catatan_pekerjaan" rows="4" required placeholder="Contoh: Pemeliharaan berkala server dan perapihan cabling rack selesai 100%." class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
                </div>

                <button type="button" onclick="getGPSCheckOut()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white text-[13px] font-semibold rounded-lg shadow-sm transition">
                    📍 Ambil GPS & Check-Out
                </button>
            </form>
        </div>
    @endif
    @if(Auth::user()->id_role == 3 && $kunjungan->status == 'Dikerjakan' && !$sudahCheckout && $kunjungan->laporan && !$kunjungan->laporan->buktiPenyelesaian && !$kunjungan->laporan->ttd_dilewati)
        <div class="p-5 md:p-6 rounded-xl bg-amber-50 border border-amber-200 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-[#002266]">📝 Input Catatan & Check-Out</h4>
            <p class="text-xs text-slate-600 font-medium">Laporan sudah dibuat tetapi <span class="font-bold">belum ada tanda tangan customer</span>. Anda bisa check-out sekarang dengan melewati TTD (wajib isi alasan) — kunjungan akan ditandai untuk follow-up pimpinan.</p>

            <form id="formCheckOut" action="{{ route('kunjungan.checkout', $kunjungan->nomor) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="lokasi_gps" id="lokasi_gps_checkout">
                <input type="hidden" name="lewati_ttd" value="1">

                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Deskripsi / Hasil Pekerjaan Lapangan:</label>
                    <textarea name="catatan" rows="4" required placeholder="Contoh: Pemeliharaan berkala server selesai 100%." class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Alasan TTD Dilewati <span class="text-rose-600">*</span>:</label>
                    <textarea name="alasan_tanpa_ttd" rows="2" required placeholder="Contoh: PIC customer sudah pulang, tidak ada yang bisa tanda tangan." class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
                </div>

                <button type="button" onclick="getGPSCheckOut()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white text-[13px] font-semibold rounded-lg shadow-sm transition">
                    📍 Ambil GPS & Check-Out (Tanpa TTD)
                </button>
            </form>
        </div>
    @endif

    <!-- SECTION LAPORAN: Kelola Laporan Kunjungan (terpisah sendiri: Bagian 1 Data + Bagian 2 TTD) -->
    {{-- Engineer: buat & revisi. Pimpinan/Kepala Pimpinan: revisi laporan yang sudah ada. --}}
    @if((Auth::user()->id_role == 3 && ($sudahCheckin || $kunjungan->laporan)) || (Auth::user()->id_role != 3 && $kunjungan->laporan))
    <div class="p-5 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h4 class="text-sm font-bold text-[#002266]">📄 Kelola Laporan Kunjungan</h4>
            @if($kunjungan->laporan && $kunjungan->laporan->pembuat)
            <span class="text-[11px] text-slate-500 font-medium">Dibuat oleh <span class="font-bold text-[#003399]">{{ $kunjungan->laporan->pembuat->user->nama ?? '-' }}</span></span>
            @endif
        </div>

        @if(!$kunjungan->laporan)
        {{-- STATE A: BELUM ADA LAPORAN --}}
        <p class="text-xs text-slate-600 font-medium">Anda sudah check-in. Laporan dikelola dalam 2 bagian: <span class="font-bold">Bagian 1 — Isi Data Laporan</span> dan <span class="font-bold">Bagian 2 — Tanda Tangan</span>. <span class="font-bold text-amber-700">Hanya 1 laporan per kunjungan</span> — siapa yang buat duluan, dia yang jadi pembuatnya.</p>
        <button type="button" onclick="toggleFormLaporan('formLaporanBaru')" class="w-full py-3 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg shadow-sm transition">📄 Kelola Laporan Kunjungan</button>

        <div id="formLaporanBaru" class="hidden">
            <form id="signatureForm" action="{{ route('kunjungan.simpan-laporan', $kunjungan->nomor) }}" method="POST" data-ada-ttd="0" class="space-y-5">
                @csrf
                <input type="hidden" name="signature" id="signatureInput">
                <input type="hidden" name="signature_engineer" id="signatureEngineerInput">

                {{-- BAGIAN 1: ISI DATA LAPORAN --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <h5 class="text-[13px] font-bold text-[#002266]">Bagian 1 — Isi Data Laporan</h5>
                        <div class="flex items-center gap-2">
                            <span id="draftLaporanBadge" class="hidden text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-lg">💾 Draft tersimpan otomatis</span>
                            <span id="lockBadgeBagian1" class="text-[11px] font-bold px-2 py-1 rounded-lg border text-emerald-700 bg-emerald-50 border-emerald-200">✏️ Mode Isi Data</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium">Tulis dengan bahasa yang mudah dipahami customer. Data inilah yang menjadi output laporan PDF. Ketikan Anda tersimpan otomatis sebagai draft — aman walau halaman di-refresh.</p>
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Hasil Pekerjaan <span class="text-red-500">*</span></label>
                        <textarea id="draftHasilPekerjaan" name="hasil_pekerjaan" rows="3" required placeholder="Contoh: Pemeliharaan server selesai. Sistem berjalan normal dan sudah dites bersama PIC." class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">{{ old('hasil_pekerjaan', $kunjungan->draft_hasil_pekerjaan ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Catatan Tambahan <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <textarea id="draftCatatanTambahan" name="catatan_tambahan" rows="2" placeholder="Contoh: Disarankan penggantian kabel LAN lantai 2 bulan depan." class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">{{ old('catatan_tambahan', $kunjungan->draft_catatan_tambahan ?? '') }}</textarea>
                    </div>
                    <button type="button" id="lockBtnBagian1" onclick="toggleLockBagian1()" class="w-full sm:w-auto px-4 py-2 bg-amber-100 border border-amber-300 text-amber-800 rounded-xl text-xs font-bold hover:bg-amber-200 transition">🔒 Kunci</button>
                </div>

                {{-- BAGIAN 2: TANDA TANGAN --}}
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 space-y-4">
                    <h5 class="text-[13px] font-bold text-amber-700">Bagian 2 — Tanda Tangan</h5>
                    <p class="text-[11px] text-amber-800 font-medium">Sodorkan HP ke Customer / PIC <strong>({{ $kunjungan->customer->pic ?? 'PIC Perusahaan' }})</strong> untuk tanda tangan di kotak pertama, lalu Engineer di kotak kedua. Cukup <strong>salah satu</strong> terisi — satunya bisa menyusul lewat revisi. Tekan "Buka Kunci TTD" dulu sebelum menggambar.</p>

                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <p class="text-xs font-bold text-slate-700">Tanda Tangan Customer / PIC:</p>
                            <div id="sigBadgeCustomer" class="px-2 py-1 rounded-lg text-[11px] font-bold border text-slate-700 bg-slate-200 border-slate-300">🔒 Terkunci</div>
                        </div>
                        <div id="sigWrapCustomer" data-locked="1" class="relative border-2 border-slate-300 bg-white rounded-xl overflow-hidden shadow-inner touch-pan-x touch-pan-y">
                            <canvas id="signaturePad" class="w-full h-56 block cursor-crosshair"></canvas>
                        </div>
                        <div class="mt-2 flex flex-col sm:flex-row gap-2">
                            <button type="button" id="lockBtnCustomer" onclick="togglePadLock('customer')" class="w-full sm:w-auto px-4 py-2 bg-amber-100 border border-amber-300 text-amber-800 rounded-xl text-xs font-bold hover:bg-amber-200 transition">🔓 Buka Kunci TTD</button>
                            <button type="button" onclick="clearSignature()" class="w-full sm:w-auto px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition">Hapus & Ulangi TTD Customer</button>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <p class="text-xs font-bold text-slate-700">Tanda Tangan Engineer ({{ $kunjungan->engineer->user->nama ?? 'Engineer' }}):</p>
                            <div id="sigBadgeEngineer" class="px-2 py-1 rounded-lg text-[11px] font-bold border text-slate-700 bg-slate-200 border-slate-300">🔒 Terkunci</div>
                        </div>
                        <div id="sigWrapEngineer" data-locked="1" class="relative border-2 border-slate-300 bg-white rounded-xl overflow-hidden shadow-inner touch-pan-x touch-pan-y">
                            <canvas id="signaturePadEngineer" class="w-full h-56 block cursor-crosshair"></canvas>
                        </div>
                        <div class="mt-2 flex flex-col sm:flex-row gap-2">
                            <button type="button" id="lockBtnEngineer" onclick="togglePadLock('engineer')" class="w-full sm:w-auto px-4 py-2 bg-amber-100 border border-amber-300 text-amber-800 rounded-xl text-xs font-bold hover:bg-amber-200 transition">🔓 Buka Kunci TTD</button>
                            <button type="button" onclick="clearSignatureEngineer()" class="w-full sm:w-auto px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition">Hapus & Ulangi TTD Engineer</button>
                        </div>
                    </div>

                    <div class="pt-1">
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" name="lewati_ttd" value="1" id="lewatiTtdBuat" onchange="document.getElementById('alasanTtdWrap').classList.toggle('hidden', !this.checked)" class="mt-1 w-4 h-4 accent-amber-600">
                            <span class="text-xs font-bold text-slate-700">Customer tidak bisa / tidak mau tanda tangan (lewati TTD)</span>
                        </label>
                        <div id="alasanTtdWrap" class="hidden mt-2">
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">Alasan Tanpa TTD <span class="text-red-500">*</span></label>
                            <textarea name="alasan_tanpa_ttd" rows="2" placeholder="Contoh: PIC sudah pulang, tidak ada yang bisa tanda tangan." class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">{{ old('alasan_tanpa_ttd') }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="submitLaporanLengkap(false)" class="w-full py-3 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg shadow-sm transition">📄 Buat Laporan Sekarang</button>
            </form>
        </div>

        @else
        {{-- STATE B: LAPORAN SUDAH ADA --}}
        <button type="button" onclick="bukaPreviewPdf()" class="w-full py-3 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg shadow-sm transition">👁️ Lihat PDF Laporan</button>
        @if($kunjungan->laporan->buktiPenyelesaian)
        <p class="text-[11px] text-emerald-700 font-medium">✅ Ditandatangani pada {{ $kunjungan->laporan->buktiPenyelesaian->tanggal_tanda_tangan }}</p>
        @elseif($kunjungan->laporan->ttd_dilewati)
        <p class="text-[11px] text-amber-700 font-medium">⚠️ TTD dilewati — alasan: {{ $kunjungan->laporan->alasan_tanpa_ttd }}</p>
        @endif

        @if($kunjungan->laporan->status_approval == 'Disetujui')
        <p class="text-xs text-slate-500 font-medium text-center">🔒 Laporan sudah disetujui pimpinan dan tidak dapat direvisi lagi.</p>
        @else
        <button type="button" onclick="toggleFormLaporan('formRevisiLaporan')" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white text-[13px] font-semibold rounded-lg shadow-sm transition">✏️ Revisi Laporan Kunjungan</button>

        <div id="formRevisiLaporan" class="hidden">
            <form id="signatureForm" action="{{ route('kunjungan.simpan-revisi-laporan', $kunjungan->nomor) }}" method="POST" data-ada-ttd="{{ $kunjungan->laporan->buktiPenyelesaian ? '1' : '0' }}" class="space-y-5">
                @csrf
                <input type="hidden" name="signature" id="signatureInput">
                <input type="hidden" name="signature_engineer" id="signatureEngineerInput">

                {{-- BAGIAN 1: ISI DATA LAPORAN --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <h5 class="text-[13px] font-bold text-[#002266]">Bagian 1 — Isi Data Laporan</h5>
                        <span id="lockBadgeBagian1Revisi" class="text-[11px] font-bold px-2 py-1 rounded-lg border text-emerald-700 bg-emerald-50 border-emerald-200">✏️ Mode Isi Data</span>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Hasil Pekerjaan <span class="text-red-500">*</span></label>
                        <textarea id="revisiHasilPekerjaan" name="hasil_pekerjaan" rows="3" required class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">{{ old('hasil_pekerjaan', $kunjungan->laporan->hasil_pekerjaan ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Catatan Tambahan <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <textarea id="revisiCatatanTambahan" name="catatan_tambahan" rows="2" class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">{{ old('catatan_tambahan', $kunjungan->laporan->catatan_tambahan ?? '') }}</textarea>
                    </div>
                    <button type="button" id="lockBtnBagian1Revisi" onclick="toggleLockBagian1('revisi')" class="w-full sm:w-auto px-4 py-2 bg-amber-100 border border-amber-300 text-amber-800 rounded-xl text-xs font-bold hover:bg-amber-200 transition">🔒 Kunci</button>
                </div>

                {{-- BAGIAN 2: TANDA TANGAN --}}
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 space-y-4">
                    <h5 class="text-[13px] font-bold text-amber-700">Bagian 2 — Tanda Tangan</h5>
                    @if($kunjungan->laporan->buktiPenyelesaian)
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white p-2 rounded-xl border border-slate-200 text-center">
                            <p class="text-[11px] font-bold text-slate-500 mb-1">TTD Customer saat ini</p>
                            <img src="{{ $kunjungan->laporan->buktiPenyelesaian->tanda_tangan_customer }}" alt="TTD Customer" class="h-14 object-contain mx-auto">
                        </div>
                        <div class="bg-white p-2 rounded-xl border border-slate-200 text-center">
                            <p class="text-[11px] font-bold text-slate-500 mb-1">TTD Engineer saat ini</p>
                            <img src="{{ $kunjungan->laporan->buktiPenyelesaian->tanda_tangan_engineer }}" alt="TTD Engineer" class="h-14 object-contain mx-auto">
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium">TTD terakhir sudah dimuat di kotak di bawah — biarkan untuk mempertahankan, atau tekan "Hapus & Ulangi" lalu gambar ulang untuk mengganti.</p>
                    @endif
                    <p class="text-[11px] text-amber-800 font-medium">Tekan "Buka Kunci TTD" dulu sebelum menggambar.</p>

                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <p class="text-xs font-bold text-slate-700">Tanda Tangan Customer / PIC:</p>
                            <div id="sigBadgeCustomer" class="px-2 py-1 rounded-lg text-[11px] font-bold border text-slate-700 bg-slate-200 border-slate-300">🔒 Terkunci</div>
                        </div>
                        <div id="sigWrapCustomer" data-locked="1" class="relative border-2 border-slate-300 bg-white rounded-xl overflow-hidden shadow-inner touch-pan-x touch-pan-y">
                            <canvas id="signaturePad" class="w-full h-56 block cursor-crosshair"></canvas>
                        </div>
                        <div class="mt-2 flex flex-col sm:flex-row gap-2">
                            <button type="button" id="lockBtnCustomer" onclick="togglePadLock('customer')" class="w-full sm:w-auto px-4 py-2 bg-amber-100 border border-amber-300 text-amber-800 rounded-xl text-xs font-bold hover:bg-amber-200 transition">🔓 Buka Kunci TTD</button>
                            <button type="button" onclick="clearSignature()" class="w-full sm:w-auto px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition">Hapus & Ulangi TTD Customer</button>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <p class="text-xs font-bold text-slate-700">Tanda Tangan Engineer:</p>
                            <div id="sigBadgeEngineer" class="px-2 py-1 rounded-lg text-[11px] font-bold border text-slate-700 bg-slate-200 border-slate-300">🔒 Terkunci</div>
                        </div>
                        <div id="sigWrapEngineer" data-locked="1" class="relative border-2 border-slate-300 bg-white rounded-xl overflow-hidden shadow-inner touch-pan-x touch-pan-y">
                            <canvas id="signaturePadEngineer" class="w-full h-56 block cursor-crosshair"></canvas>
                        </div>
                        <div class="mt-2 flex flex-col sm:flex-row gap-2">
                            <button type="button" id="lockBtnEngineer" onclick="togglePadLock('engineer')" class="w-full sm:w-auto px-4 py-2 bg-amber-100 border border-amber-300 text-amber-800 rounded-xl text-xs font-bold hover:bg-amber-200 transition">🔓 Buka Kunci TTD</button>
                            <button type="button" onclick="clearSignatureEngineer()" class="w-full sm:w-auto px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition">Hapus & Ulangi TTD Engineer</button>
                        </div>
                    </div>

                    <div class="pt-1">
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" name="lewati_ttd" value="1" id="lewatiTtdRevisi" {{ $kunjungan->laporan->ttd_dilewati ? 'checked' : '' }} onchange="document.getElementById('alasanTtdWrapRevisi').classList.toggle('hidden', !this.checked)" class="mt-1 w-4 h-4 accent-amber-600">
                            <span class="text-xs font-bold text-slate-700">Customer tidak bisa / tidak mau tanda tangan (lewati TTD)</span>
                        </label>
                        <div id="alasanTtdWrapRevisi" class="{{ $kunjungan->laporan->ttd_dilewati ? '' : 'hidden' }} mt-2">
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">Alasan Tanpa TTD <span class="text-red-500">*</span></label>
                            <textarea name="alasan_tanpa_ttd" rows="2" placeholder="Contoh: PIC sudah pulang, tidak ada yang bisa tanda tangan." class="w-full p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">{{ old('alasan_tanpa_ttd', $kunjungan->laporan->alasan_tanpa_ttd ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="submitLaporanLengkap(true)" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white text-[13px] font-semibold rounded-lg shadow-sm transition">💾 Simpan Revisi</button>
            </form>
        </div>
        @endif
        @endif
    </div>
    @endif
    <!-- SECTION 6: Detail Informasi Tiket, Tools & Log GPS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-3 text-xs">
            <h4 class="text-xs font-bold text-[#002266] uppercase tracking-wider border-b border-slate-100 pb-2">Detail Informasi Tiket</h4>
            <div class="space-y-2 font-medium text-slate-600">
                <p><strong class="text-slate-800">Customer:</strong>
                    @if($kunjungan->customer)
                        <a href="{{ route('master.customer.show', $kunjungan->customer->kode) }}" class="text-[#003399] font-bold hover:underline">{{ $kunjungan->customer->nama_perusahaan }}</a>
                        <span class="font-mono text-[10px] text-slate-400 font-bold">({{ $kunjungan->customer->kode }})</span>
                    @else
                        -
                    @endif
                </p>
                <p><strong class="text-slate-800">PIC:</strong> {{ $kunjungan->customer->pic ?? '-' }} ({{ $kunjungan->customer->telepon ?? '-' }})</p>
                <p><strong class="text-slate-800">Lead Engineer:</strong> {{ $kunjungan->engineer->user->nama ?? 'Belum Ditugaskan' }}
                    @if($kunjungan->engineer)
                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#003399] border border-blue-200">Penanggung Jawab</span>
                    @endif
                </p>
                <p><strong class="text-slate-800">Tim Support:</strong> 
                    @if($kunjungan->supportEngineers->count() > 0)
                        {{$kunjungan->supportEngineers->pluck('user.nama')->implode(', ') }}
                    @else
                        <span class="italic text-slate-400">Tidak ada tim support</span>
                    @endif
                </p>
                <p><strong class="text-slate-800">Alat Kerja Terbawa:</strong></p>
                <ul class="list-disc list-inside pl-2">
                    @forelse($kunjungan->tools as $tool)
                        <li><a href="{{ route('master.tool.show', $tool->kode) }}" class="text-[#003399] font-bold hover:underline">{{ $tool->nama_alat }}</a> <span class="font-mono text-[10px] text-slate-400 font-bold">({{ $tool->kode }})</span> <span class="text-slate-500 font-semibold">— Jumlah dibawa: {{ $tool->pivot->jumlah ?? 1 }}</span></li>
                    @empty
                        <li class="italic text-slate-400">Tidak ada tools khusus</li>
                    @endforelse
                </ul>
            </div>
        </div>

    <!-- SECTION 6b: Revisi Tools (QA #8 — engineer terlibat / pimpinan) -->
    @if((in_array(Auth::user()->id_role, [1, 2]) || $peran) && !in_array($kunjungan->status, ['Selesai', 'Reschedule']))
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4 text-xs">
            <h4 class="text-xs font-bold text-[#002266] uppercase tracking-wider border-b border-slate-100 pb-2">🧰 Revisi Tools yang Dibawa</h4>
            <p class="text-slate-500 font-medium">Koreksi tools bila ada kesalahan penugasan (tool tertukar, jumlah kurang/lebih). Stok dan catatan peminjaman menyesuaikan otomatis.</p>
            @if($revisiTerkunci)
                <p class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-700 font-semibold">Revisi tools terkunci: engineer sudah check-in di lokasi klien, tools yang dibawa tidak dapat diubah lagi.</p>
            @else
            <form action="{{ route('kunjungan.tools.revisi', $kunjungan->nomor) }}" method="POST" class="space-y-2">
                @csrf
                @php $selQtyShow = $kunjungan->tools->mapWithKeys(fn($t) => [$t->id_tool => (int) $t->pivot->jumlah])->toArray(); @endphp
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($tools as $t)
                        @php $jatahShow = $selQtyShow[$t->id_tool] ?? 0; $maksShow = $t->stok + $jatahShow; @endphp
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-2">
                            <input type="checkbox" name="tools[]" value="{{ $t->id_tool }}" {{ isset($selQtyShow[$t->id_tool]) ? 'checked' : '' }} {{ $maksShow < 1 ? 'disabled' : '' }} class="w-4 h-4 rounded border-slate-300 text-[#003399] focus:ring-[#003399]">
                            <span class="flex-1 text-sm truncate text-slate-700 font-medium">{{ $t->nama_alat }} <span class="font-mono text-[10px] text-slate-400 font-bold">({{ $t->kode }})</span></span>
                            @if($maksShow > 0)
                                <span class="text-[11px] font-semibold text-emerald-600 whitespace-nowrap">Stok tersedia: {{ $t->stok }}</span>
                                <input type="number" name="tools_qty[{{ $t->id_tool }}]" value="{{ $selQtyShow[$t->id_tool] ?? 1 }}" min="1" max="{{ $maksShow }}" class="w-16 h-8 px-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]" title="Jumlah dibawa">
                            @else
                                <span class="text-[11px] font-bold text-rose-500 whitespace-nowrap">Tidak Tersedia</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white rounded-lg text-[13px] font-semibold shadow-sm transition">Simpan Revisi Tools</button>
                </div>
            </form>
            @endif
        </div>
    @endif

        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-5 text-xs">
            <h4 class="text-xs font-bold text-[#002266] uppercase tracking-wider border-b border-slate-100 pb-2">Log Waktu & Lokasi GPS</h4>
            @forelse($kunjungan->aktivitas->sortBy('waktu_mulai') as $act)
                @php
                    $namaEng = $act->engineer->user->nama ?? '-';
                    $isLead = $act->id_engineer == $kunjungan->id_engineer;
                    $ciLat = $coLat = $ciLng = $coLng = null;
                    if ($act->lokasi) {
                        $ciParts = explode(',', $act->lokasi);
                        $ciLat = trim($ciParts[0] ?? '');
                        $ciLng = trim($ciParts[1] ?? '');
                    }
                    if ($act->lokasi_checkout) {
                        $coParts = explode(',', $act->lokasi_checkout);
                        $coLat = trim($coParts[0] ?? '');
                        $coLng = trim($coParts[1] ?? '');
                    }
                    // Fallback ke koordinat global kunjungan untuk lead
                    if ($isLead) {
                        if (!$ciLat && $kunjungan->check_in_latitude) { $ciLat = $kunjungan->check_in_latitude; $ciLng = $kunjungan->check_in_longitude; }
                        if (!$coLat && $kunjungan->check_out_latitude) { $coLat = $kunjungan->check_out_latitude; $coLng = $kunjungan->check_out_longitude; }
                    }
                @endphp
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-sm text-slate-800">👷 {{ $namaEng }}</p>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $isLead ? 'bg-[#002266] text-white' : 'bg-slate-200 text-slate-600' }}">{{ $isLead ? 'LEAD ENGINEER' : 'TIM SUPPORT' }}</span>
                    </div>

                    <!-- Lokasi Check-in -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-bold text-slate-800">📍 Lokasi Check-in</p>
                            @if($ciLat && $ciLng)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $ciLat }},{{ $ciLng }}" target="_blank" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg text-[11px] font-bold transition flex items-center gap-1.5 border border-slate-200">
                                🗺️ Buka Google Maps
                            </a>
                            @endif
                        </div>
                        @if($ciLat && $ciLng)
                            <p class="font-mono font-bold text-[#003399]">{{ $ciLat }}, {{ $ciLng }}</p>
                            <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-200 shadow-inner">
                                <iframe class="w-full h-full border-0" loading="lazy" allowfullscreen
                                    src="https://maps.google.com/maps?q={{ $ciLat }},{{ $ciLng }}&z=16&output=embed"></iframe>
                            </div>
                        @else
                            <p class="italic text-slate-400 font-medium">Belum check-in</p>
                        @endif
                        <p class="font-medium text-slate-600"><strong class="text-slate-800">Waktu Check-in:</strong> {{ $act->waktu_mulai ?? '-' }}</p>
                    </div>

                    <!-- Lokasi Check-out -->
                    <div class="space-y-2 border-t border-slate-200 pt-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-bold text-slate-800">📍 Lokasi Check-out</p>
                            @if($coLat && $coLng)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $coLat }},{{ $coLng }}" target="_blank" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg text-[11px] font-bold transition flex items-center gap-1.5 border border-slate-200">
                                🗺️ Buka Google Maps
                            </a>
                            @endif
                        </div>
                        @if($coLat && $coLng)
                            <p class="font-mono font-bold text-[#003399]">{{ $coLat }}, {{ $coLng }}</p>
                            <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-200 shadow-inner">
                                <iframe class="w-full h-full border-0" loading="lazy" allowfullscreen
                                    src="https://maps.google.com/maps?q={{ $coLat }},{{ $coLng }}&z=16&output=embed"></iframe>
                            </div>
                        @else
                            <p class="italic text-slate-400 font-medium">Belum check-out</p>
                        @endif
                        <p class="font-medium text-slate-600"><strong class="text-slate-800">Waktu Check-out:</strong> {{ $act->waktu_selesai ?? '-' }}</p>
                    </div>

                    <p class="font-medium text-slate-600 border-t border-slate-200 pt-3"><strong class="text-slate-800">Catatan Engineer:</strong> {{ $act->catatan ?? '-' }}</p>
                </div>
            @empty
                <p class="italic text-slate-400 font-medium">Belum ada aktivitas check-in.</p>
            @endforelse
        </div>
    </div>

    <!-- Tabel Rincian Pengeluaran -->
    <div class="p-5 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm">
        <h4 class="text-sm font-bold text-[#002266] mb-4">Rincian Pengeluaran Operasional</h4>
        @php $bolehUbah = in_array(Auth::user()->id_role, [1, 2]) || $peran; @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Jenis Biaya</th>
                        <th class="px-4 py-3 text-left font-semibold">Dikeluarkan Oleh</th>
                        <th class="px-4 py-3 text-left font-semibold">Nominal (Rp)</th>
                        <th class="px-4 py-3 text-left font-semibold">Keterangan</th>
                        <th class="px-4 py-3 text-left font-semibold">Bukti Nota</th>
                        <th class="px-4 py-3 text-left font-semibold">Status Ganti</th>
                        @if($bolehUbah)
                            <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $totalPengeluaran = 0; @endphp
                    @forelse($kunjungan->pengeluaran as $peng)
                        @php $totalPengeluaran +=$peng->nominal; @endphp
                        <tr class="hover:bg-slate-50 transition-colors font-medium">
                            <td class="px-4 py-3 border-t border-slate-100 font-bold text-slate-800">{{ $peng->jenis_biaya }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $peng->engineer->user->nama ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-emerald-600 font-bold">Rp {{ number_format($peng->nominal, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $peng->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                @if($peng->bukti_nota)
                                    <a href="{{ \App\Support\Uploads::url($peng->bukti_nota) }}" target="_blank" class="text-[#0044cc] hover:underline font-bold">Lihat Bukti</a>
                                @else
                                    <span class="text-slate-400 italic">Tidak ada struk</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                {{-- QA ronde 2 #3: tata letak kolom status ganti dirapikan (badge + tombol versi baru, tidak menumpuk) --}}
                                <div class="flex flex-col items-start gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border whitespace-nowrap {{ $peng->status_ganti == 'Sudah Diganti' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">{{ $peng->status_ganti ?? 'Belum Diganti' }}</span>
                                    @if(in_array(Auth::user()->id_role, [1, 2]))
                                        <form action="{{ route('kunjungan.pengeluaran.ganti', [$kunjungan->nomor, $peng->id_pengeluaran]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-white border border-[#003399]/30 hover:bg-[#003399] text-[#003399] hover:text-white rounded-lg text-[11px] font-bold transition whitespace-nowrap">{{ $peng->status_ganti == 'Sudah Diganti' ? 'Batalkan' : 'Tandai Sudah Diganti' }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            @if($bolehUbah)
                                <td class="px-4 py-3 border-t border-slate-100">
                                    <div class="flex items-center gap-1">
                                        <button type="button" onclick="document.getElementById('modalEditPengeluaran-{{ $peng->id_pengeluaran }}').classList.remove('hidden')" class="p-1.5 rounded-lg text-slate-400 hover:text-[#003399] hover:bg-blue-50 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('kunjungan.pengeluaran.destroy', [$kunjungan->nomor, $peng->id_pengeluaran]) }}" method="POST" onsubmit="return confirm('Hapus pengeluaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $bolehUbah ? 7 : 6 }}" class="p-4 text-center text-slate-400 font-medium italic">Belum ada catatan pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($totalPengeluaran > 0)
                    <tfoot>
                        <tr class="bg-slate-50 font-bold border-t-2 border-slate-200">
                            <td colspan="2" class="px-4 py-3 border-t border-slate-100 text-right text-slate-800">TOTAL KESELURUHAN:</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-emerald-600 text-sm">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                            <td colspan="{{ $bolehUbah ? 4 : 3 }}"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        @if($kunjungan->pengeluaran->count() > 0)
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs">
                <p class="font-bold text-slate-700 mb-1.5">Total per Engineer:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($kunjungan->pengeluaran->groupBy(fn($p) => $p->engineer->user->nama ?? 'Tidak tercatat') as $namaPeng => $listPeng)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 border border-blue-100 text-[#003399] font-semibold">{{ $namaPeng }}: Rp {{ number_format($listPeng->sum('nominal'), 0, ',', '.') }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if($bolehUbah)
        @foreach($kunjungan->pengeluaran as $peng)
            <div id="modalEditPengeluaran-{{ $peng->id_pengeluaran }}" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Edit Pengeluaran</h3>
                    <form action="{{ route('kunjungan.pengeluaran.update', [$kunjungan->nomor, $peng->id_pengeluaran]) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs font-medium">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Jenis Biaya</label>
                            <input type="text" name="jenis_biaya" value="{{ $peng->jenis_biaya }}" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Dikeluarkan Oleh</label>
                            <select name="id_engineer" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                                <option value="">— Tidak diubah —</option>
                                @foreach($timKunjungan as $anggota)
                                    <option value="{{ $anggota->id_engineer }}" {{ $peng->id_engineer == $anggota->id_engineer ? 'selected' : '' }}>{{ $anggota->user->nama ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Nominal (Rp)</label>
                            <input type="number" name="nominal" value="{{ $peng->nominal }}" min="0" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Keterangan</label>
                            <input type="text" name="keterangan" value="{{ $peng->keterangan }}" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Ganti Bukti Nota (opsional)</label>
                            <input type="file" name="bukti_nota" accept="image/*,video/*" class="w-full text-slate-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800">
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modalEditPengeluaran-{{ $peng->id_pengeluaran }}').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                            <button type="submit" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Galeri Foto Lapangan -->
    <div class="p-5 md:p-6 rounded-xl bg-white border border-slate-200 shadow-sm">
        <h4 class="text-sm font-bold text-[#002266] mb-4">Galeri Dokumentasi On-Site</h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @forelse($kunjungan->dokumentasi as $doc)
                <div class="rounded-xl overflow-hidden bg-slate-50 border border-slate-200 shadow-sm">
                    @php $ext = strtolower(pathinfo($doc->file_foto, PATHINFO_EXTENSION)); $urlFoto = \App\Support\Uploads::url($doc->file_foto); @endphp
                    @if(in_array($ext, ['mp4','mov','3gp','webm']))
                        <video src="{{ $urlFoto }}" controls class="w-full h-32 object-cover bg-black"></video>
                    @else
                        {{-- QA ronde 2 #10: klik thumbnail -> lightbox ukuran besar --}}
                        <img src="{{ $urlFoto }}" alt="Dokumentasi" class="w-full h-32 object-cover cursor-zoom-in" onclick="bukaLightboxFoto('{{ $urlFoto }}', '{{ addslashes($doc->kategori_foto) }} — {{ addslashes($doc->keterangan ?? '') }}')">
                    @endif
                    <div class="p-2 text-[10px]">
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-[#003399] font-bold uppercase">{{ $doc->kategori_foto }}</span>
                        <p class="text-slate-600 mt-1.5 font-medium truncate">{{ $doc->keterangan ?? '-' }}</p>
                        @if($bolehUbah)
                            <div class="flex items-center gap-1 mt-1.5">
                                <button type="button" onclick="document.getElementById('modalEditDokumentasi-{{ $doc->id_dokumentasi }}').classList.remove('hidden')" class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-500 hover:text-[#003399] font-bold text-[10px]">Edit</button>
                                <form action="{{ route('kunjungan.dokumentasi.destroy', [$kunjungan->nomor, $doc->id_dokumentasi]) }}" method="POST" onsubmit="return confirm('Hapus dokumentasi ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded bg-white border border-slate-200 text-slate-500 hover:text-rose-600 font-bold text-[10px]">Hapus</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-6 text-slate-400 text-xs font-medium italic">Belum ada foto dokumentasi diunggah.</div>
            @endforelse
        </div>
    </div>

    {{-- QA ronde 2 #10: Lightbox foto dokumentasi --}}
    <div id="lightboxFoto" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[80] flex items-center justify-center p-4" onclick="if(event.target===this) tutupLightboxFoto()">
        <div class="max-w-3xl w-full">
            <div class="flex justify-between items-center mb-3">
                <p id="lightboxCaption" class="text-white text-sm font-semibold truncate pr-4"></p>
                <button type="button" onclick="tutupLightboxFoto()" class="p-1.5 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition text-3xl leading-none">&times;</button>
            </div>
            <img id="lightboxImg" src="" alt="Dokumentasi ukuran penuh" class="w-full max-h-[80vh] object-contain rounded-xl bg-black/40">
        </div>
    </div>
    <script>
        function bukaLightboxFoto(url, caption) {
            document.getElementById('lightboxImg').src = url;
            document.getElementById('lightboxCaption').innerText = caption || '';
            document.getElementById('lightboxFoto').classList.remove('hidden');
        }
        function tutupLightboxFoto() {
            document.getElementById('lightboxFoto').classList.add('hidden');
            document.getElementById('lightboxImg').src = '';
        }
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutupLightboxFoto(); });
    </script>

    @if($bolehUbah)
        @foreach($kunjungan->dokumentasi as $doc)
            <div id="modalEditDokumentasi-{{ $doc->id_dokumentasi }}" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Edit Dokumentasi</h3>
                    <form action="{{ route('kunjungan.dokumentasi.update', [$kunjungan->nomor, $doc->id_dokumentasi]) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs font-medium">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Kategori Foto</label>
                            <select name="kategori_foto" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                                @php $labelKatEdit = ['Sebelum' => 'Foto Sebelum Pengerjaan', 'Proses' => 'Foto Saat Pengerjaan', 'Sesudah' => 'Foto Setelah Selesai']; @endphp
                                @foreach($opsiKategoriFoto as $opKat)
                                    <option value="{{ $opKat }}" {{ $doc->kategori_foto == $opKat ? 'selected' : '' }}>{{ $labelKatEdit[$opKat] ?? ('Foto ' . $opKat) }}</option>
                                @endforeach
                                <option value="Lainnya" {{ $doc->kategori_foto == 'Lainnya' ? 'selected' : '' }}>Foto Lainnya / Kondisi Khusus</option>
                                @if(!in_array($doc->kategori_foto, array_merge($opsiKategoriFoto, ['Lainnya'])))
                                    <option value="{{ $doc->kategori_foto }}" selected>{{ $doc->kategori_foto }}</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Keterangan Foto <span class="text-rose-500 font-semibold">(wajib jika kategori Lainnya)</span></label>
                            <input type="text" name="keterangan" value="{{ $doc->keterangan }}" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Ganti File Foto/Video (opsional)</label>
                            <input type="file" name="foto" accept="image/*,video/*" class="w-full text-slate-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800">
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modalEditDokumentasi-{{ $doc->id_dokumentasi }}').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                            <button type="submit" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

</div>

<!-- MODAL INFO KHUSUS ALERT -->
<div id="modalInfoGPS" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-xs p-6 shadow-2xl text-center transition-all">
        <div id="modalInfoIcon" class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4">
        </div>
        <h3 id="modalInfoTitle" class="text-base font-bold text-[#002266] mb-2"></h3>
        <p id="modalInfoMessage" class="text-xs text-slate-600 mb-6 font-medium leading-relaxed"></p>
        <button type="button" onclick="document.getElementById('modalInfoGPS').classList.add('hidden')" class="w-full px-4 py-2.5 bg-[#002266] hover:bg-[#001233] text-white font-bold rounded-xl text-xs transition">
            Tutup
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    function showGPSModal(title, message, isSuccess) {
        document.getElementById('modalInfoTitle').innerText = title;
        document.getElementById('modalInfoMessage').innerText = message;
        
        const iconContainer = document.getElementById('modalInfoIcon');
        if(isSuccess) {
            iconContainer.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 bg-emerald-100 text-emerald-600 border border-emerald-200';
            iconContainer.innerHTML = '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        } else {
            iconContainer.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 bg-rose-100 text-rose-600 border border-rose-200';
            iconContainer.innerHTML = '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        }
        
        document.getElementById('modalInfoGPS').classList.remove('hidden');
    }

    function getGPSCheckIn() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const coords = `${position.coords.latitude}, ${position.coords.longitude}`;
                    document.getElementById('lokasi_gps_checkin').value = coords;
                    showConfirmModal(
                        document.getElementById('formCheckIn'),
                        'Konfirmasi Check-In',
                        `Koordinat Anda: ${coords}. Apakah Anda yakin ingin melakukan Check-In sekarang?`
                    );
                },
                (error) => {
                    showGPSModal('Gagal Membaca GPS', 'Pastikan GPS aktif dan izin lokasi (Location) di browser HP Anda telah diizinkan.', false);
                },
                { enableHighAccuracy: true }
            );
        } else {
            showGPSModal('Tidak Mendukung', 'Perangkat Anda tidak mendukung fitur geolokasi.', false);
        }
    }

    function getGPSCheckOut() {
        const catatan = document.getElementById('catatan_pekerjaan').value;
        if (!catatan.trim()) {
            showGPSModal('Deskripsi Kosong', 'Harap isi deskripsi hasil pekerjaan terlebih dahulu sebelum melakukan Check-Out!', false);
            return;
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const coords = `${position.coords.latitude}, ${position.coords.longitude}`;
                    document.getElementById('lokasi_gps_checkout').value = coords;
                    showConfirmModal(
                        document.getElementById('formCheckOut'),
                        'Konfirmasi Check-Out',
                        `Koordinat GPS Check-Out terdeteksi: ${coords}. Apakah Anda yakin ingin mengakhiri pekerjaan ini?`
                    );
                },
                (error) => {
                    showGPSModal('Gagal Membaca GPS', 'Pastikan GPS aktif dan izin lokasi di browser HP Anda telah diizinkan.', false);
                },
                { enableHighAccuracy: true }
            );
        } else {
            showGPSModal('Tidak Mendukung', 'Perangkat Anda tidak mendukung fitur geolokasi.', false);
        }
    }

    let signaturePad, signaturePadEngineer;
    // Draft TTD dari server (tersimpan otomatis), direstore agar tidak hilang saat refresh
    const draftTtdCustomer = @json($kunjungan->draft_ttd_customer ?? $kunjungan->laporan?->buktiPenyelesaian?->tanda_tangan_customer);
    const draftTtdEngineer = @json($kunjungan->draft_ttd_engineer ?? $kunjungan->laporan?->buktiPenyelesaian?->tanda_tangan_engineer);
    const kunjunganId = @json($kunjungan->nomor);
    const draftTimers = {};

    function initPad(canvasId, draftDataUrl) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        const pad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
        pad._draftUrl = draftDataUrl || null;
        // Flag isi manual: isEmpty() bawaan tidak mendeteksi gambar hasil restore
        pad._hasContent = !!draftDataUrl;
        // Flag coretan baru dari user (beda dari isi hasil restore/muat otomatis)
        pad._userDrew = false;
        pad._restoring = false;
        function restoreDraft() {
            if (!pad._draftUrl) return;
            pad._restoring = true;
            const img = new Image();
            img.onload = () => {
                try {
                    // Canvas sudah di-scale ratio; gambar pas 1:1 piksel
                    canvas.getContext('2d').drawImage(img, 0, 0, canvas.offsetWidth, canvas.offsetHeight);
                    pad._hasContent = true;
                } catch (e) { console.error(e); }
                pad._restoring = false;
            };
            img.onerror = () => { pad._restoring = false; };
            img.src = pad._draftUrl;
        }
        function resizeCanvas() {
            // Selamatkan coretan user yang belum sempat tersimpan jadi draft
            try {
                if (pad._userDrew && !pad.isEmpty() && canvas.offsetWidth > 0) {
                    pad._draftUrl = pad.toDataURL();
                }
            } catch (e) {}
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            pad.clear();
            restoreDraft();
        }
        // Simpan draft otomatis setiap selesai menggores (debounce 1 detik)
        pad.addEventListener('endStroke', () => {
            pad._hasContent = true;
            pad._userDrew = true;
            updateLewatiTtdState();
            const key = canvasId;
            clearTimeout(draftTimers[key]);
            draftTimers[key] = setTimeout(() => {
                saveDraft(canvasId === 'signaturePad' ? 'customer' : 'engineer');
            }, 1000);
        });
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();
        // Diekspos agar bisa dipanggil ulang saat form hidden dibuka (canvas 0px saat init)
        pad._resizeCanvas = resizeCanvas;
        return pad;
    }

    // Cek isi pad: gabungan flag manual + isEmpty() bawaan
    function padIsEmpty(pad) {
        return !(pad && (pad._hasContent || !pad.isEmpty()));
    }

    // Aturan: kalau ada TTD terisi di pad (customer/engineer), checkbox
    // "customer tidak mau tanda tangan" tidak bisa dicentang. Kalau sedang
    // dicentang lalu TTD digambar, centangnya otomatis lepas + checkbox dikunci.
    function updateLewatiTtdState() {
        const form = document.getElementById('signatureForm');
        if (!form) return;
        const cb = form.querySelector('[name="lewati_ttd"][type="checkbox"]');
        if (!cb) return;
        // Aturan: kalau ada TTD terisi di pad mana pun (termasuk TTD lama yang
        // dimuat otomatis di form revisi), checkbox "lewati TTD" tidak bisa
        // dicentang. Hapus dulu semua TTD ("Hapus & Ulangi") untuk membukanya.
        const adaIsi = !padIsEmpty(signaturePad) || !padIsEmpty(signaturePadEngineer);
        const label = cb.closest('label');
        if (adaIsi) {
            cb.checked = false;
            cb.disabled = true;
            cb.title = 'Tidak bisa dicentang karena sudah ada tanda tangan terisi';
            if (label) label.classList.add('opacity-50');
            const wrap = document.getElementById(cb.id === 'lewatiTtdRevisi' ? 'alasanTtdWrapRevisi' : 'alasanTtdWrap');
            if (wrap) wrap.classList.add('hidden');
        } else {
            cb.disabled = false;
            cb.title = '';
            if (label) label.classList.remove('opacity-50');
        }
    }

    // Kirim draft TTD ke server agar tidak hilang saat refresh
    async function saveDraft(which) {
        const isCustomer = which === 'customer';
        const pad = isCustomer ? signaturePad : signaturePadEngineer;
        // Jangan simpan saat gambar restore masih loading (dataURL belum lengkap)
        if (!pad || pad._restoring) return;
        const dataUrl = padIsEmpty(pad) ? null : pad.toDataURL();
        pad._draftUrl = dataUrl;
        const tokenEl = document.querySelector('#signatureForm input[name="_token"]');
        try {
            await fetch(`/kunjungan/${kunjunganId}/signature-draft`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tokenEl ? tokenEl.value : '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ type: which, signature: dataUrl }),
            });
        } catch (e) {
            console.error('Gagal menyimpan draft TTD:', e);
        }
    }

    // Draft teks Bagian 1 (Hasil Pekerjaan + Catatan Tambahan): konsep sama seperti
    // draft TTD — tersimpan otomatis ke server saat mengetik, direstore saat refresh.
    let laporanDraftTimer = null;
    function initLaporanDraft() {
        const hasilEl = document.getElementById('draftHasilPekerjaan');
        const catatanEl = document.getElementById('draftCatatanTambahan');
        const badge = document.getElementById('draftLaporanBadge');
        if (!hasilEl || !catatanEl) return;
        const simpan = () => {
            clearTimeout(laporanDraftTimer);
            laporanDraftTimer = setTimeout(async () => {
                const tokenEl = document.querySelector('#signatureForm input[name="_token"]');
                try {
                    const res = await fetch(`/kunjungan/${kunjunganId}/laporan-draft`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': tokenEl ? tokenEl.value : '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            hasil_pekerjaan: hasilEl.value,
                            catatan_tambahan: catatanEl.value,
                        }),
                    });
                    if (res.ok && badge) {
                        badge.classList.remove('hidden');
                        clearTimeout(badge._t);
                        badge._t = setTimeout(() => badge.classList.add('hidden'), 2500);
                    }
                } catch (e) {
                    console.error('Gagal menyimpan draft laporan:', e);
                }
            }, 1000);
        };
        hasilEl.addEventListener('input', simpan);
        catatanEl.addEventListener('input', simpan);
    }
    document.addEventListener('DOMContentLoaded', initLaporanDraft);

    // Kunci Bagian 1 (Hasil Pekerjaan + Catatan): konsep sama seperti kunci TTD.
    // Berlaku untuk form BUAT laporan maupun form REVISI laporan.
    // Terkunci = readonly (nilai tetap ikut ke-submit), badge + tombol ikut berubah.
    // Status kunci disimpan per-kunjungan agar tetap sama saat halaman di-refresh.
    const LOCK_FORMS_BAGIAN1 = {
        buat:   { hasil: 'draftHasilPekerjaan',   catatan: 'draftCatatanTambahan',   btn: 'lockBtnBagian1',       badge: 'lockBadgeBagian1',       key: 'bagian1_locked_' },
        revisi: { hasil: 'revisiHasilPekerjaan',  catatan: 'revisiCatatanTambahan',  btn: 'lockBtnBagian1Revisi', badge: 'lockBadgeBagian1Revisi', key: 'bagian1_revisi_locked_' },
    };
    function setLockBagian1(locked, form) {
        const cfg = LOCK_FORMS_BAGIAN1[form || 'buat'];
        const hasilEl = document.getElementById(cfg.hasil);
        const catatanEl = document.getElementById(cfg.catatan);
        const btn = document.getElementById(cfg.btn);
        const badge = document.getElementById(cfg.badge);
        if (!hasilEl || !catatanEl || !btn || !badge) return;
        hasilEl.readOnly = locked;
        catatanEl.readOnly = locked;
        [hasilEl, catatanEl].forEach(el => {
            el.classList.toggle('bg-slate-100', locked);
            el.classList.toggle('bg-white', !locked);
        });
        badge.textContent = locked ? '🔒 Terkunci' : '✏️ Mode Isi Data';
        badge.className = 'text-[11px] font-bold px-2 py-1 rounded-lg border ' + (locked ? 'text-slate-700 bg-slate-200 border-slate-300' : 'text-emerald-700 bg-emerald-50 border-emerald-200');
        btn.innerHTML = locked ? '🔓 Buka Kunci' : '🔒 Kunci';
        try { localStorage.setItem(cfg.key + kunjunganId, locked ? '1' : '0'); } catch (e) {}
    }
    function toggleLockBagian1(form) {
        form = form || 'buat';
        const cfg = LOCK_FORMS_BAGIAN1[form];
        const hasilEl = document.getElementById(cfg.hasil);
        if (!hasilEl) return;
        setLockBagian1(!hasilEl.readOnly, form);
    }
    document.addEventListener('DOMContentLoaded', () => {
        ['buat', 'revisi'].forEach(form => {
            const cfg = LOCK_FORMS_BAGIAN1[form];
            let locked = false;
            try { locked = localStorage.getItem(cfg.key + kunjunganId) === '1'; } catch (e) {}
            setLockBagian1(locked, form);
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        signaturePad = initPad('signaturePad', draftTtdCustomer);
        signaturePadEngineer = initPad('signaturePadEngineer', draftTtdEngineer);
        // Default terkunci agar scroll HP tidak mencoret TTD tanpa sengaja
        setPadLock('customer', true);
        setPadLock('engineer', true);
        // Kunci checkbox lewati-TTD jika draft TTD sudah terisi
        updateLewatiTtdState();
    });

    // Buka/tutup form laporan: ukur ulang canvas TTD setelah form tampil
    // (canvas di dalam form hidden berukuran 0px saat init)
    function toggleFormLaporan(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('hidden');
        if (!el.classList.contains('hidden')) {
            if (signaturePad && signaturePad._resizeCanvas) signaturePad._resizeCanvas();
            if (signaturePadEngineer && signaturePadEngineer._resizeCanvas) signaturePadEngineer._resizeCanvas();
        }
    }
    // Dari menu Laporan (tombol Revisi pimpinan): langsung buka form revisi
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get('buka') === 'revisi') {
            const el = document.getElementById('formRevisiLaporan');
            if (el && el.classList.contains('hidden')) toggleFormLaporan('formRevisiLaporan');
            const target = document.getElementById('formRevisiLaporan');
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    function setPadLock(which, locked) {
        const isCustomer = which === 'customer';
        const pad = isCustomer ? signaturePad : signaturePadEngineer;
        const wrap = document.getElementById(isCustomer ? 'sigWrapCustomer' : 'sigWrapEngineer');
        const btn = document.getElementById(isCustomer ? 'lockBtnCustomer' : 'lockBtnEngineer');
        const badge = document.getElementById(isCustomer ? 'sigBadgeCustomer' : 'sigBadgeEngineer');
        if (!pad || !wrap || !btn || !badge) return;
        wrap.dataset.locked = locked ? '1' : '0';
        if (locked) { pad.off(); saveDraft(which); } else { pad.on(); }
        wrap.classList.toggle('touch-none', !locked);
        wrap.classList.toggle('touch-pan-x', locked);
        wrap.classList.toggle('touch-pan-y', locked);
        badge.textContent = locked ? '🔒 Terkunci' : '🔓 Mode Tanda Tangan';
        badge.className = 'px-2 py-1 rounded-lg text-[11px] font-bold border ' + (locked ? 'text-slate-700 bg-slate-200 border-slate-300' : 'text-emerald-700 bg-emerald-50 border-emerald-200');
        btn.innerHTML = locked ? '🔓 Buka Kunci TTD' : '🔒 Kunci TTD';
    }

    function togglePadLock(which) {
        const isCustomer = which === 'customer';
        const wrap = document.getElementById(isCustomer ? 'sigWrapCustomer' : 'sigWrapEngineer');
        if (!wrap) return;
        setPadLock(which, wrap.dataset.locked !== '1');
    }

    function clearSignature() {
        if (signaturePad) { signaturePad.clear(); signaturePad._hasContent = false; signaturePad._userDrew = false; saveDraft('customer'); }
        updateLewatiTtdState();
    }

    function clearSignatureEngineer() {
        if (signaturePadEngineer) { signaturePadEngineer.clear(); signaturePadEngineer._hasContent = false; signaturePadEngineer._userDrew = false; saveDraft('engineer'); }
        updateLewatiTtdState();
    }

    function submitSignature() {
        if (padIsEmpty(signaturePad)) {
            showGPSModal('Tanda Tangan Kosong', 'Customer belum membubuhkan tanda tangan. Silakan isi terlebih dahulu pada kotak TTD Customer.', false);
            return;
        }
        if (padIsEmpty(signaturePadEngineer)) {
            showGPSModal('Tanda Tangan Kosong', 'Engineer belum membubuhkan tanda tangan. Silakan isi terlebih dahulu pada kotak TTD Engineer.', false);
            return;
        }
        document.getElementById('signatureInput').value = signaturePad.toDataURL();
        document.getElementById('signatureEngineerInput').value = signaturePadEngineer.toDataURL();
        showConfirmModal(
            document.getElementById('signatureForm'),
            'Kunci Laporan',
            'Dengan menandatangani dokumen ini, pekerjaan dinyatakan selesai secara resmi. Lanjutkan?'
        );
    }

    // Submit gabungan Bagian 1 (data) + Bagian 2 (TTD) untuk Kelola Laporan Kunjungan
    function submitLaporanLengkap(isRevisi) {
        const form = document.getElementById('signatureForm');
        if (!form) return;
        const hasil = form.querySelector('[name="hasil_pekerjaan"]');
        if (!hasil || !hasil.value.trim()) {
            showGPSModal('Data Belum Lengkap', 'Isi dulu Hasil Pekerjaan pada Bagian 1.', false);
            if (hasil) hasil.focus();
            return;
        }
        const lewati = form.querySelector('[name="lewati_ttd"]');
        const alasan = form.querySelector('[name="alasan_tanpa_ttd"]');
        const sigInput = document.getElementById('signatureInput');
        const sigEngInput = document.getElementById('signatureEngineerInput');
        if (lewati && lewati.checked) {
            if (!alasan || !alasan.value.trim()) {
                showGPSModal('Alasan Wajib Diisi', 'Wajib isi alasan jika tanda tangan customer dilewati.', false);
                if (alasan) alasan.focus();
                return;
            }
            sigInput.value = '';
            sigEngInput.value = '';
        } else {
            // TTD lama yang dimuat ke pad butuh waktu sepersekian detik (async);
            // jangan submit dulu sampai selesai dimuat agar tidak tertimpa gambar kosong.
            if ((signaturePad && signaturePad._restoring) || (signaturePadEngineer && signaturePadEngineer._restoring)) {
                showGPSModal('Mohon Tunggu', 'Tanda tangan masih dimuat, coba tekan lagi sebentar.', false);
                return;
            }
            const custEmpty = padIsEmpty(signaturePad);
            const engEmpty = padIsEmpty(signaturePadEngineer);
            if (isRevisi) {
                // Revisi: tiap pad independen. Hanya pad yang DIGAMBAR ULANG
                // (_userDrew) yang dikirim untuk mengganti TTD lama; pad yang
                // dibiarkan (termasuk TTD lama hasil muat otomatis) = pertahankan.
                // Ini juga menghindari re-encode PNG raksasa yang tidak perlu.
                sigInput.value = (custEmpty || !signaturePad._userDrew) ? '' : signaturePad.toDataURL();
                sigEngInput.value = (engEmpty || !signaturePadEngineer._userDrew) ? '' : signaturePadEngineer.toDataURL();
            } else {
                // Buat baru: cukup salah satu TTD terisi (satunya bisa menyusul lewat revisi)
                if (custEmpty && engEmpty) {
                    showGPSModal('Tanda Tangan Belum Lengkap', 'Isi minimal satu tanda tangan (customer atau engineer), atau centang Lewati TTD beserta alasannya.', false);
                    return;
                }
                sigInput.value = custEmpty ? '' : signaturePad.toDataURL();
                sigEngInput.value = engEmpty ? '' : signaturePadEngineer.toDataURL();
            }
        }
        showConfirmModal(
            form,
            isRevisi ? 'Simpan Revisi Laporan' : 'Buat Laporan Sekarang',
            isRevisi
                ? 'Simpan perubahan laporan? PDF akan otomatis mengikuti data terbaru.'
                : 'Buat laporan kunjungan sekarang? Pastikan data Bagian 1 dan tanda tangan Bagian 2 sudah benar.'
        );
    }
</script>

    <!-- MODAL PREVIEW PDF -->
    <div id="modalPreviewPdf" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" onclick="tutupPreviewPdf()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
                <h4 class="text-sm font-bold text-[#002266]">👁️ Preview Laporan PDF</h4>
                <button type="button" onclick="tutupPreviewPdf()" class="text-slate-400 hover:text-slate-700 text-xl leading-none">✕</button>
            </div>
            <div class="flex-1 overflow-y-auto bg-slate-200 p-3 max-h-[60vh]" id="pdfPreviewBody">
                <div id="pdfPreviewLoading" class="text-center py-10">
                    <p class="text-sm font-bold text-slate-600">⏳ Memuat preview...</p>
                </div>
                <div id="pdfPreviewImages" class="space-y-3"></div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 px-5 py-4 border-t border-slate-200">
                <a id="btnDownloadPdf" href="" class="flex-1 px-4 py-2.5 bg-[#003399] hover:bg-[#002266] text-white rounded-xl text-xs font-bold text-center transition">⬇️ Download PDF</a>
                <button type="button" onclick="tutupPreviewPdf()" class="flex-1 px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition">Tutup</button>
            </div>
        </div>
    </div>
    <script>
        async function bukaPreviewPdf() {
            const t = Date.now();
            document.getElementById('btnDownloadPdf').href = "{{ route('laporan.pdf', $kunjungan->nomor) }}?download=1&t=" + t;
            document.getElementById('modalPreviewPdf').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            const loading = document.getElementById('pdfPreviewLoading');
            const wrap = document.getElementById('pdfPreviewImages');
            loading.style.display = 'block';
            wrap.innerHTML = '';
            try {
                const res = await fetch("{{ route('laporan.preview', $kunjungan->nomor) }}?t=" + t);
                const data = await res.json();
                loading.style.display = 'none';
                if (data.images && data.images.length) {
                    data.images.forEach(src => {
                        const img = document.createElement('img');
                        img.src = src;
                        img.className = 'w-full rounded-lg shadow-md border border-slate-300';
                        img.alt = 'Preview PDF';
                        wrap.appendChild(img);
                    });
                } else if (data.pdf_url) {
                    const frame = document.createElement('iframe');
                    frame.src = data.pdf_url;
                    frame.className = 'w-full rounded-lg shadow-md border border-slate-300 bg-white';
                    frame.style.height = '60vh';
                    frame.title = 'Preview PDF';
                    wrap.appendChild(frame);
                } else {
                    wrap.innerHTML = '<p class="text-center text-sm text-rose-600 font-bold py-6">Gagal memuat preview.</p>';
                }
            } catch (e) {
                loading.style.display = 'none';
                wrap.innerHTML = '<p class="text-center text-sm text-rose-600 font-bold py-6">Gagal memuat preview.</p>';
            }
        }
        function tutupPreviewPdf() {
            document.getElementById('modalPreviewPdf').classList.add('hidden');
            document.body.style.overflow = '';
        }
    </script>

@endsection