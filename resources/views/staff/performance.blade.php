@extends('layouts.staff')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h2 class="font-header text-4xl text-primary">Performa Saya</h2>
        <p class="font-body text-secondary opacity-70">Analisa pencapaian KPI Anda dalam 30 hari terakhir.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-8 rounded-3xl shadow-sm border-t-4 border-primary">
            <p class="text-secondary text-sm font-bold uppercase mb-2">Rata-rata Skor</p>
            <div class="text-5xl font-header text-primary">{{ number_format($stats->avg_score ?? 0, 2) }}</div>
            <p class="text-xs text-gray-400 mt-2">Target Perusahaan: 85.00</p>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border-t-4 border-secondary">
            <p class="text-secondary text-sm font-bold uppercase mb-2">Avg. Response Time</p>
            <div class="text-5xl font-header text-secondary">{{ number_format($avgResponse, 1) }} <span class="text-lg">Min</span></div>
            <p class="text-xs text-gray-400 mt-2">Semakin kecil semakin baik</p>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border-t-4 border-accent">
            <p class="text-secondary text-sm font-bold uppercase mb-2">Laporan Disetujui</p>
            <div class="text-5xl font-header text-accent">{{ $stats->total_reports ?? 0 }}</div>
            <p class="text-xs text-gray-400 mt-2">Total pengajuan yang di-ACC</p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-3xl shadow-sm border border-white">
        <h3 class="font-header text-2xl text-primary mb-6">Tren Skor KPI (15 Hari Terakhir)</h3>
        <div class="h-80">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const chartData = @json($chartData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(data => {
                const date = new Date(data.assessment_date);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                });
            }),
            datasets: [{
                label: 'Skor Akhir',
                data: chartData.map(data => data.total_final_score),
                borderColor: '#09637E',
                backgroundColor: 'rgba(9, 99, 126, 0.1)',
                borderWidth: 4,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#088395',
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        borderDash: [5, 5]
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection