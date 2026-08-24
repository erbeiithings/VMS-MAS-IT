@extends('layouts.app')

@section('title', 'Dashboard Engineer')
@section('header_title', 'Area Tugas Lapangan')

@section('content')
<div class="space-y-6">

    <!-- Status Engineer Profile -->
    <div class="p-5 md:p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-[#0a1533] to-[#040817] border border-slate-800 flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-400">Selamat Bekerja,</p>
            <h3 class="text-lg md:text-xl font-bold text-white mt-0.5">{{ Auth::user()->nama }}</h3>
            <p class="text-xs text-blue-400 mt-1">Status Ketersediaan: <span class="font-semibold">{{ $engineer->status_ketersediaan ?? 'Tersedia' }}</span></p>
        </div>
        <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center text-blue-400 shrink-0">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
    </div>

    <!-- Active Task Card -->
    <div class="p-5 md:p-6 rounded-2xl bg-white/[0.03] border border-blue-500/30">
        <h4 class="text-sm font-semibold text-slate-200 mb-4 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
            Tugas Kunjungan Aktif Saat Ini
        </h4>

        @if($kunjunganAktif)
            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
                    <div>
                        <span class="px-2 py-0.5 rounded bg-blue-600/20 text-blue-400 text-[10px] font-semibold uppercase">{{ $kunjunganAktif->nomor }}</span>
                        <h5 class="text-base font-bold text-white mt-1">{{ $kunjunganAktif->pekerjaan }}</h5>
                        <p class="text-xs text-slate-300 font-medium">{{ $kunjunganAktif->customer->nama_perusahaan ?? '-' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $kunjunganAktif->lokasi }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        {{ $kunjunganAktif->status }}
                    </span>
                </div>

                <!-- Tombol Masuk ke Lembar Kerja On-Site -->
                <div class="pt-3 border-t border-slate-800 flex flex-wrap gap-2">
                    <a href="{{ route('kunjungan.show', $kunjunganAktif->id_kunjungan) }}" 
                       class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-xs text-center shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2">
                        <span>Buka Lembar Kerja Kunjungan (GPS, Foto, Laporan)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @else
            <div class="p-8 text-center bg-slate-900/40 rounded-xl border border-slate-800/60">
                <p class="text-xs text-slate-400">Tidak ada penugasan aktif saat ini. Anda berada dalam status standby.</p>
            </div>
        @endif
    </div>

    <!-- Riwayat Kunjungan Selesai -->
    <div class="p-5 md:p-6 rounded-2xl bg-white/[0.03] border border-slate-800/80">
        <h4 class="text-sm font-semibold text-slate-200 mb-4">Riwayat Kunjungan Selesai Terakhir</h4>
        <div class="space-y-3">
            @forelse($riwayatKunjungan as $history)
                <div class="p-3 bg-slate-900/50 rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <p class="font-semibold text-slate-200">{{ $history->pekerjaan }}</p>
                        <p class="text-[11px] text-slate-400">{{ $history->customer->nama_perusahaan ?? '-' }} • {{ $history->tanggal }}</p>
                    </div>
                    <a href="{{ route('kunjungan.show', $history->id_kunjungan) }}" class="text-emerald-400 font-medium text-[11px] hover:underline">
                        Lihat Detail ✔
                    </a>
                </div>
            @empty
                <p class="text-xs text-slate-500 italic">Belum ada riwayat kunjungan selesai.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection