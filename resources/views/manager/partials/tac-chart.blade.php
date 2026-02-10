<div class="organic-card p-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h3 class="font-header font-bold text-lg text-white uppercase">Performance Metrics</h3>
            <p class="text-xs text-slate-500 italic uppercase tracking-tighter">Data analisis berdasarkan laporan yang disetujui</p>
        </div>
        <div class="flex bg-slate-900 rounded-2xl p-1 border border-white/5">
            <a href="{{ route('manager.dashboard', ['view_type' => 'all']) }}"
                class="px-5 py-2 text-[10px] font-bold uppercase rounded-xl transition-all {{ $viewType == 'all' ? 'bg-primary text-white shadow-lg' : 'text-slate-500 hover:text-slate-300' }}">
                Division Summary
            </a>
            <a href="{{ route('manager.dashboard', ['view_type' => 'compare']) }}"
                class="px-5 py-2 text-[10px] font-bold uppercase rounded-xl transition-all {{ $viewType == 'compare' ? 'bg-primary text-white shadow-lg' : 'text-slate-500 hover:text-slate-300' }}">
                Staff Comparison
            </a>
        </div>
    </div>

    @if($viewType == 'all')
    {{-- --- VIEW: SUMMARY --- --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- 1. Donut: Inisiatif --}}
        <div class="bg-slate-900/40 p-5 rounded-[30px] border border-white/5 flex flex-col items-center">
            <h4 class="text-[9px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Source: Temuan Sendiri vs Penugasan</h4>
            <div class="relative w-full max-w-[150px]">
                <canvas id="donutInisiatif"></canvas>
            </div>
            <div class="flex gap-4 mt-4 text-[10px] font-bold uppercase">
                <span class="text-rose-500">● Temuan</span>
                <span class="text-slate-600">● Laporan</span>
            </div>
        </div>

        {{-- 2. Donut: Mandiri --}}
        <div class="bg-slate-900/40 p-5 rounded-[30px] border border-white/5 flex flex-col items-center">
            <h4 class="text-[9px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Execution: Mandiri vs Bantuan</h4>
            <div class="relative w-full max-w-[150px]">
                <canvas id="donutMandiri"></canvas>
            </div>
            <div class="flex gap-4 mt-4 text-[10px] font-bold uppercase">
                <span class="text-emerald-500">● Mandiri</span>
                <span class="text-slate-600">● Bantuan</span>
            </div>
        </div>

        {{-- 3. Bar: Threshold --}}
        <div class="bg-slate-900/40 p-5 rounded-[30px] border border-white/5">
            <h4 class="text-[9px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Avg Response Time (Limit: 15m)</h4>
            <canvas id="barResponseThreshold" height="200"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Plugin Annotation untuk Garis Threshold --}}
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.0.1"></script>

    <script>
        const commonDonutOptions = {
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a,b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} Case (${percentage}%)`;
                        }
                    }
                }
            }
        };

        // Donut Inisiatif
        new Chart(document.getElementById('donutInisiatif'), {
            type: 'doughnut',
            data: {
                labels: ['Temuan', 'Laporan'],
                datasets: [{
                    data: [{{ $chartData['proaktif'] }}, {{ $chartData['penugasan'] }}],
                    backgroundColor: ['#f43f5e', '#1e293b'],
                    borderWidth: 0
                }]
            },
            options: commonDonutOptions
        });

        // Donut Mandiri
        new Chart(document.getElementById('donutMandiri'), {
            type: 'doughnut',
            data: {
                labels: ['Mandiri', 'Bantuan'],
                datasets: [{
                    data: [{{ $chartData['mandiri'] }}, {{ $chartData['bantuan'] }}],
                    backgroundColor: ['#10b981', '#1e293b'],
                    borderWidth: 0
                }]
            },
            options: commonDonutOptions
        });

        // Bar Threshold
        new Chart(document.getElementById('barResponseThreshold'), {
            type: 'bar',
            data: {
                labels: ['Team Average'],
                datasets: [{
                    data: [{{ $chartData['avg_time'] }}],
                    backgroundColor: '{{ $chartData['avg_time'] > 15 ? "#f43f5e" : "#3b82f6" }}',
                    borderRadius: 12,
                    barThickness: 60,
                }]
            },
            options: {
                plugins: {
                    annotation: {
                        annotations: {
                            line1: {
                                type: 'line',
                                yMin: 15,
                                yMax: 15,
                                borderColor: 'rgba(244, 63, 94, 0.5)',
                                borderWidth: 2,
                                borderDash: [6, 6],
                                label: {
                                    display: true,
                                    content: 'Limit 15m',
                                    backgroundColor: '#f43f5e',
                                    font: {
                                        size: 8
                                    }
                                }
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        }
                    },
                    x: {
                        display: false
                    }
                }
            }
        });
    </script>

    @else
    {{-- --- VIEW: COMPARISON --- --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Total Cases</h4>
            <canvas id="chartCountCase"></canvas>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Avg Response Time</h4>
            <canvas id="chartAvgTime"></canvas>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Inisiatif Count</h4>
            <canvas id="chartInisiatif"></canvas>
        </div>
        <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5">
            <h4 class="text-[10px] font-bold uppercase text-slate-500 mb-4 tracking-widest text-center">Mandiri Count</h4>
            <canvas id="chartMandiri"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($chartData->pluck('nama_lengkap'));
        const baseOptions = {
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

        new Chart(document.getElementById('chartCountCase'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: @json($chartData->pluck('total_case')),
                    backgroundColor: '#3b82f6',
                    borderRadius: 5
                }]
            },
            options: baseOptions
        });

        new Chart(document.getElementById('chartAvgTime'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: @json($chartData->pluck('avg_time')),
                    borderColor: '#fbbf24',
                    fill: true,
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
                    tension: 0.3
                }]
            },
            options: baseOptions
        });

        new Chart(document.getElementById('chartInisiatif'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: @json($chartData->pluck('inisiatif_count')),
                    backgroundColor: '#f43f5e',
                    borderRadius: 5
                }]
            },
            options: baseOptions
        });

        new Chart(document.getElementById('chartMandiri'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: @json($chartData->pluck('mandiri_count')),
                    backgroundColor: '#10b981',
                    borderRadius: 5
                }]
            },
            options: baseOptions
        });
    </script>
    @endif
</div>