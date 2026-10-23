@extends('layouts.app')

@section('title', 'Dashboard Engineer')
@section('header_title', 'Area Tugas Lapangan')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
        </span>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Dashboard Engineer</h1>
            <p class="text-xs text-slate-500">Area Tugas Lapangan</p>
        </div>
    </div>

    <!-- QA #22: Pengeluaran saya yang belum diganti perusahaan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pengeluaran Saya — Belum Diganti</p>
            <p class="text-xl font-extrabold text-rose-600 mt-1.5">Rp {{ number_format($pengeluaranBelumDiganti, 0, ',', '.') }}</p>
        </div>
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pengeluaran Saya — Sudah Diganti</p>
            <p class="text-xl font-extrabold text-emerald-600 mt-1.5">Rp {{ number_format($pengeluaranSudahDiganti, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- D-DAY ALERT BANNER -->
    @if($alertHariIni->count() > 0)
        <div class="px-5 py-4 rounded-xl bg-rose-50 border border-rose-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 animate-pulse-slow">
            <div class="flex items-center gap-3 text-rose-700">
                <span class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-sm uppercase tracking-wider">PENGINGAT HARI H!</h3>
                    <p class="text-xs text-rose-600">Anda memiliki <strong>{{ $alertHariIni->count() }} jadwal kunjungan</strong> yang harus dilaksanakan hari ini.</p>
                </div>
            </div>
        </div>
        <style>
            .animate-pulse-slow { animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
            @keyframes pulse-slow { 0%, 100% { opacity: 1; } 50% { opacity: 0.8; } }
        </style>
    @endif

    <!-- Status Engineer Profile -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center justify-between gap-4">
        <div>
            <p class="text-xs text-slate-500">Selamat Bekerja,</p>
            <h3 class="text-lg font-bold text-slate-900 mt-0.5">{{ Auth::user()->nama }}</h3>
            <p class="text-xs text-slate-500 mt-1">Status Ketersediaan: <span class="font-semibold text-[#003399]">{{ $engineer->status_ketersediaan ?? 'Tersedia' }}</span></p>
        </div>
        <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
    </div>

    <!-- Active Task Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-400"></span>
            </span>
            Tugas Kunjungan Belum Selesai ({{ $tugasAktif->count() ?? 0 }})
        </h4>

        @forelse($tugasAktif ?? [] as $tugas)
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4 mb-3">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
                    <div>
                        <a href="{{ route('kunjungan.show', $tugas->nomor) }}" class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 border border-blue-100 text-[#003399] text-[11px] font-semibold hover:underline">{{ $tugas->nomor }}</a>
                        <h5 class="text-base font-bold text-slate-900 mt-2">{{ $tugas->pekerjaan }}</h5>
                        <p class="text-xs text-slate-600 font-semibold">{{ $tugas->customer->nama_perusahaan ?? '-' }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">📍 {{ $tugas->alamat_sinkron }}</p>
                        @if($tugas->patokan)
                            <p class="text-[11px] text-amber-600 font-semibold mt-0.5">📎 {{ $tugas->patokan }}</p>
                        @endif
                        <p class="text-[11px] text-slate-500 mt-1">📅 {{ \Carbon\Carbon::parse($tugas->tanggal)->format('d M Y') }}</p>

                        <p class="text-[11px] mt-2 inline-flex items-center px-2.5 py-1 rounded-full bg-white border border-slate-200 text-slate-600">
                            Peran Anda: <strong class="text-[#003399] ml-1">{{ $tugas->id_engineer == $engineer->id_engineer ? 'Lead Engineer' : 'Tim Support' }}</strong>
                        </p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                        {{ $tugas->statusLabel() }}
                    </span>
                </div>

                <div class="pt-3 border-t border-slate-200 flex flex-wrap gap-2">
                    <a href="{{ route('kunjungan.show', $tugas->nomor) }}"
                       class="w-full sm:w-auto px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white rounded-lg font-semibold text-[13px] text-center flex items-center justify-center gap-2 transition">
                        <span>Buka Lembar Kerja Kunjungan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center bg-slate-50 rounded-xl border border-slate-200">
                <p class="text-xs text-slate-500">Tidak ada penugasan aktif saat ini. Anda berada dalam status standby.</p>
            </div>
        @endforelse
    </div>

    <!-- Riwayat Kunjungan Selesai -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h4 class="text-sm font-bold text-slate-900">Riwayat Kunjungan Selesai Terakhir</h4>
        </div>
        <div class="p-5 space-y-3">
            @forelse($riwayatKunjungan as $history)
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between gap-3 text-xs">
                    <div>
                        <p class="font-bold text-slate-700">{{ $history->pekerjaan }}</p>
                        <p class="text-[11px] text-slate-500">{{ $history->customer->nama_perusahaan ?? '-' }} • {{ $history->tanggal }}</p>
                    </div>
                    <a href="{{ route('kunjungan.show', $history->nomor) }}" class="text-emerald-600 font-bold text-[11px] hover:underline shrink-0">
                        Lihat Detail ✔
                    </a>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic">Belum ada riwayat kunjungan selesai.</p>
            @endforelse
        </div>

        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex justify-center">
            <a href="{{ route('kunjungan.index') }}" class="text-[13px] font-semibold text-[#003399] hover:text-[#001a4d] flex items-center gap-1.5 transition">
                Lihat Semua Riwayat Kunjungan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

</div>
@endsection
