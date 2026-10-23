@extends('layouts.app')

@section('title', 'Detail Tool - ' . $tool->kode)
@section('header_title', 'Detail Tool')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="{{ route('master.tool.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-slate-500 hover:text-[#003399] transition w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Tool
        </a>
        <a href="{{ route('peminjaman.riwayat', $tool->kode) }}" class="inline-flex items-center gap-1.5 h-10 px-4 bg-white hover:bg-slate-50 text-[#002266] border border-slate-200 rounded-lg text-[13px] font-semibold shadow-sm transition w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Riwayat Lengkap
        </a>
    </div>

    {{-- Info Tool --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <span class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                </span>
                <div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-[#003399] border border-blue-100 font-mono uppercase">{{ $tool->kode }}</span>
                    <h2 class="text-xl font-bold text-slate-900 mt-2">{{ $tool->nama_alat }}</h2>
                    <p class="text-xs text-slate-500 mt-1">{{ $tool->kategori }}</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $tool->status_ketersediaan == 'Tersedia' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                {{ $tool->status_ketersediaan }}
            </span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-5">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-wider">Stok Tersedia</p>
                <p class="text-xl font-bold text-emerald-600 mt-1">{{ $tool->stok }}</p>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-wider">Kategori</p>
                <p class="text-sm font-bold text-slate-800 mt-1">{{ $tool->kategori }}</p>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 col-span-2">
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-wider">Spesifikasi</p>
                <p class="text-sm font-medium text-slate-700 mt-1">{{ $tool->spesifikasi ?? '-' }}</p>
            </div>
        </div>
        @if($tool->keterangan)
        <p class="text-xs text-slate-500 mt-4"><span class="font-bold">Keterangan:</span> {{ $tool->keterangan }}</p>
        @endif
    </div>

    {{-- Riwayat Peminjaman Terakhir --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3.5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Peminjaman Terakhir</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. Kunjungan</th>
                        <th class="px-4 py-3 text-left font-semibold">Engineer</th>
                        <th class="px-4 py-3 text-center font-semibold">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold">Dipinjam</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100">
                                @if($r->kunjungan)
                                    <a href="{{ route('kunjungan.show', $r->kunjungan->nomor) }}" class="font-mono text-[11px] font-semibold text-[#003399] uppercase hover:underline">{{ $r->kunjungan->nomor }}</a>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">{{ $r->kunjungan->customer->nama_perusahaan ?? '' }}</span>
                                @else
                                    <span class="italic text-slate-400">{{ $r->keterangan ?? 'Keperluan lain' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 font-semibold text-slate-800">{{ $r->engineer->user->nama ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ ($r->id_kunjungan && $r->jumlah == 0) ? 'Alat Tim' : $r->jumlah }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-700 whitespace-nowrap">{{ $r->tanggal_pinjam->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $r->status == 'Dipinjam' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100' }}">
                                    {{ $r->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400 font-medium italic">Belum ada riwayat peminjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">{{ $riwayat->links() }}</div>
        @endif
    </div>
</div>
@endsection
