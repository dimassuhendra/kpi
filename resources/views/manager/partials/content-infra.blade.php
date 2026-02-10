<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fadeIn">
    {{-- Card 1: Donut Chart Distribusi Kategori --}}
    <div class="lg:col-span-1 organic-card p-6 border border-white/5 flex flex-col">
        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">
            <i class="fas fa-chart-pie mr-2 text-primary"></i> Workload Distribution
        </h3>
        <div class="flex-grow flex items-center justify-center relative" style="min-height: 250px;">
            <canvas id="infraWorkloadChart"></canvas>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-2 border-t border-white/5 pt-4">
            @forelse ($infraWorkload as $kat => $total)
                <div class="flex items-center gap-2">
                    <span
                        class="w-2 h-2 rounded-full 
                        @if ($kat == 'Network') bg-blue-500 
                        @elseif($kat == 'CCTV') bg-emerald-500 
                        @elseif($kat == 'GPS') bg-amber-500 
                        @else bg-slate-500 @endif">
                    </span>
                    <span class="text-[10px] text-slate-400 uppercase font-medium">
                        {{ $kat }}: {{ $total }}
                    </span>
                </div>
            @empty
                <div class="col-span-2 text-center text-slate-500 text-[10px]">No category data available</div>
            @endforelse
        </div>
    </div>

    {{-- Card 2: Stacked Bar Productivity Staff --}}
    <div class="lg:col-span-2 organic-card p-6 border border-white/5 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                <i class="fas fa-users-cog mr-2 text-primary"></i> Staff Technical Focus
            </h3>
            <div class="flex bg-slate-800/50 p-1 rounded-lg border border-white/5">
                <button onclick="updateChartMode('total')" id="btn-mode-total"
                    class="px-3 py-1 text-[10px] font-bold rounded-md transition-all bg-primary text-white">
                    TOTAL
                </button>
                <button onclick="updateChartMode('staff')" id="btn-mode-staff"
                    class="px-3 py-1 text-[10px] font-bold rounded-md transition-all text-slate-400 hover:text-white">
                    PER STAFF
                </button>
            </div>
        </div>
        <div class="flex-grow" style="min-height: 300px;">
            <canvas id="staffInfraChart"></canvas>
        </div>
    </div>
</div>

<script>
    // 1. Definisikan variabel di level global agar bisa diakses fungsi updateChartMode
    let infraChartInstance = null;
    let infraDataRaw = @json($infraWorkload);
    let staffDataRaw = @json($staffInfraData);

    // 2. Fungsi untuk Switch Mode (Diletakkan di luar agar bisa dipanggil onclick)
    function updateChartMode(mode) {
        if (!infraChartInstance) return;

        const btnTotal = document.getElementById('btn-mode-total');
        const btnStaff = document.getElementById('btn-mode-staff');

        if (mode === 'total') {
            const total = {
                n: staffDataRaw.reduce((a, b) => a + (b.network || 0), 0),
                c: staffDataRaw.reduce((a, b) => a + (b.cctv || 0), 0),
                g: staffDataRaw.reduce((a, b) => a + (b.gps || 0), 0),
                l: staffDataRaw.reduce((a, b) => a + (b.lainnya || 0), 0)
            };

            infraChartInstance.data.labels = ['All Staff Sum'];
            infraChartInstance.data.datasets[0].data = [total.n];
            infraChartInstance.data.datasets[1].data = [total.c];
            infraChartInstance.data.datasets[2].data = [total.g];
            infraChartInstance.data.datasets[3].data = [total.l];

            btnTotal.classList.add('bg-primary', 'text-white');
            btnStaff.classList.remove('bg-primary', 'text-white');
        } else {
            infraChartInstance.data.labels = staffDataRaw.map(s => s.nama);
            infraChartInstance.data.datasets[0].data = staffDataRaw.map(s => s.network);
            infraChartInstance.data.datasets[1].data = staffDataRaw.map(s => s.cctv);
            infraChartInstance.data.datasets[2].data = staffDataRaw.map(s => s.gps);
            infraChartInstance.data.datasets[3].data = staffDataRaw.map(s => s.lainnya);

            btnStaff.classList.add('bg-primary', 'text-white');
            btnTotal.classList.remove('bg-primary', 'text-white');
        }
        infraChartInstance.update();
    }

    // 3. Inisialisasi Chart saat DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error("Chart.js tidak ditemukan!");
            return;
        }

        // --- DONUT CHART ---
        const ctxDonut = document.getElementById('infraWorkloadChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(infraDataRaw),
                    datasets: [{
                        data: Object.values(infraDataRaw),
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444',
                            '#64748b'],
                        borderWidth: 0
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
        }

        // --- BAR CHART INITIAL ---
        const ctxBar = document.getElementById('staffInfraChart');
        if (ctxBar && staffDataRaw.length > 0) {
            const totalData = {
                network: staffDataRaw.reduce((a, b) => a + (b.network || 0), 0),
                cctv: staffDataRaw.reduce((a, b) => a + (b.cctv || 0), 0),
                gps: staffDataRaw.reduce((a, b) => a + (b.gps || 0), 0),
                lainnya: staffDataRaw.reduce((a, b) => a + (b.lainnya || 0), 0),
            };

            infraChartInstance = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['All Staff Sum'],
                    datasets: [{
                            label: 'Network',
                            data: [totalData.network],
                            backgroundColor: '#3b82f6',
                            borderRadius: 4
                        },
                        {
                            label: 'CCTV',
                            data: [totalData.cctv],
                            backgroundColor: '#10b981',
                            borderRadius: 4
                        },
                        {
                            label: 'GPS',
                            data: [totalData.gps],
                            backgroundColor: '#f59e0b',
                            borderRadius: 4
                        },
                        {
                            label: 'Lainnya',
                            data: [totalData.lainnya],
                            backgroundColor: '#64748b',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: false,
                            ticks: {
                                color: '#94a3b8'
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#94a3b8',
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                boxWidth: 12
                            }
                        }
                    }
                }
            });
        }
    });
</script>
