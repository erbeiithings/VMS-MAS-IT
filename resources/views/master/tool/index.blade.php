@extends('layouts.app')

@section('title', 'Data Tools & Alat Kerja')
@section('header_title', 'Manajemen Tools & Alat Kerja')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-[#002266]">Inventaris Alat Kerja & Tools</h3>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Monitoring status ketersediaan dan kondisi fisik alat operasional</p>
        </div>
        <button onclick="document.getElementById('modalTambahTool').classList.remove('hidden')" 
                class="px-4 py-2.5 bg-[#002266] hover:bg-[#001233] text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-900/20 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tool
        </button>
    </div>

    <!-- FITUR BARU: Baris Filter & Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-4 items-end">
        <form action="{{ route('master.tool.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 w-full">
                <label for="search" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian Data</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama Alat, Kode, atau Kategori..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-[#003399]">
                </div>
            </div>

            <!-- Sortir -->
            <div class="w-full sm:w-48">
                <label for="sort" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Urutkan</label>
                <select id="sort" name="sort" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-[#003399]">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Paling Baru (Terbaru)</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Paling Lama (Terlama)</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-[#002266] hover:bg-[#001233] text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-900/20 transition flex items-center justify-center gap-2">
                    Terapkan
                </button>
                @if(request()->has('search') || request()->has('sort'))
                    <a href="{{ route('master.tool.index') }}" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition flex items-center justify-center text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4 font-bold">Kode & Nama Alat</th>
                        <th class="p-4 font-bold">Kategori</th>
                        <th class="p-4 font-bold">Kondisi</th>
                        <th class="p-4 font-bold">Ketersediaan</th>
                        <th class="p-4 font-bold">Spesifikasi</th>
                        <th class="p-4 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tools as $t)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <span class="font-mono text-[10px] text-[#003399] font-bold uppercase">{{ $t->kode }}</span>
                                <p class="font-bold text-slate-800 mt-1">{{ $t->nama_alat }}</p>
                            </td>
                            <td class="p-4 font-medium text-slate-700">{{ $t->kategori }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $t->kondisi == 'Baik' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200' }}">
                                    {{ $t->kondisi }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $t->status_ketersediaan == 'Tersedia' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-rose-100 text-rose-700 border-rose-200' }}">
                                    {{ $t->status_ketersediaan }}
                                </span>
                            </td>
                            <td class="p-4 font-medium text-slate-500 max-w-xs truncate">{{ $t->spesifikasi ?? '-' }}</td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button onclick="openEditToolModal({{ json_encode($t) }})" class="p-1.5 bg-amber-50 border border-amber-100 hover:bg-amber-500 text-amber-600 hover:text-white rounded-lg transition shadow-sm" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.tool.destroy', $t->id_tool) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Tool', 'Apakah Anda yakin ingin menghapus alat {{ $t->nama_alat }} ({{ $t->kode }})?')" 
                                                class="p-1.5 bg-rose-50 border border-rose-100 hover:bg-rose-600 text-rose-600 hover:text-white rounded-lg transition shadow-sm" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400 font-medium italic">Belum ada inventaris tool.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Menampilkan Pagination Bawaan Laravel yang sudah otomatis menghandle filter (appends) -->
        <div class="p-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
            {{ $tools->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah Tool -->
<div id="modalTambahTool" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-[#002266]">Tambah Tool / Alat Baru</h4>
            <button onclick="document.getElementById('modalTambahTool').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition text-lg">&times;</button>
        </div>
        <form action="{{ route('master.tool.store') }}" method="POST" class="space-y-4 text-xs font-medium">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Kode Alat</label>
                    <input type="text" name="kode" placeholder="Misal: TOOL-NET-002" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Kategori</label>
                    <input type="text" name="kategori" placeholder="Misal: Networking" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-slate-700 font-bold mb-1.5">Nama Alat</label>
                <input type="text" name="nama_alat" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Kondisi</label>
                    <select name="kondisi" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Ketersediaan</label>
                    <select name="status_ketersediaan" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-700 font-bold mb-1.5">Spesifikasi</label>
                <textarea name="spesifikasi" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="document.getElementById('modalTambahTool').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001233] text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition">Simpan Tool</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Tool -->
<div id="modalEditTool" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-[#002266]">Edit Data Tool</h4>
            <button onclick="document.getElementById('modalEditTool').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition text-lg">&times;</button>
        </div>
        <form id="formEditTool" method="POST" class="space-y-4 text-xs font-medium">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Kode Alat</label>
                    <input type="text" id="edit_tool_kode" name="kode" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Kategori</label>
                    <input type="text" id="edit_tool_kategori" name="kategori" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-slate-700 font-bold mb-1.5">Nama Alat</label>
                <input type="text" id="edit_tool_nama" name="nama_alat" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Kondisi</label>
                    <select id="edit_tool_kondisi" name="kondisi" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">Ketersediaan</label>
                    <select id="edit_tool_status" name="status_ketersediaan" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-700 font-bold mb-1.5">Spesifikasi</label>
                <textarea id="edit_tool_spesifikasi" name="spesifikasi" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399]"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="document.getElementById('modalEditTool').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-[#0044cc] hover:bg-[#003399] text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditToolModal(tool) {
        document.getElementById('formEditTool').action = `/master/tool/${tool.id_tool}`;
        document.getElementById('edit_tool_kode').value = tool.kode;
        document.getElementById('edit_tool_kategori').value = tool.kategori;
        document.getElementById('edit_tool_nama').value = tool.nama_alat;
        document.getElementById('edit_tool_kondisi').value = tool.kondisi;
        document.getElementById('edit_tool_status').value = tool.status_ketersediaan;
        document.getElementById('edit_tool_spesifikasi').value = tool.spesifikasi || '';
        document.getElementById('modalEditTool').classList.remove('hidden');
    }
</script>
@endsection