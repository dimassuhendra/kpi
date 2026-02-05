@extends('layouts.staff')

@section('content')
<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="bg-accent/20 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest">Workspace</span>
        </div>
        <h2 class="font-header text-4xl text-primary leading-tight">Halo, <span class="text-secondary">{{ Auth::user()->name }}</span>!</h2>
        <p class="text-gray-500 font-medium">Siap untuk mencapai target KPI hari ini?</p>
    </div>

    <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $todaySubmission ? 'bg-green-400' : 'bg-red-400' }} opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 {{ $todaySubmission ? 'bg-green-500' : 'bg-red-500' }}"></span>
        </div>
        <span class="text-sm font-bold text-primary italic">Status: {{ $todaySubmission ? 'Laporan Terkirim' : 'Menunggu Laporan' }}</span>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">

    <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary rounded-[2rem] blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
        <div class="relative bg-white p-8 rounded-[2rem] shadow-sm hover:-translate-y-2 transition-transform duration-300">
            <div class="flex flex-col gap-4">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Average Score</p>
                    <h3 class="text-4xl font-header text-primary mt-1">{{ number_format($averageScore, 1) }}</h3>
                </div>
                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: {{ $averageScore }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-accent to-secondary rounded-[2rem] blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
        <div class="relative bg-white p-8 rounded-[2rem] shadow-sm hover:-translate-y-2 transition-transform duration-300">
            <div class="flex flex-col gap-4">
                <div class="w-14 h-14 bg-accent/10 rounded-2xl flex items-center justify-center text-accent">
                    <i class="fas fa-rocket text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Cases</p>
                    <h3 class="text-4xl font-header text-accent mt-1">
                        {{ \App\Models\KpiCaseLog::whereHas('submission', fn($q) => $q->where('user_id', Auth::id())->where('status', 'approved'))->count() }}
                    </h3>
                </div>
                <p class="text-[10px] text-gray-400 font-bold italic">*30 Hari Terakhir</p>
            </div>
        </div>
    </div>

    <div class="relative group">
        @if($todaySubmission)
        <div class="absolute inset-0 bg-green-400 rounded-[2rem] blur-xl opacity-20"></div>
        <div class="relative bg-green-50 p-8 rounded-[2rem] border border-green-100 hover:-translate-y-2 transition-all">
            <div class="flex flex-col gap-4">
                <div class="w-14 h-14 bg-green-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-200">
                    <i class="fas fa-check-double text-2xl"></i>
                </div>
                <div>
                    <p class="text-green-700 text-xs font-bold uppercase tracking-widest">Submission</p>
                    <h3 class="text-2xl font-header text-green-800 mt-1">Done Today!</h3>
                </div>
            </div>
        </div>
        @else
        <div class="absolute inset-0 bg-red-400 rounded-[2rem] blur-xl opacity-20"></div>
        <div class="relative bg-red-50 p-8 rounded-[2rem] border border-red-100 hover:-translate-y-2 transition-all">
            <div class="flex flex-col gap-4">
                <div class="w-14 h-14 bg-red-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-200">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <div>
                    <p class="text-red-700 text-xs font-bold uppercase tracking-widest">Submission</p>
                    <h3 class="text-2xl font-header text-red-800 mt-1">Not Filled!</h3>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="relative group h-full">
        <a href="{{ route('staff.kpi.create') }}" class="block h-full shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all duration-300 rounded-[2rem] bg-primary overflow-hidden group">
            <div class="relative h-full p-8 flex flex-col justify-center items-center text-center gap-2">
                <div class="absolute top-0 right-0 p-4 opacity-20 group-hover:scale-125 transition-transform">
                    <i class="fas fa-circle-plus text-6xl text-white"></i>
                </div>

                <h3 class="text-white font-header text-xl relative z-10">Input KPI</h3>
                <p class="text-white/60 text-xs relative z-10">Klik untuk buat laporan harian</p>
                <div class="mt-4 bg-white/20 p-2 rounded-full px-6 text-white text-xs font-bold backdrop-blur-sm group-hover:bg-white group-hover:text-primary transition-all">
                    Gas Sekarang <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 p-8 border border-gray-50">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="font-header text-2xl text-primary leading-none mb-2">Statistik Mingguan</h3>
                <p class="text-gray-400 text-sm font-medium">Grafik fluktuasi skor KPI Anda</p>
            </div>
            <div class="p-4 bg-background rounded-2xl text-primary">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
        </div>
        <div class="h-80">
            @if($chartData->isEmpty())
            <div class="flex flex-col items-center justify-center h-full border-4 border-dashed border-gray-50 rounded-[2rem]">
                <img src="https://illustrations.popsy.co/teal/falling.svg" class="h-32 mb-4 opacity-50" alt="">
                <p class="text-gray-400 font-bold tracking-widest uppercase text-xs">No Data Available</p>
            </div>
            @else
            <canvas id="lineChart"></canvas>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 p-8 border border-gray-50">
        <h3 class="font-header text-2xl text-primary leading-none mb-8">Skill Radar</h3>
        <div class="h-80 relative flex items-center justify-center">
            @if($variableDistributions->isEmpty())
            <p class="text-gray-400 text-center italic">Belum ada data distribusi variabel</p>
            @else
            <canvas id="donutChart"></canvas>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Konfigurasi Global Chart.js
    Chart.defaults.font.family = 'Sniglet';
    Chart.defaults.color = '#09637E';

    // 1. Line Chart
    const lineCtx = document.getElementById('lineChart')?.getContext('2d');
    if (lineCtx) {
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: {
                    !!json_encode($chartData - > map(fn($d) => date('d/m', strtotime($d - > assessment_date)))) !!
                },
                datasets: [{
                    label: 'Skor KPI',
                    data: {
                        !!json_encode($chartData - > pluck('total_final_score')) !!
                    },
                    borderColor: '#088395',
                    backgroundColor: (context) => {
                        const gradient = context.chart.ctx.createRadialGradient(
                            context.chart.chartArea.left, 0, 0,
                            context.chart.chartArea.left, 500, 500
                        );
                        gradient.addColorStop(0, 'rgba(8, 131, 149, 0.2)');
                        gradient.addColorStop(1, 'rgba(8, 131, 149, 0)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#088395',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
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
                        max: 100,
                        grid: {
                            display: true,
                            color: '#f3f4f6'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // 2. Donut Chart
    const donutCtx = document.getElementById('donutChart')?.getContext('2d');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: {
                    !!json_encode($variableDistributions - > pluck('variable_name')) !!
                },
                datasets: [{
                    data: {
                        !!json_encode($variableDistributions - > pluck('avg_val')) !!
                    },
                    backgroundColor: ['#09637E', '#088395', '#05BFDB', '#7AB2B2'],
                    borderWidth: 5,
                    borderColor: '#ffffff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                },
                cutout: '75%'
            }
        });
    }
</script>
@endsection