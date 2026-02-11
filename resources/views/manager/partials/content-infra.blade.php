<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Card 1: Donut Chart --}}
    <div class="lg:col-span-1 p-6 border border-white/5 rounded-xl flex flex-col" style="background: var(--dark-card)">
        <h3 class="text-sm font-bold uppercase tracking-widest mb-6" style="color: var(--text-muted)">
            <i class="fas fa-chart-pie mr-2" style="color: var(--primary)"></i> Workload Distribution
        </h3>
        <div class="flex-grow flex items-center justify-center relative" style="min-height: 250px;">
            <canvas id="infraWorkloadChart"></canvas>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-2 border-t border-white/5 pt-4">
            @forelse ($infraWorkload as $kat => $total)
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full"
                        style="background-color: @if ($kat == 'Network') var(--primary) @elseif($kat == 'CCTV') var(--accent) @else var(--text-muted) @endif">
                    </span>
                    <span class="text-[10px] uppercase font-medium" style="color: var(--text-muted)">
                        {{ $kat }}: {{ $total }}
                    </span>
                </div>
            @empty
                <div class="col-span-2 text-center text-[10px]" style="color: var(--text-muted)">No category data</div>
            @endforelse
        </div>
    </div>

    {{-- Card 2: Bar Chart Productivity Staff --}}
    <div class="lg:col-span-2 p-6 border border-white/5 rounded-xl flex flex-col" style="background: var(--dark-card)">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold uppercase tracking-widest" style="color: var(--text-muted)">
                <i class="fas fa-users-cog mr-2" style="color: var(--primary)"></i> Staff Technical Focus
            </h3>
            <div class="flex p-1 rounded-lg" style="background: rgba(0,0,0,0.2)">
                <button onclick="updateChartMode('total')" id="btn-mode-total"
                    class="px-3 py-1 text-[10px] font-bold rounded-md transition-all btn-primary">TOTAL</button>
                <button onclick="updateChartMode('staff')" id="btn-mode-staff"
                    class="px-3 py-1 text-[10px] font-bold rounded-md transition-all"
                    style="color: var(--text-muted)">PER STAFF</button>
            </div>
        </div>
        <div class="flex-grow" style="min-height: 300px;">
            <canvas id="staffInfraChart"></canvas>
        </div>
    </div>
</div>

<script>
    let infraChartInstance = null;

    // Helper untuk mengambil warna asli dari CSS Variable
    const getThemeColor = (varName) => getComputedStyle(document.documentElement).getPropertyValue(varName).trim();

    const infraDataRaw = @json($infraWorkload);
    const staffDataRaw = @json($staffInfraData);
    const categories = @json($availableCategories);

    // Color map sekarang merujuk ke variabel CSS
    const getColorMap = () => ({
        'Network': getThemeColor('--primary'),
        'CCTV': getThemeColor('--accent'),
        'GPS': '#f59e0b', // Tetap jika tidak ada variabelnya
        'Lainnya': '#ef4444'
    });

    function updateChartMode(mode) {
        if (!infraChartInstance) return;
        const btnTotal = document.getElementById('btn-mode-total');
        const btnStaff = document.getElementById('btn-mode-staff');
        const colors = getColorMap();

        if (mode === 'total') {
            infraChartInstance.data.labels = ['Total Kolektif'];
            infraChartInstance.data.datasets.forEach(ds => {
                ds.data = [staffDataRaw.reduce((acc, curr) => acc + (Number(curr[ds.label]) || 0), 0)];
            });
            btnTotal.style.backgroundColor = 'var(--primary)';
            btnTotal.style.color = 'white';
            btnStaff.style.backgroundColor = 'transparent';
        } else {
            infraChartInstance.data.labels = staffDataRaw.map(s => s.nama);
            infraChartInstance.data.datasets.forEach(ds => {
                ds.data = staffDataRaw.map(s => Number(s[ds.label]) || 0);
            });
            btnStaff.style.backgroundColor = 'var(--primary)';
            btnStaff.style.color = 'white';
            btnTotal.style.backgroundColor = 'transparent';
        }
        infraChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const colors = getColorMap();
        const textMuted = getThemeColor('--text-muted');

        // Donut Chart
        const ctxDonut = document.getElementById('infraWorkloadChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(infraDataRaw),
                    datasets: [{
                        data: Object.values(infraDataRaw),
                        backgroundColor: Object.keys(infraDataRaw).map(k => colors[k] ||
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
                        backgroundColor: colors[cat] || '#64748b',
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
                                color: textMuted
                            }
                        },
                        x: {
                            ticks: {
                                color: textMuted
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textMuted,
                                boxWidth: 12
                            }
                        }
                    }
                }
            });
        }
    });

    // Listener agar chart update saat manager ganti tema klik tombol
    window.addEventListener('click', function(e) {
        if (e.target.onclick && e.target.onclick.toString().includes('setTheme')) {
            setTimeout(() => {
                location.reload(); // Cara paling aman agar Chart.js merender ulang warna variabel baru
            }, 100);
        }
    });
</script>
