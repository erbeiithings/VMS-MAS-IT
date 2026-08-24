@extends('layouts.app')

@section('title', 'Data Customer')
@section('header_title', 'Manajemen Customer')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-white">Daftar Customer / Klien</h3>
            <p class="text-xs text-slate-400">Kelola informasi perusahaan customer mitra MAS-IT</p>
        </div>
        <button onclick="document.getElementById('modalTambahCustomer').classList.remove('hidden')" 
                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Customer
        </button>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl bg-white/[0.03] border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase bg-slate-900/80 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-4">Perusahaan</th>
                        <th class="p-4">PIC</th>
                        <th class="p-4">Kontak / Email</th>
                        <th class="p-4">Alamat</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="p-4 font-semibold text-white">{{ $c->nama_perusahaan }}</td>
                            <td class="p-4 text-blue-400 font-medium">{{ $c->pic }}</td>
                            <td class="p-4">
                                <p>{{ $c->telepon }}</p>
                                <p class="text-[10px] text-slate-500">{{ $c->email }}</p>
                            </td>
                            <td class="p-4 text-slate-400 max-w-xs truncate">{{ $c->alamat }}</td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button onclick="openEditModal({{ json_encode($c) }})" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.customer.destroy', $c->id_customer) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Customer', 'Apakah Anda yakin ingin menghapus customer {{ $c->nama_perusahaan }}?')" 
                                                class="p-1.5 bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white rounded-lg transition" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-500 italic">Belum ada data customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $customers->links() }}
        </div>
    </div>

</div>

<!-- Model Tambah -->
<div id="modalTambahCustomer" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#0b132b] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-white">Tambah Customer Baru</h4>
            <button onclick="document.getElementById('modalTambahCustomer').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('master.customer.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block text-slate-300 mb-1">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">Person in Charge (PIC)</label>
                    <input type="text" name="pic" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="telepon" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Email Perusahaan / PIC</label>
                <input type="email" name="email" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Alamat Kantor</label>
                <textarea name="alamat" rows="3" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahCustomer').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl">Simpan Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEditCustomer" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#0b132b] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-bold text-white">Edit Customer</h4>
            <button onclick="document.getElementById('modalEditCustomer').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form id="formEditCustomer" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-slate-300 mb-1">Nama Perusahaan</label>
                <input type="text" id="edit_nama_perusahaan" name="nama_perusahaan" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 mb-1">PIC</label>
                    <input type="text" id="edit_pic" name="pic" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">Telepon</label>
                    <input type="text" id="edit_telepon" name="telepon" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Email</label>
                <input type="email" id="edit_email" name="email" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Alamat</label>
                <textarea id="edit_alamat" name="alamat" rows="3" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditCustomer').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(customer) {
        document.getElementById('formEditCustomer').action = `/master/customer/${customer.id_customer}`;
        document.getElementById('edit_nama_perusahaan').value = customer.nama_perusahaan;
        document.getElementById('edit_pic').value = customer.pic;
        document.getElementById('edit_telepon').value = customer.telepon;
        document.getElementById('edit_email').value = customer.email;
        document.getElementById('edit_alamat').value = customer.alamat;
        document.getElementById('modalEditCustomer').classList.remove('hidden');
    }
</script>
@endsection