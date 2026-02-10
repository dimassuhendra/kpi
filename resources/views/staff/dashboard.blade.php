@extends('layouts.staff')

@section('content')
<div class="container mx-auto">
    <div class="mb-10">
        <h1 class="text-4xl font-header font-bold text-white">Main Station</h1>
        <p class="text-slate-400 font-body">Overview pencapaian unit TAC hari ini.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="organic-card p-6 bg-gradient-to-br from-primary/10 to-transparent">
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Daily Case</p>
            <h2 class="text-5xl font-header font-bold text-primary">{{ $dailyCount }}</h2>
            <p class="text-slate-400 text-sm mt-2 italic">Aktivitas hari ini</p>
        </div>
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
    </div>

    <div class="organic-card p-8 mb-8">
        <h3 class="text-white font-header mb-6 flex items-center">
            <i class="fas fa-wave-square mr-3 text-primary"></i> Productivity Rhythm (Last 7 Days)
        </h3>
        <div class="h-[300px]">
            <canvas id="trendChart"
                data-labels='{!! json_encode($trendData->pluck("tanggal")->map(fn($d) => date("d M", strtotime($d)))) !!}'
                data-values='{!! json_encode($trendData->pluck("total")) !!}'>
            </canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="organic-card p-6">
            <h3 class="text-white font-header text-sm mb-4 text-center">Work Autonomy</h3>
            <canvas id="autonomyChart"
                data-mandiri="{{ $autonomyData->where('is_mandiri', 1)->first()->total ?? 0 }}"
                data-bantuan="{{ $autonomyData->where('is_mandiri', 0)->first()->total ?? 0 }}">
            </canvas>
        </div>
        <div class="organic-card p-6">
            <h3 class="text-white font-header text-sm mb-4 text-center">Proactive Discovery</h3>
            <canvas id="sourceChart"
                data-temuan="{{ $sourceData->where('temuan_sendiri', 1)->first()->total ?? 0 }}"
                data-laporan="{{ $sourceData->where('temuan_sendiri', 0)->first()->total ?? 0 }}">
            </canvas>
        </div>

        <div class="organic-card p-6 bg-gradient-to-br from-primary/5 to-transparent border-l-4 border-primary h-full">
            <h3 class="text-white font-header text-sm mb-6 flex items-center">
                <i class="fas fa-chart-pie mr-2 text-primary"></i> Ringkasan Performa
            </h3>

            <div class="space-y-5">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-slate-400 text-[10px] uppercase tracking-wider">Rerata Kasus</span>
                        <span class="text-slate-200 text-xs">Temuan case harian</span>
                    </div>
                    <span class="text-white font-bold text-lg">{{ number_format($trendData->avg('total'), 1) }}</span>
                </div>

                <div class="flex justify-between items-center">
                    @php
                    $totalAut = $autonomyData->sum('total');
                    $mandiri = $autonomyData->where('is_mandiri', 1)->first()->total ?? 0;
                    $autPercent = $totalAut > 0 ? round(($mandiri / $totalAut) * 100) : 0;
                    @endphp
                    <div class="flex flex-col">
                        <span class="text-slate-400 text-[10px] uppercase tracking-wider">Kerja Mandiri</span>
                        <span class="text-slate-200 text-xs">Tanpa bantuan tim infra</span>
                    </div>
                    <div class="text-right">
                        <span class="block text-primary font-bold text-lg">{{ $mandiri }} <small class="text-[10px] text-slate-500 font-normal">Kasus</small></span>
                        <span class="text-[10px] text-primary/80 bg-primary/10 px-1 rounded">{{ $autPercent }}%</span>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    @php
                    $totalSrc = $sourceData->sum('total');
                    $temuan = $sourceData->where('temuan_sendiri', 1)->first()->total ?? 0;
                    $proPercent = $totalSrc > 0 ? round(($temuan / $totalSrc) * 100) : 0;
                    @endphp
                    <div class="flex flex-col">
                        <span class="text-slate-400 text-[10px] uppercase tracking-wider">Inisiatif Proaktif</span>
                        <span class="text-slate-200 text-xs">Masalah yang ditemukan</span>
                    </div>
                    <div class="text-right">
                        <span class="block text-blue-400 font-bold text-lg">{{ $temuan }} <small class="text-[10px] text-slate-500 font-normal">Kasus</small></span>
                        <span class="text-[10px] text-blue-400/80 bg-blue-400/10 px-1 rounded">{{ $proPercent }}%</span>
                    </div>
                </div>

                <hr class="border-slate-700/50 my-2">

                <div class="pt-2">
                    <p class="text-slate-400 text-[11px] italic leading-relaxed bg-slate-800/50 p-2 rounded border border-slate-700">
                        <i class="fas fa-info-circle mr-1 text-primary"></i>
                        Data diambil dari 7 hari terakhir.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- blade-formatter-disable --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Fungsi Helper Tooltip (Dibuat tradisional agar aman dari formatter)
            function getTooltipLabel(context) {
                var value = parseFloat(context.raw) || 0;
                var dataset = context.dataset.data;
                var total = 0;
                for (var i = 0; i < dataset.length; i++) {
                    total += parseFloat(dataset[i]);
                }
                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                return " " + context.label + ": " + value + " Case (" + percentage + "%)";
            }

            // 2. Trend Line Chart
            var trendCtx = document.getElementById('trendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: JSON.parse(trendCtx.dataset.labels || "[]"),
                        datasets: [{
                            label: 'Total Cases',
                            data: JSON.parse(trendCtx.dataset.values || "[]"),
                            borderColor: '#10b981',
                            tension: 0.4,
                            fill: true,
                            backgroundColor: 'rgba(16, 185, 129, 0.1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            // Options bersama untuk Doughnut
            var doughnutOptions = {
                cutout: '75%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: getTooltipLabel
                        }
                    }
                }
            };

            // 3. Autonomy Doughnut
            var autCtx = document.getElementById('autonomyChart');
            if (autCtx) {
                new Chart(autCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Diselesaikan sendiri', 'Diselesaikan tim Infra'],
                        datasets: [{
                            data: [
                                parseFloat(autCtx.dataset.mandiri) || 0,
                                parseFloat(autCtx.dataset.bantuan) || 0
                            ],
                            backgroundColor: ['#10b981', 'oklch(0.704 0.191 22.216)'],
                            borderWidth: 0
                        }]
                    },
                    options: doughnutOptions
                });
            }

            // 4. Source Doughnut
            var srcCtx = document.getElementById('sourceChart');
            if (srcCtx) {
                new Chart(srcCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Temuan sendiri', 'Laporan pelanggan'],
                        datasets: [{
                            data: [
                                parseFloat(srcCtx.dataset.temuan) || 0,
                                parseFloat(srcCtx.dataset.laporan) || 0
                            ],
                            backgroundColor: ['#3b82f6', 'oklch(0.704 0.191 22.216)'],
                            borderWidth: 0
                        }]
                    },
                    options: doughnutOptions
                });
            }
        });
    </script>
    {{-- blade-formatter-enable --}}

    @endsection