@extends('layouts.app')

@section('title', 'Data Engineer')
@section('header_title', 'Manajemen Engineer Lapangan')

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
            <h3 class="text-lg font-bold text-white">Daftar Engineer & Akun Login</h3>
            <p class="text-xs text-slate-400">Kelola data teknisi dan hak akses kunjungan lapangan</p>
        </div>
        <button onclick="document.getElementById('modalTambahEngineer').classList.remove('hidden')" 
                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Engineer
        </button>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl bg-white/[0.03] border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase bg-slate-900/80 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-4">Nama Engineer</th>
                        <th class="p-4">Username & Email</th>
                        <th class="p-4">Kontak</th>
                        <th class="p-4">Ketersediaan</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($engineers as $e)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="p-4 font-semibold text-white">{{ $e->user->nama ?? '-' }}</td>
                            <td class="p-4">
                                <p class="text-blue-400 font-mono">{{ $e->user->username ?? '-' }}</p>
                                <p class="text-[10px] text-slate-500">{{ $e->user->email ?? '-' }}</p>
                            </td>
                            <td class="p-4">{{ $e->kontak }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $e->status_ketersediaan == 'Tersedia' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $e->status_ketersediaan }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button onclick="openEditEngineerModal({{ json_encode($e) }})" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.engineer.destroy', $e->id_engineer) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Engineer', 'Apakah Anda yakin ingin menghapus akun engineer {{ $e->user->nama ?? '' }}?')" 
                                                class="p-1.5 bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white rounded-lg transition" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-500 italic">Belum ada data engineer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $engineers->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah Engineer -->
<div id="modalTambahEngineer" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#0b132b] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-white">Tambah Engineer Baru</h4>
            <button onclick="document.getElementById('modalTambahEngineer').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('master.engineer.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block text-slate-300 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Username</label>
                    <input type="text" name="username" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Password Awal</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Kontak / No. WA</label>
                    <input type="text" name="kontak" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahEngineer').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl">Buat Akun Engineer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Engineer -->
<div id="modalEditEngineer" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#0b132b] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-white">Edit Data Engineer</h4>
            <button onclick="document.getElementById('modalEditEngineer').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form id="formEditEngineer" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-slate-300 mb-1">Nama Lengkap</label>
                <input type="text" id="edit_eng_nama" name="nama" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Username</label>
                    <input type="text" id="edit_eng_username" name="username" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Email</label>
                    <input type="email" id="edit_eng_email" name="email" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Kontak</label>
                    <input type="text" id="edit_eng_kontak" name="kontak" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Status Ketersediaan</label>
                <select id="edit_eng_status" name="status_ketersediaan" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                    <option value="Tersedia">Tersedia</option>
                    <option value="Tidak Tersedia">Tidak Tersedia</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditEngineer').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl">Simpan Perubahan</button>
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
@endsection