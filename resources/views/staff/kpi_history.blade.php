@extends('layouts.staff')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-0">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="font-header text-2xl md:text-4xl text-primary">Riwayat Laporan</h2>
            <p class="font-body text-slate-400 text-sm md:text-base opacity-80">Pantau status pengerjaan dan skor KPI Anda.</p>
        </div>
        <a href="{{ route('staff.kpi.create') }}" class="w-full md:w-auto bg-primary text-white px-6 py-3 rounded-2xl font-header text-center hover:bg-emerald-600 transition shadow-lg shadow-primary/20 flex items-center justify-center">
            <i class="fas fa-plus mr-2 text-sm"></i> Input Baru
        </a>
    </div>

    {{-- Main Content Card --}}
    <div class="organic-card bg-darkCard overflow-hidden border border-white/5">

        {{-- Desktop Table (Tampil di Tablet ke Atas) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 text-slate-500 text-[10px] uppercase tracking-[0.2em] font-bold">
                        <th class="py-5 px-8">Tanggal Laporan</th>
                        <th class="py-5 px-8">Jumlah Case</th>
                        <th class="py-5 px-8 text-center">Status</th>
                        <th class="py-5 px-8 text-center">Skor Akhir</th>
                        <th class="py-5 px-8 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($submissions as $data)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-5 px-8">
                            <div class="font-bold text-slate-200">
                                {{ \Carbon\Carbon::parse($data->assessment_date)->translatedFormat('d F Y') }}
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-lg text-xs font-bold border border-primary/20">
                                {{ $data->caseLogs->count() }} Case
                            </span>
                        </td>
                        <td class="py-5 px-8 text-center">
                            @if($data->status == 'pending')
                            <span class="px-3 py-1 bg-amber-500/10 text-amber-500 rounded-full text-[10px] font-bold uppercase border border-amber-500/20">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                            @elseif($data->status == 'approved')
                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-[10px] font-bold uppercase border border-emerald-500/20">
                                <i class="fas fa-check-circle mr-1"></i> Approved
                            </span>
                            @else
                            <span class="px-3 py-1 bg-red-500/10 text-red-500 rounded-full text-[10px] font-bold uppercase border border-red-500/20">
                                <i class="fas fa-times-circle mr-1"></i> Rejected
                            </span>
                            @endif
                        </td>
                        <td class="py-5 px-8 text-center">
                            <div class="text-xl font-header {{ $data->total_final_score >= 80 ? 'text-emerald-400' : 'text-primary' }}">
                                {{ number_format($data->total_final_score, 2) }}
                            </div>
                        </td>
                        <td class="py-5 px-8 text-right">
                            <button class="text-slate-400 hover:text-primary transition p-2.5 bg-white/5 rounded-xl border border-white/5">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    {{-- Handled by the common empty state below --}}
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile List (Tampil hanya di HP) --}}
        <div class="md:hidden divide-y divide-white/5">
            @forelse($submissions as $data)
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase text-slate-500 font-bold tracking-widest mb-1">Tanggal Laporan</p>
                        <p class="text-slate-200 font-bold">{{ \Carbon\Carbon::parse($data->assessment_date)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase text-slate-500 font-bold tracking-widest mb-1">Skor Akhir</p>
                        <p class="text-xl font-header {{ $data->total_final_score >= 80 ? 'text-emerald-400' : 'text-primary' }}">
                            {{ number_format($data->total_final_score, 2) }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-between items-center bg-white/5 p-3 rounded-2xl border border-white/5">
                    <div class="flex items-center">
                        <i class="fas fa-ticket-alt text-primary mr-2 text-sm"></i>
                        <span class="text-xs text-slate-300">{{ $data->caseLogs->count() }} Tiket</span>
                    </div>

                    @if($data->status == 'pending')
                    <span class="text-[10px] font-bold uppercase text-amber-500 tracking-tighter">
                        <i class="fas fa-circle text-[6px] mr-1 align-middle"></i> Pending
                    </span>
                    @elseif($data->status == 'approved')
                    <span class="text-[10px] font-bold uppercase text-emerald-500 tracking-tighter">
                        <i class="fas fa-circle text-[6px] mr-1 align-middle"></i> Approved
                    </span>
                    @else
                    <span class="text-[10px] font-bold uppercase text-red-500 tracking-tighter">
                        <i class="fas fa-circle text-[6px] mr-1 align-middle"></i> Rejected
                    </span>
                    @endif
                </div>

                <button class="w-full flex items-center justify-center py-3 bg-white/5 text-slate-400 rounded-xl border border-white/5 text-sm active:bg-white/10 transition">
                    <i class="fas fa-eye mr-2"></i> Lihat Detail Laporan
                </button>
            </div>
            @empty
            <div class="py-20 text-center">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/5">
                    <i class="fas fa-folder-open text-3xl text-slate-700"></i>
                </div>
                <p class="text-slate-500 font-body text-sm px-10 leading-relaxed">Belum ada riwayat laporan yang terekam di sistem.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-8 px-2">
        {{ $submissions->links() }}
    </div>
</div>

<style>
    /* Styling khusus untuk Laravel Pagination agar masuk ke tema Dark */
    .pagination {
        @apply flex space-x-2 justify-center;
    }

    .page-item {
        @apply rounded-xl overflow-hidden border border-white/5;
    }

    .page-link {
        @apply bg-darkCard text-slate-400 px-4 py-2 block hover:bg-primary hover:text-white transition;
    }

    .page-item.active .page-link {
        @apply bg-primary text-white border-primary;
    }
</style>
@endsection