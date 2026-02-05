@extends('layouts.manager')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h2 class="font-header text-3xl text-white italic text-primary">Team <span class="text-white">Analytics</span></h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Individual Performance Comparison --}}
        <div class="lg:col-span-2 organic-card p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white font-bold">Tren Performa Individu</h3>
                <form class="flex gap-2">
                    <select name="staff_a" onchange="this.form.submit()" class="bg-secondary text-white text-[10px] rounded-lg border-none focus:ring-primary">
                        @foreach($staffMembers as $s)
                        <option value="{{ $s->id }}" {{ request('staff_a') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    <span class="text-slate-500 self-center text-xs text-primary">VS</span>
                    <select name="staff_b" onchange="this.form.submit()" class="bg-secondary text-white text-[10px] rounded-lg border-none focus:ring-primary">
                        @foreach($staffMembers as $s)
                        <option value="{{ $s->id }}" {{ request('staff_b') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <canvas id="performanceChart" height="250"></canvas>
        </div>

        {{-- Variable Strength (Radar Chart) --}}
        <div class="organic-card p-6">
            <h3 class="text-white font-bold mb-6">Kekuatan Variabel Tim</h3>
            <canvas id="radarChart"></canvas>
            <p class="text-[10px] text-slate-500 mt-4 italic text-center text-primary">* Rata-rata nilai input staff per variabel</p>
        </div>

        {{-- Busy Hours (Heatmap-like Bar) --}}
        <div class="lg:col-span-3 organic-card p-6">
            <h3 class="text-white font-bold mb-4">Jam Sibuk Pelaporan (Traffic)</h3>
            <div class="h-48 flex items-end gap-1">
                @for($i = 0; $i < 24; $i++)
                    @php
                    $val=$busyHours[$i] ?? 0;
                    $height=$val> 0 ? ($val / max($busyHours ?: [1]) * 100) : 5;
                    @endphp
                    <div class="group relative flex-1">
                        <div class="bg-primary/20 group-hover:bg-primary transition-all rounded-t-md" style="height: {{ $height }}%"></div>
                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 text-[10px] text-white opacity-0 group-hover:opacity-100">{{ $val }} lap</span>
                        <span class="block text-[8px] text-slate-600 mt-2 text-center">{{ $i }}</span>
                    </div>
                    @endfor
            </div>
        </div>
    </div>
</div>



<script>
    // 1. Performance Line Chart
    new Chart(document.getElementById('performanceChart'), {
        type: 'line',
        data: {
            labels: @json($performanceTrend['labels']),
            datasets: [{
                label: '{{ $performanceTrend["nameA"] }}',
                data: @json($performanceTrend['staffA']),
                borderColor: '#6366f1',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.1)'
            }, {
                label: '{{ $performanceTrend["nameB"] }}',
                data: @json($performanceTrend['staffB']),
                borderColor: '#ec4899',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(236, 72, 153, 0.1)'
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    grid: {
                        color: 'rgba(255,255,255,0.05)'
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
        }
    });

    // 2. Variable Strength Radar Chart
    new Chart(document.getElementById('radarChart'), {
        type: 'radar',
        data: {
            labels: @json($variableStrength['labels']),
            datasets: [{
                label: 'Rata-rata Tim',
                data: @json($variableStrength['values']),
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: '#6366f1',
                pointBackgroundColor: '#6366f1',
            }]
        },
        options: {
            scales: {
                r: {
                    angleLines: {
                        color: 'rgba(255,255,255,0.05)'
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.1)'
                    },
                    pointLabels: {
                        color: '#94a3b8'
                    },
                    ticks: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection