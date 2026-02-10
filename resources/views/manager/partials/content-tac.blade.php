{{-- A. STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="organic-card p-6 border-b-2 border-rose-500 group transition-all">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Reports Pending</p>
        <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">
            {{ $stats['pending'] }}</h2>
    </div>
    <div class="organic-card p-6 border-b-2 border-primary group transition-all">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Efficiency Rate</p>
        <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">
            {{ number_format($stats['avg_response_time'], 1) }} <small class="text-xs">Min/Case</small></h2>
    </div>
    <div class="organic-card p-6 border-b-2 border-emerald-500 group transition-all">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Monthly Resolved</p>
        <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">
            {{ $stats['resolved_month'] }}</h2>
    </div>
    <div class="organic-card p-6 border-b-2 border-amber-500 group transition-all">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Staff Active Today</p>
        <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">
            {{ $stats['active_today'] }}</h2>
    </div>
</div>

{{-- B. PERFORMANCE METRICS --}}
<div class="organic-card p-8 mt-10">
    <div class="mb-8">
        <h3 class="font-header font-bold text-lg text-white uppercase tracking-tight">Performance Metrics</h3>
        <p class="text-xs text-slate-500 italic uppercase tracking-widest">Analisis performa kolektif dan individu staff
        </p>
    </div>

    {{-- --- ROW 1: DIVISION SUMMARY (ATAS) --- --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="bg-slate-900/40 p-5 rounded-[30px] border border-white/5 flex flex-col items-center">
            <h4 class="text-[9px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Source: Temuan vs
                Laporan</h4>
            <div style="position: relative; height:150px; width:100%">
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
            <div style="position: relative; height:150px; width:100%">
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
            <div style="position: relative; height:180px; width:100%">
                <canvas id="barResponseThreshold"></canvas>
            </div>
        </div>
    </div>

    <hr class="border-white/5 mb-10">

    {{-- TRENDS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-secondary p-6 rounded-2xl border border-white/5">
            <h3 class="text-white font-bold mb-4 text-sm uppercase">Productivity Mix</h3>
            <div style="position: relative; height:250px; width:100%">
                <canvas id="chartWorkloadMix"></canvas>
            </div>
        </div>

        <div class="bg-secondary p-6 rounded-2xl border border-white/5">
            <h3 class="text-white font-bold mb-4 text-sm uppercase">Daily Activity Trend</h3>
            <div style="position: relative; height:250px; width:100%">
                <canvas id="chartDailyTrend"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-secondary p-6 rounded-2xl border border-white/5 mb-8 text-center">
        <h3 class="text-white font-bold mb-4 text-sm uppercase">Staff Productivity Ratio</h3>
        <div style="position: relative; height:300px; width:100%">
            <canvas id="chartStaffProductivity"></canvas>
        </div>
    </div>

    {{-- --- ROW 2: STAFF COMPARISON (BAWAH) --- --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Total Cases</h4>
            <div style="position: relative; height:150px; width:100%"><canvas id="chartCountCase"></canvas></div>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Avg Response
            </h4>
            <div style="position: relative; height:150px; width:100%"><canvas id="chartAvgTime"></canvas></div>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Inisiatif Count
            </h4>
            <div style="position: relative; height:150px; width:100%"><canvas id="chartInisiatif"></canvas></div>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Mandiri Count
            </h4>
            <div style="position: relative; height:150px; width:100%"><canvas id="chartMandiri"></canvas></div>
        </div>
    </div>
</div>

{{-- Script dengan perbaikan tinggi otomatis --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.0.1"></script>

<script>
    // Pastikan plugin terdaftar
    Chart.register(window['chartjs-plugin-annotation']);

    const staffLabels = @json($staffChartData->pluck('nama_lengkap'));

    // Default Style Global
    Chart.defaults.color = '#64748b';
    Chart.defaults.font.size = 10;

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255,255,255,0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    };

    // --- RENDER ---
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
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%'
        }
    });

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
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%'
        }
    });

    new Chart(document.getElementById('barResponseThreshold'), {
        type: 'bar',
        data: {
            labels: ['Team Avg'],
            datasets: [{
                data: [{{ $summaryData['avg_time'] }}],
                backgroundColor: '{{ $summaryData['avg_time'] > 15 ? '#f43f5e' : '#3b82f6' }}',
                borderRadius: 10,
                barThickness: 50
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                annotation: {
                    annotations: {
                        line1: {
                            type: 'line',
                            yMin: 15,
                            yMax: 15,
                            borderColor: '#f43f5e',
                            borderDash: [6, 6],
                            borderWidth: 2,
                            label: {
                                display: true,
                                content: 'LIMIT',
                                backgroundColor: '#f43f5e',
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            }
        }
    });

    // Chart Staff Comparison (Bawah)
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
        options: commonOptions
    });

    new Chart(document.getElementById('chartAvgTime'), {
        type: 'line',
        data: {
            labels: staffLabels,
            datasets: [{
                data: @json($staffChartData->pluck('avg_time')),
                borderColor: '#fbbf24',
                tension: 0.3,
                fill: false
            }]
        },
        options: commonOptions
    });

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
        options: commonOptions
    });

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
        options: commonOptions
    });

    // Productivity & Trend
    new Chart(document.getElementById('chartWorkloadMix'), {
        type: 'doughnut',
        data: {
            labels: ['Technical', 'General'],
            datasets: [{
                data: [{{ $workloadMix['case'] ?? 0 }}, {{ $workloadMix['activity'] ?? 0 }}],
                backgroundColor: ['#3b82f6', '#94a3b8'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });

    new Chart(document.getElementById('chartDailyTrend'), {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                    label: 'Cases',
                    data: @json($trendCases),
                    borderColor: '#3b82f6',
                    tension: 0.3
                },
                {
                    label: 'Activities',
                    data: @json($trendActivities),
                    borderColor: '#94a3b8',
                    tension: 0.3
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });

    new Chart(document.getElementById('chartStaffProductivity'), {
        type: 'bar',
        data: {
            labels: @json($staffChartData->pluck('nama')),
            datasets: [{
                    label: 'Technical',
                    data: @json($staffChartData->pluck('cases')),
                    backgroundColor: '#3b82f6'
                },
                {
                    label: 'General',
                    data: @json($staffChartData->pluck('activities')),
                    backgroundColor: '#94a3b8'
                }
            ]
        },
        options: {
            ...commonOptions,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
</script>
