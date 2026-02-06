@extends('layouts.manager')

@section('content')
<div x-data="{ 
    viewMode: 'performance', 
    selectedStaff: null,
    showDetail: false,
    staffData: @js($reportData),
    comparisonData: @js($comparisonData),

    // Fungsi untuk update chart besar secara dinamis
    updateComparisonChart() {
        const labels = this.staffData.map(s => s.name);
        let dataValues, labelName, color, bgColor;

        if (this.viewMode === 'performance') {
            dataValues = this.staffData.map(s => s.avg_score);
            labelName = 'Avg Performance Score';
            color = '#3b82f6';
            bgColor = 'rgba(59, 130, 246, 0.1)';
        } else {
            // Menghitung persentase kedisiplinan (On-Time / Total Reports)
            dataValues = this.staffData.map(s => (s.on_time_count / (s.total_reports || 1)) * 100);
            labelName = 'Discipline Rate (%)';
            color = '#f59e0b';
            bgColor = 'rgba(245, 158, 11, 0.1)';
        }

        comparisonChart.data.labels = labels;
        comparisonChart.data.datasets[0].data = dataValues;
        comparisonChart.data.datasets[0].label = labelName;
        comparisonChart.data.datasets[0].borderColor = color;
        comparisonChart.data.datasets[0].backgroundColor = bgColor;
        comparisonChart.update();
    }
}" x-init="$nextTick(() => initBaseChart())" class="space-y-8">

    {{-- Header & Export --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-header text-3xl text-white italic">Executive <span class="text-primary">Analytics</span></h2>
            <p class="text-slate-500 text-sm">Perbandingan performa tim dan analisis mendalam staf.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('manager.reports.export.excel', ['month' => $month, 'year' => $year]) }}" class="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 px-4 py-2 rounded-xl font-bold text-xs hover:bg-emerald-500 hover:text-white transition">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </a>
        </div>
    </div>

    {{-- Filter & Global Comparison --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            {{-- Filter Form --}}
            <div class="organic-card p-6 shadow-xl">
                <form action="{{ route('manager.reports.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] text-slate-500 uppercase font-bold mb-1 block">Bulan</label>
                            <select name="month" class="w-full bg-secondary border border-white/10 text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-primary">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                    @endfor
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500 uppercase font-bold mb-1 block">Tahun</label>
                            <select name="year" class="w-full bg-secondary border border-white/10 text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-primary">
                                <option value="2026" {{ $year == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2025" {{ $year == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ $year == '2024' ? 'selected' : '' }}>2024</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-primary text-white py-2 rounded-xl font-bold text-sm hover:brightness-110 transition">Apply</button>
                        <a href="{{ route('manager.reports.index') }}" class="px-4 py-2 bg-white/5 text-slate-400 rounded-xl hover:text-white transition border border-white/5">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Mode Switcher --}}
            <div class="organic-card p-2 flex bg-darkCard/50 border border-white/5">
                <button @click="viewMode = 'performance'; updateComparisonChart()"
                    :class="viewMode === 'performance' ? 'bg-primary text-white' : 'text-slate-500'"
                    class="flex-1 py-2 rounded-lg text-[10px] font-bold transition-all uppercase">Performance</button>
                <button @click="viewMode = 'conformance'; updateComparisonChart()"
                    :class="viewMode === 'conformance' ? 'bg-amber-500 text-white' : 'text-slate-500'"
                    class="flex-1 py-2 rounded-lg text-[10px] font-bold transition-all uppercase">Conformance</button>
            </div>
        </div>

        {{-- Global Rating Comparison Chart --}}
        <div class="lg:col-span-2 organic-card p-6 min-h-[250px]">
            <h4 class="text-white text-xs font-bold uppercase tracking-widest mb-4 opacity-50 text-center"
                x-text="viewMode === 'performance' ? 'Team Performance Rating' : 'Team Discipline Rating (%)'"></h4>
            <div class="h-48">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Detail Analytics Section --}}
    <div x-show="showDetail" x-transition class="organic-card p-8 border-t-4 border-primary bg-gradient-to-b from-primary/5 to-transparent">
        <div class="flex justify-between items-start mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary/20 rounded-2xl flex items-center justify-center text-primary shadow-lg">
                    <i class="fas fa-user-astronaut text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl text-white font-header" x-text="'Deep Analysis: ' + (selectedStaff?.name)"></h3>
                    <p class="text-xs text-slate-500" x-text="'Periode: ' + '{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}'"></p>
                </div>
            </div>
            <button @click="showDetail = false" class="text-slate-500 hover:text-white bg-white/5 p-2 rounded-xl">&times; Close</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-5 bg-black/20 p-6 rounded-3xl border border-white/5">
                <p class="text-[10px] text-center text-slate-500 uppercase mb-4 font-bold">KPI Variable Balance (Radar)</p>
                <div class="h-64">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>

            <div class="md:col-span-3 bg-black/20 p-6 rounded-3xl border border-white/5">
                <p class="text-[10px] text-center text-slate-500 uppercase mb-4 font-bold">Reporting Discipline</p>
                <div class="h-48">
                    <canvas id="donutChart"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-xs text-white font-bold" x-text="selectedStaff?.on_time_count + ' On-Time / ' + selectedStaff?.late_count + ' Late'"></p>
                    <p class="text-[10px] text-slate-500 mt-1">Based on Manager Correction</p>
                </div>
            </div>

            <div class="md:col-span-4 space-y-4">
                <div class="bg-primary/10 border border-primary/20 p-4 rounded-2xl">
                    <span class="text-[10px] text-primary uppercase font-bold block">Avg Performance Score</span>
                    <span class="text-3xl text-white font-header" x-text="selectedStaff?.avg_score.toFixed(1)"></span>
                </div>
                <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl">
                    <span class="text-[10px] text-emerald-500 uppercase font-bold block">Responsiveness</span>
                    <span class="text-3xl text-white font-header" x-text="selectedStaff?.avg_response + 'm'"></span>
                </div>
                <div class="bg-white/5 border border-white/10 p-4 rounded-2xl">
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Total Case Handled</span>
                    <span class="text-3xl text-white font-header" x-text="selectedStaff?.total_cases"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="organic-card overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-white/5 text-slate-500 text-[10px] uppercase font-bold tracking-widest">
                <tr>
                    <th class="p-5">Staff Name</th>
                    <th class="p-5 text-center" x-text="viewMode === 'performance' ? 'Avg Response' : 'Discipline'"></th>
                    <th class="p-5 text-center">Cases</th>
                    <th class="p-5 text-right">Final Score</th>
                    <th class="p-5 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($reportData as $row)
                <tr class="hover:bg-white/[0.02] transition group">
                    <td class="p-5 text-white font-bold">{{ $row['name'] }}</td>
                    <td class="p-5 text-center">
                        <template x-if="viewMode === 'performance'">
                            <span class="text-primary font-mono">{{ $row['avg_response'] }}m</span>
                        </template>
                        <template x-if="viewMode === 'conformance'">
                            <span class="text-emerald-500">{{ $row['on_time_count'] }} / {{ $row['total_reports'] }}</span>
                        </template>
                    </td>
                    <td class="p-5 text-center text-slate-400">{{ $row['total_cases'] }}</td>
                    <td class="p-5 text-right font-header text-xl text-primary">{{ number_format($row['avg_score'], 1) }}</td>
                    <td class="p-5 text-center">
                        <button @click="selectedStaff = @js($row); showDetail = true; $nextTick(() => initDetailCharts(selectedStaff))"
                            class="bg-primary/20 text-primary hover:bg-primary hover:text-white px-4 py-2 rounded-xl text-[10px] font-bold transition border border-primary/20">
                            ANALYZE
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let radarChart = null,
        donutChart = null,
        comparisonChart = null;

    // Inisialisasi Chart Utama (Comparison)
    function initBaseChart() {
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        const initialData = @js($comparisonData);

        comparisonChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: initialData.labels,
                datasets: [{
                    label: 'Avg Score',
                    data: initialData.scores,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            color: '#64748b'
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#64748b'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Inisialisasi Chart Detail (Radar & Donut)
    function initDetailCharts(staff) {
        // Radar Chart
        const radarCtx = document.getElementById('radarChart').getContext('2d');
        if (radarChart) radarChart.destroy();
        radarChart = new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: staff.chart_labels,
                datasets: [{
                    label: 'Competency',
                    data: staff.chart_values,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: '#3b82f6',
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            color: 'rgba(255,255,255,0.1)'
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.1)'
                        },
                        pointLabels: {
                            color: '#94a3b8',
                            font: {
                                size: 10
                            }
                        },
                        ticks: {
                            display: false
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Donut Chart
        const donutCtx = document.getElementById('donutChart').getContext('2d');
        if (donutChart) donutChart.destroy();
        donutChart = new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['On-Time', 'Late'],
                datasets: [{
                    data: [staff.on_time_count, staff.late_count],
                    backgroundColor: ['#10b981', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection