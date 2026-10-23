@extends('layouts.app')

@section('title', 'Pengembalian Tools')
@section('header_title', 'Pengembalian Tools & Alat Kerja')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
            </span>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Pengembalian Tools</h1>
                <p class="text-xs text-slate-500">Kembalikan tools setelah selesai dipakai agar stok bertambah kembali</p>
            </div>
        </div>
        <button onclick="document.getElementById('modalPinjamTool').classList.remove('hidden')"
                class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Pinjam Tool (Keperluan Lain)
        </button>
    </div>

    <!-- Daftar Pinjaman Aktif -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Belum Dikembalikan</h2>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">{{ $pinjaman->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Tool</th>
                        <th class="px-4 py-3 text-left font-semibold text-center">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold">Dipinjam Pada</th>
                        <th class="px-4 py-3 text-left font-semibold">Keperluan</th>
                        <th class="px-4 py-3 text-left font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjaman as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100">
                                @if($p->tool)
                                    <a href="{{ route('master.tool.show', $p->tool->kode) }}" class="font-mono text-[11px] text-[#003399] font-semibold uppercase hover:underline">{{ $p->tool->kode }}</a>
                                @else
                                    <span class="font-mono text-[11px] text-slate-400 font-semibold uppercase">-</span>
                                @endif
                                <p class="font-semibold text-slate-800 mt-0.5">{{ $p->tool->nama_alat ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">{{ ($p->id_kunjungan && $p->jumlah == 0) ? 'Alat Tim' : $p->jumlah }}</span></td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $p->tanggal_pinjam->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-500 max-w-xs">
                                @if($p->id_kunjungan)
                                    <span class="text-[#003399] font-semibold">{{ $p->kunjungan->nomor ?? '-' }}</span>
                                    <span class="block text-[11px]">{{ $p->kunjungan->customer->nama_perusahaan ?? '' }}</span>
                                @else
                                    <span class="italic">{{ $p->keterangan ?? 'Keperluan lain' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center">
                                <button onclick="openKembalikanModal({{ $p->id_peminjaman }}, '{{ addslashes($p->tool->nama_alat ?? '') }}', {{ $p->jumlah }})"
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[12px] font-semibold rounded-lg transition">
                                    Kembalikan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400 italic">Tidak ada tools yang sedang dipinjam. Semua sudah dikembalikan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pinjaman->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">{{ $pinjaman->links() }}</div>
        @endif
    </div>

    <!-- Riwayat Pengembalian Saya -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-sm font-bold text-slate-900">Riwayat Pengembalian Saya</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Tool</th>
                        <th class="px-4 py-3 text-left font-semibold text-center">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold">Dipinjam</th>
                        <th class="px-4 py-3 text-left font-semibold">Dikembalikan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Kondisi</th>
                        <th class="px-4 py-3 text-left font-semibold">Keperluan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="font-mono text-[11px] text-[#003399] font-semibold uppercase">{{ $r->tool->kode ?? '-' }}</span>
                                <p class="font-semibold text-slate-800 mt-0.5">{{ $r->tool->nama_alat ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center font-semibold">{{ ($r->id_kunjungan && $r->jumlah == 0) ? 'Alat Tim' : $r->jumlah }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">{{ $r->tanggal_pinjam->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-emerald-600">{{ $r->tanggal_kembali?->format('d M Y, H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $r->status == 'Dibatalkan' ? 'bg-slate-50 text-slate-700 border-slate-200' : 'bg-emerald-50 text-emerald-700 border-emerald-100' }}">
                                    {{ $r->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                @if($r->kondisi_kembali)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $r->kondisi_kembali == 'Baik' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($r->kondisi_kembali == 'Rusak Ringan' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-rose-50 text-rose-700 border-rose-100') }}">
                                        {{ $r->kondisi_kembali }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-500">
                                @if($r->id_kunjungan)
                                    <span class="text-[#003399] font-semibold">{{ $r->kunjungan->nomor ?? '-' }}</span>
                                @else
                                    <span class="italic">{{ $r->keterangan ?? 'Keperluan lain' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400 italic">Belum ada riwayat pengembalian.</td>                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">{{ $riwayat->appends(['riwayat_page' => $riwayat->currentPage()])->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal Pinjam Tool -->
<div id="modalPinjamTool" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-xl p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-bold text-slate-900">Pinjam Tool (Keperluan Lain)</h4>
            <button onclick="document.getElementById('modalPinjamTool').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('peminjaman.pinjam') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            <div>
                <label class="block text-slate-700 text-sm font-semibold mb-1.5">Pilih Tool</label>
                <select name="id_tool" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <option value="">-- Pilih Tool --</option>
                    <optgroup label="Daftar Tools">
@foreach($tools as $t)
                        <option value="{{ $t->id_tool }}">{{ $t->nama_alat }} (stok: {{ $t->stok }})</option>
                    @endforeach
                    </optgroup>
                </select>
            </div>
            <div>
                <label class="block text-slate-700 text-sm font-semibold mb-1.5">Jumlah</label>
                <input type="number" name="jumlah" value="1" min="1" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div>
                <label class="block text-slate-700 text-sm font-semibold mb-1.5">Keterangan Keperluan</label>
                <textarea name="keterangan" rows="2" placeholder="Contoh: Dipakai untuk perbaikan di kantor..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalPinjamTool').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Pinjam</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Kembalikan Tool (dengan kondisi) -->
<div id="modalKembalikan" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-xl p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-bold text-slate-900">Kembalikan Tool</h4>
            <button onclick="document.getElementById('modalKembalikan').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <p class="text-xs text-slate-500 mb-4">Tool: <span id="kembaliNama" class="font-semibold text-slate-800"></span> (<span id="kembaliJumlah" class="font-semibold text-[#003399]"></span>)</p>
        <form id="formKembalikan" method="POST" class="space-y-4 text-sm">
            @csrf
            <div>
                <label class="block text-slate-700 text-sm font-semibold mb-1.5">Kondisi Tool Saat Dikembalikan <span class="text-rose-500">*</span></label>
                <select name="kondisi_kembali" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                    <option value="Baik">Baik — tidak ada kerusakan</option>
                    <option value="Rusak Ringan">Rusak Ringan — masih bisa dipakai</option>
                    <option value="Rusak Berat">Rusak Berat — tidak bisa dipakai</option>
                </select>
            </div>
            <div>
                <label class="block text-slate-700 text-sm font-semibold mb-1.5">Catatan (opsional)</label>
                <textarea name="catatan_kembali" rows="2" placeholder="Contoh: kabel sedikit terkelupas..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalKembalikan').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-semibold rounded-lg transition">Kembalikan & Tambah Stok</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openKembalikanModal(id, nama, jumlah) {
        document.getElementById('formKembalikan').action = `/peminjaman/${id}/kembalikan`;
        document.getElementById('kembaliNama').textContent = nama;
        document.getElementById('kembaliJumlah').textContent = jumlah;
        document.getElementById('modalKembalikan').classList.remove('hidden');
    }
</script>
@endsection
