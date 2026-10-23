@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')
@section('header_title', 'Laporan Kunjungan & Berita Acara')

@section('content')
<div class="space-y-6">

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Rekapitulasi Laporan Kunjungan</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pemantauan laporan pekerjaan dan persetujuan (Approval) internal.</p>
            </div>
        </div>
    </div>

    <!-- FITUR BARU: Baris Filter & Pencarian -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-4 items-end">
        <form action="{{ route('laporan.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 w-full">
                <label for="search" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian Data</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari No. Kunjungan, Nama Klien, atau Engineer..." class="w-full h-11 pl-9 pr-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>

            <!-- Sortir -->
            <div class="w-full sm:w-48">
                <label for="sort" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Urutkan</label>
                <select id="sort" name="sort" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Paling Baru (Terbaru)</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Paling Lama (Terlama)</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition flex items-center justify-center gap-2">
                    Terapkan
                </button>
                @if(request()->has('search') || request()->has('sort'))
                    <a href="{{ route('laporan.index') }}" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition flex items-center justify-center text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="rounded-xl bg-white border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nomor Kunjungan</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer & Engineer</th>
                        <th class="px-4 py-3 text-center font-semibold">Status Dokumen</th>
                        <th class="px-4 py-3 text-center font-semibold">Cetak PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporanList as $l)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100 align-top">
                                @if($l->kunjungan)
                                    <a href="{{ route('kunjungan.show', $l->kunjungan->nomor) }}" class="font-mono text-[#003399] font-bold hover:underline">{{ $l->kunjungan->nomor }}</a>
                                @else
                                    <span class="font-mono text-slate-400 font-bold">-</span>
                                @endif
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">{{ $l->kunjungan->pekerjaan ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $l->kunjungan->tanggal ?? '-' }}</p>
                            </td>
                            
                            <td class="px-4 py-3 border-t border-slate-100 align-top">
                                @if($l->kunjungan && $l->kunjungan->customer)
                                    <a href="{{ route('master.customer.show', $l->kunjungan->customer->kode) }}" class="font-bold text-slate-800 mb-1 hover:text-[#003399] hover:underline">{{ $l->kunjungan->customer->nama_perusahaan }}</a>
                                @else
                                    <p class="font-bold text-slate-800 mb-1">{{ $l->kunjungan->customer->nama_perusahaan ?? '-' }}</p>
                                @endif
                                <p class="text-[10px] text-slate-500 font-medium">Eng: {{ $l->kunjungan->engineer->user->nama ?? '-' }}</p>
                            </td>
                            
                            <td class="px-4 py-3 border-t border-slate-100 align-top text-center">
                                @if($l->buktiPenyelesaian)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-emerald-50 text-emerald-700 border-emerald-100">
                                        ✔ Ditandatangani
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-slate-100 text-slate-600 border-slate-200">
                                        Menunggu
                                    </span>
                                @endif
                            </td>



                            <!-- Cetak PDF & Kirim Email (khusus atasan) -->
                            <td class="px-4 py-3 border-t border-slate-100 align-top text-center space-y-2 min-w-[120px]">
                                @if(auth()->user()->id_role != 3)
                                <a href="{{ route('kunjungan.show', $l->kunjungan->nomor) }}?buka=revisi"
                                   class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white border border-amber-300 hover:bg-amber-50 text-amber-700 rounded-lg text-[10px] font-bold transition w-full shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Revisi</span>
                                </a>
                                @endif
                                <button type="button" onclick="bukaPreviewPdf('{{ $l->kunjungan->nomor }}')"
                                   class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 rounded-lg text-[10px] font-bold transition w-full shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Cetak PDF</span>
                                </button>
                                
                                @if($l->buktiPenyelesaian)
                                    <form action="{{ route('laporan.email', $l->kunjungan->nomor) }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="showConfirmModal(this.form, 'Kirim Email ke Klien', 'Apakah Anda yakin ingin mengirim Berita Acara ini ke email klien?')" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-[#002266] hover:bg-[#001233] text-white rounded-lg text-[10px] font-bold transition w-full shadow-sm">
                                        <span>📧 Kirim Klien</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-slate-400 font-medium italic">Belum ada laporan kunjungan yang diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200 bg-slate-50 rounded-b-xl">
            {{ $laporanList->links() }}
        </div>
    </div>

</div>

<!-- MODAL PREVIEW PDF (sama seperti di halaman kunjungan: lihat dulu, baru download) -->
<div id="modalPreviewPdf" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60" onclick="tutupPreviewPdf()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
            <h4 class="text-sm font-bold text-[#002266]">👁️ Preview Laporan PDF</h4>
            <button type="button" onclick="tutupPreviewPdf()" class="text-slate-400 hover:text-slate-700 text-xl leading-none">✕</button>
        </div>
        <div class="flex-1 overflow-y-auto bg-slate-200 p-3 max-h-[60vh]" id="pdfPreviewBody">
            <div id="pdfPreviewLoading" class="text-center py-10">
                <p class="text-sm font-bold text-slate-600">⏳ Memuat preview...</p>
            </div>
            <div id="pdfPreviewImages" class="space-y-3"></div>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 px-5 py-4 border-t border-slate-200">
            <a id="btnDownloadPdf" href="" class="flex-1 px-4 py-2.5 bg-[#003399] hover:bg-[#002266] text-white rounded-xl text-xs font-bold text-center transition">⬇️ Download PDF</a>
            <button type="button" onclick="tutupPreviewPdf()" class="flex-1 px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition">Tutup</button>
        </div>
    </div>
</div>
<script>
    async function bukaPreviewPdf(nomor) {
        const t = Date.now();
        document.getElementById('btnDownloadPdf').href = "/laporan/" + nomor + "/pdf?download=1&t=" + t;
        document.getElementById('modalPreviewPdf').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        const loading = document.getElementById('pdfPreviewLoading');
        const wrap = document.getElementById('pdfPreviewImages');
        loading.style.display = 'block';
        wrap.innerHTML = '';
        try {
            const res = await fetch("/laporan/" + nomor + "/preview?t=" + t);
            const data = await res.json();
            loading.style.display = 'none';
            if (data.images && data.images.length) {
                data.images.forEach(src => {
                    const img = document.createElement('img');
                    img.src = src;
                    img.className = 'w-full rounded-lg shadow-md border border-slate-300';
                    img.alt = 'Preview PDF';
                    wrap.appendChild(img);
                });
            } else if (data.pdf_url) {
                const frame = document.createElement('iframe');
                frame.src = data.pdf_url;
                frame.className = 'w-full rounded-lg shadow-md border border-slate-300 bg-white';
                frame.style.height = '60vh';
                frame.title = 'Preview PDF';
                wrap.appendChild(frame);
            } else {
                wrap.innerHTML = '<p class="text-center text-sm text-rose-600 font-bold py-6">Gagal memuat preview.</p>';
            }
        } catch (e) {
            loading.style.display = 'none';
            wrap.innerHTML = '<p class="text-center text-sm text-rose-600 font-bold py-6">Gagal memuat preview.</p>';
        }
    }
    function tutupPreviewPdf() {
        document.getElementById('modalPreviewPdf').classList.add('hidden');
        document.body.style.overflow = '';
    }
    // ESC menutup modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') tutupPreviewPdf();
    });
</script>
@endsection