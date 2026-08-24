@extends('layouts.app')

@section('title', 'Data Tools & Alat Kerja')
@section('header_title', 'Manajemen Tools & Alat Kerja')

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
            <h3 class="text-lg font-bold text-white">Inventaris Alat Kerja & Tools</h3>
            <p class="text-xs text-slate-400">Monitoring status ketersediaan dan kondisi fisik alat operasional</p>
        </div>
        <button onclick="document.getElementById('modalTambahTool').classList.remove('hidden')" 
                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tool
        </button>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl bg-white/[0.03] border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase bg-slate-900/80 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-4">Kode & Nama Alat</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Kondisi</th>
                        <th class="p-4">Ketersediaan</th>
                        <th class="p-4">Spesifikasi</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($tools as $t)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="p-4">
                                <span class="font-mono text-[10px] text-blue-400 font-semibold uppercase">{{ $t->kode }}</span>
                                <p class="font-semibold text-white mt-0.5">{{ $t->nama_alat }}</p>
                            </td>
                            <td class="p-4 text-slate-300">{{ $t->kategori }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $t->kondisi == 'Baik' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                    {{ $t->kondisi }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $t->status_ketersediaan == 'Tersedia' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $t->status_ketersediaan }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-400 max-w-xs truncate">{{ $t->spesifikasi ?? '-' }}</td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button onclick="openEditToolModal({{ json_encode($t) }})" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.tool.destroy', $t->id_tool) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Tool', 'Apakah Anda yakin ingin menghapus alat {{ $t->nama_alat }} ({{ $t->kode }})?')" 
                                                class="p-1.5 bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white rounded-lg transition" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500 italic">Belum ada inventaris tool.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $tools->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah Tool -->
<div id="modalTambahTool" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#0b132b] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-white">Tambah Tool / Alat Baru</h4>
            <button onclick="document.getElementById('modalTambahTool').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('master.tool.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Kode Alat</label>
                    <input type="text" name="kode" placeholder="Misal: TOOL-NET-002" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Kategori</label>
                    <input type="text" name="kategori" placeholder="Misal: Networking" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Nama Alat</label>
                <input type="text" name="nama_alat" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Kondisi</label>
                    <select name="kondisi" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Ketersediaan</label>
                    <select name="status_ketersediaan" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Spesifikasi</label>
                <textarea name="spesifikasi" rows="2" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahTool').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl">Simpan Tool</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Tool -->
<div id="modalEditTool" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#0b132b] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-white">Edit Data Tool</h4>
            <button onclick="document.getElementById('modalEditTool').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form id="formEditTool" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Kode Alat</label>
                    <input type="text" id="edit_tool_kode" name="kode" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Kategori</label>
                    <input type="text" id="edit_tool_kategori" name="kategori" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Nama Alat</label>
                <input type="text" id="edit_tool_nama" name="nama_alat" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Kondisi</label>
                    <select id="edit_tool_kondisi" name="kondisi" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Ketersediaan</label>
                    <select id="edit_tool_status" name="status_ketersediaan" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Spesifikasi</label>
                <textarea id="edit_tool_spesifikasi" name="spesifikasi" rows="2" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditTool').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl">Simpan Perubahan</button>
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