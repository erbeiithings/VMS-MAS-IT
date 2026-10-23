@extends('layouts.app')

@section('title', 'Data Pengguna')
@section('header_title', 'Master Data — Data Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-lg font-bold text-slate-900">Data Pengguna</h1>
            <p class="text-xs text-slate-500 mt-0.5">Semua pimpinan & engineer dalam satu halaman, disortir berdasarkan role. Kelola detail lewat tombol di tiap baris.</p>
        </div>
        <div class="flex gap-2">
            @if(Auth::user()->id_role == 1)
                <a href="{{ route('master.pimpinan.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-[#003399] rounded-lg text-[13px] font-semibold shadow-sm transition">Kelola Pimpinan</a>
            @endif
            <a href="{{ route('master.engineer.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-[#003399] rounded-lg text-[13px] font-semibold shadow-sm transition">Kelola Engineer</a>
        </div>
    </div>

    {{-- QA ronde 2 #1: filter berdasarkan grup role --}}
    <div class="flex gap-2">
        <a href="{{ route('master.pengguna.index') }}" class="px-4 py-2 rounded-lg text-[12px] font-bold border transition {{ $filter === 'semua' ? 'bg-[#002266] text-white border-[#002266]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">Semua</a>
        <a href="{{ route('master.pengguna.index', ['role' => 'pimpinan']) }}" class="px-4 py-2 rounded-lg text-[12px] font-bold border transition {{ $filter === 'pimpinan' ? 'bg-[#002266] text-white border-[#002266]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">Pimpinan</a>
        <a href="{{ route('master.pengguna.index', ['role' => 'engineer']) }}" class="px-4 py-2 rounded-lg text-[12px] font-bold border transition {{ $filter === 'engineer' ? 'bg-[#002266] text-white border-[#002266]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">Engineer</a>
    </div>

    <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nama</th>
                        <th class="px-4 py-3 font-semibold">Username</th>
                        <th class="px-4 py-3 font-semibold">Role</th>
                        <th class="px-4 py-3 font-semibold">Kontak</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pimpinan as $u)
                        <tr class="hover:bg-slate-50 transition-colors font-medium">
                            <td class="px-4 py-3 border-t border-slate-100">
                                <p class="font-bold text-slate-800">{{ $u->nama }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $u->email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $u->username }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $u->id_role == 1 ? 'bg-[#002266] text-white border-[#002266]' : 'bg-blue-50 text-blue-700 border-blue-100' }}">{{ $u->id_role == 1 ? 'Kepala Pimpinan' : 'Pimpinan' }}</span>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $u->kontak ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                {{-- Kolom Status dinamis: pimpinan menampilkan Status Akun --}}
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ ($u->status_akun ?? 'Aktif') === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200' }}">{{ $u->status_akun ?? 'Aktif' }}</span>
                            </td>
                        </tr>
                    @endforeach
                    @foreach($engineers as $e)
                        <tr class="hover:bg-slate-50 transition-colors font-medium">
                            <td class="px-4 py-3 border-t border-slate-100">
                                <p class="font-bold text-slate-800">{{ $e->user->nama ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $e->user->email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $e->user->username ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-emerald-50 text-emerald-700 border-emerald-100">Engineer</span>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $e->kontak ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                {{-- Kolom Status dinamis: engineer menampilkan Ketersediaan / tugas aktif --}}
                                @isset($tugasAktif[$e->id_engineer])
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-amber-50 text-amber-700 border-amber-200">Sedang Bertugas</span>
                                    <a href="{{ route('kunjungan.show', $tugasAktif[$e->id_engineer]->nomor) }}" class="block mt-1.5 text-[11px] font-semibold text-[#003399] hover:underline">Sedang ditugaskan: {{ $tugasAktif[$e->id_engineer]->nomor }}</a>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $e->status_ketersediaan == 'Tersedia' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">{{ $e->status_ketersediaan }}</span>
                                @endisset
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
