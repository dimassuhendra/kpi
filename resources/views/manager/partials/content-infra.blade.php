{{-- SECTION: INFRASTRUCTURE METRICS --}}
<div class="mb-10">
    <div class="flex items-center gap-2 mb-4">
        <h2 class="text-lg font-black text-slate-800 uppercase tracking-widest border-l-4 border-amber-500 pl-3">
            Infrastructure <span class="text-amber-500">Analytics</span>
        </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Donut Chart: Distribusi Kategori --}}
        <div x-data="chartInfraDonut()"
            class="lg:col-span-1 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-center">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 text-center">Distribusi
                Kategori Pekerjaan</h3>
            <div x-ref="chart" class="flex justify-center"></div>
        </div>

        {{-- Stacked Bar Chart: Distribusi per Staf --}}
        <div x-data="chartInfraStacked()" class="lg:col-span-2 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Volume Pekerjaan Berdasarkan
                Staff & Kategori</h3>
            <div x-ref="chart"></div>
        </div>
    </div>

    {{-- Line Chart: Tren Kategori Harian --}}
    <div x-data="chartInfraTrend()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tren Kategori Pekerjaan Harian
        </h3>
        <div x-ref="chart"></div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        // Palet warna konsisten untuk kategori Infra
        const infraColors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#64748b'];

        // --- 1. DONUT CHART INFRA ---
        Alpine.data('chartInfraDonut', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', (e) => this.render(e.detail));
                window.addEventListener('update-charts', (e) => this.update(e.detail));
            },
            getOptions(data) {
                return {
                    series: data.infra.donut_kategori,
                    chart: {
                        type: 'pie',
                        height: 280,
                        fontFamily: 'inherit'
                    },
                    labels: data.infra.categories,
                    colors: infraColors,
                    stroke: {
                        width: 2,
                        colors: ['#fff']
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '12px',
                            fontWeight: 800
                        },
                        dropShadow: {
                            enabled: true,
                            top: 1,
                            left: 1,
                            blur: 1,
                            opacity: 0.5
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '11px',
                        fontWeight: 700
                    }
                };
            },
            render(data) {
                if (!data.infra.donut_kategori) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (!data.infra.donut_kategori) return;
                this.chart.updateSeries(data.infra.donut_kategori);
            }
        }));

        // --- 2. STACKED BAR CHART (NILAI DI TENGAH) ---
        Alpine.data('chartInfraStacked', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', (e) => this.render(e.detail));
                window.addEventListener('update-charts', (e) => this.update(e.detail));
            },
            getOptions(data) {
                return {
                    series: data.infra.stacked_staff,
                    chart: {
                        type: 'bar',
                        height: 300,
                        stacked: true,
                        fontFamily: 'inherit',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: infraColors,
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            borderRadius: 4,
                            dataLabels: {
                                position: 'center'
                            } // MEMASTIKAN ANGKA MUNCUL DI TENGAH
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '12px',
                            fontWeight: 800,
                            colors: ['#fff']
                        },
                        formatter: function(val) {
                            return val > 0 ? val : "";
                        } // Sembunyikan jika nilainya 0
                    },
                    xaxis: {
                        categories: data.staff_labels,
                        labels: {
                            style: {
                                fontSize: '10px',
                                fontWeight: 700
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '11px',
                        fontWeight: 700
                    },
                    fill: {
                        opacity: 1
                    }
                };
            },
            render(data) {
                if (!data.infra.stacked_staff) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (!data.infra.stacked_staff) return;
                this.chart.updateSeries(data.infra.stacked_staff);
                this.chart.updateOptions({
                    xaxis: {
                        categories: data.staff_labels
                    }
                });
            }
        }));

        // --- 3. LINE CHART TREN HARIAN ---
        Alpine.data('chartInfraTrend', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', (e) => this.render(e.detail));
                window.addEventListener('update-charts', (e) => this.update(e.detail));
            },
            getOptions(data) {
                return {
                    series: data.infra.trend_kategori,
                    chart: {
                        type: 'line',
                        height: 300,
                        fontFamily: 'inherit',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: infraColors,
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    }, // Garis dibuat mulus
                    markers: {
                        size: 4,
                        hover: {
                            sizeOffset: 3
                        }
                    },
                    xaxis: {
                        categories: data.trend_labels,
                        labels: {
                            style: {
                                fontSize: '10px',
                                fontWeight: 700
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '10px',
                                fontWeight: 700
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '11px',
                        fontWeight: 700
                    }
                };
            },
            render(data) {
                if (!data.infra.trend_kategori) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (!data.infra.trend_kategori) return;
                this.chart.updateSeries(data.infra.trend_kategori);
                this.chart.updateOptions({
                    xaxis: {
                        categories: data.trend_labels
                    }
                });
            }
        }));
    });
</script>
