<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Card 1: Donut Chart --}}
    <div class="lg:col-span-1 info-card p-6 flex flex-col">
        <h3 class="text-[10px] font-black uppercase tracking-widest mb-8 text-slate-500">
            <i class="fas fa-chart-pie mr-2 text-emerald-600"></i> Workload Distribution
        </h3>
        <div class="flex-grow flex items-center justify-center relative" style="min-height: 250px;">
            <canvas id="infraWorkloadChart"></canvas>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-3 border-t border-slate-50 pt-5">
            @forelse ($infraWorkload as $kat => $total)
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full"
                        style="background-color: @if ($kat == 'Network') #059669 @elseif($kat == 'CCTV') #0ea5e9 @else #94a3b8 @endif">
                    </span>
                    <span class="text-[10px] uppercase font-black text-slate-400">
                        {{ $kat }}: <span class="text-slate-700">{{ $total }}</span>
                    </span>
                </div>
            @empty
                <div class="col-span-2 text-center text-[10px] font-bold text-slate-300">No category data</div>
            @endforelse
        </div>
    </div>

    {{-- Card 2: Bar Chart Productivity Staff --}}
    <div class="lg:col-span-2 info-card p-6 flex flex-col">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                <i class="fas fa-users-cog mr-2 text-emerald-600"></i> Staff Technical Focus
            </h3>
            <div class="flex p-1 bg-slate-100 rounded-xl">
                <button onclick="updateChartMode('total')" id="btn-mode-total"
                    class="px-4 py-1.5 text-[9px] font-black rounded-lg transition-all bg-white text-emerald-700 shadow-sm border border-emerald-100/50">
                    TOTAL
                </button>
                <button onclick="updateChartMode('staff')" id="btn-mode-staff"
                    class="px-4 py-1.5 text-[9px] font-black rounded-lg transition-all text-slate-400 hover:text-slate-600">
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
    let infraChartInstance = null;

    const infraDataRaw = @json($infraWorkload);
    const staffDataRaw = @json($staffInfraData);
    const categories = @json($availableCategories);

    // Light Palette senada Master Manager
    const colorPalette = {
        'Network': '#059669', // Emerald 600
        'CCTV': '#0ea5e9', // Sky 500
        'GPS': '#f59e0b', // Amber 500
        'Lainnya': '#94a3b8' // Slate 400
    };

    function updateChartMode(mode) {
        if (!infraChartInstance) return;
        const btnTotal = document.getElementById('btn-mode-total');
        const btnStaff = document.getElementById('btn-mode-staff');

        if (mode === 'total') {
            infraChartInstance.data.labels = ['Total Kolektif'];
            infraChartInstance.data.datasets.forEach(ds => {
                ds.data = [staffDataRaw.reduce((acc, curr) => acc + (Number(curr[ds.label]) || 0), 0)];
            });

            // UI Toggle
            btnTotal.className =
                "px-4 py-1.5 text-[9px] font-black rounded-lg transition-all bg-white text-emerald-700 shadow-sm border border-emerald-100/50";
            btnStaff.className =
                "px-4 py-1.5 text-[9px] font-black rounded-lg transition-all text-slate-400 hover:text-slate-600";
        } else {
            infraChartInstance.data.labels = staffDataRaw.map(s => s.nama);
            infraChartInstance.data.datasets.forEach(ds => {
                ds.data = staffDataRaw.map(s => Number(s[ds.label]) || 0);
            });

            // UI Toggle
            btnStaff.className =
                "px-4 py-1.5 text-[9px] font-black rounded-lg transition-all bg-white text-emerald-700 shadow-sm border border-emerald-100/50";
            btnTotal.className =
                "px-4 py-1.5 text-[9px] font-black rounded-lg transition-all text-slate-400 hover:text-slate-600";
        }
        infraChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Global Chart Defaults for Light Mode
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.font.weight = '700';
        Chart.defaults.color = '#94a3b8';

        // Donut Chart
        const ctxDonut = document.getElementById('infraWorkloadChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(infraDataRaw),
                    datasets: [{
                        data: Object.values(infraDataRaw),
                        backgroundColor: Object.keys(infraDataRaw).map(k => colorPalette[k] ||
                            '#cbd5e1'),
                        borderWidth: 6,
                        borderColor: '#ffffff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Bar Chart
        const ctxBar = document.getElementById('staffInfraChart');
        if (ctxBar) {
            infraChartInstance = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Total Kolektif'],
                    datasets: categories.map(cat => ({
                        label: cat,
                        data: [staffDataRaw.reduce((acc, curr) => acc + (Number(curr[
                            cat]) || 0), 0)],
                        backgroundColor: colorPalette[cat] || '#cbd5e1',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f8fafc'
                            },
                            ticks: {
                                padding: 10
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 25,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
