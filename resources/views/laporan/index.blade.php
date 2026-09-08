@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')
@section('header_title', 'Laporan Kunjungan & Berita Acara')

@section('content')
<div class="space-y-6">

    <!-- Alert Notifikasi (Akan muncul jika ada error/sukses dari controller) -->
    @if(session('success'))
        <div class="p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-white">Rekapitulasi Laporan Kunjungan</h3>
            <p class="text-xs text-slate-400">Pemantauan laporan pekerjaan dan persetujuan (Approval) internal.</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl bg-white/[0.03] border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase bg-slate-900/80 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-4">Nomor Kunjungan</th>
                        <th class="p-4">Customer & Engineer</th>
                        <th class="p-4 text-center">Status Dokumen</th>
                        <th class="p-4 text-center">Status Approval</th>
                        <th class="p-4">Aksi / Catatan Atasan</th>
                        <th class="p-4 text-center">Cetak PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($laporanList as $l)
                        <tr class="hover:bg-slate-800/20 transition">
                            <!-- Nomor & Pekerjaan -->
                            <td class="p-4 align-top">
                                <span class="font-mono text-blue-400 font-semibold">{{ $l->kunjungan->nomor ?? '-' }}</span>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $l->kunjungan->pekerjaan ?? '-' }}</p>
                                <p class="text-[10px] text-slate-500 mt-1">{{ $l->kunjungan->tanggal ?? '-' }}</p>
                            </td>
                            
                            <!-- Customer & Engineer -->
                            <td class="p-4 align-top">
                                <p class="font-medium text-white mb-1">{{ $l->kunjungan->customer->nama_perusahaan ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">Eng: {{ $l->kunjungan->engineer->user->nama ?? '-' }}</p>
                            </td>
                            
                            <!-- Status Tanda Tangan -->
                            <td class="p-4 align-top text-center">
                                @if($l->buktiPenyelesaian)
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        ✔ Ditandatangani
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        Menunggu
                                    </span>
                                @endif
                            </td>

                            <!-- Badge Status Approval -->
                            <td class="p-4 align-top text-center">
                                @if($l->status_approval == 'Disetujui')
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">✔ Disetujui</span>
                                @elseif($l->status_approval == 'Revisi')
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">✍ Revisi</span>
                                @elseif($l->status_approval == 'Ditolak')
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">✖ Ditolak</span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20">⌛ Menunggu</span>
                                @endif
                            </td>

                            <!-- Form Approval (Untuk Atasan) / Catatan (Untuk Engineer) -->
                            <td class="p-4 min-w-[240px] align-top">
                                @if(auth()->user()->id_role != 3) 
                                    {{-- Form ini khusus buat Role Kepala & Pimpinan --}}
                                    <form action="{{ route('laporan.approve', $l->id_laporan) }}" method="POST" class="flex flex-col gap-2">
                                        @csrf
                                        <div class="flex gap-2">
                                            <select name="status_approval" class="bg-slate-900 border border-slate-700 text-xs rounded-lg p-1.5 text-slate-300 outline-none focus:border-blue-500 w-full transition">
                                                <option value="Menunggu Persetujuan" {{ $l->status_approval == 'Menunggu Persetujuan' ? 'selected' : '' }}>⌛ Menunggu</option>
                                                <option value="Disetujui" {{ $l->status_approval == 'Disetujui' ? 'selected' : '' }}>✔ Disetujui</option>
                                                <option value="Revisi" {{ $l->status_approval == 'Revisi' ? 'selected' : '' }}>✍ Revisi</option>
                                                <option value="Ditolak" {{ $l->status_approval == 'Ditolak' ? 'selected' : '' }}>✖ Ditolak</option>
                                            </select>
                                            <button type="submit" class="bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 text-xs px-3 py-1.5 rounded-lg transition font-semibold">
                                                Simpan
                                            </button>
                                        </div>
                                        <input type="text" name="catatan_approval" value="{{ $l->catatan_approval }}" placeholder="Tambahkan catatan jika direvisi..." class="bg-slate-900 border border-slate-700 text-xs rounded-lg p-1.5 text-slate-300 w-full outline-none focus:border-blue-500 transition">
                                    </form>
                                @else 
                                    {{-- Kalau Engineer yang buka, cuma bisa baca catatannya aja --}}
                                    <div class="bg-slate-900/50 rounded-lg p-2 border border-slate-800">
                                        <p class="text-xs text-slate-400 italic">
                                            {{ $l->catatan_approval ? '"'.$l->catatan_approval.'"' : 'Tidak ada catatan dari atasan.' }}
                                        </p>
                                    </div>
                                @endif
                            </td>

                            <!-- Cetak PDF & Kirim Email -->
                            <td class="p-4 align-top text-center space-y-2 min-w-[120px]">
                                <!-- Tombol Download Biasa -->
                                <a href="{{ route('laporan.pdf', $l->id_kunjungan) }}" target="_blank" 
                                   class="flex items-center justify-center gap-1.5 px-3 py-1.5 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded-lg text-[10px] font-semibold transition w-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Cetak PDF</span>
                                </a>
                                
                                <!-- Tombol Email (Hanya muncul jika sudah di-ACC) -->
                                @if($l->status_approval == 'Disetujui')
                                    <form action="{{ route('laporan.email', $l->id_kunjungan) }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="showConfirmModal(this.form, 'Kirim Email ke Klien', 'Apakah Anda yakin ingin mengirim Berita Acara ini ke email klien?')" class="flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white rounded-lg text-[10px] font-semibold transition w-full">
                                        <span>📧 Kirim Klien</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500 italic">Belum ada laporan kunjungan yang diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $laporanList->links() }}
        </div>
    </div>

</div>
@endsection