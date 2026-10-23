@extends('layouts.app')

@section('title', 'Data Engineer')
@section('header_title', 'Manajemen Engineer Lapangan')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Daftar Engineer</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola data teknisi dan hak akses kunjungan lapangan</p>
            </div>
        </div>
        <button onclick="document.getElementById('modalTambahEngineer').classList.remove('hidden')"
                class="bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg px-4 py-2.5 transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Engineer
        </button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('master.engineer.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian Data</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama Engineer atau Username..." class="w-full h-11 pl-10 pr-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <label for="sort" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Urutkan</label>
                <select id="sort" name="sort" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Paling Baru (Terbaru)</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Paling Lama (Terlama)</option>
                </select>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="h-11 px-5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition flex items-center justify-center">
                    Terapkan
                </button>
                @if(request()->has('search') || request()->has('sort'))
                    <a href="{{ route('master.engineer.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-semibold rounded-lg transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nama Engineer</th>
                        <th class="px-4 py-3 text-left font-semibold">Username & Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Kontak</th>
                        <th class="px-4 py-3 text-left font-semibold">Ketersediaan</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($engineers as $e)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="font-semibold text-[#003399]">{{ $e->user->nama ?? '-' }}</span>
                                @if($e->kode)<div class="text-[10px] font-mono font-semibold text-slate-400 mt-0.5">{{ $e->kode }}</div>@endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <p class="text-slate-800 font-mono font-semibold">{{ $e->user->username ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $e->user->email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-700">{{ $e->kontak }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                @isset($tugasAktif[$e->id_engineer])
                                    {{-- QA ronde 2 #7: badge mengikuti kondisi aktual, bukan field statis --}}
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-amber-50 text-amber-700 border-amber-200">
                                        Sedang Bertugas
                                    </span>
                                    <a href="{{ route('kunjungan.show', $tugasAktif[$e->id_engineer]->nomor) }}" class="block mt-1.5 text-[11px] font-semibold text-[#003399] hover:underline">Sedang ditugaskan: {{ $tugasAktif[$e->id_engineer]->nomor }} • {{ \Carbon\Carbon::parse($tugasAktif[$e->id_engineer]->tanggal)->format('d M Y') }} {{ substr((string) $tugasAktif[$e->id_engineer]->waktu, 0, 5) }}</a>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $e->status_ketersediaan == 'Tersedia' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                                        {{ $e->status_ketersediaan }}
                                    </span>
                                @endisset
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button onclick="openEditEngineerModal({{ json_encode($e) }})" class="p-2 rounded-lg text-slate-400 hover:text-[#003399] hover:bg-blue-50 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.engineer.destroy', $e->id_engineer) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Engineer', 'Apakah Anda yakin ingin menghapus akun engineer {{ $e->user->nama ?? '' }}?')"
                                                class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400 font-medium italic">Data engineer tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $engineers->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah Engineer -->
<div id="modalTambahEngineer" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-lg font-bold text-slate-900">Tambah Engineer Baru</h4>
            <button onclick="document.getElementById('modalTambahEngineer').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('master.engineer.store') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Awal</label>
                    <input type="password" name="password" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kontak / No. WA</label>
                    <input type="tel" name="kontak" inputmode="tel" pattern="\+?[0-9\s\-().]+" title="Hanya angka (boleh diawali +, spasi, strip, titik, kurung)" placeholder="cth: 081234567890" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahEngineer').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Buat Akun Engineer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Engineer -->
<div id="modalEditEngineer" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-lg font-bold text-slate-900">Edit Data Engineer</h4>
            <button onclick="document.getElementById('modalEditEngineer').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form id="formEditEngineer" method="POST" class="space-y-4 text-sm">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" id="edit_eng_nama" name="nama" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" id="edit_eng_username" name="username" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="edit_eng_email" name="email" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kontak</label>
                    <input type="tel" id="edit_eng_kontak" name="kontak" inputmode="tel" pattern="\+?[0-9\s\-().]+" title="Hanya angka (boleh diawali +, spasi, strip, titik, kurung)" placeholder="cth: 081234567890" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Ketersediaan</label>
                <select id="edit_eng_status" name="status_ketersediaan" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <option value="Tersedia">Tersedia</option>
                    <option value="Tidak Tersedia">Tidak Tersedia</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditEngineer').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditEngineerModal(engineer) {
        document.getElementById('formEditEngineer').action = `/master/engineer/${engineer.id_engineer}`;
        document.getElementById('edit_eng_nama').value = engineer.user.nama;
        document.getElementById('edit_eng_username').value = engineer.user.username;
        document.getElementById('edit_eng_email').value = engineer.user.email;
        document.getElementById('edit_eng_kontak').value = engineer.kontak;
        document.getElementById('edit_eng_status').value = engineer.status_ketersediaan;
        document.getElementById('modalEditEngineer').classList.remove('hidden');
    }
</script>

<script>
    // Kontak/No.WA: otomatis buang karakter selain angka, +, spasi, strip, titik, kurung
    document.querySelectorAll('input[name="kontak"]').forEach(el => {
        el.addEventListener('input', () => {
            const pos = el.selectionStart;
            const bersih = el.value.replace(/[^0-9+\s\-().]/g, '');
            if (bersih !== el.value) {
                el.value = bersih;
                try { el.setSelectionRange(pos - 1, pos - 1); } catch (e) {}
            }
        });
    });
</script>
@endsection
