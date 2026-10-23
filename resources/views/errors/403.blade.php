@extends('layouts.app')

@section('title', 'Akses Ditolak')
@section('header_title', 'Akses Ditolak')

@section('content')
<div class="max-w-lg mx-auto mt-10">
    <div class="p-6 md:p-8 rounded-2xl bg-white border border-rose-200 shadow-sm text-center space-y-4">
        <span class="mx-auto w-14 h-14 rounded-full bg-rose-50 border border-rose-100 text-rose-500 flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </span>
        <h2 class="text-base font-bold text-slate-800">Akses Ditolak</h2>
        <p class="text-sm text-slate-500">{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk halaman atau aksi ini.' }}</p>
        <div class="flex items-center justify-center gap-2 pt-2">
            <button onclick="history.back()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Kembali</button>
            @php $dashRoute = match(Auth::user()->id_role ?? 1) { 1 => 'kepala.dashboard', 2 => 'pimpinan.dashboard', 3 => 'engineer.dashboard', default => 'kepala.dashboard' }; @endphp
            <a href="{{ route($dashRoute) }}" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Ke Dashboard</a>
        </div>
    </div>
</div>
@endsection
