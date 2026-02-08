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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 organic-card p-8">
            <h3 class="text-white font-header mb-6 flex items-center">
                <i class="fas fa-wave-square mr-3 text-primary"></i> Productivity Rhythm (Last 7 Days)
            </h3>
            <div class="h-[300px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <div class="space-y-8">
            <div class="organic-card p-6">
                <h3 class="text-white font-header text-sm mb-4 text-center">Work Autonomy</h3>
                <canvas id="autonomyChart"></canvas>
            </div>
            <div class="organic-card p-6">
                <h3 class="text-white font-header text-sm mb-4 text-center">Proactive Discovery</h3>
                <canvas id="sourceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Trend Line Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {
                !!json_encode($trendData - > pluck('tanggal') - > map(fn($d) => date('d M', strtotime($d)))) !!
            },
            datasets: [{
                label: 'Total Cases',
                data: {
                    !!json_encode($trendData - > pluck('total')) !!
                },
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

    // 2. Autonomy Doughnut
    new Chart(document.getElementById('autonomyChart'), {
        type: 'doughnut',
        data: {
            labels: ['Mandiri', 'Bantuan'],
            datasets: [{
                data: [{
                        {
                            $autonomyData - > where('is_mandiri', 1) - > first() - > total ?? 0
                        }
                    },
                    {
                        {
                            $autonomyData - > where('is_mandiri', 0) - > first() - > total ?? 0
                        }
                    }
                ],
                backgroundColor: ['#10b981', '#1e293b'],
                borderWidth: 0
            }]
        }
    });

    // 3. Source Doughnut
    new Chart(document.getElementById('sourceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Temuan', 'Laporan'],
            datasets: [{
                data: [{
                        {
                            $sourceData - > where('temuan_sendiri', 1) - > first() - > total ?? 0
                        }
                    },
                    {
                        {
                            $sourceData - > where('temuan_sendiri', 0) - > first() - > total ?? 0
                        }
                    }
                ],
                backgroundColor: ['#3b82f6', '#1e293b'],
                borderWidth: 0
            }]
        }
    });
</script>
@endsection