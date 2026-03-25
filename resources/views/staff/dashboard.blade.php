@extends('layouts.staff')

@section('content')
    <div class="space-y-8 pb-10" x-data="staffDashboard()">

        {{-- Header Section --}}
        <div
            class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-header font-black text-slate-800 tracking-tight uppercase">
                    My <span class="text-amber-500 italic">Station</span>
                </h1>
                <p class="text-slate-500 text-xs font-bold tracking-widest uppercase mt-1">
                    Personal Performance:
                    <span class="text-indigo-600">
                        {{ Auth::user()->divisi_id == 2 ? 'INFRASTRUCTURE' : (Auth::user()->divisi_id == 1 ? 'TAC' : 'BACKOFFICE') }}
                    </span>
                </p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Waktu Server</p>
                <p class="text-lg font-black text-slate-700">{{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
            </div>
        </div>

        {{-- Top Cards Section (Global) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm border-b-4 border-b-emerald-500 relative overflow-hidden group">
                <i
                    class="fas fa-bolt absolute -right-4 -top-4 text-7xl text-slate-50 group-hover:scale-110 transition-transform"></i>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1 relative z-10">Aktivitas Hari
                    Ini</p>
                <h2 class="text-5xl font-black text-emerald-500 relative z-10">{{ $dailyCount }}</h2>
            </div>

            <div
                class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm border-b-4 border-b-blue-500 relative overflow-hidden group">
                <i
                    class="fas fa-calendar-week absolute -right-4 -top-4 text-7xl text-slate-50 group-hover:scale-110 transition-transform"></i>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1 relative z-10">Total Minggu
                    Ini</p>
                <h2 class="text-5xl font-black text-blue-500 relative z-10">{{ $weeklyCount }}</h2>
            </div>

            <div
                class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm border-b-4 border-b-purple-500 relative overflow-hidden group">
                <i
                    class="fas fa-award absolute -right-4 -top-4 text-7xl text-slate-50 group-hover:scale-110 transition-transform"></i>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1 relative z-10">Akumulasi Bulan
                    Ini</p>
                <h2 class="text-5xl font-black text-purple-500 relative z-10">{{ $monthlyCount }}</h2>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- LOGIKA KHUSUS DIVISI 1: TAC                               --}}
        {{-- ========================================================= --}}
        @if (Auth::user()->divisi_id == 1)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Tren Aktivitas (Line Chart) --}}
                <div class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Tren Personal (7 Hari
                        Terakhir)</h3>
                    <div x-ref="tacTrendChart"></div>
                </div>

                {{-- Gauge Waktu Respon --}}
                <div
                    class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col items-center justify-center relative">
                    <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2 text-center">Rata-rata
                        Respon Saya</h3>
                    <div x-ref="tacGaugeChart" class="-mt-4"></div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest -mt-6">Batas Maksimal: 15 Menit
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Case vs
                        Activity</h3>
                    <div x-ref="tacDonutCaseAct" class="flex justify-center"></div>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Deteksi Dini
                        vs Laporan</h3>
                    <div x-ref="tacDonutTemuan" class="flex justify-center"></div>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Mandiri vs
                        Eskalasi</h3>
                    <div x-ref="tacDonutMandiri" class="flex justify-center"></div>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- LOGIKA KHUSUS DIVISI 2: INFRASTRUKTUR                     --}}
            {{-- ========================================================= --}}
        @elseif (Auth::user()->divisi_id == 2)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Donut Distribusi Kategori (Bulan Ini) --}}
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 text-center">Distribusi
                        Pekerjaan (Bulan Ini)</h3>
                    <div x-ref="infraDonut" class="flex justify-center"></div>
                </div>

                {{-- Line Chart Tren (7 Hari Terakhir) --}}
                <div class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Tren Kategori (7 Hari
                        Terakhir)</h3>
                    <div x-ref="infraTrend"></div>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- LOGIKA KHUSUS DIVISI BACKOFFICE                           --}}
            {{-- ========================================================= --}}
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Line Chart Tren (7 Hari Terakhir) --}}
                <div class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Produktivitas (7 Hari
                        Terakhir)</h3>
                    <div x-ref="boTrend"></div>
                </div>

                {{-- Last Activities --}}
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 border-b pb-2">
                        Riwayat: {{ $lastReportDate ? date('d M Y', strtotime($lastReportDate)) : 'Belum ada data' }}
                    </h3>
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                        @forelse ($yesterdayActivities as $act)
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 mt-1.5 rounded-full bg-emerald-500 shrink-0"></div>
                                <div>
                                    <h4 class="text-slate-700 font-bold text-sm">{{ $act->judul_kegiatan }}</h4>
                                    <p class="text-slate-500 text-xs mt-0.5">
                                        {{ $act->deskripsi_kegiatan ?? $act->deskripsi }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 text-xs italic text-center py-4">Tidak ada catatan aktivitas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- CDN ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('staffDashboard', () => ({
                chartData: @json($chartData),
                divisiId: {{ Auth::user()->divisi_id }},

                init() {
                    this.$nextTick(() => {
                        this.renderCharts();
                    });
                },

                // Fungsi Helper untuk membuat Donut berpersentase di tengah
                createDonut(refName, series, labels, colors) {
                    if (!this.$refs[refName]) return;
                    new ApexCharts(this.$refs[refName], {
                        series: series,
                        chart: {
                            type: 'donut',
                            height: 260,
                            fontFamily: 'inherit'
                        },
                        labels: labels,
                        colors: colors,
                        stroke: {
                            width: 2,
                            colors: ['#fff']
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            fontSize: '10px',
                                            color: '#94a3b8',
                                            offsetY: -5
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '18px',
                                            fontWeight: 800,
                                            color: '#334155',
                                            offsetY: 5,
                                            formatter: function(val, w) {
                                                let numVal = parseFloat(val) || 0;
                                                let total = w.globals.seriesTotals.reduce((
                                                    a, b) => a + b, 0);
                                                let pct = total > 0 ? (numVal / total) *
                                                    100 : 0;
                                                return numVal + " (" + pct.toFixed(1) +
                                                "%)";
                                            }
                                        },
                                        total: {
                                            show: true,
                                            showAlways: false,
                                            label: 'Total',
                                            fontSize: '11px',
                                            fontWeight: 600,
                                            color: '#64748b'
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'bottom',
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    }).render();
                },

                renderCharts() {
                    // ==========================================
                    // RENDER CHART TAC
                    // ==========================================
                    if (this.divisiId === 1 && this.chartData.tac) {
                        const tac = this.chartData.tac;

                        // Donuts
                        this.createDonut('tacDonutCaseAct', tac.case_vs_activity, ['Case', 'Activity'],
                            ['#ef4444', '#94a3b8']);
                        this.createDonut('tacDonutTemuan', tac.temuan_vs_laporan, ['Temuan Dini',
                            'Laporan'
                        ], ['#f59e0b', '#3b82f6']);
                        this.createDonut('tacDonutMandiri', tac.mandiri_vs_eskalasi, ['Mandiri',
                            'Eskalasi'
                        ], ['#10b981', '#f43f5e']);

                        // Gauge Rata-rata Respon
                        let val = tac.avg_response_time || 0;
                        let pct = Math.min((val / 30) * 100, 100);
                        let color = val <= 10 ? '#10b981' : (val <= 15 ? '#f59e0b' : '#ef4444');

                        if (this.$refs.tacGaugeChart) {
                            new ApexCharts(this.$refs.tacGaugeChart, {
                                series: [pct],
                                chart: {
                                    type: 'radialBar',
                                    height: 280,
                                    fontFamily: 'inherit'
                                },
                                colors: [color],
                                plotOptions: {
                                    radialBar: {
                                        startAngle: -135,
                                        endAngle: 135,
                                        hollow: {
                                            size: '60%'
                                        },
                                        track: {
                                            background: '#f1f5f9',
                                            strokeWidth: '100%'
                                        },
                                        dataLabels: {
                                            name: {
                                                show: true,
                                                fontSize: '10px',
                                                color: '#64748b',
                                                offsetY: 20
                                            },
                                            value: {
                                                formatter: () => val + "m",
                                                fontSize: '24px',
                                                fontWeight: 900,
                                                color: color,
                                                offsetY: -10
                                            }
                                        }
                                    }
                                },
                                labels: [val <= 10 ? 'Sangat Baik' : (val <= 15 ?
                                    'Batas Wajar' : 'Kritis (Lewat Batas)')]
                            }).render();
                        }

                        // Line Trend
                        if (this.$refs.tacTrendChart) {
                            new ApexCharts(this.$refs.tacTrendChart, {
                                series: [{
                                        name: 'Case',
                                        data: tac.trend_cases
                                    },
                                    {
                                        name: 'Activity',
                                        data: tac.trend_activities
                                    }
                                ],
                                chart: {
                                    type: 'area',
                                    height: 280,
                                    fontFamily: 'inherit',
                                    toolbar: {
                                        show: false
                                    }
                                },
                                colors: ['#ef4444', '#94a3b8'],
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                fill: {
                                    type: 'gradient',
                                    gradient: {
                                        shadeIntensity: 1,
                                        opacityFrom: 0.4,
                                        opacityTo: 0.05,
                                        stops: [0, 90, 100]
                                    }
                                },
                                xaxis: {
                                    categories: tac.trend_labels,
                                    labels: {
                                        style: {
                                            fontSize: '10px',
                                            fontWeight: 700
                                        }
                                    }
                                },
                                legend: {
                                    position: 'top',
                                    fontSize: '11px',
                                    fontWeight: 700
                                }
                            }).render();
                        }
                    }

                    // ==========================================
                    // RENDER CHART INFRA
                    // ==========================================
                    else if (this.divisiId === 2 && this.chartData.infra) {
                        const infra = this.chartData.infra;
                        const infraColors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#64748b'];

                        this.createDonut('infraDonut', infra.donut_kategori, infra.categories,
                            infraColors);

                        if (this.$refs.infraTrend) {
                            new ApexCharts(this.$refs.infraTrend, {
                                series: infra.trend_kategori,
                                chart: {
                                    type: 'line',
                                    height: 280,
                                    fontFamily: 'inherit',
                                    toolbar: {
                                        show: false
                                    }
                                },
                                colors: infraColors,
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 4
                                },
                                xaxis: {
                                    categories: infra.trend_labels,
                                    labels: {
                                        style: {
                                            fontSize: '10px',
                                            fontWeight: 700
                                        }
                                    }
                                },
                                legend: {
                                    position: 'top',
                                    fontSize: '11px',
                                    fontWeight: 700
                                }
                            }).render();
                        }
                    }

                    // ==========================================
                    // RENDER CHART BACKOFFICE
                    // ==========================================
                    else if (this.chartData.bo) {
                        if (this.$refs.boTrend) {
                            new ApexCharts(this.$refs.boTrend, {
                                series: [{
                                    name: 'Total Pekerjaan',
                                    data: this.chartData.bo.trend_volume
                                }],
                                chart: {
                                    type: 'area',
                                    height: 280,
                                    fontFamily: 'inherit',
                                    toolbar: {
                                        show: false
                                    }
                                },
                                colors: ['#8b5cf6'],
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                fill: {
                                    type: 'gradient',
                                    gradient: {
                                        shadeIntensity: 1,
                                        opacityFrom: 0.4,
                                        opacityTo: 0.05,
                                        stops: [0, 90, 100]
                                    }
                                },
                                xaxis: {
                                    categories: this.chartData.bo.trend_labels,
                                    labels: {
                                        style: {
                                            fontSize: '10px',
                                            fontWeight: 700
                                        }
                                    }
                                }
                            }).render();
                        }
                    }
                }
            }));
        });
    </script>
@endsection
