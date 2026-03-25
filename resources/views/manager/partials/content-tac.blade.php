{{-- SECTION: TAC ANALYTICS --}}
<div class="mb-10 space-y-6">
    <div class="flex items-center gap-2 mb-4">
        <h2 class="text-lg font-black text-slate-800 uppercase tracking-widest border-l-4 border-amber-500 pl-3">
            TAC <span class="text-amber-500">Analytics</span>
        </h2>
    </div>

    {{-- ROW 1: RASIO PENANGANAN (2 Kolom) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div x-data="chartTacDonutTemuan()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Deteksi Dini vs
                Laporan Gangguan</h3>
            <div x-ref="chart" class="flex justify-center"></div>
        </div>
        <div x-data="chartTacDonutMandiri()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Penyelesaian
                Mandiri vs Eskalasi</h3>
            <div x-ref="chart" class="flex justify-center"></div>
        </div>
    </div>

    {{-- ROW 2: DISTRIBUSI & SLA (3 Kolom) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div x-data="chartTacDonutCaseAct()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Case vs General
                Activity</h3>
            <div x-ref="chart" class="flex justify-center"></div>
        </div>
        <div x-data="chartTacDonutNetGps()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Case Network vs
                GPS</h3>
            <div x-ref="chart" class="flex justify-center"></div>
        </div>
        <div x-data="chartTacGaugeTime()"
            class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 bg-slate-50/50"></div>
            <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2 text-center relative z-10">
                Rata-rata Waktu Respon</h3>
            <div x-ref="chart" class="relative z-10 -mt-4"></div>
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest relative z-10 -mt-6">Batas Maksimal:
                15 Menit</p>
        </div>
    </div>

    {{-- ROW 3: KINERJA INDIVIDU (3 Kolom) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div x-data="chartTacBarTotal()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Case per Staff</h3>
            <div x-ref="chart"></div>
        </div>
        <div x-data="chartTacBarAvg()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Rata-rata Respon (Menit)
            </h3>
            <div x-ref="chart"></div>
        </div>
        <div x-data="chartTacBarGrouped()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Mandiri & Temuan per Staff
            </h3>
            <div x-ref="chart"></div>
        </div>
    </div>

    {{-- ROW 4: TREN HARIAN (2 Kolom) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div x-data="chartTacTrendCaseAct()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tren Activity vs Case Harian
            </h3>
            <div x-ref="chart"></div>
        </div>
        <div x-data="chartTacTrendGps()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Volume Kendaraan GPS Harian
            </h3>
            <div x-ref="chart"></div>
        </div>
    </div>

    {{-- ROW 5: KEPATUHAN & EVALUASI (Dipindah dari Executive) --}}
    <div class="flex items-center gap-2 mb-4 mt-8">
        <h2 class="text-lg font-black text-slate-800 uppercase tracking-widest border-l-4 border-indigo-500 pl-3">
            Compliance & <span class="text-indigo-500">Evaluation</span>
        </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div x-data="chartCompliance('Dashboard')" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Kepatuhan
                Dashboard KPI</h3>
            <div x-ref="chart" class="flex justify-center"></div>
        </div>
        <div x-data="chartCompliance('GPS')" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-center">Kepatuhan Report
                GPS</h3>
            <div x-ref="chart" class="flex justify-center"></div>
        </div>
    </div>

    <div class="grid grid-cols-1">
        <div x-data="chartCustomerFeedback()" class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Customer Feedback (Volume vs
                Rating)</h3>
            <div x-ref="chart"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        // Fungsi pembantu untuk membuat opsi Donut yang rapi
        const createDonutOptions = (series, labels, colors) => ({
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
                                // === PERBAIKAN DI SINI ===
                                formatter: function(val, w) {
                                    // 1. Ambil nilai asli (pastikan berupa angka)
                                    let numericVal = parseFloat(val) || 0;

                                    // 2. Hitung total keseluruhan data dari globals
                                    let total = w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0);

                                    // 3. Kalkulasi persentase manual
                                    let percent = total > 0 ? (numericVal / total) * 100 : 0;

                                    // 4. Kembalikan format: "Nilai (Persentase%)"
                                    return numericVal + " (" + percent.toFixed(1) + "%)";
                                }
                            },
                            total: {
                                show: true,
                                showAlways: false,
                                label: 'Total',
                                fontSize: '11px',
                                fontWeight: 600,
                                color: '#64748b',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false // Matikan label yang melayang di luar potongan
            },
            legend: {
                position: 'bottom',
                fontSize: '11px',
                fontWeight: 700
            }
        });

        // --- ROW 1: DONUT CHARTS ---
        Alpine.data('chartTacDonutTemuan', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, createDonutOptions(data.tac
                    .temuan_vs_laporan, ['Deteksi Dini', 'Laporan (Tiket)'], ['#f59e0b',
                        '#3b82f6'
                    ]));
                this.chart.render();
            },
            update(data) {
                if (data.tac) this.chart.updateSeries(data.tac.temuan_vs_laporan);
            }
        }));

        Alpine.data('chartTacDonutMandiri', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, createDonutOptions(data.tac
                    .mandiri_vs_eskalasi, ['Diselesaikan Mandiri', 'Eskalasi / Bantuan'], [
                        '#10b981', '#f43f5e'
                    ]));
                this.chart.render();
            },
            update(data) {
                if (data.tac) this.chart.updateSeries(data.tac.mandiri_vs_eskalasi);
            }
        }));

        // --- ROW 2: DONUT & GAUGE CHARTS ---
        Alpine.data('chartTacDonutCaseAct', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, createDonutOptions(data.tac
                    .case_vs_activity, ['Case (Gangguan)', 'General Activity'], ['#ef4444',
                        '#94a3b8'
                    ]));
                this.chart.render();
            },
            update(data) {
                if (data.tac) this.chart.updateSeries(data.tac.case_vs_activity);
            }
        }));

        Alpine.data('chartTacDonutNetGps', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, createDonutOptions(data.tac
                    .network_vs_gps, ['Network', 'GPS'], ['#8b5cf6', '#14b8a6']));
                this.chart.render();
            },
            update(data) {
                if (data.tac) this.chart.updateSeries(data.tac.network_vs_gps);
            }
        }));

        // GAUGE CHART (SLA Waktu Respon)
        Alpine.data('chartTacGaugeTime', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            getOptions(data) {
                let val = data.tac.avg_response_time || 0;
                let percent = Math.min((val / 30) * 100,
                    100); // 30 Menit dianggap 100% penuh lingkaran
                let color = val <= 10 ? '#10b981' : (val <= 15 ? '#f59e0b' :
                    '#ef4444'); // Hijau <10, Kuning <=15, Merah >15

                return {
                    series: [percent],
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
                                    formatter: function() {
                                        return val + " Menit";
                                    },
                                    fontSize: '24px',
                                    fontWeight: 900,
                                    color: color,
                                    offsetY: -10
                                }
                            }
                        }
                    },
                    labels: [val <= 10 ? 'Sangat Baik' : (val <= 15 ? 'Batas Wajar' :
                        'Kritis (Lewat Batas)')]
                };
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (!data.tac) return;
                this.chart.updateOptions(this.getOptions(data));
            }
        }));

        // --- ROW 3: STAFF KINERJA BARS ---
        const createBarOptions = (seriesName, seriesData, categories, color) => ({
            series: [{
                name: seriesName,
                data: seriesData
            }],
            chart: {
                type: 'bar',
                height: 260,
                fontFamily: 'inherit',
                toolbar: {
                    show: false
                }
            },
            colors: [color],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '40%',
                    distributed: true
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '10px'
                }
            },
            xaxis: {
                categories: categories,
                labels: {
                    style: {
                        fontSize: '9px',
                        fontWeight: 700
                    }
                }
            },
            legend: {
                show: false
            }
        });

        Alpine.data('chartTacBarTotal', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, createBarOptions('Total Case', data
                    .tac.staff_total_case, data.staff_labels, '#3b82f6'));
                this.chart.render();
            },
            update(data) {
                if (data.tac) {
                    this.chart.updateSeries([{
                        data: data.tac.staff_total_case
                    }]);
                    this.chart.updateOptions({
                        xaxis: {
                            categories: data.staff_labels
                        }
                    });
                }
            }
        }));

        Alpine.data('chartTacBarAvg', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            render(data) {
                if (!data.tac) return;
                // Kita beri warna dinamis untuk respon: jika staf > 15 merah, dsb.
                let options = createBarOptions('Rata-rata Respon', data.tac.staff_avg_response, data
                    .staff_labels, '#f59e0b');
                options.plotOptions.bar.colors = {
                    ranges: [{
                        from: 0,
                        to: 10,
                        color: '#10b981'
                    }, {
                        from: 10.1,
                        to: 15,
                        color: '#f59e0b'
                    }, {
                        from: 15.1,
                        to: 1000,
                        color: '#ef4444'
                    }]
                };
                options.dataLabels.formatter = function(val) {
                    return val + "m";
                }; // Tambah m (menit)
                this.chart = new ApexCharts(this.$refs.chart, options);
                this.chart.render();
            },
            update(data) {
                if (data.tac) {
                    this.chart.updateSeries([{
                        data: data.tac.staff_avg_response
                    }]);
                    this.chart.updateOptions({
                        xaxis: {
                            categories: data.staff_labels
                        }
                    });
                }
            }
        }));

        // GROUPED BAR
        Alpine.data('chartTacBarGrouped', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            getOptions(data) {
                return {
                    series: [{
                            name: 'Selesai Mandiri',
                            data: data.tac.staff_mandiri
                        },
                        {
                            name: 'Deteksi Dini',
                            data: data.tac.staff_temuan
                        }
                    ],
                    chart: {
                        type: 'bar',
                        height: 260,
                        fontFamily: 'inherit',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#10b981', '#f59e0b'],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '50%',
                            borderRadius: 2
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: data.staff_labels,
                        labels: {
                            style: {
                                fontSize: '9px',
                                fontWeight: 700
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        fontSize: '10px',
                        fontWeight: 700
                    }
                }
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (data.tac) {
                    this.chart.updateSeries([{
                        data: data.tac.staff_mandiri
                    }, {
                        data: data.tac.staff_temuan
                    }]);
                    this.chart.updateOptions({
                        xaxis: {
                            categories: data.staff_labels
                        }
                    });
                }
            }
        }));

        // --- ROW 4: TREND CHARTS ---
        Alpine.data('chartTacTrendCaseAct', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            getOptions(data) {
                return {
                    series: [{
                            name: 'Case (Gangguan)',
                            data: data.tac.trend_case || []
                        },
                        {
                            name: 'General Activity',
                            data: data.tac.trend_activity || []
                        }
                    ],
                    chart: {
                        type: 'line',
                        height: 300,
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
                    markers: {
                        size: 4
                    },
                    xaxis: {
                        categories: data.trend_labels,
                        labels: {
                            style: {
                                fontSize: '9px',
                                fontWeight: 700
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        fontSize: '11px',
                        fontWeight: 700
                    }
                }
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (data.tac) {
                    this.chart.updateSeries([{
                        data: data.tac.trend_case
                    }, {
                        data: data.tac.trend_activity
                    }]);
                    this.chart.updateOptions({
                        xaxis: {
                            categories: data.trend_labels
                        }
                    });
                }
            }
        }));

        Alpine.data('chartTacTrendGps', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', e => this.render(e.detail));
                window.addEventListener('update-charts', e => this.update(e.detail));
            },
            getOptions(data) {
                return {
                    series: [{
                        name: 'Volume Kendaraan GPS',
                        data: data.tac.trend_qty_gps || []
                    }],
                    chart: {
                        type: 'area',
                        height: 300,
                        fontFamily: 'inherit',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#14b8a6'],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: data.trend_labels,
                        labels: {
                            style: {
                                fontSize: '9px',
                                fontWeight: 700
                            }
                        }
                    },
                }
            },
            render(data) {
                if (!data.tac) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (data.tac) {
                    this.chart.updateSeries([{
                        data: data.tac.trend_qty_gps
                    }]);
                    this.chart.updateOptions({
                        xaxis: {
                            categories: data.trend_labels
                        }
                    });
                }
            }
        }));

        // --- CHARTS KEPATUHAN & EVALUASI (Pindahan) ---
        Alpine.data('chartCompliance', (type) => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', (e) => this.render(e.detail));
                window.addEventListener('update-charts', (e) => this.update(e.detail));
            },
            getOptions(data) {
                const compData = type === 'Dashboard' ? data.compliance.dashboard : data.compliance
                    .gps;
                return {
                    series: [compData.ontime, compData.late],
                    chart: {
                        type: 'donut',
                        height: 260,
                        fontFamily: 'inherit'
                    },
                    labels: ['Tepat Waktu', 'Terlambat'],
                    colors: ['#10b981', '#f43f5e'],

                    // === PERBAIKAN PLOT OPTIONS DI SINI ===
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true, // Ubah jadi true agar label "Tepat Waktu/Terlambat" muncul saat di-hover
                                        fontSize: '10px',
                                        color: '#94a3b8',
                                        offsetY: -5
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '20px',
                                        fontWeight: 800,
                                        offsetY: 5,
                                        // Rumus Kalkulasi Persentase Manual
                                        formatter: function(val, w) {
                                            let numericVal = parseFloat(val) || 0;
                                            let total = w.globals.seriesTotals.reduce((a,
                                                b) => {
                                                return a + b
                                            }, 0);
                                            let percent = total > 0 ? (numericVal / total) *
                                                100 : 0;
                                            return numericVal + " (" + percent.toFixed(1) +
                                                "%)";
                                        }
                                    },
                                    total: {
                                        show: true,
                                        showAlways: false,
                                        label: 'Total',
                                        fontSize: '11px',
                                        fontWeight: 600,
                                        color: '#64748b',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => {
                                                return a + b
                                            }, 0);
                                        }
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
                };
            },
            render(data) {
                if (!data.compliance) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (!data.compliance) return;
                const compData = type === 'Dashboard' ? data.compliance.dashboard : data.compliance
                    .gps;
                this.chart.updateSeries([compData.ontime, compData.late]);
            }
        }));

        Alpine.data('chartCustomerFeedback', () => ({
            chart: null,
            init() {
                window.addEventListener('render-charts', (e) => this.render(e.detail));
                window.addEventListener('update-charts', (e) => this.update(e.detail));
            },
            getOptions(data) {
                const feedback = data.evaluation.feedback;
                return {
                    series: [{
                            name: 'Total Survey',
                            type: 'column',
                            data: feedback.map(f => f.total_survey)
                        },
                        {
                            name: 'Rata-rata Rating',
                            type: 'line',
                            data: feedback.map(f => parseFloat(f.avg_rating).toFixed(1))
                        }
                    ],
                    chart: {
                        height: 280,
                        type: 'line',
                        fontFamily: 'inherit',
                        toolbar: {
                            show: false
                        }
                    },
                    stroke: {
                        width: [0, 4]
                    },
                    colors: ['#e2e8f0', '#8b5cf6'],
                    labels: feedback.map(f => f.date),
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            style: {
                                fontSize: '10px',
                                fontWeight: 700
                            }
                        }
                    },
                    yaxis: [{
                            title: {
                                text: 'Jumlah',
                                style: {
                                    fontSize: '9px',
                                    fontWeight: 700
                                }
                            }
                        },
                        {
                            opposite: true,
                            min: 0,
                            max: 5,
                            title: {
                                text: 'Rating',
                                style: {
                                    fontSize: '9px',
                                    fontWeight: 700
                                }
                            }
                        }
                    ],
                    legend: {
                        position: 'top',
                        fontSize: '11px',
                        fontWeight: 700
                    }
                };
            },
            render(data) {
                if (!data.evaluation) return;
                this.chart = new ApexCharts(this.$refs.chart, this.getOptions(data));
                this.chart.render();
            },
            update(data) {
                if (!data.evaluation) return;
                const feedback = data.evaluation.feedback;
                this.chart.updateSeries([{
                        data: feedback.map(f => f.total_survey)
                    },
                    {
                        data: feedback.map(f => parseFloat(f.avg_rating).toFixed(1))
                    }
                ]);
                this.chart.updateOptions({
                    labels: feedback.map(f => f.date)
                });
            }
        }));
    });
</script>
