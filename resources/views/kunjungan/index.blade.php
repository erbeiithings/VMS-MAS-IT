@extends('layouts.app')

@section('title', 'Kunjungan Kerja')
@section('header_title', 'Manajemen Kunjungan Kerja')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-white">Daftar Kunjungan Engineer</h3>
            <p class="text-xs text-slate-400">Monitoring seluruh siklus kunjungan dari penugasan hingga verifikasi</p>
        </div>
        @if(Auth::user()->id_role == 2)
            <button onclick="document.getElementById('modalTambahKunjungan').classList.remove('hidden')" 
                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Jadwal Kunjungan
            </button>
        @endif
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl bg-white/[0.03] border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase bg-slate-900/80 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-4">Nomor & Tanggal</th>
                        <th class="p-4">Customer & Lokasi</th>
                        <th class="p-4">Pekerjaan</th>
                        <th class="p-4">Engineer</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($kunjunganList as $k)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="p-4">
                                <span class="font-mono text-blue-400 font-semibold">{{ $k->nomor }}</span>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $k->tanggal }} ({{ $k->waktu }})</p>
                            </td>
                            <td class="p-4">
                                <p class="font-semibold text-white">{{ $k->customer->nama_perusahaan ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400 truncate max-w-xs">{{ $k->lokasi }}</p>
                            </td>
                            <td class="p-4 text-slate-200">{{ $k->pekerjaan }}</td>
                            <td class="p-4">
                                <span class="text-slate-300">{{ $k->engineer->user->nama ?? 'Belum Ditugaskan' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $k->status == 'Selesai' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($k->status == 'Dikerjakan' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20') }}">
                                    {{ $k->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('kunjungan.show', $k->id_kunjungan) }}" class="px-3 py-1.5 bg-blue-600/10 hover:bg-blue-600 text-blue-400 hover:text-white rounded-lg text-xs font-medium transition">
                                    Detail Alur
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500 italic">Belum ada kunjungan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $kunjunganList->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah Kunjungan -->
@if(Auth::user()->id_role == 2)
<div id="modalTambahKunjungan" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#0b132b] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-white">Buat Jadwal Kunjungan Baru</h4>
            <button onclick="document.getElementById('modalTambahKunjungan').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('kunjungan.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block text-slate-300 mb-1">Customer / Klien</label>
                <select name="id_customer" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                    <option value="">Pilih Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id_customer }}">{{ $c->nama_perusahaan }} ({{ $c->pic }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Tugaskan Engineer</label>
                <select name="id_engineer" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                    <option value="">Pilih Engineer (Opsional)</option>
                    @foreach($engineers as $e)
                        <option value="{{ $e->id_engineer }}">{{ $e->user->nama ?? '-' }} - ({{ $e->status_ketersediaan }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Waktu</label>
                    <input type="time" name="waktu" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Deskripsi Pekerjaan</label>
                <input type="text" name="pekerjaan" placeholder="Misal: Instalasi Router Core & Switch" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Lokasi Kunjungan</label>
                <textarea name="lokasi" rows="2" placeholder="Alamat lengkap / gedung tempat pekerjaan" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none"></textarea>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Tools & Alat yang Dibawa</label>
                <div class="grid grid-cols-2 gap-2 bg-slate-900/60 p-3 rounded-xl border border-slate-800 max-h-32 overflow-y-auto">
                    @foreach($tools as $t)
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                            <input type="checkbox" name="tools[]" value="{{ $t->id_tool }}" class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-0">
                            <span class="text-[11px] truncate">{{ $t->nama_alat }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahKunjungan').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl">Simpan & Jadwalkan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection