<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Card 1: Donut Chart Distribusi Kategori (Infra) --}}
    <div class="lg:col-span-1 p-6 bg-slate-900 border border-white/5 rounded-xl flex flex-col">
        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">
            <i class="fas fa-chart-pie mr-2 text-blue-500"></i> Workload Distribution
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
                        @elseif($kat == 'Lainnya') bg-red-500 
                        @else bg-slate-500 @endif">
                    </span>
                    <span class="text-[10px] text-slate-400 uppercase font-medium">
                        {{ $kat }}: {{ $total }}
                    </span>
                </div>
            @empty
                <div class="col-span-2 text-center text-slate-500 text-[10px]">No category data</div>
            @endforelse
        </div>
    </div>

    {{-- Card 2: Bar Chart Productivity Staff --}}
    <div class="lg:col-span-2 p-6 bg-slate-900 border border-white/5 rounded-xl flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                <i class="fas fa-users-cog mr-2 text-blue-500"></i> Staff Technical Focus
            </h3>
            <div class="flex bg-slate-800 p-1 rounded-lg">
                <button onclick="updateChartMode('total')" id="btn-mode-total"
                    class="px-3 py-1 text-[10px] font-bold rounded-md transition-all bg-blue-600 text-white">TOTAL</button>
                <button onclick="updateChartMode('staff')" id="btn-mode-staff"
                    class="px-3 py-1 text-[10px] font-bold rounded-md transition-all text-slate-400">PER STAFF</button>
            </div>
        </div>
        <div class="flex-grow" style="min-height: 300px;">
            <canvas id="staffInfraChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let infraChartInstance = null;

    // Ambil data langsung dari variabel yang dikirim controller
    const infraDataRaw = @json($infraWorkload);
    const staffDataRaw = @json($staffInfraData);
    const categories = @json($availableCategories);

    const colorMap = {
        'Network': '#3b82f6',
        'CCTV': '#10b981',
        'GPS': '#f59e0b',
        'Lainnya': '#ef4444'
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
            btnTotal.classList.add('bg-blue-600', 'text-white');
            btnStaff.classList.remove('bg-blue-600', 'text-white');
        } else {
            infraChartInstance.data.labels = staffDataRaw.map(s => s.nama);
            infraChartInstance.data.datasets.forEach(ds => {
                ds.data = staffDataRaw.map(s => Number(s[ds.label]) || 0);
            });
            btnStaff.classList.add('bg-blue-600', 'text-white');
            btnTotal.classList.remove('bg-blue-600', 'text-white');
        }
        infraChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Donut Chart
        const ctxDonut = document.getElementById('infraWorkloadChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(infraDataRaw),
                    datasets: [{
                        data: Object.values(infraDataRaw),
                        backgroundColor: Object.keys(infraDataRaw).map(k => colorMap[k] ||
                            '#64748b'),
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
                        backgroundColor: colorMap[cat] || '#64748b',
                        borderRadius: 4
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#94a3b8'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#94a3b8'
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
