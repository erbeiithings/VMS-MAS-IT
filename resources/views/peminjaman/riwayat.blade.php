@extends('layouts.app')

@section('title', 'Riwayat Tools')
@section('header_title', 'Riwayat Pemakaian Tools')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div>
                <h1 class="text-lg font-bold text-slate-900">{{ $tool->nama_alat }}</h1>
                <p class="text-xs text-slate-500">
                    <a href="{{ route('master.tool.show', $tool->kode) }}" class="font-mono text-[#003399] font-semibold uppercase hover:underline">{{ $tool->kode }}</a> &middot;
                    Stok saat ini: <span class="font-semibold text-emerald-600">{{ $tool->stok }}</span> &middot;
                    Riwayat pemakaian & pinjaman
                </p>
            </div>
        </div>
        <a href="{{ url()->previous() }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition shrink-0">Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Engineer</th>
                        <th class="px-4 py-3 text-left font-semibold text-center">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold">Dipinjam</th>
                        <th class="px-4 py-3 text-left font-semibold">Dikembalikan</th>
                        <th class="px-4 py-3 text-left font-semibold">Kondisi Kembali</th>
                        <th class="px-4 py-3 text-left font-semibold">Keperluan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100 font-semibold text-slate-800">{{ $r->engineer->user->nama ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center font-semibold">{{ ($r->id_kunjungan && $r->jumlah == 0) ? 'Alat Tim' : $r->jumlah }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $r->tanggal_pinjam->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $r->tanggal_kembali?->format('d M Y, H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                @if($r->kondisi_kembali)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $r->kondisi_kembali == 'Baik' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($r->kondisi_kembali == 'Rusak Ringan' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-rose-50 text-rose-700 border-rose-100') }}" title="{{ $r->catatan_kembali ?? '' }}">
                                        {{ $r->kondisi_kembali }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-500 max-w-xs">
                                @if($r->id_kunjungan)
                                    <a href="{{ route('kunjungan.show', $r->kunjungan->nomor) }}" class="text-[#003399] font-semibold hover:underline">{{ $r->kunjungan->nomor ?? '-' }}</a>
                                    <span class="block text-[11px]">{{ $r->kunjungan->customer->nama_perusahaan ?? '' }}</span>
                                @else
                                    <span class="italic">{{ $r->keterangan ?? 'Keperluan lain' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $r->status == 'Dipinjam' ? 'bg-amber-50 text-amber-700 border-amber-100' : ($r->status == 'Dibatalkan' ? 'bg-slate-50 text-slate-700 border-slate-200' : 'bg-emerald-50 text-emerald-700 border-emerald-100') }}">
                                    {{ $r->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400 italic">Belum ada riwayat pemakaian untuk tool ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">{{ $riwayat->links() }}</div>
        @endif
    </div>
</div>
@endsection
