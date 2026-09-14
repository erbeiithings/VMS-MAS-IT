@extends('layouts.app')

@section('title', 'Kunjungan Kerja')
@section('header_title', 'Manajemen Kunjungan Kerja')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
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
                            <td class="p-4 align-top">
                                <span class="font-mono text-blue-400 font-semibold">{{ $k->nomor }}</span>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $k->tanggal }} ({{ $k->waktu }})</p>
                            </td>
                            <td class="p-4 align-top">
                                <p class="font-semibold text-white">{{ $k->customer->nama_perusahaan ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400 truncate max-w-xs">{{ $k->lokasi }}</p>
                            </td>
                            <td class="p-4 text-slate-200 align-top">{{ $k->pekerjaan }}</td>
                            <td class="p-4 align-top">
                                <span class="text-slate-300">{{ $k->engineer->user->nama ?? 'Belum Ditugaskan' }}</span>
                            </td>
                            <td class="p-4 align-top">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $k->status == 'Selesai' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($k->status == 'Dikerjakan' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : ($k->status == 'Reschedule' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20')) }}">
                                    {{ $k->status }}
                                </span>
                                @if($k->status == 'Reschedule')
                                    <p class="text-[9px] text-rose-400 mt-1 max-w-[120px] truncate" title="{{ $k->alasan_reschedule }}">Alasan: {{ $k->alasan_reschedule }}</p>
                                @endif
                            </td>
                            <td class="p-4 align-top text-center flex flex-col items-center gap-2">
                                <a href="{{ route('kunjungan.show', $k->id_kunjungan) }}" class="w-full px-3 py-1.5 bg-blue-600/10 hover:bg-blue-600 text-blue-400 hover:text-white rounded-lg text-[10px] font-semibold transition">
                                    Detail
                                </a>
                                
                                <!-- Akses Khusus Pimpinan (Bisa Edit & Hapus jika belum Selesai) -->
                                @if(Auth::user()->id_role == 2 && $k->status != 'Selesai')
                                    <button onclick="document.getElementById('modalEditKunjungan-{{ $k->id_kunjungan }}').classList.remove('hidden')" class="w-full px-3 py-1.5 bg-amber-600/10 hover:bg-amber-600 text-amber-400 hover:text-white rounded-lg text-[10px] font-semibold transition">
                                        Edit / Reschedule
                                    </button>
                                    
                                    <form id="formDeleteKunjungan-{{ $k->id_kunjungan }}" action="{{ route('kunjungan.destroy', $k->id_kunjungan) }}" method="POST" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(document.getElementById('formDeleteKunjungan-{{ $k->id_kunjungan }}'), 'Hapus Jadwal', 'Apakah Anda yakin ingin menghapus jadwal kunjungan ini secara permanen? Data yang sudah dihapus tidak dapat dikembalikan.')" class="w-full px-3 py-1.5 bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-lg text-[10px] font-semibold transition">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal Edit Kunjungan (Spesifik per ID) -->
                        @if(Auth::user()->id_role == 2 && $k->status != 'Selesai')
                        <div id="modalEditKunjungan-{{ $k->id_kunjungan }}" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 text-left">
                            <div class="bg-[#0b132b] border border-slate-700/80 rounded-2xl w-full max-w-lg p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-base font-bold text-white">Edit Jadwal: {{ $k->nomor }}</h4>
                                    <button onclick="document.getElementById('modalEditKunjungan-{{ $k->id_kunjungan }}').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
                                </div>
                                <form action="{{ route('kunjungan.update', $k->id_kunjungan) }}" method="POST" class="space-y-4 text-xs">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-slate-300 mb-1">Customer / Klien</label>
                                        <select name="id_customer" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                                            @foreach($customers as $c)
                                                <option value="{{ $c->id_customer }}" {{ $k->id_customer == $c->id_customer ? 'selected' : '' }}>{{ $c->nama_perusahaan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-300 mb-1">Tugaskan Engineer</label>
                                        <select name="id_engineer" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                                            <option value="">Pilih Engineer</option>
                                            @foreach($engineers as $e)
                                                <option value="{{ $e->id_engineer }}" {{ $k->id_engineer == $e->id_engineer ? 'selected' : '' }}>{{ $e->user->nama ?? '-' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-300 mb-1">Tim Support (Maks. 4 Orang)</label>
                                        <div class="grid grid-cols-2 gap-2 bg-slate-900/60 p-3 rounded-xl border border-slate-800 max-h-32 overflow-y-auto">
                                            @php $selectedSupport = $k->supportEngineers->pluck('id_engineer')->toArray(); @endphp
                                            @foreach($engineers as $e)
                                                <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                                                    <input type="checkbox" name="support_engineers[]" value="{{ $e->id_engineer }}" {{ in_array($e->id_engineer, $selectedSupport) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-0">
                                                    <span class="text-[11px] truncate">{{ $e->user->nama ?? '-' }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-slate-300 mb-1">Tanggal</label>
                                            <input type="date" name="tanggal" value="{{ $k->tanggal }}" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-slate-300 mb-1">Waktu</label>
                                            <input type="time" name="waktu" value="{{ $k->waktu }}" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-slate-300 mb-1">Deskripsi Pekerjaan</label>
                                        <input type="text" name="pekerjaan" value="{{ $k->pekerjaan }}" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-slate-300 mb-1">Lokasi Kunjungan</label>
                                        <textarea name="lokasi" rows="2" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none">{{ $k->lokasi }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-slate-300 mb-1">Tools & Alat yang Dibawa</label>
                                        <div class="grid grid-cols-2 gap-2 bg-slate-900/60 p-3 rounded-xl border border-slate-800 max-h-32 overflow-y-auto">
                                            @php $selectedTools = $k->tools->pluck('id_tool')->toArray(); @endphp
                                            @foreach($tools as $t)
                                                <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                                                    <input type="checkbox" name="tools[]" value="{{ $t->id_tool }}" {{ in_array($t->id_tool, $selectedTools) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-0">
                                                    <span class="text-[11px] truncate">{{ $t->nama_alat }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" onclick="document.getElementById('modalEditKunjungan-{{ $k->id_kunjungan }}').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl">Update Jadwal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

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
            <div>
                <label class="block text-slate-300 mb-1">Tim Support (Maks. 4 Orang)</label>
                <div class="grid grid-cols-2 gap-2 bg-slate-900/60 p-3 rounded-xl border border-slate-800 max-h-32 overflow-y-auto">
                    @foreach($engineers as $e)
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                            <input type="checkbox" name="support_engineers[]" value="{{ $e->id_engineer }}" class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-0">
                            <span class="text-[11px] truncate">{{ $e->user->nama ?? '-' }}</span>
                        </label>
                    @endforeach
                </div>
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
<!-- SCRIPT UNTUK MENCEGAH LEAD DAN SUPPORT SAMA -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk form Tambah
        const leadSelectAdd = document.querySelector('#modalTambahKunjungan select[name="id_engineer"]');
        const supportCheckboxesAdd = document.querySelectorAll('#modalTambahKunjungan input[name="support_engineers[]"]');
        
        if(leadSelectAdd) {
            leadSelectAdd.addEventListener('change', function() {
                const selectedLeadId = this.value;
                supportCheckboxesAdd.forEach(cb => {
                    if(cb.value === selectedLeadId) {
                        cb.checked = false;
                        cb.disabled = true;
                        cb.nextElementSibling.classList.add('line-through', 'opacity-50');
                    } else {
                        cb.disabled = false;
                        cb.nextElementSibling.classList.remove('line-through', 'opacity-50');
                    }
                });
            });
        }

        // Fungsi untuk form Edit (Looping karena id modalnya beda-beda)
        const editModals = document.querySelectorAll('[id^="modalEditKunjungan-"]');
        editModals.forEach(modal => {
            const leadSelectEdit = modal.querySelector('select[name="id_engineer"]');
            const supportCheckboxesEdit = modal.querySelectorAll('input[name="support_engineers[]"]');
            
            if(leadSelectEdit) {
                // Run sekali pas modal dibuka (untuk nyesuain data awal)
                const initialSelectedLeadId = leadSelectEdit.value;
                supportCheckboxesEdit.forEach(cb => {
                    if(cb.value === initialSelectedLeadId) {
                        cb.checked = false;
                        cb.disabled = true;
                        cb.nextElementSibling.classList.add('line-through', 'opacity-50');
                    }
                });

                // Run tiap kali dropdown diganti
                leadSelectEdit.addEventListener('change', function() {
                    const selectedLeadId = this.value;
                    supportCheckboxesEdit.forEach(cb => {
                        if(cb.value === selectedLeadId) {
                            cb.checked = false;
                            cb.disabled = true;
                            cb.nextElementSibling.classList.add('line-through', 'opacity-50');
                        } else {
                            cb.disabled = false;
                            cb.nextElementSibling.classList.remove('line-through', 'opacity-50');
                        }
                    });
                });
            }
        });
    });
</script>
@endsection