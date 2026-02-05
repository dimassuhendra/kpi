@extends('layouts.manager')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-header text-3xl text-white italic">Monthly <span class="text-primary">Reporting</span></h2>
            <p class="text-slate-500 text-sm">Rekapitulasi performa bulanan untuk HR/Senior Manager.</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('manager.reports.export.excel', ['month' => $month, 'year' => $year]) }}" class="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 px-5 py-3 rounded-2xl font-bold text-xs hover:bg-emerald-500 hover:text-white transition shadow-lg">
                <i class="fas fa-file-excel mr-2"></i> Export Excel
            </a>
            <a href="{{ route('manager.reports.export.pdf', ['month' => $month, 'year' => $year]) }}" target="_blank" class="bg-red-500/10 text-red-500 border border-red-500/20 px-5 py-3 rounded-2xl font-bold text-xs hover:bg-red-500 hover:text-white transition shadow-lg">
                <i class="fas fa-file-pdf mr-2"></i> Generate PDF/Print
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="organic-card p-6">
        <form action="{{ route('manager.reports.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-1 block ml-2">Bulan</label>
                <select name="month" class="bg-secondary border border-white/10 text-white rounded-xl px-4 py-2 text-sm outline-none focus:border-primary">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                </select>
            </div>
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-1 block ml-2">Tahun</label>
                <select name="year" class="bg-secondary border border-white/10 text-white rounded-xl px-4 py-2 text-sm outline-none focus:border-primary">
                    <option value="2024" {{ $year == '2024' ? 'selected' : '' }}>2024</option>
                    <option value="2025" {{ $year == '2025' ? 'selected' : '' }}>2025</option>
                </select>
            </div>
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-xl font-bold text-sm">Filter</button>
        </form>
    </div>

    {{-- Preview Table --}}
    <div class="organic-card overflow-hidden">
        <table class="w-full text-left">
            <thead class="text-slate-500 text-[10px] uppercase tracking-widest bg-darkCard">
                <tr>
                    <th class="p-5 font-medium">Nama Staff</th>
                    <th class="p-5 font-medium text-center">Total Laporan</th>
                    <th class="p-5 font-medium text-center">On-Time</th>
                    <th class="p-5 font-medium text-center">Late</th>
                    <th class="p-5 font-medium text-right">Rata-rata Skor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-slate-300">
                @foreach($reportData as $row)
                <tr class="hover:bg-white/[0.02]">
                    <td class="p-5">
                        <p class="text-white font-bold">{{ $row['name'] }}</p>
                    </td>
                    <td class="p-5 text-center">{{ $row['total_reports'] }}</td>
                    <td class="p-5 text-center text-emerald-500 font-bold">{{ $row['on_time_count'] }}</td>
                    <td class="p-5 text-center text-red-500 font-bold">{{ $row['late_count'] }}</td>
                    <td class="p-5 text-right">
                        <span class="text-lg font-header text-primary">{{ number_format($row['avg_score'], 1) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection