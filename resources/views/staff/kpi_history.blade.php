@extends('layouts.staff')

@section('content')
<div class="max-w-6xl mx-auto px-2 sm:px-0" x-data="{ openModal: false, selectedReport: {} }">

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

        {{-- Desktop Table --}}
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
                            {{-- Button Eye untuk memicu modal --}}
                            <button
                                @click="openModal = true; selectedReport = {{ $data->toJson() }}"
                                class="text-slate-400 hover:text-primary transition p-2.5 bg-white/5 rounded-xl border border-white/5">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile List --}}
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
                    {{-- Status badge --}}
                    <span class="text-[10px] font-bold uppercase {{ $data->status == 'approved' ? 'text-emerald-500' : ($data->status == 'pending' ? 'text-amber-500' : 'text-red-500') }} tracking-tighter">
                        <i class="fas fa-circle text-[6px] mr-1 align-middle"></i> {{ ucfirst($data->status) }}
                    </span>
                </div>

                <button
                    @click="openModal = true; selectedReport = {{ $data->toJson() }}"
                    class="w-full flex items-center justify-center py-3 bg-white/5 text-slate-400 rounded-xl border border-white/5 text-sm active:bg-white/10 transition">
                    <i class="fas fa-eye mr-2"></i> Lihat Detail Laporan
                </button>
            </div>
            @empty
            <div class="py-20 text-center">
                <i class="fas fa-folder-open text-3xl text-slate-700 mb-4 block"></i>
                <p class="text-slate-500 text-sm">Belum ada riwayat laporan.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div x-show="openModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-cloak>

        <div class="bg-darkCard w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-3xl border border-white/10 shadow-2xl flex flex-col" @click.away="openModal = false">

            {{-- Modal Header --}}
            <div class="p-6 border-b border-white/5 flex justify-between items-center bg-darkCard/50 backdrop-blur-xl">
                <div>
                    <h3 class="font-header text-xl text-white">Analisis Skor KPI</h3>
                    <p class="text-xs text-slate-400 mt-1" x-text="'Laporan Tanggal: ' + selectedReport.assessment_date"></p>
                </div>
                <button @click="openModal = false" class="text-slate-400 hover:text-white transition bg-white/5 w-10 h-10 rounded-xl">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-8 flex-1 custom-scrollbar">

                {{-- Bagian 1: Rincian Kalkulasi Variabel --}}
                <div>
                    <h4 class="text-[10px] uppercase font-bold text-primary tracking-[0.2em] mb-4 flex items-center gap-2">
                        <i class="fas fa-calculator text-xs"></i> Rincian Perhitungan Variabel
                    </h4>
                    <div class="grid grid-cols-1 gap-3">
                        <template x-for="detail in selectedReport.details" :key="detail.id">
                            <div class="group bg-white/[0.02] hover:bg-white/[0.05] border border-white/5 p-4 rounded-2xl transition-all">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-primary shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                            <p class="text-sm text-slate-200 font-bold" x-text="detail.variable.variable_name"></p>
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-1 italic">
                                            Perhitungan: <span class="text-slate-400" x-text="'(Skor ' + (detail.manager_correction ?? detail.staff_value ?? 0) + ' x Bobot ' + detail.variable.weight + '%)'"></span>
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-6">
                                        <div class="text-center md:text-right">
                                            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Skor Dasar</p>
                                            <p class="text-sm font-header text-white" x-text="(detail.calculated_score / (detail.variable.weight / 100)).toFixed(0)"></p>
                                        </div>
                                        <div class="w-px h-8 bg-white/10 hidden md:block"></div>
                                        <div class="text-right">
                                            <p class="text-[10px] text-primary uppercase font-bold tracking-widest">Poin Akhir</p>
                                            <p class="text-xl font-header text-primary" x-text="parseFloat(detail.calculated_score).toFixed(2)"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Bagian 2: Analisis Performa Tiket --}}
                <div>
                    <h4 class="text-[10px] uppercase font-bold text-amber-500 tracking-[0.2em] mb-4 flex items-center gap-2">
                        <i class="fas fa-ticket-alt text-xs"></i> Analisis Daily KPI
                    </h4>
                    <div class="bg-white/[0.02] border border-white/5 rounded-2xl overflow-hidden">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-white/5 text-slate-500 font-bold uppercase tracking-widest">
                                <tr>
                                    <th class="p-4">Nama Case</th>
                                    <th class="p-4">Durasi Respon</th>
                                    <th class="p-4 text-center">Kontribusi Skor</th>
                                    <th class="p-4 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <template x-for="log in selectedReport.case_logs" :key="log.id">
                                    <tr class="hover:bg-white/[0.02] transition-colors">
                                        <td class="p-4 font-mono text-primary font-bold" x-text="'#' + log.ticket_number"></td>
                                        <td class="p-4 text-slate-300" x-text="log.response_time_minutes + ' Menit'"></td>
                                        <td class="p-4 text-center font-bold">
                                            <span x-text="log.response_time_minutes <= 15 ? '100' : (log.response_time_minutes <= 30 ? '80' : (log.response_time_minutes <= 60 ? '60' : '40'))"
                                                :class="log.response_time_minutes <= 15 ? 'text-emerald-400' : 'text-amber-500'"></span>
                                        </td>
                                        <td class="p-4 text-right">
                                            <template x-if="log.response_time_minutes <= 15">
                                                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 rounded-md text-[9px] font-bold border border-emerald-500/20">EXCELLENT</span>
                                            </template>
                                            <template x-if="log.response_time_minutes > 15">
                                                <span class="px-2 py-0.5 bg-amber-500/10 text-amber-500 rounded-md text-[9px] font-bold border border-amber-500/20">DELAYED</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-[9px] text-slate-500 mt-3 italic">* Skor variabel teknis diambil dari rata-rata seluruh skor tiket di atas.</p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-white/5 bg-white/[0.02] flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="flex items-center gap-4 bg-darkCard p-3 rounded-2xl border border-white/5 w-full md:w-auto">
                    <div class="w-12 h-12 rounded-xl bg-primary/20 flex flex-col items-center justify-center text-primary border border-primary/20">
                        <span class="text-xs font-bold leading-none">KPI</span>
                        <span class="text-lg font-header leading-none mt-1" x-text="Math.round(selectedReport.total_final_score)"></span>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Hasil Akhir Laporan</p>
                        <p class="text-sm text-slate-200 font-bold" x-text="selectedReport.total_final_score >= 100 ? 'Sempurna!' : (selectedReport.total_final_score >= 80 ? 'Sangat Baik' : 'Butuh Peningkatan')"></p>
                    </div>
                </div>
                <button @click="openModal = false" class="w-full md:w-auto bg-white/10 hover:bg-white/20 text-white px-8 py-3 rounded-xl text-sm font-bold transition-all border border-white/10 active:scale-95">
                    Tutup Analisis
                </button>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-8 px-2">
        {{ $submissions->links() }}
    </div>
</div>

<style>
    /* Menghilangkan scrollbar pada modal namun tetap bisa scroll */
    .max-h-\[90vh\]::-webkit-scrollbar {
        width: 4px;
    }

    .max-h-\[90vh\]::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    [x-cloak] {
        display: none !important;
    }
</style>
@endsection