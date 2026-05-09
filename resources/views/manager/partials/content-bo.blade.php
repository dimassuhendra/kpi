{{-- SECTION: BACKOFFICE / GENERAL METRICS --}}
<div class="mb-10">
    <div class="flex items-center gap-2 mb-4">
        <h2 class="text-lg font-black text-slate-800 uppercase tracking-widest border-l-4 border-blue-500 pl-3">
            General <span class="text-blue-500">Analytics</span>
        </h2>
    </div>

    {{-- BARIS 1: Trend Volume & Top 5 Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Area Chart: Trend Produktivitas --}}
        <div x-data="chartBoTrend()"
            class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                    <i class="fas fa-chart-line text-indigo-500 mr-2"></i>Tren Produktivitas Harian
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">Total Volume Kegiatan per
                    Hari</p>
            </div>
            <div x-ref="chart" class="-ml-2 min-h-[250px]"></div>
        </div>

        {{-- Bar Chart: Top 5 Kegiatan --}}
        <div x-data="chartBoTopActivities()"
            class="lg:col-span-1 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                    <i class="fas fa-list-ol text-amber-500 mr-2"></i>Top Kategori Kegiatan
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">Tindakan Paling Sering</p>
            </div>
            <div x-ref="chart" class="-ml-2 min-h-[250px]"></div>
        </div>
    </div>

    {{-- BARIS 2: Word Cloud & Timeline Feed --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- WORD CLOUD (Pengganti Heatmap) --}}
        <div x-data="chartBoWordCloud()"
            class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                    <i class="fas fa-cloud text-emerald-500 mr-2"></i>Topic Word Cloud
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-4">Kata Yang Paling Sering
                    Muncul Dalam Laporan</p>
            </div>

            {{-- Kanvas tempat Word Cloud akan digambar --}}
            <div class="flex-1 flex justify-center items-center w-full relative h-[300px]">
                <canvas x-ref="canvas" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        {{-- Live Timeline Feed --}}
        <div class="lg:col-span-1 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col h-[400px]">
            <div>
                <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-1">
                    <i class="fas fa-rss text-blue-500 mr-2"></i>Live Log Feed
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-4">Aktivitas Terbaru</p>
            </div>

            {{-- Wrapper Timeline dengan custom scrollbar --}}
            <div class="flex-1 overflow-y-auto pr-2 space-y-4 custom-scrollbar">
                <template x-for="(item, index) in chartData.bo.timeline" :key="index">
                    <div class="relative pl-4 border-l-2 border-slate-100 pb-2">
                        <div class="absolute -left-[5px] top-1.5 w-2 h-2 rounded-full bg-blue-500 ring-4 ring-white">
                        </div>

                        <div
                            class="bg-slate-50 p-3 rounded-xl border border-slate-100 hover:border-blue-200 transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest"
                                    x-text="item.staff"></span>
                                <span class="text-[9px] font-bold text-slate-400" x-text="item.waktu"></span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 leading-snug" x-text="item.judul"></h4>
                            <template x-if="item.deskripsi">
                                <p class="text-[10px] text-slate-500 mt-1.5 font-medium leading-relaxed"
                                    x-text="item.deskripsi"></p>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="!chartData.bo.timeline || chartData.bo.timeline.length === 0">
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-3xl text-slate-200 mb-3 block"></i>
                        <span class="text-xs font-bold text-slate-400">Belum ada log kegiatan.</span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

{{-- LIBRARY WORD CLOUD JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {

        // --- 1. TREN PRODUKTIVITAS (AREA CHART) ---
        Alpine.data('chartBoTrend', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', (e) => this.render(e.detail));
                window.addEventListener('update-charts', (e) => this.update(e.detail));
            },
            getOptions(data) {
                return {
                    series: [{
                        name: 'Total Kegiatan',
                        data: data.bo.trend_volume || []
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
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        categories: data.trend_labels || [],
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '10px',
                                fontWeight: 600
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '10px',
                                fontWeight: 600
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4
                    }
                };
            },
            render(data) {
                if (!data.bo || !data.bo.trend_volume) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (!data.bo || !data.bo.trend_volume) return;
                this.chart.updateSeries([{
                    name: 'Total Kegiatan',
                    data: data.bo.trend_volume
                }]);
                this.chart.updateOptions({
                    xaxis: {
                        categories: data.trend_labels
                    }
                });
            }
        }));

        // --- 2. TOP 5 AKTIVITAS (BAR CHART HORIZONTAL DENGAN CUSTOM TOOLTIP) ---
        Alpine.data('chartBoTopActivities', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', (e) => this.render(e.detail));
                window.addEventListener('update-charts', (e) => this.update(e.detail));
            },
            getOptions(data) {
                return {
                    series: [{
                        name: 'Frekuensi',
                        data: data.bo.top_activities_series || []
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit'
                    },
                    colors: ['#f59e0b'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 4,
                            barHeight: '60%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '10px',
                            colors: ['#fff']
                        }
                    },
                    xaxis: {
                        categories: data.bo.top_activities_labels || [],
                        labels: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#475569',
                                fontSize: '10px',
                                fontWeight: 700
                            },
                            formatter: function(val) {
                                // Memotong sumbu Y agar rapi, tapi tenang, teks aslinya bisa dibaca di Tooltip saat hover
                                return val && val.length > 12 ? val.substring(0, 12) + '...' :
                                    val;
                            }
                        }
                    },
                    grid: {
                        show: false
                    },

                    // Injeksi data rincian ke dalam config grafik
                    customDetails: data.bo.top_activities_details || [],

                    // CUSTOM TOOLTIP (Munculkan daftar aktivitas riil)
                    tooltip: {
                        custom: function({
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            let category = w.globals.labels[dataPointIndex];
                            let count = series[seriesIndex][dataPointIndex];
                            let details = w.config.customDetails[dataPointIndex] || [];

                            // Render list item HTML
                            let listHtml = details.map(item =>
                                `<li style="margin-bottom: 3px; line-height: 1.3;">&bull; ${item}</li>`
                                ).join('');

                            // Jika ada lebih dari 5, tandai ada yang lain
                            if (details.length >= 5) {
                                listHtml +=
                                    `<li style="font-style: italic; color: #94a3b8; margin-top: 4px;">dan aktivitas lainnya...</li>`;
                            }

                            // Kembalikan desain pop-up Tooltip
                            return `
                                <div style="padding: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-radius: 12px; background: white; border: 1px solid #e2e8f0; max-width: 280px; white-space: normal;">
                                    <div style="font-weight: 900; font-size: 11px; color: #1e293b; margin-bottom: 6px; text-transform: uppercase; border-bottom: 2px dashed #f1f5f9; padding-bottom: 6px;">
                                        ${category}
                                    </div>
                                    <div style="font-size: 10px; color: #475569; font-weight: 700; margin-bottom: 8px;">
                                        Total: <span style="background: #fef3c7; color: #d97706; padding: 2px 6px; border-radius: 4px;">${count} Kegiatan</span>
                                    </div>
                                    <ul style="font-size: 9.5px; color: #64748b; margin: 0; padding: 0; list-style: none;">
                                        ${listHtml}
                                    </ul>
                                </div>
                            `;
                        }
                    }
                };
            },
            render(data) {
                if (!data.bo || !data.bo.top_activities_series) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (!data.bo || !data.bo.top_activities_series) return;
                this.chart.updateSeries([{
                    name: 'Frekuensi',
                    data: data.bo.top_activities_series
                }]);
                this.chart.updateOptions({
                    xaxis: {
                        categories: data.bo.top_activities_labels
                    },
                    customDetails: data.bo.top_activities_details // Update rinciannya juga
                });
            }
        }));

        // --- 3. WORD CLOUD ---
        Alpine.data('chartBoWordCloud', () => ({
            init() {
                window.addEventListener('render-charts', (e) => this.render(e.detail));
                window.addEventListener('update-charts', (e) => this.render(e.detail));
            },
            render(data) {
                if (!data.bo || !data.bo.wordcloud_data || data.bo.wordcloud_data.length === 0) {
                    return; // Abaikan jika tidak ada kata
                }

                const canvas = this.$refs.canvas;
                // Ambil ukuran kontainer asli untuk mengatasi canvas gepeng
                const container = canvas.parentElement;
                canvas.width = container.clientWidth;
                canvas.height = container.clientHeight;

                // Library wordcloud2.js
                WordCloud(canvas, {
                    list: data.bo.wordcloud_data,
                    fontFamily: 'Nunito, ui-sans-serif, system-ui',
                    weightFactor: 1, // Bobot sudah kita kalikan dari controller
                    color: function(word, weight) {
                        // Variasi warna estetik (Biru, Hijau, Ungu, Kuning)
                        const colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b',
                            '#64748b', '#0ea5e9'
                        ];
                        return colors[Math.floor(Math.random() * colors.length)];
                    },
                    rotateRatio: 0.5, // 50% kata akan miring
                    rotationSteps: 2,
                    backgroundColor: 'transparent',
                    shape: 'circle',
                    shrinkToFit: true
                });
            }
        }));

    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
