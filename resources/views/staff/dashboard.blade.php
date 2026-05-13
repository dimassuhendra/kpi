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
            <div class="space-y-6">
                {{-- BARIS 1: Trend & Top Activities --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Trend Area Chart --}}
                    <div x-data="chartBoTrend()"
                        class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                                <i class="fas fa-chart-line text-indigo-500 mr-2"></i>Produktivitas Saya
                            </h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">Volume Kegiatan (7
                                Hari Terakhir)</p>
                        </div>
                        <div x-ref="chart" class="-ml-2 min-h-[250px]"></div>
                    </div>

                    {{-- Top Categories Bar Chart --}}
                    <div x-data="chartBoTopActivities()"
                        class="lg:col-span-1 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                                <i class="fas fa-list-ol text-amber-500 mr-2"></i>Fokus Kegiatan
                            </h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">Kategori Paling
                                Sering</p>
                        </div>
                        <div x-ref="chart" class="-ml-2 min-h-[250px]"></div>
                    </div>
                </div>

                {{-- BARIS 2: Briefing Archive (Slider) --}}
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                                <i class="fas fa-comments text-blue-500 mr-2"></i>Team Briefing Archive
                            </h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Catatan Briefing Harian
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="$refs.notulenSlider.scrollBy({left: -350, behavior: 'smooth'})"
                                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-500 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                            <button @click="$refs.notulenSlider.scrollBy({left: 350, behavior: 'smooth'})"
                                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-500 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div x-ref="notulenSlider"
                        class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory custom-scrollbar"
                        style="scroll-behavior: smooth;">
                        <template x-for="(note, index) in chartData.bo.notulen_slider" :key="index">
                            <div
                                class="snap-start min-w-[300px] md:min-w-[380px] bg-slate-50 border border-slate-100 rounded-2xl p-5 hover:border-blue-300 transition-all group flex flex-col">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                                        <span class="block text-[9px] font-black text-blue-500 uppercase leading-none mb-1"
                                            x-text="note.hari"></span>
                                        <span class="block text-xs font-black text-slate-800" x-text="note.tanggal"></span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400" x-text="note.staff"></span>
                                </div>
                                <h4 class="text-sm font-black text-slate-800 mb-3 group-hover:text-blue-600 transition-colors"
                                    x-text="note.judul"></h4>
                                <div
                                    class="bg-white/60 p-4 rounded-xl h-[180px] overflow-y-auto custom-scrollbar shadow-inner">
                                    <p class="text-[11px] text-slate-600 leading-relaxed whitespace-pre-line font-mono"
                                        x-text="note.isi"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- BARIS 3: Word Cloud & Timeline --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div x-data="chartBoWordCloud()"
                        class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                        <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                            <i class="fas fa-cloud text-emerald-500 mr-2"></i>My Words
                        </h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-4">Topik Laporan Saya
                        </p>
                        <div class="h-[300px] relative">
                            <canvas x-ref="canvas" class="absolute inset-0 w-full h-full"></canvas>
                        </div>
                    </div>

                    <div
                        class="lg:col-span-1 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col h-[400px]">
                        <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                            <i class="fas fa-rss text-blue-500 mr-2"></i>My Recent Logs
                        </h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-4">Riwayat Aktivitas
                            Terakhir</p>
                        <div class="flex-1 overflow-y-auto pr-2 space-y-4 custom-scrollbar">
                            <template x-for="(item, index) in chartData.bo.timeline" :key="index">
                                <div class="relative pl-4 border-l-2 border-slate-100 pb-2">
                                    <div class="absolute -left-[5px] top-1.5 w-2 h-2 rounded-full bg-blue-500"></div>
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-[9px] font-black text-blue-600"
                                                x-text="item.tanggal"></span>
                                            <span class="text-[9px] text-slate-400" x-text="item.waktu"></span>
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-800" x-text="item.judul"></h4>
                                        <p class="text-[10px] text-slate-500 mt-1 line-clamp-2" x-text="item.deskripsi">
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- CDN ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>


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

            // Di dalam script blade, tambahkan fungsi ini untuk menangani data chart Backoffice
            Alpine.data('chartBoTrend', () => ({
                init() {
                    if (!this.chartData.bo) return;
                    new ApexCharts(this.$refs.chart, {
                        series: [{
                            name: 'Total Kegiatan',
                            data: this.chartData.bo.trend_volume
                        }],
                        chart: {
                            type: 'area',
                            height: 250,
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'inherit'
                        },
                        colors: ['#6366f1'],
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        xaxis: {
                            categories: this.chartData.bo.trend_labels
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                opacityFrom: 0.4,
                                opacityTo: 0.05
                            }
                        }
                    }).render();
                }
            }));

            Alpine.data('chartBoTopActivities', () => ({
                init() {
                    if (!this.chartData.bo) return;
                    const bo = this.chartData.bo;

                    new ApexCharts(this.$refs.chart, {
                        series: [{
                            name: 'Frekuensi',
                            data: bo.top_activities_series
                        }],
                        chart: {
                            type: 'bar',
                            height: 250,
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'inherit'
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                borderRadius: 4,
                                barHeight: '60%'
                            }
                        },
                        colors: ['#f59e0b'],
                        xaxis: {
                            categories: bo.top_activities_labels,
                            labels: {
                                show: false
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '10px',
                                    fontWeight: 700
                                },
                                formatter: function(val) {
                                    // Memotong label di sumbu Y agar tidak makan tempat, 
                                    // Nama lengkap akan muncul di Tooltip
                                    return val && val.length > 12 ? val.substring(0, 12) +
                                        '...' : val;
                                }
                            }
                        },
                        // Injeksi data rincian kegiatan ke config
                        customDetails: bo.top_activities_details || [],
                        tooltip: {
                            custom: function({
                                series,
                                seriesIndex,
                                dataPointIndex,
                                w
                            }) {
                                const category = w.globals.labels[dataPointIndex];
                                const count = series[seriesIndex][dataPointIndex];
                                const details = w.config.customDetails[dataPointIndex] ||
                            [];

                                // Buat list item kegiatan
                                let listHtml = details.map(item =>
                                    `<li style="margin-bottom: 5px; line-height: 1.4; border-bottom: 1px solid #f1f5f9; padding-bottom: 3px;">&bull; ${item}</li>`
                                ).join('');

                                if (details.length >= 5) {
                                    listHtml +=
                                        `<li style="font-style: italic; color: #94a3b8; margin-top: 5px;">...dan kegiatan lainnya</li>`;
                                }

                                return `
                        <div style="padding: 12px; border-radius: 12px; background: white; border: 1px solid #e2e8f0; max-width: 300px; white-space: normal; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                            <div style="font-weight: 900; font-size: 11px; color: #1e293b; margin-bottom: 8px; text-transform: uppercase; border-bottom: 2px solid #f59e0b; padding-bottom: 4px;">
                                ${category}
                            </div>
                            <div style="font-size: 10px; color: #475569; font-weight: 700; margin-bottom: 10px;">
                                Total: <span style="background: #fef3c7; color: #d97706; padding: 2px 6px; border-radius: 4px;">${count} Laporan</span>
                            </div>
                            <ul style="font-size: 10px; color: #64748b; margin: 0; padding: 0; list-style: none;">
                                ${listHtml}
                            </ul>
                        </div>
                    `;
                            }
                        }
                    }).render();
                }
            }));

            Alpine.data('chartBoWordCloud', () => ({
                init() {
                    if (!this.chartData.bo || !this.chartData.bo.wordcloud_data) return;
                    const canvas = this.$refs.canvas;
                    const container = canvas.parentElement;
                    canvas.width = container.clientWidth;
                    canvas.height = container.clientHeight;
                    WordCloud(canvas, {
                        list: this.chartData.bo.wordcloud_data,
                        fontFamily: 'Nunito',
                        weightFactor: 1,
                        color: 'random-dark',
                        rotateRatio: 0.5,
                        backgroundColor: 'transparent',
                        shape: 'circle'
                    });
                }
            }));
        });
    </script>
@endsection
