@extends('layouts.staff')

@section('content')
    <div class="container mx-auto px-4 lg:px-0">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-header font-bold text-white">Achievement Stats</h1>
            <p class="text-slate-400 font-body text-xs md:text-sm">Analisis mendalam performa dan konsistensi kerja Anda.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="organic-card p-4 md:p-6">
                <h3 class="text-white font-header text-xs md:text-sm mb-6 text-center uppercase tracking-widest">Skill
                    Equilibrium (Radar)</h3>
                <div class="h-[300px] md:h-[350px] flex justify-center relative">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>

            <div class="organic-card p-4 md:p-6">
                <h3
                    class="text-white font-header text-xs md:text-sm mb-6 uppercase tracking-widest text-center lg:text-left">
                    Perbandingan Performa Tim</h3>
                <div class="space-y-6">
                    @foreach ($userStats as $key => $value)
                        <div>
                            <div class="flex justify-between text-[10px] md:text-xs mb-2">
                                <span class="text-slate-400 uppercase tracking-widest">{{ $key }}</span>
                                <span class="text-primary font-bold">{{ round($value) }}%</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2 relative overflow-hidden">
                                <div class="absolute h-full border-r-2 border-white/40 z-10"
                                    style="left: {{ $teamAverage[$key] }}%"
                                    title="Rata-rata Tim: {{ round($teamAverage[$key]) }}%">
                                </div>
                                <div class="bg-primary h-full rounded-full transition-all duration-1000"
                                    style="width: {{ $value }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <p class="text-[9px] text-slate-500 italic">* Garis vertikal: Rata-rata tim</p>
                                <p class="text-[9px] text-slate-400 font-bold">Avg: {{ round($teamAverage[$key]) }}%</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="organic-card p-4 md:p-6 mb-8">
            <h3 class="text-white font-header text-xs md:text-sm mb-6 uppercase tracking-widest">Activity Heatmap
                ({{ date('Y') }})</h3>

            <div class="w-full overflow-x-auto custom-scrollbar pb-4">
                <div id="heatmapChart" class="min-w-[650px] lg:min-w-full"></div>
            </div>
        </div>
    </div>

    <style>
        /* Scrollbar tipis untuk wrapper heatmap mobile */
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 10px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // --- RADAR CHART ---
        const ctxRadar = document.getElementById('radarChart').getContext('2d');
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: {!! json_encode(array_keys($userStats)) !!},
                datasets: [{
                    label: 'Performa Anda',
                    data: {!! json_encode(array_values($userStats)) !!},
                    fill: true,
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: '#10b981',
                    pointBackgroundColor: '#10b981',
                }, {
                    label: 'Rata-rata Tim',
                    data: {!! json_encode(array_values($teamAverage)) !!},
                    fill: true,
                    backgroundColor: 'rgba(255, 255, 255, 0.05)',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderDash: [5, 5],
                    pointRadius: 0,
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
                                size: window.innerWidth < 768 ? 10 : 12
                            }
                        },
                        ticks: {
                            display: false,
                            stepSize: 20
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#fff',
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });

        // --- HEATMAP CHART (Kembali ke Config Lama Anda) ---
        var optionsHeatmap = {
            series: {!! json_encode($heatmapData) !!},
            chart: {
                height: 280, // Sesuai permintaan lama agar lega
                type: 'heatmap',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: false
                },
                foreColor: '#ffffff'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 3,
                colors: ['#0f172a']
            },
            colors: ["#10b981"],
            plotOptions: {
                heatmap: {
                    radius: 2,
                    enableShades: false,
                    colorScale: {
                        ranges: [{
                                from: 0,
                                to: 0,
                                color: '#1e293b'
                            },
                            {
                                from: 1,
                                to: 2,
                                color: '#064e3b'
                            },
                            {
                                from: 3,
                                to: 5,
                                color: '#059669'
                            },
                            {
                                from: 6,
                                to: 100,
                                color: '#10b981'
                            }
                        ]
                    }
                }
            },
            xaxis: {
                type: 'category',
                labels: {
                    show: false
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#fff',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            },
            legend: {
                labels: {
                    colors: '#ffffff'
                }
            },
            tooltip: {
                custom: function({
                    series,
                    seriesIndex,
                    dataPointIndex,
                    w
                }) {
                    var val = w.globals.initialSeries[seriesIndex].data[dataPointIndex].y;
                    var date = w.globals.initialSeries[seriesIndex].data[dataPointIndex].date;
                    return '<div class="bg-slate-800 p-2 text-xs border border-slate-700 text-white">' +
                        '<strong>' + val + ' Kasus</strong> pada ' + date +
                        '</div>';
                }
            }
        };

        var chartHeatmap = new ApexCharts(document.querySelector("#heatmapChart"), optionsHeatmap);
        chartHeatmap.render();
    </script>
@endsection
