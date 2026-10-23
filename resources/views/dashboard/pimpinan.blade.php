@extends('layouts.app')

@section('title', 'Dashboard Pimpinan')
@section('header_title', 'Operasional & Penjadwalan')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </span>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Dashboard Pimpinan</h1>
            <p class="text-xs text-slate-500">Operasional & Penjadwalan</p>
        </div>
    </div>

    <!-- KPI Operasional -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-500">Kunjungan Hari Ini</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $kunjunganHariIni }}</h3>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Jadwal tanggal {{ date('d M Y') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-500">Engineer Tersedia</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $engineerTersedia }}</h3>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Siap ditugaskan</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-500">Tools / Alat Siap Pakai</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $toolTersedia }}</h3>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Kondisi baik & tersedia</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-500">Total Kunjungan</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalKunjungan }}</h3>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Sepanjang masa</p>
        </div>
    </div>

    <!-- Quick Action Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Buat Jadwal Kunjungan Baru</h4>
                <p class="text-xs text-slate-500 mt-0.5">Jadwalkan kunjungan teknis ke lokasi customer dan tugaskan engineer.</p>
            </div>
        </div>
        <button onclick="window.location.href='{{ route('kunjungan.index') }}'" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition shrink-0">
            + Jadwalkan Kunjungan
        </button>
    </div>

    <!-- Daftar Kunjungan Aktif -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h4 class="text-sm font-bold text-slate-900">Daftar Kunjungan Operasional</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nomor</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer & Lokasi</th>
                        <th class="px-4 py-3 text-left font-semibold">Engineer Bertugas</th>
                        <th class="px-4 py-3 text-left font-semibold">Tools Dibawa</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kunjunganList as $kunjungan)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 border-t border-slate-100 font-semibold"><a href="{{ route('kunjungan.show', $kunjungan->nomor) }}" class="text-[#003399] hover:underline">{{ $kunjungan->nomor }}</a></td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <p class="font-semibold text-slate-800">{{ $kunjungan->customer->nama_perusahaan ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $kunjungan->alamat_sinkron }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-600">
                                {{ $kunjungan->engineer->user->nama ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="text-slate-500">{{ $kunjungan->tools->count() }} Alat Terlampir</span>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $kunjungan->status == 'Selesai' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                    {{ $kunjungan->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 border-t border-slate-100 text-center text-slate-400 italic">Belum ada aktivitas kunjungan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex justify-center">
            <a href="{{ route('kunjungan.index') }}" class="text-[13px] font-semibold text-[#003399] hover:text-[#001a4d] flex items-center gap-1.5 transition">
                Lihat Semua Kunjungan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <!-- QA #22: Pengeluaran engineer yang belum diganti perusahaan -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-sm font-bold text-slate-900">Pengeluaran Engineer Belum Diganti</h4>
            <span class="text-sm font-extrabold text-rose-600">Total: Rp {{ number_format($totalBelumDiganti, 0, ',', '.') }}</span>
        </div>
        @forelse($reimbursePerEngineer as $r)
            <div class="flex items-center justify-between py-2 border-t border-slate-100 text-sm">
                <span class="font-medium text-slate-700">{{ $r->engineer->user->nama ?? 'Engineer' }} <span class="text-slate-400 text-xs">({{ $r->jumlah }} pengeluaran)</span></span>
                <span class="font-bold text-slate-800">Rp {{ number_format($r->total, 0, ',', '.') }}</span>
            </div>
        @empty
            <p class="text-xs text-slate-400 italic">Tidak ada pengeluaran yang menunggu penggantian.</p>
        @endforelse
    </div>

</div>
@endsection
