@extends('layouts.app')

@section('title', 'Dashboard Kepala Pimpinan')
@section('header_title', 'Monitoring & Analisis Strategis')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </span>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Dashboard Kepala Pimpinan</h1>
            <p class="text-xs text-slate-500">Monitoring & Analisis Strategis</p>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-500">Total Kunjungan</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalKunjungan }}</h3>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Semua status alur kerja</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-500">Total Customer</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalCustomer }}</h3>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Klien terdaftar</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-500">Engineer Aktif</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalEngineer }}</h3>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Tim teknis lapangan</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-500">Total Tools / Alat</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalTool }}</h3>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Aset operasional</p>
        </div>
    </div>

    <!-- QA #22: Pengeluaran engineer yang belum diganti perusahaan -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-sm font-bold text-slate-900">Pengeluaran Engineer Belum Diganti</h4>
            <span class="text-sm font-extrabold text-rose-600">Total: Rp {{ number_format($totalBelumDiganti, 0, ',', '.') }}</span>
        </div>
        @forelse($reimbursePerEngineer as $r)
            <div class="flex items-center justify-between py-2 border-t border-slate-100 text-sm">
                <span class="font-medium text-slate-700">{{ $r->engineer->user->nama ?? 'Engineer' }} <span class="text-slate-400 text-xs">({{ $r->jumlah }} pengeluaran)</span></span>
                <span class="font-bold text-slate-800">Rp {{ number_format($r->total, 0, ',', '.') }}</span>
            </div>
        @empty
            <p class="text-xs text-slate-400 italic">Tidak ada pengeluaran yang menunggu penggantian.</p>
        @endforelse
    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Status Kunjungan Donut Chart -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-sm font-bold text-slate-900 mb-4">Distribusi Status Kunjungan</h4>
            <div class="h-56 relative flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                <div class="p-2 bg-slate-50 border border-slate-100 rounded-lg">
                    <p class="text-sm font-extrabold text-amber-600">{{ $kunjunganTerjadwal }}</p>
                    <p class="text-[10px] text-slate-500">Terjadwal</p>
                </div>
                <div class="p-2 bg-slate-50 border border-slate-100 rounded-lg">
                    <p class="text-sm font-extrabold text-[#0044cc]">{{ $kunjunganDikerjakan }}</p>
                    <p class="text-[10px] text-slate-500">Sedang Dikerjakan</p>
                </div>
                <div class="p-2 bg-slate-50 border border-slate-100 rounded-lg">
                    <p class="text-sm font-extrabold text-emerald-600">{{ $kunjunganSelesai }}</p>
                    <p class="text-[10px] text-slate-500">Selesai</p>
                </div>
            </div>
        </div>

        <!-- Analisis Jenis Pekerjaan Terbanyak -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-sm font-bold text-slate-900 mb-4">Jenis Pekerjaan Terbanyak</h4>
            <div class="space-y-4">
                @forelse($pekerjaanTerbanyak as $item)
                    <div>
                        <div class="flex justify-between text-xs font-semibold text-slate-700 mb-1.5">
                            <span>{{ $item->pekerjaan }}</span>
                            <span class="text-[#003399]">{{ $item->total }} Kunjungan</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-[#002266] rounded-full" style="width: {{ ($item->total / max($totalKunjungan, 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">Belum ada data kunjungan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Kunjungan Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h4 class="text-sm font-bold text-slate-900">Monitoring Kunjungan Terbaru</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nomor</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Engineer</th>
                        <th class="px-4 py-3 text-left font-semibold">Pekerjaan</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal & Waktu</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentKunjungan as $kunjungan)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 border-t border-slate-100 font-semibold"><a href="{{ route('kunjungan.show', $kunjungan->nomor) }}" class="text-[#003399] hover:underline">{{ $kunjungan->nomor }}</a></td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-800">{{ $kunjungan->customer->nama_perusahaan ?? '-' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-600">{{ $kunjungan->engineer->user->nama ?? 'Belum Ditugaskan' }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-600">{{ $kunjungan->pekerjaan }}</td>
                            <td class="px-4 py-3 border-t border-slate-100 text-slate-500">{{ $kunjungan->tanggal }} ({{ $kunjungan->waktu }})</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $kunjungan->status == 'Selesai' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($kunjungan->status == 'Dikerjakan' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-amber-50 text-amber-700 border-amber-100') }}">
                                    {{ $kunjungan->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 border-t border-slate-100 text-center text-slate-400 italic">Belum ada kunjungan terbaru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex justify-center">
            <a href="{{ route('kunjungan.index') }}" class="text-[13px] font-semibold text-[#003399] hover:text-[#001a4d] flex items-center gap-1.5 transition">
                Lihat Semua Kunjungan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Terjadwal', 'Sedang Dikerjakan', 'Selesai'],
                datasets: [{
                    data: [{{ $kunjunganTerjadwal }}, {{ $kunjunganDikerjakan }}, {{ $kunjunganSelesai }}],
                    backgroundColor: ['#f59e0b', '#0044cc', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
