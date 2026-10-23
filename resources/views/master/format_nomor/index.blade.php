@extends('layouts.app')

@section('title', 'Format Nomor')
@section('header_title', 'Pengaturan Format Nomor')

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
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
            </span>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Format Penomoran</h1>
                <p class="text-xs text-slate-500 mt-0.5">Atur prefix, tahun, dan digit nomor — naik +1 otomatis setiap ada data baru</p>
            </div>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg px-4 py-2.5 transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Format
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold">Deskripsi</th>
                        <th class="px-4 py-3 text-left font-semibold">Prefix</th>
                        <th class="px-4 py-3 text-left font-semibold">Tahun</th>
                        <th class="px-4 py-3 text-center font-semibold">Digit</th>
                        <th class="px-4 py-3 text-center font-semibold">Terakhir</th>
                        <th class="px-4 py-3 text-left font-semibold">Nomor Berikutnya</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($formats as $f)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 border-t border-slate-100">
                            <div class="font-semibold text-slate-900">{{ $f->jenis }}</div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $f->kode }}</div>
                        </td>
                        <td class="px-4 py-3 border-t border-slate-100 max-w-[220px]"><span class="text-slate-500">{{ $f->deskripsi ?? '-' }}</span></td>
                        <td class="px-4 py-3 border-t border-slate-100"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100 font-mono">{{ $f->prefix }}</span></td>
                        <td class="px-4 py-3 border-t border-slate-100 font-semibold text-slate-700">{{ $f->tahun ?? '-' }}</td>
                        <td class="px-4 py-3 border-t border-slate-100 text-center font-semibold text-slate-700">{{ $f->digit }}</td>
                        <td class="px-4 py-3 border-t border-slate-100 text-center">
                            @if($f->nomor_terakhir > 0)
                                <span class="font-mono text-slate-500">{{ $f->nomorTerkini() }}</span>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border-t border-slate-100"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 font-mono">{{ $f->nomorBerikutnya() }}</span></td>
                        <td class="px-4 py-3 border-t border-slate-100">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="bukaEdit({{ $f->id }})" class="p-2 rounded-lg text-slate-400 hover:text-[#003399] hover:bg-blue-50 transition" title="Ubah">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if(!in_array($f->kode, ['kunjungan', 'customer', 'tool']))
                                <form action="{{ route('master.format-nomor.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Hapus format ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400">Belum ada format nomor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-800">
        <span class="font-bold">Cara kerja:</span> setiap ada kunjungan/customer baru, sistem otomatis mengambil nomor berikutnya dari format ini (counter naik +1). Format bawaan <span class="font-mono font-semibold">kunjungan</span>, <span class="font-mono font-semibold">customer</span>, dan <span class="font-mono font-semibold">tool</span> tidak bisa dihapus, tapi prefix, tahun, digit, dan counter-nya bebas diubah.
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <form action="{{ route('master.format-nomor.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="flex justify-between items-center">
                <h4 class="text-lg font-bold text-slate-900">Tambah Format Nomor</h4>
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kode (kunci, huruf kecil tanpa spasi)</label>
                <input type="text" name="kode" required placeholder="misal: laporan" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base font-mono focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jenis</label>
                <input type="text" name="jenis" required placeholder="misal: ID Laporan" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="2" placeholder="Menjelaskan ID ini untuk apa..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Prefix</label>
                    <input type="text" name="prefix" data-preview required placeholder="vmsmit" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base font-mono focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun</label>
                    <input type="number" name="tahun" data-preview value="{{ date('Y') }}" min="2000" max="2100" placeholder="Opsional" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan kalau tidak pakai tahun.</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Digit</label>
                    <input type="number" name="digit" data-preview required value="3" min="1" max="10" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nomor terakhir dipakai</label>
                <input type="number" name="nomor_terakhir" data-preview required value="0" min="0" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Nomor berikutnya</div>
                <div data-preview-out class="font-mono font-bold text-emerald-700 text-base">-</div>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="flex-1 h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="flex-1 h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <form id="formEdit" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="flex justify-between items-center">
                <h4 class="text-lg font-bold text-slate-900">Ubah Format Nomor</h4>
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jenis</label>
                <input type="text" name="jenis" id="edit_jenis" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" id="edit_deskripsi" rows="2" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Prefix</label>
                    <input type="text" name="prefix" id="edit_prefix" data-preview required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base font-mono focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun</label>
                    <input type="number" name="tahun" id="edit_tahun" data-preview min="2000" max="2100" placeholder="Opsional" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan kalau tidak pakai tahun.</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Digit</label>
                    <input type="number" name="digit" id="edit_digit" data-preview required min="1" max="10" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nomor terakhir dipakai</label>
                <input type="number" name="nomor_terakhir" id="edit_nomor_terakhir" data-preview required min="0" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                <p class="text-[10px] text-slate-400 mt-1">Ubah angka ini kalau mau lompat/mundur nomor urut.</p>
            </div>
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Nomor berikutnya</div>
                <div data-preview-out class="font-mono font-bold text-emerald-700 text-base">-</div>
            </div>
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-100">
                <p class="text-[11px] text-amber-700 leading-relaxed"><span class="font-bold">Perhatian:</span> kalau prefix, tahun, atau digit diubah, <span class="font-bold">semua kode yang sudah ada</span> otomatis disinkronkan ke format baru.</p>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="flex-1 h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="flex-1 h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
const formats = @json($formats->keyBy('id'));

function previewNomor(prefix, tahun, digit, terakhir) {
    const yy = tahun ? String(tahun).slice(-2) : '';
    const next = String(Number(terakhir) + 1).padStart(Number(digit) || 1, '0');
    return (prefix || '') + yy + next;
}

function bindPreview(form) {
    const out = form.querySelector('[data-preview-out]');
    const update = () => {
        const get = n => form.querySelector(`[name="${n}"]`).value;
        out.textContent = previewNomor(get('prefix'), get('tahun'), get('digit'), get('nomor_terakhir'));
    };
    form.querySelectorAll('[data-preview]').forEach(el => el.addEventListener('input', update));
    update();
}

document.querySelectorAll('#modalTambah form, #formEdit').forEach(bindPreview);

function bukaEdit(id) {
    const f = formats[id];
    document.getElementById('formEdit').action = "{{ url('master/format-nomor') }}/" + id;
    document.getElementById('edit_jenis').value = f.jenis;
    document.getElementById('edit_deskripsi').value = f.deskripsi ?? '';
    document.getElementById('edit_prefix').value = f.prefix;
    document.getElementById('edit_tahun').value = f.tahun ?? '';
    document.getElementById('edit_digit').value = f.digit;
    document.getElementById('edit_nomor_terakhir').value = f.nomor_terakhir;
    document.getElementById('edit_prefix').dispatchEvent(new Event('input'));
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>
@endsection
