@extends('layouts.app')

@section('title', 'Dashboard Pimpinan')
@section('header_title', 'Operasional & Penjadwalan')

@section('content')
<div class="space-y-6">

    <!-- KPI Operasional -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80">
            <p class="text-xs font-medium text-slate-400">Kunjungan Hari Ini</p>
            <h3 class="text-2xl font-bold text-blue-400 mt-1">{{ $kunjunganHariIni }}</h3>
            <p class="text-[11px] text-slate-500 mt-2">Jadwal tanggal {{ date('d M Y') }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80">
            <p class="text-xs font-medium text-slate-400">Engineer Tersedia</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ $engineerTersedia }}</h3>
            <p class="text-[11px] text-slate-500 mt-2">Siap ditugaskan</p>
        </div>
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80">
            <p class="text-xs font-medium text-slate-400">Tools / Alat Siap Pakai</p>
            <h3 class="text-2xl font-bold text-indigo-400 mt-1">{{ $toolTersedia }}</h3>
            <p class="text-[11px] text-slate-500 mt-2">Kondisi baik & tersedia</p>
        </div>
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80">
            <p class="text-xs font-medium text-slate-400">Total Kunjungan</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $totalKunjungan }}</h3>
            <p class="text-[11px] text-slate-500 mt-2">Sepanjang masa</p>
        </div>
    </div>

    <!-- Quick Action Card -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-blue-900/40 via-indigo-950/40 to-slate-900/60 border border-blue-500/20 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="text-base font-semibold text-white">Buat Jadwal Kunjungan Baru</h4>
            <p class="text-xs text-slate-300 mt-1">Jadwalkan kunjungan teknis ke lokasi customer dan tugaskan engineer.</p>
        </div>
        <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition">
            + Jadwalkan Kunjungan
        </button>
    </div>

    <!-- Daftar Kunjungan Aktif -->
    <div class="p-6 rounded-2xl bg-white/[0.03] border border-slate-800/80">
        <h4 class="text-sm font-semibold text-slate-200 mb-4">Daftar Kunjungan Operasional</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase bg-slate-900/80 text-slate-400">
                    <tr>
                        <th class="p-3">Nomor</th>
                        <th class="p-3">Customer & Lokasi</th>
                        <th class="p-3">Engineer Bertugas</th>
                        <th class="p-3">Tools Dibawa</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($kunjunganList as $kunjungan)
                        <tr class="hover:bg-slate-800/20">
                            <td class="p-3 font-semibold text-blue-400">{{ $kunjungan->nomor }}</td>
                            <td class="p-3">
                                <p class="font-medium text-slate-100">{{ $kunjungan->customer->nama_perusahaan ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $kunjungan->lokasi }}</p>
                            </td>
                            <td class="p-3">
                                {{ $kunjungan->engineer->user->nama ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="p-3">
                                <span class="text-slate-400">{{ $kunjungan->tools->count() }} Alat Terlampir</span>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-medium {{ $kunjungan->status == 'Selesai' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                    {{ $kunjungan->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-500 italic">Belum ada aktivitas kunjungan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection