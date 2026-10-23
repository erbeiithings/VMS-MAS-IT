@extends('layouts.app')

@section('title', 'Data Tools & Alat Kerja')
@section('header_title', 'Manajemen Tools & Alat Kerja')

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
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
            </span>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Inventaris Tools</h1>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring stok terkini, total unit, dan riwayat pemakaian alat operasional</p>
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('master.peminjaman.riwayat-semua') }}" class="inline-flex items-center gap-2 h-11 px-4 bg-white hover:bg-slate-50 text-[#003399] border border-slate-200 rounded-lg text-[13px] font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Peminjaman
            </a>
            <button onclick="document.getElementById('modalTambahTool').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Tool
            </button>
        </div>
    </div>

    <!-- Kartu Ringkasan Stok -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Jenis Tools</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $ringkasan['jenis'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Stok (Unit)</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $ringkasan['total_unit'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Stok Tersedia</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $ringkasan['tersedia'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Sedang Dipinjam</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $ringkasan['dipinjam'] }}</p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('master.tool.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian Data</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama Alat, Kode, atau Kategori..." class="w-full h-11 pl-10 pr-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
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
                    <a href="{{ route('master.tool.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-semibold rounded-lg transition flex items-center justify-center">
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
                        <th class="px-4 py-3 text-left font-semibold">Kode & Nama Alat</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold">Total Stok</th>
                        <th class="px-4 py-3 text-center font-semibold">Stok Terkini</th>
                        <th class="px-4 py-3 text-left font-semibold">Ketersediaan</th>
                        <th class="px-4 py-3 text-left font-semibold">Spesifikasi</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tools as $t)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100">
                                <a href="{{ route('master.tool.show', $t->kode) }}" class="font-mono text-[11px] text-[#003399] font-semibold uppercase hover:underline" title="Lihat detail tool">{{ $t->kode }}</a>
                                <p class="font-semibold text-slate-800 mt-1">{{ $t->nama_alat }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-700">{{ $t->kategori }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $t->stok + ($t->sedang_dipinjam ?? 0) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $t->stok <= 0 ? 'bg-rose-50 text-rose-700 border-rose-100' : ($t->stok <= 2 ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100') }}">
                                    {{ $t->stok }}
                                </span>
                                @if(($t->sedang_dipinjam ?? 0) > 0)
                                    <p class="text-[10px] text-amber-600 font-semibold mt-1">{{ $t->sedang_dipinjam }} dipinjam</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $t->status_ketersediaan == 'Tersedia' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                                    {{ $t->status_ketersediaan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-500 max-w-xs truncate">{{ $t->spesifikasi ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button onclick="openTambahStokModal('{{ $t->kode }}', '{{ addslashes($t->nama_alat) }}', {{ $t->stok }})" class="p-2 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Tambah Stok">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </button>
                                    <a href="{{ route('peminjaman.riwayat', $t->kode) }}" class="p-2 rounded-lg text-slate-400 hover:text-[#003399] hover:bg-blue-50 transition" title="Riwayat Peminjaman">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </a>
                                    <button onclick="openEditToolModal({{ json_encode($t) }})" class="p-2 rounded-lg text-slate-400 hover:text-[#003399] hover:bg-blue-50 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.tool.destroy', $t->kode) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Tool', 'Apakah Anda yakin ingin menghapus alat {{ $t->nama_alat }} ({{ $t->kode }})?')"
                                                class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400 font-medium italic">Belum ada inventaris tool.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $tools->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah Tool -->
<div id="modalTambahTool" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-lg font-bold text-slate-900">Tambah Tool / Alat Baru</h4>
            <button onclick="document.getElementById('modalTambahTool').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('master.tool.store') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Alat</label>
                    <div class="w-full h-11 px-3 flex items-center bg-slate-100 border border-slate-200 rounded-lg text-slate-500 text-xs">Otomatis dari Format Nomor</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori</label>
                    <input type="text" name="kategori" placeholder="Misal: Networking" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Alat</label>
                <input type="text" name="nama_alat" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Stok Awal</label>
                    <input type="number" name="stok" value="0" min="0" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ketersediaan</label>
                    <select name="status_ketersediaan" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Spesifikasi</label>
                <textarea name="spesifikasi" rows="2" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahTool').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan Tool</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Tool -->
<div id="modalEditTool" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-lg font-bold text-slate-900">Edit Data Tool</h4>
            <button onclick="document.getElementById('modalEditTool').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form id="formEditTool" method="POST" class="space-y-4 text-sm">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Alat</label>
                    <div id="edit_tool_kode" class="w-full h-11 px-3 flex items-center bg-slate-100 border border-slate-200 rounded-lg text-slate-500 font-mono font-semibold text-sm"></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori</label>
                    <input type="text" id="edit_tool_kategori" name="kategori" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Alat</label>
                <input type="text" id="edit_tool_nama" name="nama_alat" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Stok</label>
                    <input type="number" id="edit_tool_stok" name="stok" min="0" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ketersediaan</label>
                    <select id="edit_tool_status" name="status_ketersediaan" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Spesifikasi</label>
                <textarea id="edit_tool_spesifikasi" name="spesifikasi" rows="2" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditTool').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Stok -->
<div id="modalTambahStok" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-bold text-slate-900">Tambah Stok Tool</h4>
            <button onclick="document.getElementById('modalTambahStok').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <p class="text-xs text-slate-500 font-medium mb-1">Alat: <span id="tambahStokNama" class="font-semibold text-slate-800"></span></p>
        <p class="text-xs text-slate-500 font-medium mb-4">Stok saat ini: <span id="tambahStokSaatIni" class="font-semibold text-[#003399]"></span></p>
        <form id="formTambahStok" method="POST" class="space-y-4 text-sm">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jumlah Ditambah</label>
                <input type="number" id="tambahStokJumlah" name="jumlah" value="1" min="1" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="document.getElementById('modalTambahStok').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-semibold rounded-lg transition">Tambah Stok</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditToolModal(tool) {
        document.getElementById('formEditTool').action = `/master/tool/${tool.kode}`;
        document.getElementById('edit_tool_kode').textContent = tool.kode;
        document.getElementById('edit_tool_kategori').value = tool.kategori;
        document.getElementById('edit_tool_nama').value = tool.nama_alat;
        document.getElementById('edit_tool_stok').value = tool.stok ?? 0;
        document.getElementById('edit_tool_status').value = tool.status_ketersediaan;
        document.getElementById('edit_tool_spesifikasi').value = tool.spesifikasi || '';
        document.getElementById('modalEditTool').classList.remove('hidden');
    }

    function openTambahStokModal(kode, nama, stok) {
        document.getElementById('formTambahStok').action = `/master/tool/${kode}/tambah-stok`;
        document.getElementById('tambahStokNama').textContent = nama;
        document.getElementById('tambahStokSaatIni').textContent = stok;
        document.getElementById('tambahStokJumlah').value = 1;
        document.getElementById('modalTambahStok').classList.remove('hidden');
    }
</script>
@endsection
