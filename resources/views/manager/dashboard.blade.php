@extends('layouts.manager')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8">
    {{-- Bagian Header & Greeting --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="font-header text-3xl md:text-4xl text-white italic">Manager <span class="text-primary">Dashboard</span></h2>
            <p class="font-body text-slate-400 mt-1">Pantau kinerja divisi {{ Auth::user()->division->name }} secara real-time.</p>
        </div>
        <div class="organic-card px-6 py-3 bg-primary/5 flex items-center gap-3">
            <i class="fas fa-calendar-day text-primary"></i>
            <span class="text-white font-header">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    {{-- Row 1: Statistik & Counter --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="organic-card p-6 border-l-4 border-primary">
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-tighter">Rata-rata Skor Tim</p>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-4xl font-header text-white">{{ number_format($avgScore, 1) }}</span>
                <span class="text-xs text-slate-500">Pts</span>
            </div>
            <p class="text-[10px] text-emerald-500 mt-2 font-bold"><i class="fas fa-arrow-up"></i> Bulan Ini</p>
        </div>

        <div class="organic-card p-6 border-l-4 border-amber-500 group cursor-pointer hover:bg-amber-500/5 transition-colors">
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-tighter">Pending Approval</p>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-4xl font-header text-white">{{ $pendingCount }}</span>
                <span class="text-xs text-slate-500">Laporan</span>
            </div>
            <p class="text-[10px] text-amber-500 mt-2 font-bold group-hover:underline">Review Sekarang <i class="fas fa-chevron-right ml-1"></i></p>
        </div>

        <div class="organic-card p-6 border-l-4 border-indigo-400">
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-tighter">Top Performer</p>
            <p class="text-white font-header mt-2 truncate">{{ $topPerformer->name ?? 'Belum ada data' }}</p>
            <div class="flex items-center gap-1 text-[10px] text-indigo-400 mt-1">
                <i class="fas fa-award"></i> Leaderboard #1
            </div>
        </div>

        <div class="organic-card p-6 border-l-4 border-red-500">
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-tighter">Need Coaching</p>
            <p class="text-white font-header mt-2 truncate">{{ $bottomPerformer->name ?? 'Belum ada data' }}</p>
            <div class="flex items-center gap-1 text-[10px] text-red-500 mt-1">
                <i class="fas fa-exclamation-triangle"></i> Perlu Pembinaan
            </div>
        </div>
    </div>

    {{-- Row 2: Grafik Trend Tiket & List Performer --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 organic-card p-8">
            <div class="flex justify-between items-center mb-8">
                <h3 class="font-header text-xl text-white"><i class="fas fa-chart-area mr-2 text-primary"></i>Volume Tiket Divisi</h3>
                <select class="bg-secondary border border-white/10 text-xs rounded-lg px-2 py-1 text-slate-400 outline-none">
                    <option>7 Hari Terakhir</option>
                </select>
            </div>
            <div class="h-[300px] w-full">
                <canvas id="managerTrendChart"></canvas>
            </div>
        </div>

        <div class="space-y-6">
            <div class="organic-card p-6 bg-gradient-to-b from-darkCard to-secondary">
                <h3 class="font-header text-lg text-white mb-6 italic border-b border-white/5 pb-2">Peringkat Staff</h3>
                <div class="space-y-5">
                    {{-- Top Item --}}
                    @if($topPerformer)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-400 border border-indigo-500/20 group-hover:rotate-6 transition-transform">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div>
                                <p class="text-sm text-white font-bold">{{ $topPerformer->name }}</p>
                                <p class="text-[10px] text-slate-500">Skor: {{ number_format($topPerformer->submissions_avg_total_final_score, 1) }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] bg-indigo-500/10 text-indigo-400 px-2 py-1 rounded-md font-bold uppercase">Top</span>
                    </div>
                    @endif

                    {{-- Bottom Item --}}
                    @if($bottomPerformer && $bottomPerformer->id != ($topPerformer->id ?? 0))
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center text-red-400 border border-red-500/20 group-hover:-rotate-6 transition-transform">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div>
                                <p class="text-sm text-white font-bold">{{ $bottomPerformer->name }}</p>
                                <p class="text-[10px] text-slate-500">Skor: {{ number_format($bottomPerformer->submissions_avg_total_final_score, 1) }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] bg-red-500/10 text-red-400 px-2 py-1 rounded-md font-bold uppercase">Low</span>
                    </div>
                    @endif
                </div>

                <a href="#" class="block text-center mt-8 py-3 rounded-2xl bg-white/5 border border-white/5 text-slate-400 text-[10px] font-bold uppercase tracking-widest hover:bg-primary hover:text-white transition-all">
                    Lihat Seluruh Anggota
                </a>
            </div>

            <div class="organic-card p-6 bg-primary/10 border-primary/20 relative overflow-hidden">
                <i class="fas fa-lightbulb absolute -right-4 -bottom-4 text-7xl text-primary/10"></i>
                <h4 class="text-primary font-bold text-sm mb-2">Tips Manager</h4>
                <p class="text-slate-400 text-xs leading-relaxed">Jangan lupa untuk memberikan feedback pada laporan yang direvisi agar staff memahami poin perbaikan.</p>
            </div>
        </div>
    </div>
</div>

{{-- Script Grafik --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('managerTrendChart').getContext('2d');

        // Gradient untuk grafik
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {
                    !!json_encode($trendData - > pluck('date')) !!
                },
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: {
                        !!json_encode($trendData - > pluck('total')) !!
                    },
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: gradient
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection