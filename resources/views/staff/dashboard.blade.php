@extends('layouts.staff')

@section('content')
    <div class="container mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-header font-bold text-white">Main Station</h1>
            <p class="text-slate-400 font-body">Overview pencapaian unit
                {{ Auth::user()->divisi_id == 2 ? 'Infrastruktur' : 'TAC' }} hari ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="organic-card p-6 bg-gradient-to-br from-primary/10 to-transparent">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Daily Activity</p>
                <h2 class="text-5xl font-header font-bold text-primary">{{ $dailyCount }}</h2>
                <p class="text-slate-400 text-sm mt-2 italic">Aktivitas hari ini</p>
            </div>

            @if (Auth::user()->divisi_id != 2)
                <div class="organic-card p-6 border-b-4 border-blue-500">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Weekly Volume</p>
                    <h2 class="text-5xl font-header font-bold text-blue-400">{{ $weeklyCount }}</h2>
                    <p class="text-slate-400 text-sm mt-2 italic">Total minggu ini</p>
                </div>
                <div class="organic-card p-6 border-b-4 border-purple-500">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Monthly Milestone</p>
                    <h2 class="text-5xl font-header font-bold text-purple-400">{{ $monthlyCount }}</h2>
                    <p class="text-slate-400 text-sm mt-2 italic">Akumulasi bulan ini</p>
                </div>
                <div class="organic-card p-6 border-b-4 border-emerald-500">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Status Divisi</p>
                    <h2 class="text-3xl font-header font-bold text-emerald-400 uppercase">Active</h2>
                    <p class="text-slate-400 text-sm mt-2 italic">TAC Division</p>
                </div>
            @else
                <div class="organic-card p-6 border-b-4 border-emerald-500">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Status Divisi</p>
                    <h2 class="text-3xl font-header font-bold text-emerald-400 uppercase">Active</h2>
                    <p class="text-slate-400 text-sm mt-2 italic">Infrastructure Division</p>
                </div>
            @endif
        </div>

        @if (Auth::user()->divisi_id == 2)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="organic-card p-8 lg:col-span-2">
                    <h3 class="text-white font-header mb-6 flex items-center">
                        <i class="fas fa-microchip mr-3 text-primary"></i> Daily Technical Focus
                    </h3>
                    <div class="h-[350px]">
                        <canvas id="staffInfraChart" data-json="{{ json_encode($staffInfraData) }}"
                            data-categories="{{ json_encode($availableCategories) }}">
                        </canvas>
                    </div>
                </div>

                <div class="organic-card p-6 lg:col-span-1">
                    <h3 class="text-white font-header text-sm mb-4 text-center uppercase tracking-widest">Monthly Workload Distribution</h3>
                    <div class="h-[250px] relative">
                        <canvas id="infraWorkloadChart" data-json="{{ json_encode($infraWorkload) }}">
                        </canvas>
                    </div>
                    <div class="mt-8 space-y-3">
                        @foreach ($availableCategories as $kat)
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full mr-2"
                                        style="background-color: {{ $loop->index == 0 ? '#10b981' : ($loop->index == 1 ? '#3b82f6' : ($loop->index == 2 ? '#f59e0b' : '#6366f1')) }}"></span>
                                    <span class="text-slate-400">{{ $kat }}</span>
                                </div>
                                <span class="text-white font-bold">{{ $infraWorkload[$kat] ?? 0 }} Kegiatan</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="organic-card p-8 mb-8">
                <h3 class="text-white font-header mb-6 flex items-center">
                    <i class="fas fa-wave-square mr-3 text-primary"></i> Productivity Rhythm (Last 7 Days)
                </h3>
                <div class="h-[300px]">
                    <canvas id="trendChart" data-labels='{!! json_encode($trendData->pluck('tanggal')->map(fn($d) => date('d M', strtotime($d)))) !!}' data-values='{!! json_encode($trendData->pluck('total')) !!}'>
                    </canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="organic-card p-6">
                    <h3 class="text-white font-header text-sm mb-4 text-center">Work Autonomy</h3>
                    <div class="h-[200px]">
                        <canvas id="autonomyChart"
                            data-mandiri="{{ $autonomyData->where('is_mandiri', 1)->first()->total ?? 0 }}"
                            data-bantuan="{{ $autonomyData->where('is_mandiri', 0)->first()->total ?? 0 }}">
                        </canvas>
                    </div>
                    <p class="text-[10px] text-slate-500 text-center mt-4 italic">Mandiri vs Dibantu Tim Infra</p>
                </div>

                <div class="organic-card p-6">
                    <h3 class="text-white font-header text-sm mb-4 text-center">Proactive Discovery</h3>
                    <div class="h-[200px]">
                        <canvas id="sourceChart"
                            data-temuan="{{ $sourceData->where('temuan_sendiri', 1)->first()->total ?? 0 }}"
                            data-laporan="{{ $sourceData->where('temuan_sendiri', 0)->first()->total ?? 0 }}">
                        </canvas>
                    </div>
                    <p class="text-[10px] text-slate-500 text-center mt-4 italic">Temuan Sendiri vs Laporan Pelanggan</p>
                </div>

                <div
                    class="organic-card p-6 bg-gradient-to-br from-primary/5 to-transparent border-l-4 border-primary h-full">
                    <h3 class="text-white font-header text-sm mb-6 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-primary"></i> Ringkasan Performa
                    </h3>

                    <div class="space-y-5">
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-slate-400 text-[10px] uppercase tracking-wider">Rerata Kasus</span>
                                <span class="text-slate-200 text-xs">Volume harian (7 hari)</span>
                            </div>
                            <span
                                class="text-white font-bold text-lg">{{ number_format($trendData->avg('total'), 1) }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            @php
                                $totalAut = $autonomyData->sum('total');
                                $mandiri = $autonomyData->where('is_mandiri', 1)->first()->total ?? 0;
                                $autPercent = $totalAut > 0 ? round(($mandiri / $totalAut) * 100) : 0;
                            @endphp
                            <div class="flex flex-col">
                                <span class="text-slate-400 text-[10px] uppercase tracking-wider">Tingkat Mandiri</span>
                                <span class="text-slate-200 text-xs">Tanpa eskalasi infra</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-primary font-bold text-lg">{{ $mandiri }} <small
                                        class="text-[10px] text-slate-500 font-normal">Pcs</small></span>
                                <span
                                    class="text-[10px] text-primary/80 bg-primary/10 px-1 rounded">{{ $autPercent }}%</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            @php
                                $totalSrc = $sourceData->sum('total');
                                $temuan = $sourceData->where('temuan_sendiri', 1)->first()->total ?? 0;
                                $proPercent = $totalSrc > 0 ? round(($temuan / $totalSrc) * 100) : 0;
                            @endphp
                            <div class="flex flex-col">
                                <span class="text-slate-400 text-[10px] uppercase tracking-wider">Inisiatif</span>
                                <span class="text-slate-200 text-xs">Self-Discovery case</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-blue-400 font-bold text-lg">{{ $temuan }} <small
                                        class="text-[10px] text-slate-500 font-normal">Pcs</small></span>
                                <span
                                    class="text-[10px] text-blue-400/80 bg-blue-400/10 px-1 rounded">{{ $proPercent }}%</span>
                            </div>
                        </div>

                        <hr class="border-slate-700/50 my-2">
                        <div class="pt-1">
                            <p class="text-slate-400 text-[10px] italic leading-relaxed">
                                <i class="fas fa-info-circle mr-1 text-primary"></i> Data dihitung berdasarkan rekapitulasi
                                7 hari kalender terakhir.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- blade-formatter-disable --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Deteksi ID Divisi dari Auth Laravel
        const divisiId = {{ Auth::user()->divisi_id }};

        // Helper untuk Persentase Tooltip
        function getTooltipLabel(context) {
            var value = parseFloat(context.raw) || 0;
            var dataset = context.dataset.data;
            var total = 0;
            for (var i = 0; i < dataset.length; i++) { total += parseFloat(dataset[i]); }
            var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
            return " " + context.label + ": " + value + " (" + percentage + "%)";
        }

        if (divisiId == 2) {
            // ==========================================
            // LOGIKA JAVASCRIPT: INFRASTRUKTUR
            // ==========================================
            
            // 1. Stacked Bar Chart (Daily Focus)
            const staffInfraCtx = document.getElementById('staffInfraChart');
            if (staffInfraCtx) {
                const rawData = JSON.parse(staffInfraCtx.dataset.json || "[]");
                const categories = JSON.parse(staffInfraCtx.dataset.categories || "[]");
                
                new Chart(staffInfraCtx, {
                    type: 'bar',
                    data: {
                        labels: rawData.map(d => d.nama),
                        datasets: categories.map((cat, index) => ({
                            label: cat,
                            data: rawData.map(d => d[cat] || 0),
                            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#6366f1'][index],
                            borderRadius: 4
                        }))
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        scales: { 
                            y: { stacked: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } }, 
                            x: { stacked: true, grid: { display: false }, ticks: { color: '#64748b' } } 
                        },
                        plugins: { legend: { labels: { color: '#94a3b8', boxWidth: 10, usePointStyle: true } } }
                    }
                });
            }

            // 2. Workload Doughnut
            const infraWlCtx = document.getElementById('infraWorkloadChart');
            if (infraWlCtx) {
                const wlData = JSON.parse(infraWlCtx.dataset.json || "{}");
                new Chart(infraWlCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(wlData),
                        datasets: [{
                            data: Object.values(wlData),
                            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#6366f1'],
                            borderWidth: 0,
                            hoverOffset: 15
                        }]
                    },
                    options: { 
                        cutout: '75%', 
                        plugins: { 
                            legend: { display: false },
                            tooltip: { callbacks: { label: getTooltipLabel } }
                        } 
                    }
                });
            }

        } else {
            // ==========================================
            // LOGIKA JAVASCRIPT: TAC
            // ==========================================

            // 1. Trend Line Chart
            const trendCtx = document.getElementById('trendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: JSON.parse(trendCtx.dataset.labels || "[]"),
                        datasets: [{
                            label: 'Total Cases',
                            data: JSON.parse(trendCtx.dataset.values || "[]"),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981'
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } },
                            x: { grid: { display: false }, ticks: { color: '#64748b' } }
                        }
                    }
                });
            }

            // Options Doughnut TAC
            const tacDoughnutOptions = {
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: getTooltipLabel } }
                }
            };

            // 2. Autonomy Chart
            const autCtx = document.getElementById('autonomyChart');
            if (autCtx) {
                new Chart(autCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Mandiri', 'Bantuan Infra'],
                        datasets: [{
                            data: [parseFloat(autCtx.dataset.mandiri), parseFloat(autCtx.dataset.bantuan)],
                            backgroundColor: ['#10b981', '#1e293b'],
                            borderWidth: 0
                        }]
                    },
                    options: tacDoughnutOptions
                });
            }

            // 3. Source Chart
            const srcCtx = document.getElementById('sourceChart');
            if (srcCtx) {
                new Chart(srcCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Temuan Sendiri', 'Laporan'],
                        datasets: [{
                            data: [parseFloat(srcCtx.dataset.temuan), parseFloat(srcCtx.dataset.laporan)],
                            backgroundColor: ['#3b82f6', '#1e293b'],
                            borderWidth: 0
                        }]
                    },
                    options: tacDoughnutOptions
                });
            }
        }
    });
</script>
{{-- blade-formatter-enable --}}

@endsection
