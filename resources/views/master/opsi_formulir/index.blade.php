@extends('layouts.app')

@section('title', 'Opsi Formulir')
@section('header_title', 'Master Data — Opsi Formulir')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold shadow-sm">{{ session('error') }}</div>
    @endif

    <div>
        <h1 class="text-lg font-bold text-slate-900">Opsi Formulir Kunjungan</h1>
        <p class="text-xs text-slate-500 mt-0.5">Daftar pilihan Jenis Biaya & Kategori Foto yang muncul di formulir kunjungan. Opsi "Lainnya" selalu tersedia otomatis — engineer wajib mengisi keterangan bila memilihnya.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach([['grup' => 'jenis_biaya', 'judul' => 'Jenis Biaya Pengeluaran', 'items' => $jenisBiaya], ['grup' => 'kategori_foto', 'judul' => 'Kategori Foto Dokumentasi', 'items' => $kategoriFoto]] as $grupData)
            <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800">{{ $grupData['judul'] }}</h3>
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse($grupData['items'] as $item)
                        <li class="py-2.5 flex items-center justify-between">
                            <span class="font-medium text-slate-700">{{ $item->nama }}</span>
                            <form action="{{ route('master.opsi-formulir.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus opsi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-100 text-rose-600 text-[11px] font-bold hover:bg-rose-100 transition">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-3 text-slate-400 italic text-xs">Belum ada opsi. Tambahkan di bawah.</li>
                    @endforelse
                    <li class="py-2.5 flex items-center justify-between text-slate-400">
                        <span class="font-medium italic">Lainnya (otomatis, wajib keterangan)</span>
                        <span class="text-[11px] font-bold">Bawaan</span>
                    </li>
                </ul>
                <form action="{{ route('master.opsi-formulir.store') }}" method="POST" class="flex gap-2 pt-1">
                    @csrf
                    <input type="hidden" name="grup" value="{{ $grupData['grup'] }}">
                    <input type="text" name="nama" required maxlength="100" placeholder="Nama opsi baru..." class="flex-1 h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                    <button type="submit" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Tambah</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
