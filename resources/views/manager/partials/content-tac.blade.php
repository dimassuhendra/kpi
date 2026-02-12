{{-- A. STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="info-card p-6 border-b-4 border-rose-500 group transition-all">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Reports Pending</p>
        <h2 class="text-4xl font-header font-black text-slate-800 mt-2 group-hover:scale-105 transition-transform">
            {{ $stats['pending'] }}</h2>
    </div>
    <div class="info-card p-6 border-b-4 border-techGreen-600 group transition-all">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Efficiency Rate</p>
        <h2 class="text-4xl font-header font-black text-slate-800 mt-2 group-hover:scale-105 transition-transform">
            {{ number_format($stats['avg_response_time'], 1) }} <small
                class="text-xs text-slate-400 font-bold">Min/Case</small></h2>
    </div>
    <div class="info-card p-6 border-b-4 border-emerald-500 group transition-all">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Monthly Resolved</p>
        <h2 class="text-4xl font-header font-black text-slate-800 mt-2 group-hover:scale-105 transition-transform">
            {{ $stats['resolved_month'] }}</h2>
    </div>
    <div class="info-card p-6 border-b-4 border-amber-500 group transition-all">
        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Staff Active Today</p>
        <h2 class="text-4xl font-header font-black text-slate-800 mt-2 group-hover:scale-105 transition-transform">
            {{ $stats['active_today'] }}</h2>
    </div>
</div>

{{-- B. PERFORMANCE METRICS --}}
<div class="mt-10">
    {{-- Header Section --}}
    <div class="mb-8 ml-2">
        <h3 class="font-header font-black text-xl text-slate-800 uppercase tracking-tight">Performance Metrics</h3>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Analisis performa kolektif dan
            individu staff</p>
    </div>

    {{-- --- ROW 1: DIVISION SUMMARY --- --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="info-card p-6 flex flex-col items-center">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-6 tracking-widest">Source: Temuan vs Laporan
            </h4>
            <div style="position: relative; height:160px; width:100%"><canvas id="donutInisiatif"></canvas></div>
            <div class="flex gap-4 mt-6 text-[10px] font-black uppercase tracking-tighter">
                <span class="flex items-center gap-1 text-amber-500"><i class="fas fa-circle text-[8px]"></i>
                    Temuan</span>
                <span class="flex items-center gap-1 text-emerald-600"><i class="fas fa-circle text-[8px]"></i>
                    Laporan</span>
            </div>
        </div>

        <div class="info-card p-6 flex flex-col items-center">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-6 tracking-widest">Execution: Mandiri vs
                Bantuan</h4>
            <div style="position: relative; height:160px; width:100%"><canvas id="donutMandiri"></canvas></div>
            <div class="flex gap-4 mt-6 text-[10px] font-black uppercase tracking-tighter">
                <span class="flex items-center gap-1 text-emerald-500"><i class="fas fa-circle text-[8px]"></i>
                    Mandiri</span>
                <span class="flex items-center gap-1 text-slate-400"><i class="fas fa-circle text-[8px]"></i>
                    Bantuan</span>
            </div>
        </div>

        <div class="info-card p-6">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-6 tracking-widest text-center">Avg Response
                Time (Limit: 15m)</h4>
            <div style="position: relative; height:180px; width:100%"><canvas id="barResponseThreshold"></canvas></div>
        </div>
    </div>

    {{-- --- ROW 2: TRENDS --- --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="info-card p-6">
            <h3 class="text-slate-800 font-black mb-6 text-sm uppercase tracking-wider border-b border-slate-50 pb-3">
                Productivity Mix</h3>
            <div style="position: relative; height:250px; width:100%"><canvas id="chartWorkloadMix"></canvas></div>
        </div>

        <div class="info-card p-6">
            <h3 class="text-slate-800 font-black mb-6 text-sm uppercase tracking-wider border-b border-slate-50 pb-3">
                Daily Activity Trend</h3>
            <div style="position: relative; height:250px; width:100%"><canvas id="chartDailyTrend"></canvas></div>
        </div>
    </div>

    {{-- --- ROW 3: STAFF PRODUCTIVITY RATIO --- --}}
    <div class="info-card p-8 mb-8">
        <h3 class="text-slate-800 font-black mb-8 text-sm uppercase tracking-widest text-center">Staff Productivity
            Ratio</h3>
        <div style="position: relative; height:350px; width:100%"><canvas id="chartStaffProductivity"></canvas></div>
    </div>

    {{-- --- ROW 4: STAFF COMPARISON SMALL CARDS --- --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="info-card p-6">
            <h4 class="text-[10px] font-bold uppercase text-slate-400 mb-4 tracking-widest text-center">Total Cases</h4>
            <div style="position: relative; height:150px; width:100%"><canvas id="chartCountCase"></canvas></div>
        </div>
        <div class="info-card p-6">
            <h4 class="text-[10px] font-bold uppercase text-slate-400 mb-4 tracking-widest text-center">Avg Response
            </h4>
            <div style="position: relative; height:150px; width:100%"><canvas id="chartAvgTime"></canvas></div>
        </div>
        <div class="info-card p-6">
            <h4 class="text-[10px] font-bold uppercase text-slate-400 mb-4 tracking-widest text-center">Inisiatif Count
            </h4>
            <div style="position: relative; height:150px; width:100%"><canvas id="chartInisiatif"></canvas></div>
        </div>
        <div class="info-card p-6">
            <h4 class="text-[10px] font-bold uppercase text-slate-400 mb-4 tracking-widest text-center">Mandiri Count
            </h4>
            <div style="position: relative; height:150px; width:100%"><canvas id="chartMandiri"></canvas></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.0.1"></script>

<script>
    Chart.register(window['chartjs-plugin-annotation']);

    const staffLabels = @json($staffChartData->pluck('nama_lengkap'));

    // Default Style Global untuk Light Mode
    Chart.defaults.color = '#94a3b8'; // Slate 400
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.font.weight = '600';

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
                    color: '#f1f5f9'
                },
                ticks: {
                    color: '#64748b'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#64748b'
                }
            }
        }
    };

    // --- CHART IMPLEMENTATION WITH NEW COLOR PALETTE ---

    new Chart(document.getElementById('donutInisiatif'), {
        type: 'doughnut',
        data: {
            labels: ['Temuan', 'Laporan'],
            datasets: [{
                data: [{{ $summaryData['proaktif'] }}, {{ $summaryData['penugasan'] }}],
                backgroundColor: ['#f59e0b', '#059669'], // Amber & Emerald 600
                hoverOffset: 4,
                borderWidth: 5,
                borderColor: '#f8fafc'
            }]
        },
        options: {
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById('donutMandiri'), {
        type: 'doughnut',
        data: {
            labels: ['Mandiri', 'Bantuan'],
            datasets: [{
                data: [{{ $summaryData['mandiri'] }}, {{ $summaryData['bantuan'] }}],
                backgroundColor: ['#10b981', '#cbd5e1'], // Emerald 500 & Slate 300
                borderWidth: 5,
                borderColor: '#f8fafc'
            }]
        },
        options: {
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById('barResponseThreshold'), {
        type: 'bar',
        data: {
            labels: ['Team Avg'],
            datasets: [{
                data: [{{ $summaryData['avg_time'] }}],
                backgroundColor: '{{ $summaryData['avg_time'] > 15 ? '#f43f5e' : '#10b981' }}',
                borderRadius: 12,
                barThickness: 40
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
                            borderDash: [5, 5],
                            borderWidth: 2,
                            label: {
                                display: true,
                                content: 'LIMIT 15m',
                                position: 'end',
                                backgroundColor: '#f43f5e',
                                font: {
                                    size: 9,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('chartCountCase'), {
        type: 'bar',
        data: {
            labels: staffLabels,
            datasets: [{
                data: @json($staffChartData->pluck('total_case')),
                backgroundColor: '#0ea5e9', // Sky 500
                borderRadius: 6
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
                borderColor: '#f59e0b',
                backgroundColor: '#f59e0b',
                pointRadius: 4,
                tension: 0.4,
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
                borderRadius: 6
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
                borderRadius: 6
            }]
        },
        options: commonOptions
    });

    new Chart(document.getElementById('chartWorkloadMix'), {
        type: 'doughnut',
        data: {
            labels: ['Technical', 'General'],
            datasets: [{
                data: [{{ $workloadMix['case'] ?? 0 }}, {{ $workloadMix['activity'] ?? 0 }}],
                backgroundColor: ['#059669', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
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
                    borderColor: '#059669',
                    tension: 0.4,
                    pointRadius: 0,
                    borderWidth: 3
                },
                {
                    label: 'Activities',
                    data: @json($trendActivities),
                    borderColor: '#f59e0b',
                    tension: 0.4,
                    pointRadius: 0,
                    borderWidth: 3
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end'
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
                    backgroundColor: '#059669',
                    borderRadius: 4
                },
                {
                    label: 'General',
                    data: @json($staffChartData->pluck('activities')),
                    backgroundColor: '#cbd5e1',
                    borderRadius: 4
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
