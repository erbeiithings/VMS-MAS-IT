@extends('layouts.app')

@section('title', 'Detail Customer - ' . $customer->kode)
@section('header_title', 'Detail Customer')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <a href="{{ route('master.customer.index') }}" class="inline-flex items-center gap-2 h-11 px-4 bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 rounded-lg text-[13px] font-semibold transition shadow-sm">&larr; Kembali ke Daftar Customer</a>
    </div>

    {{-- Info Customer --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 md:p-6">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </span>
            <div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100 font-mono">{{ $customer->kode }}</span>
                <h1 class="text-lg font-bold text-slate-900 mt-1.5">{{ $customer->nama_perusahaan }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">PIC: <span class="font-semibold text-slate-700">{{ $customer->pic }}</span></p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5 text-sm">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-wider mb-2">Kontak</p>
                <p class="font-medium text-slate-700">{{ $customer->telepon }}</p>
                <p class="font-medium text-slate-700 mt-1">{{ $customer->email }}</p>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-wider mb-2">Alamat</p>
                <p class="font-medium text-slate-700">{{ $customer->alamat }}</p>
                @if($customer->latitude && $customer->longitude)
                    <p class="text-[11px] font-semibold text-emerald-600 mt-2">GPS: {{ $customer->latitude }}, {{ $customer->longitude }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Daftar Site / Cabang --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
            <h4 class="text-sm font-bold text-slate-900">Site / Cabang ({{ $customer->sites->count() }})</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Cabang</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat Lengkap</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->sites as $s)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100 font-semibold text-slate-800">{{ $s->nama_cabang }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-600">{{ $s->alamat_lengkap }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-6 border-t border-slate-100 text-center text-slate-400">Belum ada site/cabang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat Kunjungan --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
            <h4 class="text-sm font-bold text-slate-900">Riwayat Kunjungan</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. Kunjungan</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Engineer</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kunjungans as $k)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100">
                                <a href="{{ route('kunjungan.show', $k->nomor) }}" class="font-mono font-semibold text-[#003399] hover:underline">{{ $k->nomor }}</a>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-600">{{ \Carbon\Carbon::parse($k->tanggal)->format('d M Y') }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 font-semibold text-slate-800">{{ $k->engineer->user->nama ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">{{ $k->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 border-t border-slate-100 text-center text-slate-400">Belum ada kunjungan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kunjungans->hasPages())
        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">{{ $kunjungans->links() }}</div>
        @endif
    </div>
</div>
@endsection
