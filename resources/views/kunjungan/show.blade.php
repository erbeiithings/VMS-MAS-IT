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
    <div class="p-5 md:p-6 rounded-2xl bg-gradient-to-r from-[#002266] to-[#0044cc] shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="font-mono text-xs md:text-sm font-bold text-white bg-white/20 px-2 py-1 rounded-lg">{{ $kunjungan->nomor }}</span>
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-white text-[#002266] shadow-sm">
                    {{ $kunjungan->status }}
                </span>
            </div>
            <h2 class="text-lg md:text-xl font-bold text-white mt-3">{{ $kunjungan->pekerjaan }}</h2>
            <p class="text-xs text-blue-200 mt-1 font-medium">{{ $kunjungan->customer->nama_perusahaan ?? '-' }} • {{ $kunjungan->lokasi }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($kunjungan->status == 'Selesai' || $kunjungan->laporan)
                <a href="{{ route('laporan.pdf', $kunjungan->id_kunjungan) }}" target="_blank" 
                   class="px-4 py-2.5 bg-white hover:bg-slate-100 text-[#002266] rounded-xl text-xs font-bold flex items-center gap-2 shadow-md transition">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Cetak PDF Laporan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Stepper Status Kunjungan (4 Steps) -->
    <div class="p-4 md:p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Status Alur Kunjungan</h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
            <div class="p-2.5 rounded-xl font-bold {{ in_array($kunjungan->status, ['Terjadwal', 'Dikonfirmasi', 'Dikerjakan', 'Selesai']) ? 'bg-blue-50 border border-blue-200 text-[#003399]' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                1. Terima / Tolak
            </div>
            <div class="p-2.5 rounded-xl font-bold {{ in_array($kunjungan->status, ['Dikonfirmasi', 'Dikerjakan', 'Selesai']) ? 'bg-blue-50 border border-blue-200 text-[#003399]' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                2. Check-in (GPS)
            </div>
            <div class="p-2.5 rounded-xl font-bold {{ in_array($kunjungan->status, ['Dikerjakan', 'Selesai']) ? 'bg-blue-50 border border-blue-200 text-[#003399]' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                3. Pekerjaan & Check-out
            </div>
            <div class="p-2.5 rounded-xl font-bold {{ $kunjungan->status == 'Selesai' ? 'bg-emerald-50 border border-emerald-200 text-emerald-600' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                4. TTD Customer
            </div>
        </div>
    </div>

    <!-- LOGIKA PEMISAH TAMPILAN EKSKLUSIF TTD -->
    @php
        // Cek apakah laporan sudah terbuat tapi belum di TTD (Masa Tunggu TTD)
        $isWaitingForSignature = $kunjungan->laporan && (!$kunjungan->laporan->buktiPenyelesaian || $kunjungan->laporan->buktiPenyelesaian->status != 'Ditandatangani');
        // Cek apakah pekerjaan sedang jalan tapi belum di checkout
        $isDikerjakanBelumCheckout = $kunjungan->status == 'Dikerjakan' && !$kunjungan->laporan;
    @endphp

    @if($isWaitingForSignature)
        <!-- ========================================== -->
        <!-- TAMPILAN EKSKLUSIF KHUSUS TANDA TANGAN -->
        <!-- ========================================== -->
        <div class="p-5 md:p-8 rounded-3xl bg-amber-50 border-2 border-amber-300 shadow-xl space-y-6 mt-8">
            <div class="text-center space-y-2 border-b border-amber-200 pb-4">
                <span class="inline-flex w-12 h-12 rounded-full bg-amber-100 text-amber-600 items-center justify-center mb-2 shadow-sm border border-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </span>
                <h4 class="text-lg font-black text-amber-900 uppercase tracking-wide">Tahap Akhir: Pengesahan & Verifikasi</h4>
                <p class="text-xs text-amber-800 font-medium">Laporan kunjungan nomor <strong>{{ $kunjungan->nomor }}</strong> telah siap. Silakan lengkapi tanda tangan digital Engineer dan Customer ({{ $kunjungan->customer->pic ?? 'PIC' }}) untuk menyegel laporan ini.</p>
            </div>
            
            <form id="signatureForm" action="{{ route('kunjungan.signature', $kunjungan->id_kunjungan) }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="signature_engineer" id="signatureInputEngineer">
                <input type="hidden" name="signature_customer" id="signatureInputCustomer">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- TTD ENGINEER -->
                    <div class="space-y-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-center">
                            <h5 class="text-xs font-bold text-[#002266]">1. TTD Lead Engineer</h5>
                            <button type="button" onclick="clearSignature('engineer')" class="text-[10px] text-rose-600 font-bold hover:underline">Hapus</button>
                        </div>
                        <p class="text-[10px] text-slate-500 font-medium">{{ $kunjungan->engineer->user->nama ?? 'Nama Engineer' }}</p>
                        <div class="border-2 border-slate-300 rounded-xl overflow-hidden bg-slate-50 touch-none">
                            <canvas id="signaturePadEngineer" class="w-full h-48 block cursor-crosshair"></canvas>
                        </div>
                    </div>

                    <!-- TTD CUSTOMER -->
                    <div class="space-y-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-center">
                            <h5 class="text-xs font-bold text-[#002266]">2. TTD Customer / Klien</h5>
                            <button type="button" onclick="clearSignature('customer')" class="text-[10px] text-rose-600 font-bold hover:underline">Hapus</button>
                        </div>
                        <p class="text-[10px] text-slate-500 font-medium">{{ $kunjungan->customer->pic ?? 'PIC Perusahaan' }} ({{ $kunjungan->customer->nama_perusahaan ?? '-' }})</p>
                        <div class="border-2 border-slate-300 rounded-xl overflow-hidden bg-slate-50 touch-none">
                            <canvas id="signaturePadCustomer" class="w-full h-48 block cursor-crosshair"></canvas>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-amber-200 flex justify-end">
                    <button type="button" onclick="submitDualSignature()" class="w-full md:w-auto px-8 py-3.5 bg-[#002266] hover:bg-[#001233] text-white font-bold rounded-xl text-sm shadow-xl shadow-blue-900/30 transition transform hover:scale-[1.02]">
                        Sahkan & Kunci Laporan Resmi
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- ========================================== -->
        <!-- TAMPILAN NORMAL (TIDAK DALAM TAHAP TTD) -->
        <!-- ========================================== -->

        <!-- SECTION 1: MAPS TARGET & AKSI KONFIRMASI -->
        @if(in_array($kunjungan->status, ['Terjadwal', 'Dikonfirmasi']))
            @php
                $targetLat = $kunjungan->site->latitude ?? ($kunjungan->customer->latitude ?? null);
                $targetLng = $kunjungan->site->longitude ?? ($kunjungan->customer->longitude ?? null);
                $locationQuery = ($targetLat && $targetLng) ? "{$targetLat},{$targetLng}" : urlencode($kunjungan->lokasi);
            @endphp

            <div class="rounded-2xl border border-slate-200 shadow-sm overflow-hidden bg-white">
                <div class="w-full h-72 bg-slate-100">
                    <iframe class="w-full h-full border-0" loading="lazy" allowfullscreen src="https://maps.google.com/maps?q={{ $locationQuery }}&z=15&output=embed"></iframe>
                </div>
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div>
                        <h4 class="text-sm font-bold text-[#002266]">📍 Target Lokasi Kunjungan</h4>
                        <p class="text-xs text-slate-600 font-medium mt-0.5">{{ $kunjungan->site->nama_site ?? $kunjungan->customer->nama_perusahaan ?? 'Lokasi Tujuan' }} — {{ $kunjungan->lokasi }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $locationQuery }}" target="_blank" class="px-3 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">🗺️ Buka Maps</a>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $locationQuery }}" target="_blank" class="px-3 py-2 bg-[#0044cc] hover:bg-[#003399] text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">🧭 Navigasi Rute</a>
                    </div>
                </div>

                <div class="p-5">
                    @if(Auth::user()->id_role == 3)
                        @if($kunjungan->status == 'Terjadwal')
                            <div class="p-5 rounded-xl bg-amber-50 border border-amber-200 space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-bold text-amber-900">⚡ Konfirmasi Penugasan Jadwal</h4>
                                    <span class="text-xs font-semibold bg-amber-200 text-amber-800 px-2.5 py-0.5 rounded-full">Menunggu Konfirmasi</span>
                                </div>
                                <p class="text-xs text-amber-800 font-medium">Silakan konfirmasi penerimaan penugasan ini sebelum menuju lokasi kerja.</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                                    <form action="{{ route('kunjungan.terima', $kunjungan->id_kunjungan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center gap-2">✅ Terima & Konfirmasi Jadwal</button>
                                    </form>
                                    <form action="{{ route('kunjungan.reschedule', $kunjungan->id_kunjungan) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="text" name="alasan_reschedule" required placeholder="Alasan Tolak (Contoh: Jadwal Bentrok)" class="w-full px-3 py-2 bg-white border border-slate-300 focus:border-rose-400 focus:ring-rose-400 rounded-xl text-xs text-slate-800">
                                            <button type="button" onclick="if(!this.form.checkValidity()) { this.form.reportValidity(); return; } showConfirmModal(this.form, 'Tolak & Reschedule', 'Apakah Anda yakin ingin menolak jadwal ini?')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition whitespace-nowrap">❌ Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif($kunjungan->status == 'Dikonfirmasi')
                            <div class="p-5 rounded-xl bg-blue-50 border border-blue-200 text-center space-y-3">
                                <h4 class="text-sm font-bold text-[#002266]">Sudah Tiba di Lokasi Klien?</h4>
                                <p class="text-xs text-slate-600 font-medium">Klik tombol di bawah ini untuk mencatat koordinat GPS dan memulai pengerjaan.</p>
                                <form id="formCheckIn" action="{{ route('kunjungan.checkin', $kunjungan->id_kunjungan) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="lokasi_gps" id="lokasi_gps_checkin">
                                    <button type="button" onclick="getGPSCheckIn()" class="w-full max-w-md mx-auto px-4 py-3 bg-[#002266] hover:bg-[#001233] text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-900/20 transition">📍 Ambil Lokasi GPS & Check-In</button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        <!-- Alert Status Reschedule -->
        @if($kunjungan->status == 'Reschedule')
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex flex-col gap-1 shadow-sm">
                <span class="font-bold">⚠️ Status: Jadwal Perlu Dijadwalkan Ulang (Reschedule)</span>
                <p class="font-medium">Alasan penolakan dari Engineer: "{{ $kunjungan->alasan_reschedule }}"</p>
            </div>
        @endif

        <!-- TAHAP PEKERJAAN ON-SITE (HANYA TAMPIL SEBELUM CHECKOUT) -->
        @if($isDikerjakanBelumCheckout && Auth::user()->id_role == 3)
            <div class="p-5 md:p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
                <h4 class="text-sm font-bold text-[#002266] flex items-center gap-2">📷 Unggah Dokumentasi Lapangan (On-Site)</h4>
                <form action="{{ route('kunjungan.dokumentasi', $kunjungan->id_kunjungan) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-medium">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Kategori Foto</label>
                            <select name="kategori_foto" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:ring-[#003399]">
                                <option value="Sebelum">Foto Sebelum Pengerjaan</option>
                                <option value="Proses">Foto Saat Pengerjaan</option>
                                <option value="Sesudah">Foto Setelah Selesai</option>
                                <option value="Lainnya">Foto Lainnya / Kondisi Khusus</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Pilih File Foto</label>
                            <input type="file" name="foto" accept="image/*" capture="environment" required class="w-full text-slate-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#002266] file:text-white hover:file:bg-[#001233]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1.5 font-bold">Keterangan Foto</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Kondisi port switch" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:ring-[#003399]">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-[#0044cc] hover:bg-[#003399] text-white rounded-xl font-bold shadow-md">Unggah Foto Dokumentasi</button>
                </form>
            </div>

            <div class="p-5 md:p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
                <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">💸 Catat Pengeluaran Operasional</h4>
                <form action="{{ route('kunjungan.pengeluaran', $kunjungan->id_kunjungan) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-medium">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Jenis Biaya</label>
                            <select name="jenis_biaya" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:ring-[#003399]">
                                <option value="Bensin">Bensin / BBM</option>
                                <option value="Tol">Tol</option>
                                <option value="Parkir">Parkir</option>
                                <option value="Makan">Makan (Konsumsi)</option>
                                <option value="Pembelian Alat">Pembelian Alat Dadakan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Nominal (Rp)</label>
                            <input type="number" name="nominal" min="0" required placeholder="Contoh: 50000" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:ring-[#003399]">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Bukti Foto Nota / Struk (Opsional)</label>
                            <input type="file" name="bukti_nota" accept="image/*" capture="environment" class="w-full text-slate-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300">
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 font-bold">Keterangan Tambahan</label>
                            <input type="text" name="keterangan" placeholder="Contoh: Beli kabel LAN" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:ring-[#003399]">
                        </div>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-bold shadow-md">Simpan Pengeluaran</button>
                </form>
            </div>

            <div class="p-5 md:p-6 rounded-2xl bg-blue-50 border border-blue-200 shadow-sm space-y-4">
                <h4 class="text-sm font-bold text-[#002266]">📝 Input Catatan & Check-Out</h4>
                <p class="text-xs text-slate-600 font-medium">Tuliskan ringkasan hasil pengerjaan. Sistem akan memverifikasi lokasi GPS Anda untuk proses Check-Out.</p>
                <form id="formCheckOut" action="{{ route('kunjungan.checkout', $kunjungan->id_kunjungan) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="lokasi_gps" id="lokasi_gps_checkout"> 
                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">Deskripsi / Hasil Pekerjaan Lapangan:</label>
                        <textarea name="catatan" id="catatan_pekerjaan" rows="4" required placeholder="Contoh: Pemeliharaan berkala server selesai 100%." class="w-full p-3 bg-white border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]"></textarea>
                    </div>
                    <button type="button" onclick="getGPSCheckOut()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition">
                        📍 Ambil GPS Check-Out & Buat Laporan
                    </button>
                </form>
            </div>
        @endif

        <!-- BUKTI DOKUMEN (HANYA TAMPIL SETELAH SELESAI TTD) -->
        @if($kunjungan->status == 'Selesai' && $kunjungan->laporan && $kunjungan->laporan->buktiPenyelesaian && $kunjungan->laporan->buktiPenyelesaian->status == 'Ditandatangani')
            <div class="p-5 md:p-6 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
                <div class="text-center md:text-left">
                    <h4 class="text-sm font-bold text-emerald-700">Pekerjaan Selesai & Dokumen Terverifikasi Resmi</h4>
                    <p class="text-xs text-emerald-600 font-medium mt-1">Disahkan pada: {{ $kunjungan->laporan->buktiPenyelesaian->tanggal_tanda_tangan }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm text-center">
                        <span class="block text-[9px] text-slate-500 font-bold mb-1">Engineer</span>
                        <img src="{{ $kunjungan->laporan->buktiPenyelesaian->tanda_tangan_engineer }}" alt="Engineer Signature" class="h-10 object-contain mx-auto">
                    </div>
                    <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm text-center">
                        <span class="block text-[9px] text-slate-500 font-bold mb-1">Customer</span>
                        <img src="{{ $kunjungan->laporan->buktiPenyelesaian->tanda_tangan_customer }}" alt="Customer Signature" class="h-10 object-contain mx-auto">
                    </div>
                </div>
            </div>
        @endif

        <!-- DETAIL TIKET, LOG, PENGELUARAN & GALERI (Tampil Kapan Saja KECUALI saat TTD) -->
        <div class="p-5 md:p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3 text-xs mb-6 mt-6">
            <h4 class="text-sm font-bold text-[#002266] uppercase tracking-wider border-b border-slate-100 pb-3 mb-2">Detail Informasi Tiket</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-medium text-slate-600">
                <div class="space-y-2">
                    <p><strong class="text-slate-800">Customer:</strong> {{ $kunjungan->customer->nama_perusahaan ?? '-' }}</p>
                    <p><strong class="text-slate-800">PIC:</strong> {{ $kunjungan->customer->pic ?? '-' }} ({{ $kunjungan->customer->telepon ?? '-' }})</p>
                    <p><strong class="text-slate-800">Lead Engineer:</strong> {{ $kunjungan->engineer->user->nama ?? 'Belum Ditugaskan' }}</p>
                </div>
                <div class="space-y-2">
                    <p><strong class="text-slate-800">Tim Support:</strong> 
                        @if($kunjungan->supportEngineers->count() > 0)
                            {{ $kunjungan->supportEngineers->pluck('user.nama')->implode(', ') }}
                        @else
                            <span class="italic text-slate-400">Tidak ada tim support</span>
                        @endif
                    </p>
                    <p><strong class="text-slate-800">Alat Kerja Terbawa:</strong></p>
                    <ul class="list-disc list-inside pl-2">
                        @forelse($kunjungan->tools as $tool)
                            <li>{{ $tool->nama_alat }} ({{ $tool->kode }})</li>
                        @empty
                            <li class="italic text-slate-400">Tidak ada tools khusus</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="p-5 md:p-6 rounded-2xl bg-white border border-slate-200 shadow-sm text-xs mb-6">
            <h4 class="text-sm font-bold text-[#002266] uppercase tracking-wider border-b border-slate-100 pb-3 mb-4">Log Waktu & Lokasi GPS Absensi</h4>
            @php 
                $act = $kunjungan->aktivitas->last(); 
                $lokasiCheckin = ($kunjungan->check_in_latitude && $kunjungan->check_in_longitude) ? $kunjungan->check_in_latitude . ',' . $kunjungan->check_in_longitude : null;
                $lokasiCheckout = ($kunjungan->check_out_latitude && $kunjungan->check_out_longitude) ? $kunjungan->check_out_latitude . ',' . $kunjungan->check_out_longitude : null;
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Visual GPS Check-In -->
                <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h5 class="font-bold text-slate-800 flex items-center gap-2">📍 Lokasi Check-in</h5>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-bold shadow-sm">{{ $act?->waktu_mulai ?? 'Belum ada data' }}</span>
                    </div>
                    @if($lokasiCheckin)
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-300 shadow-inner">
                            <iframe class="w-full h-full border-0" loading="lazy" src="https://maps.google.com/maps?q={{ urlencode($lokasiCheckin) }}&z=15&output=embed"></iframe>
                        </div>
                        <p class="font-mono text-[10px] text-slate-500 text-center font-medium mt-1">Koordinat: {{ $lokasiCheckin }}</p>
                    @else
                        <div class="w-full h-48 rounded-xl bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-400 italic font-medium shadow-inner">Belum Check-in</div>
                    @endif
                </div>

                <!-- Visual GPS Check-Out -->
                <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h5 class="font-bold text-slate-800 flex items-center gap-2">🚩 Lokasi Check-out</h5>
                        <span class="px-2.5 py-1 {{ $act?->waktu_selesai ? 'bg-blue-100 text-[#003399] border border-blue-200' : 'bg-slate-200 text-slate-500 border border-slate-300' }} rounded-lg text-[10px] font-bold shadow-sm">{{ $act?->waktu_selesai ?? 'Belum ada data' }}</span>
                    </div>
                    @if($lokasiCheckout)
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-300 shadow-inner">
                            <iframe class="w-full h-full border-0" loading="lazy" src="https://maps.google.com/maps?q={{ urlencode($lokasiCheckout) }}&z=15&output=embed"></iframe>
                        </div>
                        <p class="font-mono text-[10px] text-slate-500 text-center font-medium mt-1">Koordinat: {{ $lokasiCheckout }}</p>
                    @else
                        <div class="w-full h-48 rounded-xl bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-400 italic font-medium shadow-inner">Belum Check-out</div>
                    @endif
                </div>
            </div>
            <div class="mt-5 p-4 bg-blue-50/50 border border-blue-100 rounded-xl shadow-sm">
                <p><strong class="text-[#002266] flex items-center gap-2">📝 Catatan Pekerjaan Engineer:</strong></p>
                <p class="text-slate-700 font-medium mt-2 leading-relaxed whitespace-pre-wrap">{{ $act?->catatan ?? 'Belum ada catatan laporan pengerjaan diisi oleh Engineer.' }}</p>
            </div>
        </div>

        <div class="p-5 md:p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <h4 class="text-sm font-bold text-[#002266] mb-4">Rincian Pengeluaran Operasional</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3 font-bold">Jenis Biaya</th>
                            <th class="p-3 font-bold">Nominal (Rp)</th>
                            <th class="p-3 font-bold">Keterangan</th>
                            <th class="p-3 font-bold">Bukti Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $totalPengeluaran = 0; @endphp
                        @forelse($kunjungan->pengeluaran as $peng)
                            @php $totalPengeluaran +=$peng->nominal; @endphp
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="p-3 font-bold text-slate-800">{{ $peng->jenis_biaya }}</td>
                                <td class="p-3 text-emerald-600 font-bold">Rp {{ number_format($peng->nominal, 0, ',', '.') }}</td>
                                <td class="p-3">{{ $peng->keterangan ?? '-' }}</td>
                                <td class="p-3">
                                    @if($peng->bukti_nota)
                                        <a href="{{ asset($peng->bukti_nota) }}" target="_blank" class="text-[#0044cc] hover:underline font-bold">Lihat Foto</a>
                                    @else
                                        <span class="text-slate-400 italic">Tidak ada struk</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-slate-400 font-medium italic">Belum ada catatan pengeluaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($totalPengeluaran > 0)
                        <tfoot>
                            <tr class="bg-slate-50 font-bold border-t-2 border-slate-200">
                                <td class="p-3 text-right text-slate-800">TOTAL KESELURUHAN:</td>
                                <td class="p-3 text-emerald-600 text-sm">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="p-5 md:p-6 rounded-2xl bg-white border border-slate-200 shadow-sm mt-6">
            <h4 class="text-sm font-bold text-[#002266] mb-4">Galeri Dokumentasi On-Site</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @forelse($kunjungan->dokumentasi as $doc)
                    <div class="rounded-xl overflow-hidden bg-slate-50 border border-slate-200 shadow-sm">
                        <img src="{{ asset($doc->file_foto) }}" alt="Dokumentasi" class="w-full h-32 object-cover">
                        <div class="p-2 text-[10px]">
                            <span class="px-2 py-0.5 rounded bg-blue-100 text-[#003399] font-bold uppercase">{{ $doc->kategori_foto }}</span>
                            <p class="text-slate-600 mt-1.5 font-medium truncate">{{ $doc->keterangan ?? '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-6 text-slate-400 text-xs font-medium italic">Belum ada foto dokumentasi diunggah.</div>
                @endforelse
            </div>
        </div>

    @endif <!-- PENUTUP IF UTAMA -->

</div>

<!-- MODAL INFO KHUSUS ALERT -->
<div id="modalInfoGPS" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-xs p-6 shadow-2xl text-center transition-all">
        <div id="modalInfoIcon" class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4"></div>
        <h3 id="modalInfoTitle" class="text-base font-bold text-[#002266] mb-2"></h3>
        <p id="modalInfoMessage" class="text-xs text-slate-600 mb-6 font-medium leading-relaxed"></p>
        <button type="button" onclick="document.getElementById('modalInfoGPS').classList.add('hidden')" class="w-full px-4 py-2.5 bg-[#002266] hover:bg-[#001233] text-white font-bold rounded-xl text-xs transition">Tutup</button>
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

    let padEngineer, padCustomer;
    
    document.addEventListener('DOMContentLoaded', () => {
        const canvasEng = document.getElementById('signaturePadEngineer');
        const canvasCust = document.getElementById('signaturePadCustomer');
        
        const padOptions = { backgroundColor: 'rgb(248, 250, 252)', penColor: 'rgb(15, 23, 42)' };

        if (canvasEng) padEngineer = new SignaturePad(canvasEng, padOptions);
        if (canvasCust) padCustomer = new SignaturePad(canvasCust, padOptions);

        function resizeCanvases() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            if(canvasEng) {
                canvasEng.width = canvasEng.offsetWidth * ratio;
                canvasEng.height = canvasEng.offsetHeight * ratio;
                canvasEng.getContext("2d").scale(ratio, ratio);
                padEngineer.clear();
            }
            if(canvasCust) {
                canvasCust.width = canvasCust.offsetWidth * ratio;
                canvasCust.height = canvasCust.offsetHeight * ratio;
                canvasCust.getContext("2d").scale(ratio, ratio);
                padCustomer.clear();
            }
        }
        
        if (canvasEng || canvasCust) {
            window.addEventListener("resize", resizeCanvases);
            resizeCanvases();
        }
    });

    function clearSignature(type) {
        if (type === 'engineer' && padEngineer) padEngineer.clear();
        if (type === 'customer' && padCustomer) padCustomer.clear();
    }

    function submitDualSignature() {
        if (padEngineer && padEngineer.isEmpty()) {
            showGPSModal('Tanda Tangan Engineer Kosong', 'Silakan isi tanda tangan Lead Engineer terlebih dahulu.', false);
            return;
        }
        if (padCustomer && padCustomer.isEmpty()) {
            showGPSModal('Tanda Tangan Customer Kosong', 'Customer belum membubuhkan tanda tangan. Silakan isi terlebih dahulu.', false);
            return;
        }
        
        document.getElementById('signatureInputEngineer').value = padEngineer.toDataURL();
        document.getElementById('signatureInputCustomer').value = padCustomer.toDataURL();
        
        if(typeof showConfirmModal === 'function') {
            showConfirmModal(
                document.getElementById('signatureForm'),
                'Kunci Laporan',
                'Dengan mengirimkan TTD ini, pekerjaan dinyatakan selesai dan tidak bisa diubah lagi. Lanjutkan?'
            );
        } else {
            if(confirm('Kunci laporan dan selesaikan pekerjaan ini?')) {
                document.getElementById('signatureForm').submit();
            }
        }
    }
</script>
@endsection