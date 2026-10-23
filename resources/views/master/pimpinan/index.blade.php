@extends('layouts.app')

@section('title', 'Data Pimpinan')
@section('header_title', 'Manajemen Pimpinan')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </span>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Daftar Pimpinan</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola data pimpinan dan hak akses operasional</p>
            </div>
        </div>
        <button onclick="document.getElementById('modalTambahPimpinan').classList.remove('hidden')"
                class="bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg px-4 py-2.5 transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Pimpinan
        </button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('master.pimpinan.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian Data</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama Pimpinan atau Username..." class="w-full h-11 pl-10 pr-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
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
                    <a href="{{ route('master.pimpinan.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-semibold rounded-lg transition flex items-center justify-center">
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
                        <th class="px-4 py-3 text-left font-semibold">Nama Pimpinan</th>
                        <th class="px-4 py-3 text-left font-semibold">Username & Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Kontak</th>
                        <th class="px-4 py-3 text-left font-semibold">Status Akun</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pimpinans as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100 font-semibold text-[#003399]">{{ $p->nama }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <p class="text-slate-800 font-mono font-semibold">{{ $p->username }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $p->email }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-700">{{ $p->kontak }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $p->status_akun == 'Aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                                    {{ $p->status_akun }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button onclick='openEditPimpinanModal(@json($p))' class="p-2 rounded-lg text-slate-400 hover:text-[#003399] hover:bg-blue-50 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @if($p->id_pengguna != Auth::user()->id_pengguna)
                                    <form action="{{ route('master.pimpinan.destroy', $p->id_pengguna) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Pimpinan', 'Apakah Anda yakin ingin menghapus akun pimpinan {{ $p->nama }}?')"
                                                class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400 font-medium italic">Data pimpinan tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $pimpinans->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah Pimpinan -->
<div id="modalTambahPimpinan" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-lg font-bold text-slate-900">Tambah Pimpinan Baru</h4>
            <button onclick="document.getElementById('modalTambahPimpinan').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('master.pimpinan.store') }}" method="POST" class="space-y-4 text-sm">
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
                <button type="button" onclick="document.getElementById('modalTambahPimpinan').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Buat Akun Pimpinan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pimpinan -->
<div id="modalEditPimpinan" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-lg font-bold text-slate-900">Edit Data Pimpinan</h4>
            <button onclick="document.getElementById('modalEditPimpinan').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form id="formEditPimpinan" method="POST" class="space-y-4 text-sm">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" id="edit_pim_nama" name="nama" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" id="edit_pim_username" name="username" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="edit_pim_email" name="email" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kontak</label>
                    <input type="tel" id="edit_pim_kontak" name="kontak" inputmode="tel" pattern="\+?[0-9\s\-().]+" title="Hanya angka (boleh diawali +, spasi, strip, titik, kurung)" placeholder="cth: 081234567890" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Akun</label>
                <select id="edit_pim_status" name="status_akun" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditPimpinan').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditPimpinanModal(pimpinan) {
        document.getElementById('formEditPimpinan').action = `/master/pimpinan/${pimpinan.id_pengguna}`;
        document.getElementById('edit_pim_nama').value = pimpinan.nama;
        document.getElementById('edit_pim_username').value = pimpinan.username;
        document.getElementById('edit_pim_email').value = pimpinan.email;
        document.getElementById('edit_pim_kontak').value = pimpinan.kontak;
        document.getElementById('edit_pim_status').value = pimpinan.status_akun;
        document.getElementById('modalEditPimpinan').classList.remove('hidden');
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
