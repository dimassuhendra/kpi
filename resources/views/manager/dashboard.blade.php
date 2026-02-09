@extends('layouts.manager')

@section('content')
<div class="space-y-10">

    {{-- 1. HEADER & FILTER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-header font-bold text-white tracking-tight uppercase">
                TAC <span class="text-primary italic">Mission Control</span>
            </h1>
            <p class="text-slate-400 text-sm italic">Monitoring performa divisi Technical Assistance Center.</p>
        </div>

        <div class="flex items-center gap-2 bg-slate-800/40 p-1.5 rounded-3xl border border-white/5">
            <a href="{{ route('manager.dashboard', ['divisi_id' => 'tac']) }}"
                class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all {{ $selectedDivisi == 'all' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200' }}">
                TAC Division
            </a>
            <div class="h-4 w-[1px] bg-white/10 mx-2"></div>
            <span class="text-[10px] font-bold uppercase text-primary px-4">Infrastructure Division</span>
        </div>
    </div>

    {{-- 2. STATS CARDS (Top Stats) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="organic-card p-6 border-b-2 border-rose-500 group">
            <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Reports Pending</p>
            <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">{{ $stats['pending'] }}</h2>
        </div>
        <div class="organic-card p-6 border-b-2 border-primary group">
            <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Efficiency Rate</p>
            <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">{{ number_format($stats['avg_response_time'], 1) }} <small>Minutes/Case</small></h2>
        </div>
        <div class="organic-card p-6 border-b-2 border-emerald-500 group">
            <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Monthly Resolved</p>
            <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">{{ $stats['resolved_month'] }}</h2>
        </div>
        <div class="organic-card p-6 border-b-2 border-amber-500 group">
            <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Staff Active Today</p>
            <h2 class="text-4xl font-header font-bold text-white mt-1 group-hover:scale-105 transition-transform">{{ $stats['active_today'] }}</h2>
        </div>
    </div>

    @if($selectedDivisi == 'TAC' || $selectedDivisi == 'all')
        @include('manager.partials.tac-chart')
    @endif

    {{-- 4. COLLECTIVE HEATMAP (1 Year GitHub Style) --}}
    <div class="space-y-4">
        <div class="flex justify-between items-end">
            <h3 class="text-xs font-bold uppercase text-slate-500 tracking-widest flex items-center gap-2">
                <i class="fas fa-calendar-alt text-primary"></i> Team Activity Log {{ date('Y') }}
            </h3>
            <div class="flex items-center gap-2 text-[9px] text-slate-600 uppercase font-bold">
                <span>Less</span>
                <div class="w-3 h-3 bg-white/5 rounded-[2px]"></div>
                <div class="w-3 h-3 bg-primary/30 rounded-[2px]"></div>
                <div class="w-3 h-3 bg-primary/60 rounded-[2px]"></div>
                <div class="w-3 h-3 bg-primary rounded-[2px]"></div>
                <span>More</span>
            </div>
        </div>

        <div class="organic-card p-8 overflow-x-auto shadow-inner">
            <div class="grid grid-flow-col grid-rows-7 gap-1.5 w-max mx-auto">
                @php
                $startOfYear = \Carbon\Carbon::now()->startOfYear();
                $endOfYear = \Carbon\Carbon::now()->endOfYear();
                $totalDays = $startOfYear->diffInDays($endOfYear);
                $emptyCells = $startOfYear->dayOfWeek;
                @endphp

                @for($e = 0; $e < $emptyCells; $e++)
                    <div class="w-3.5 h-3.5 opacity-0">
            </div>
            @endfor

            @for($i = 0; $i <= $totalDays; $i++)
                @php
                $currentDate=$startOfYear->copy()->addDays($i);
                $dateStr = $currentDate->format('Y-m-d');
                $count = $heatmapData[$dateStr] ?? 0;
                $colorClass = 'bg-white/5';
                if($count > 0) $colorClass = 'bg-primary/20';
                if($count > 3) $colorClass = 'bg-primary/50';
                if($count > 7) $colorClass = 'bg-primary';
                @endphp
                <div class="w-3.5 h-3.5 rounded-[3px] {{ $colorClass }} transition-all hover:scale-150 hover:z-10 cursor-help group relative">
                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100 whitespace-nowrap z-50 pointer-events-none shadow-xl border border-white/10">
                        {{ $currentDate->format('d M Y') }}: <span class="text-primary font-bold">{{ $count }} Laporan</span>
                    </div>
                </div>
                @endfor
        </div>
    </div>
</div>

{{-- 5. BOTTOM SECTION: TOP PERFORMANCE ONLY --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">
    <div class="space-y-4">
        <h3 class="text-xs font-bold uppercase text-slate-500 tracking-widest px-2">Leaderboard</h3>
        <div class="organic-card p-6 space-y-5">
            @foreach($leaderboard as $index => $staff)
            <div class="flex items-center gap-4">
                <span class="text-lg font-header font-bold {{ $index == 0 ? 'text-amber-500' : 'text-slate-600' }}">0{{ $index + 1 }}</span>
                <div class="flex-grow">
                    <p class="text-sm font-bold text-slate-200 leading-none">{{ $staff->nama_lengkap }}</p>
                    <p class="text-[9px] text-slate-500 uppercase mt-1">Avg: {{ number_format($staff->reports_avg_total_nilai_harian, 1) }}</p>
                </div>
                <div class="w-2 h-2 rounded-full {{ $index == 0 ? 'bg-amber-500' : 'bg-primary/30' }}"></div>
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>
@endsection