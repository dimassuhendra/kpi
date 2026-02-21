@extends('layouts.staff')

@section('content')
    <div class="container mx-auto">
        {{-- Header Section --}}
        <div class="mb-10">
            <h1 class="text-4xl font-header font-bold text-white">Main Station</h1>
            <p class="text-slate-400 font-body">Overview pencapaian unit
                {{ Auth::user()->divisi_id == 2 ? 'Infrastruktur' : (Auth::user()->divisi_id == 1 ? 'TAC' : 'Backoffice') }}
                hari ini.
            </p>
        </div>

        {{-- Top Cards Section --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="organic-card p-6 bg-gradient-to-br from-primary/10 to-transparent">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Daily Activity</p>
                <h2 class="text-5xl font-header font-bold text-primary">{{ $dailyCount }}</h2>
                <p class="text-slate-400 text-sm mt-2 italic">Aktivitas hari ini</p>
            </div>

            {{-- Gunakan isset() untuk variabel yang tidak selalu ada di semua divisi --}}
            @if (isset($weeklyCount))
                <div class="organic-card p-6 border-b-4 border-blue-500">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Weekly Volume</p>
                    <h2 class="text-5xl font-header font-bold text-blue-400">{{ $weeklyCount }}</h2>
                    <p class="text-slate-400 text-sm mt-2 italic">Total minggu ini</p>
                </div>
            @endif

            @if (isset($monthlyCount))
                <div class="organic-card p-6 border-b-4 border-purple-500">
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Monthly Milestone</p>
                    <h2 class="text-5xl font-header font-bold text-purple-400">{{ $monthlyCount }}</h2>
                    <p class="text-slate-400 text-sm mt-2 italic">Akumulasi bulan ini</p>
                </div>
            @endif
        </div>

        {{-- --- LOGIKA KHUSUS DIVISI 2: INFRASTRUKTUR --- --}}
        @if (Auth::user()->divisi_id == 2)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="organic-card p-8 lg:col-span-2">
                    <h3 class="text-white font-header mb-6 flex items-center">
                        <i class="fas fa-microchip mr-3 text-primary"></i> Daily Technical Focus
                    </h3>
                    <div class="h-[350px]">
                        <canvas id="staffInfraChart" data-json="{{ json_encode($staffInfraData ?? []) }}"
                            data-categories="{{ json_encode($availableCategories ?? []) }}">
                        </canvas>
                    </div>
                </div>

                <div class="organic-card p-6 lg:col-span-1">
                    <h3 class="text-white font-header text-sm mb-4 text-center uppercase tracking-widest">Monthly Workload
                        Distribution</h3>
                    <div class="h-[250px] relative">
                        <canvas id="infraWorkloadChart" data-json="{{ json_encode($infraWorkload ?? []) }}">
                        </canvas>
                    </div>
                    <div class="mt-8 space-y-3">
                        @foreach ($availableCategories as $kat)
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full mr-2"
                                        style="background-color: {{ ['#10b981', '#3b82f6', '#f59e0b', '#6366f1'][$loop->index % 4] }}"></span>
                                    <span class="text-slate-400">{{ $kat }}</span>
                                </div>
                                <span class="text-white font-bold">{{ $infraWorkload[$kat] ?? 0 }} Kegiatan</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- --- LOGIKA KHUSUS DIVISI 1: TAC --- --}}
        @elseif (Auth::user()->divisi_id == 1)
            <div class="organic-card p-8 mb-8">
                <h3 class="text-white font-header mb-6 flex items-center">
                    <i class="fas fa-wave-square mr-3 text-primary"></i> Productivity Rhythm (Last 7 Days)
                </h3>
                <div class="h-[300px]">
                    <canvas id="trendChart" data-labels='{!! json_encode($trendData->pluck('tanggal')->map(fn($d) => date('d M', strtotime($d))) ?? []) !!}' data-values='{!! json_encode($trendData->pluck('total') ?? []) !!}'>
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
                            <span class="text-white font-bold text-lg">
                                {{ $trendData->count() > 0 ? number_format($trendData->avg('total'), 1) : 0 }}
                            </span>
                        </div>
                        @php
                            $totalAut = $autonomyData->sum('total') ?? 0;
                            $mandiri = $autonomyData->where('is_mandiri', 1)->first()->total ?? 0;
                            $autPercent = $totalAut > 0 ? round(($mandiri / $totalAut) * 100) : 0;

                            $totalSrc = $sourceData->sum('total') ?? 0;
                            $temuan = $sourceData->where('temuan_sendiri', 1)->first()->total ?? 0;
                            $proPercent = $totalSrc > 0 ? round(($temuan / $totalSrc) * 100) : 0;
                        @endphp
                        <div class="flex justify-between items-center">
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
                    </div>
                </div>
            </div>
        @endif

        {{-- --- LOGIKA KHUSUS DIVISI 6: BACKOFFICE --- --}}
        @if (Auth::user()->divisi_id == 6)
            <div class="organic-card p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <h3 class="text-white font-header text-lg flex items-center">
                            <i class="fas fa-history mr-3 text-amber-400"></i> Rekap Pekerjaan Sebelumnya
                        </h3>
                        <p class="text-slate-500 text-xs mt-1">
                            Menampilkan aktivitas pada tanggal:
                            <span
                                class="text-slate-300 font-bold">{{ isset($lastReportDate) && $lastReportDate ? date('d M Y', strtotime($lastReportDate)) : 'Belum ada data' }}</span>
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($yesterdayActivities ?? [] as $act)
                        <div
                            class="group flex items-center gap-4 p-4 rounded-2xl bg-slate-800/30 border border-slate-700/50 hover:bg-slate-800/60 hover:border-emerald-500/50 transition-all">
                            <div class="w-2 h-10 bg-slate-700 group-hover:bg-emerald-500 rounded-full transition-colors">
                            </div>
                            <div class="flex-1">
                                <h4 class="text-slate-200 font-bold text-sm group-hover:text-white transition-colors">
                                    {{ $act->judul_kegiatan }}</h4>
                                <p class="text-slate-500 text-xs mt-0.5 line-clamp-1 italic">{{ $act->deskripsi }}</p>
                            </div>
                            <div class="text-right flex flex-col">
                                <span
                                    class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Selesai</span>
                                <span
                                    class="text-xs font-bold text-slate-400">{{ date('H:i', strtotime($act->created_at)) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-slate-500 italic text-sm">Tidak ada riwayat pekerjaan sebelumnya.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>


    {{-- --- SCRIPT CHART.JS --- --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const divisiId = {{ Auth::user()->divisi_id }};

            // Helper Persentase
            function getTooltipLabel(context) {
                var value = parseFloat(context.raw) || 0;
                var dataset = context.dataset.data;
                var total = dataset.reduce((a, b) => a + parseFloat(b), 0);
                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                return " " + context.label + ": " + value + " (" + percentage + "%)";
            }

            // JS Infrastruktur (ID: 2)
            if (divisiId == 2) {
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
                                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#6366f1'][
                                    index % 4
                                ],
                                borderRadius: 4
                            }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    stacked: true
                                },
                                x: {
                                    stacked: true
                                }
                            }
                        }
                    });
                }

                const infraWlCtx = document.getElementById('infraWorkloadChart');
                if (infraWlCtx) {
                    const wlData = JSON.parse(infraWlCtx.dataset.json || "{}");
                    new Chart(infraWlCtx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(wlData),
                            datasets: [{
                                data: Object.values(wlData),
                                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#6366f1']
                            }]
                        },
                        options: {
                            cutout: '75%',
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: getTooltipLabel
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // JS TAC (ID: 1)
            else if (divisiId == 1) {
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
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }

                // Autonomy & Source Charts
                ['autonomyChart', 'sourceChart'].forEach(id => {
                    const ctx = document.getElementById(id);
                    if (ctx) {
                        const isAut = id === 'autonomyChart';
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: isAut ? ['Mandiri', 'Bantuan'] : ['Temuan', 'Laporan'],
                                datasets: [{
                                    data: isAut ? [ctx.dataset.mandiri, ctx.dataset
                                        .bantuan
                                    ] : [ctx.dataset.temuan, ctx.dataset
                                        .laporan
                                    ],
                                    backgroundColor: [isAut ? '#10b981' : '#3b82f6',
                                        '#1e293b'
                                    ]
                                }]
                            },
                            options: {
                                cutout: '75%'
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection
