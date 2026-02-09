@extends('layouts.manager')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-header font-bold text-white uppercase">Intelligence <span class="text-primary">Reports</span></h1>
        <p class="text-slate-400 text-xs italic font-bold uppercase tracking-widest">Ekspor data performa & validasi KPI</p>
    </div>

    <div class="organic-card p-8">
        <form action="{{ route('manager.reports.export') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Filter Staff --}}
                <div class="col-span-full">
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-1">Pilih Staff (Opsional)</label>
                    <select name="user_id" class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-3 text-sm text-white focus:border-primary outline-none appearance-none">
                        <option value="">Semua Staff TAC</option>
                        @foreach($staffs as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tanggal --}}
                <div>
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-3 text-sm text-white focus:border-primary outline-none">
                </div>
                <div>
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-3 text-sm text-white focus:border-primary outline-none">
                </div>
            </div>

            <div class="pt-6 border-t border-white/5">
                <button type="submit" class="w-full bg-primary hover:bg-primary/80 text-white py-4 rounded-2xl font-bold uppercase text-xs tracking-widest transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-3">
                    <i class="fas fa-file-excel text-lg"></i>
                    Generate Excel Report
                </button>
            </div>
        </form>
    </div>

    {{-- Info Card --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-slate-900/50 border border-white/5 p-4 rounded-2xl">
            <i class="fas fa-info-circle text-primary mb-2"></i>
            <p class="text-[10px] text-slate-400 leading-relaxed italic">Data yang diekspor hanya laporan yang berstatus <span class="text-primary font-bold">APPROVED</span>.</p>
        </div>
    </div>
</div>
@endsection