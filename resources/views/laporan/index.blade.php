@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')
@section('header_title', 'Laporan Kunjungan & Berita Acara')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-white">Rekapitulasi Laporan Kunjungan</h3>
            <p class="text-xs text-slate-400">Unduh bukti penyelesaian pekerjaan (Service Completion Receipt) bertanda tangan digital</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl bg-white/[0.03] border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase bg-slate-900/80 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-4">Nomor Kunjungan</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Engineer</th>
                        <th class="p-4">Tanggal Kunjungan</th>
                        <th class="p-4">Status Dokumen</th>
                        <th class="p-4 text-center">Cetak PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($laporanList as $l)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="p-4">
                                <span class="font-mono text-blue-400 font-semibold">{{ $l->kunjungan->nomor ?? '-' }}</span>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $l->kunjungan->pekerjaan ?? '-' }}</p>
                            </td>
                            <td class="p-4 font-medium text-white">{{ $l->kunjungan->customer->nama_perusahaan ?? '-' }}</td>
                            <td class="p-4">{{ $l->kunjungan->engineer->user->nama ?? '-' }}</td>
                            <td class="p-4 text-slate-400">{{ $l->kunjungan->tanggal ?? '-' }}</td>
                            <td class="p-4">
                                @if($l->buktiPenyelesaian)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        ✔ Ditandatangani
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Menunggu Tanda Tangan
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('laporan.pdf', $l->id_kunjungan) }}" target="_blank" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded-lg text-xs font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Download PDF</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500 italic">Belum ada laporan kunjungan yang diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $laporanList->links() }}
        </div>
    </div>

</div>
@endsection