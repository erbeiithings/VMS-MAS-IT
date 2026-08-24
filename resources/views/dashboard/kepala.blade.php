@extends('layouts.app')

@section('title', 'Dashboard Kepala Pimpinan')
@section('header_title', 'Monitoring & Analisis Strategis')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80 backdrop-blur-sm">
            <p class="text-xs font-medium text-slate-400">Total Kunjungan</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $totalKunjungan }}</h3>
            <p class="text-[11px] text-blue-400 mt-2">Semua status alur kerja</p>
        </div>
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80 backdrop-blur-sm">
            <p class="text-xs font-medium text-slate-400">Total Customer</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $totalCustomer }}</h3>
            <p class="text-[11px] text-emerald-400 mt-2">Klien terdaftar</p>
        </div>
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80 backdrop-blur-sm">
            <p class="text-xs font-medium text-slate-400">Engineer Aktif</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $totalEngineer }}</h3>
            <p class="text-[11px] text-indigo-400 mt-2">Tim teknis lapangan</p>
        </div>
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-slate-800/80 backdrop-blur-sm">
            <p class="text-xs font-medium text-slate-400">Total Tools / Alat</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $totalTool }}</h3>
            <p class="text-[11px] text-amber-400 mt-2">Aset operasional</p>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Status Kunjungan Donut Chart -->
        <div class="p-6 rounded-2xl bg-white/[0.03] border border-slate-800/80">
            <h4 class="text-sm font-semibold text-slate-200 mb-4">Distribusi Status Kunjungan</h4>
            <div class="h-56 relative flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="grid grid-cols-3 gap-2 mt-4 text-center text-xs">
                <div class="p-2 bg-slate-900/50 rounded-lg">
                    <p class="text-amber-400 font-bold">{{ $kunjunganTerjadwal }}</p>
                    <p class="text-[10px] text-slate-400">Terjadwal</p>
                </div>
                <div class="p-2 bg-slate-900/50 rounded-lg">
                    <p class="text-blue-400 font-bold">{{ $kunjunganDikerjakan }}</p>
                    <p class="text-[10px] text-slate-400">Dikerjakan</p>
                </div>
                <div class="p-2 bg-slate-900/50 rounded-lg">
                    <p class="text-emerald-400 font-bold">{{ $kunjunganSelesai }}</p>
                    <p class="text-[10px] text-slate-400">Selesai</p>
                </div>
            </div>
        </div>

        <!-- Analisis Jenis Pekerjaan Terbanyak -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white/[0.03] border border-slate-800/80">
            <h4 class="text-sm font-semibold text-slate-200 mb-4">Jenis Pekerjaan Terbanyak</h4>
            <div class="space-y-4">
                @forelse($pekerjaanTerbanyak as $item)
                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-300 mb-1">
                            <span>{{ $item->pekerjaan }}</span>
                            <span class="text-blue-400">{{ $item->total }} Kunjungan</span>
                        </div>
                        <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-500 rounded-full" style="width: {{ ($item->total / max($totalKunjungan, 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic">Belum ada data kunjungan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Kunjungan Table -->
    <div class="p-6 rounded-2xl bg-white/[0.03] border border-slate-800/80">
        <h4 class="text-sm font-semibold text-slate-200 mb-4">Monitoring Kunjungan Terbaru</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase bg-slate-900/80 text-slate-400">
                    <tr>
                        <th class="p-3">Nomor</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Engineer</th>
                        <th class="p-3">Pekerjaan</th>
                        <th class="p-3">Tanggal & Waktu</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recentKunjungan as $kunjungan)
                        <tr class="hover:bg-slate-800/20">
                            <td class="p-3 font-semibold text-blue-400">{{ $kunjungan->nomor }}</td>
                            <td class="p-3">{{ $kunjungan->customer->nama_perusahaan ?? '-' }}</td>
                            <td class="p-3">{{ $kunjungan->engineer->user->nama ?? 'Belum Ditugaskan' }}</td>
                            <td class="p-3">{{ $kunjungan->pekerjaan }}</td>
                            <td class="p-3 text-slate-400">{{ $kunjungan->tanggal }} ({{ $kunjungan->waktu }})</td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-medium {{ $kunjungan->status == 'Selesai' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($kunjungan->status == 'Dikerjakan' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20') }}">
                                    {{ $kunjungan->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-500 italic">Belum ada kunjungan terbaru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Terjadwal', 'Dikerjakan', 'Selesai'],
                datasets: [{
                    data: [{{ $kunjunganTerjadwal }}, {{ $kunjunganDikerjakan }}, {{ $kunjunganSelesai }}],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981'],
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