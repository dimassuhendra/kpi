{{-- A. STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="organic-card p-6 border-b-2 border-rose-500 group">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Reports Pending</p>
        <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">
            {{ $stats['pending'] }}</h2>
    </div>
    <div class="organic-card p-6 border-b-2 border-primary group">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Efficiency Rate</p>
        <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">
            {{ number_format($stats['avg_response_time'], 1) }} <small>Minutes/Case</small></h2>
    </div>
    <div class="organic-card p-6 border-b-2 border-emerald-500 group">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Monthly Resolved</p>
        <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">
            {{ $stats['resolved_month'] }}</h2>
    </div>
    <div class="organic-card p-6 border-b-2 border-amber-500 group">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Staff Active Today</p>
        <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">
            {{ $stats['active_today'] }}</h2>
    </div>
</div>

{{-- B. PERFORMANCE METRICS (SUMMARY & COMPARISON MERGED) --}}
<div class="organic-card p-8 mt-10">
    <div class="mb-8">
        <h3 class="font-header font-bold text-lg text-white uppercase">Performance Metrics</h3>
        <p class="text-xs text-slate-500 italic uppercase tracking-tighter">Analisis performa kolektif dan individu
            staff</p>
    </div>

    {{-- --- ROW 1: DIVISION SUMMARY (DONUTS) --- --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="bg-slate-900/40 p-5 rounded-[30px] border border-white/5 flex flex-col items-center">
            <h4 class="text-[9px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Source: Temuan vs
                Penugasan</h4>
            <div class="relative w-full max-w-[150px]">
                <canvas id="donutInisiatif"></canvas>
            </div>
            <div class="flex gap-4 mt-4 text-[10px] font-bold uppercase">
                <span class="text-rose-500">● Temuan</span>
                <span class="text-slate-600">● Laporan</span>
            </div>
        </div>

        <div class="bg-slate-900/40 p-5 rounded-[30px] border border-white/5 flex flex-col items-center">
            <h4 class="text-[9px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Execution:
                Mandiri vs Bantuan</h4>
            <div class="relative w-full max-w-[150px]">
                <canvas id="donutMandiri"></canvas>
            </div>
            <div class="flex gap-4 mt-4 text-[10px] font-bold uppercase">
                <span class="text-emerald-500">● Mandiri</span>
                <span class="text-slate-600">● Bantuan</span>
            </div>
        </div>

        <div class="bg-slate-900/40 p-5 rounded-[30px] border border-white/5">
            <h4 class="text-[9px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Avg Response Time
                (Limit: 15m)</h4>
            <canvas id="barResponseThreshold" height="200"></canvas>
        </div>
    </div>

    <hr class="border-white/5 mb-10">

    {{-- --- ROW 2: STAFF COMPARISON --- --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Total Cases per
                Staff</h4>
            <canvas id="chartCountCase"></canvas>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Avg Response
                Time per Staff</h4>
            <canvas id="chartAvgTime"></canvas>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Inisiatif Count
                per Staff</h4>
            <canvas id="chartInisiatif"></canvas>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Mandiri Count
                per Staff</h4>
            <canvas id="chartMandiri"></canvas>
        </div>
    </div>
</div>

{{-- C. COLLECTIVE HEATMAP --}}
<div class="space-y-4 mt-10">
    <div class="flex justify-between items-end">
        <h3 class="text-xs font-bold uppercase text-slate-500 tracking-widest flex items-center gap-2">
            <i class="fas fa-calendar-alt text-primary"></i> Team Activity Log {{ date('Y') }}
        </h3>
        <div class="flex items-center gap-2 text-[9px] text-slate-600 uppercase font-bold">
            <span>Less</span>
            <div class="w-3 h-3 bg-white/5 rounded-[2px]"></div>
            <div class="w-3 h-3 bg-primary/30 rounded-[2px]"></div>
            <div class="w-3 h-3 bg-primary/60 rounded-[2px]"></div>
            <div class="w-3 h-3 bg-primary rounded-[2px]"></div>
            <span>More</span>
        </div>
    </div>

    <div class="organic-card p-8 overflow-x-auto shadow-inner">
        <div class="grid grid-flow-col grid-rows-7 gap-1.5 w-max mx-auto">
            @php
                $startOfYear = \Carbon\Carbon::now()->startOfYear();
                $endOfYear = \Carbon\Carbon::now()->endOfYear();
                $totalDays = $startOfYear->diffInDays($endOfYear);
                $emptyCells = $startOfYear->dayOfWeek;
            @endphp
            @for ($e = 0; $e < $emptyCells; $e++)
                <div class="w-3.5 h-3.5 opacity-0">
                </div>
            @endfor
            @for ($i = 0; $i <= $totalDays; $i++)
                @php
                    $currentDate = $startOfYear->copy()->addDays($i);
                    $dateStr = $currentDate->format('Y-m-d');
                    $count = $heatmapData[$dateStr] ?? 0;
                    $colorClass = 'bg-white/5';
                    if ($count > 0) {
                        $colorClass = 'bg-primary/20';
                    }
                    if ($count > 3) {
                        $colorClass = 'bg-primary/50';
                    }
                    if ($count > 7) {
                        $colorClass = 'bg-primary';
                    }
                @endphp
                <div
                    class="w-3.5 h-3.5 rounded-[3px] {{ $colorClass }} transition-all hover:scale-150 hover:z-10 cursor-help group relative">
                    <div
                        class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100 whitespace-nowrap z-50 pointer-events-none shadow-xl border border-white/10">
                        {{ $currentDate->format('d M Y') }}: <span class="text-primary font-bold">{{ $count }}
                            Laporan</span>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

{{-- D. BOTTOM SECTION: LEADERBOARD --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10 mt-10">
    <div class="space-y-4">
        <h3 class="text-xs font-bold uppercase text-slate-500 tracking-widest px-2">Leaderboard</h3>
        <div class="organic-card p-6 space-y-5">
            @foreach ($leaderboard as $index => $staff)
                <div class="flex items-center gap-4">
                    <span
                        class="text-lg font-header font-bold {{ $index == 0 ? 'text-amber-500' : 'text-slate-600' }}">0{{ $index + 1 }}</span>
                    <div class="flex-grow">
                        <p class="text-sm font-bold text-slate-200 leading-none">{{ $staff->nama_lengkap }}</p>
                        <p class="text-[9px] text-slate-500 uppercase mt-1">Avg:
                            {{ number_format($staff->reports_avg_total_nilai_harian, 1) }}</p>
                    </div>
                    <div class="w-2 h-2 rounded-full {{ $index == 0 ? 'bg-amber-500' : 'bg-primary/30' }}"></div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- E. ALL SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.0.1"></script>

<script>
    // 1. DATA PREPARATION
    // Mengambil nama staff dari collection staffChartData
    const staffLabels = @json($staffChartData->pluck('nama_lengkap'));

    // Options dasar untuk chart Donut
    const donutOptions = {
        cutout: '75%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let value = context.raw || 0;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    };

    // Options dasar untuk chart Bar/Line
    const baseBarOptions = {
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    };

    // --- ROW 1: DIVISION SUMMARY (Menggunakan $summaryData) ---

    // Donut Inisiatif
    new Chart(document.getElementById('donutInisiatif'), {
        type: 'doughnut',
        data: {
            labels: ['Temuan', 'Laporan'],
            datasets: [{
                data: [{{ $summaryData['proaktif'] }}, {{ $summaryData['penugasan'] }}],
                backgroundColor: ['#f43f5e', '#1e293b'],
                borderWidth: 0
            }]
        },
        options: donutOptions
    });

    // Donut Mandiri
    new Chart(document.getElementById('donutMandiri'), {
        type: 'doughnut',
        data: {
            labels: ['Mandiri', 'Bantuan'],
            datasets: [{
                data: [{{ $summaryData['mandiri'] }}, {{ $summaryData['bantuan'] }}],
                backgroundColor: ['#10b981', '#1e293b'],
                borderWidth: 0
            }]
        },
        options: donutOptions
    });

    // Bar Response Threshold
    new Chart(document.getElementById('barResponseThreshold'), {
        type: 'bar',
        data: {
            labels: ['Team Average'],
            datasets: [{
                data: [{{ $summaryData['avg_time'] }}],
                backgroundColor: '{{ $summaryData['avg_time'] > 15 ? "#f43f5e" : "#3b82f6" }}',
                borderRadius: 12,
                barThickness: 60
            }]
        },
        options: {
            ...baseBarOptions,
            plugins: {
                ...baseBarOptions.plugins,
                annotation: {
                    annotations: {
                        line1: {
                            type: 'line',
                            yMin: 15,
                            yMax: 15,
                            borderColor: 'rgba(244, 63, 94, 0.5)',
                            borderDash: [6, 6],
                            label: {
                                display: true,
                                content: 'Limit 15m',
                                backgroundColor: '#f43f5e',
                                font: { size: 8 }
                            }
                        }
                    }
                }
            }
        }
    });

    // --- ROW 2: STAFF COMPARISON (Menggunakan $staffChartData) ---

    // 1. Total Cases per Staff
    new Chart(document.getElementById('chartCountCase'), {
        type: 'bar',
        data: {
            labels: staffLabels,
            datasets: [{
                data: @json($staffChartData->pluck('total_case')),
                backgroundColor: '#3b82f6',
                borderRadius: 5
            }]
        },
        options: baseBarOptions
    });

    // 2. Avg Response Time per Staff
    new Chart(document.getElementById('chartAvgTime'), {
        type: 'line',
        data: {
            labels: staffLabels,
            datasets: [{
                data: @json($staffChartData->pluck('avg_time')),
                borderColor: '#fbbf24',
                fill: true,
                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                tension: 0.3
            }]
        },
        options: baseBarOptions
    });

    // 3. Inisiatif Count per Staff
    new Chart(document.getElementById('chartInisiatif'), {
        type: 'bar',
        data: {
            labels: staffLabels,
            datasets: [{
                data: @json($staffChartData->pluck('inisiatif_count')),
                backgroundColor: '#f43f5e',
                borderRadius: 5
            }]
        },
        options: baseBarOptions
    });

    // 4. Mandiri Count per Staff
    new Chart(document.getElementById('chartMandiri'), {
        type: 'bar',
        data: {
            labels: staffLabels,
            datasets: [{
                data: @json($staffChartData->pluck('mandiri_count')),
                backgroundColor: '#10b981',
                borderRadius: 5
            }]
        },
        options: baseBarOptions
    });
</script>
