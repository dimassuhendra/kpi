@extends('layouts.staff')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-sm border-b-4 border-secondary">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-header text-primary">Status Hari Ini</h3>
            <i class="fas fa-calendar-day text-accent text-2xl"></i>
        </div>
        @if($todaySubmission)
            <span class="px-4 py-2 bg-green-100 text-green-600 rounded-full text-sm font-bold">
                <i class="fas fa-check-circle mr-1"></i> Terisi ({{ ucfirst($todaySubmission->status) }})
            </span>
        @else
            <span class="px-4 py-2 bg-red-100 text-red-600 rounded-full text-sm font-bold">
                <i class="fas fa-exclamation-circle mr-1"></i> Belum Mengisi
            </span>
        @endif
        <p class="mt-4 text-sm text-gray-500 italic">{{ now()->translatedFormat('d F Y') }}</p>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border-b-4 border-primary">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-header text-primary">Skor Rata-rata</h3>
            <i class="fas fa-star text-yellow-400 text-2xl"></i>
        </div>
        <div class="text-4xl font-header text-primary">{{ number_format($averageScore, 2) }}</div>
        <p class="mt-2 text-xs text-secondary italic">Berdasarkan data yang sudah di-ACC</p>
    </div>

    <div class="bg-secondary p-6 rounded-3xl shadow-lg text-white flex flex-col justify-center">
        <p class="font-body mb-2 text-sm">Sudah selesai bekerja?</p>
        <a href="#" class="bg-white text-secondary text-center py-2 rounded-xl font-header hover:bg-background transition">
            Input KPI Sekarang <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm p-8">
    <h3 class="font-header text-2xl text-primary mb-6">Tren Performa 7 Hari Terakhir</h3>
    
    @if($chartData->isEmpty())
        <div class="h-64 bg-background rounded-2xl flex items-center justify-center border-2 border-dashed border-accent">
            <div class="text-center text-secondary opacity-60">
                <i class="fas fa-chart-area text-4xl mb-2"></i>
                <p>Belum ada data performa untuk ditampilkan.</p>
            </div>
        </div>
    @else
        <canvas id="performanceChart" class="h-64"></canvas>
    @endif
</div>
@endsection