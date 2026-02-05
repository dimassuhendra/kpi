@extends('layouts.manager')

@section('content')
<div class="space-y-10">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="font-header text-3xl text-white italic">KPI <span class="text-primary">Approval Center</span></h2>
        <div class="flex gap-4">
            <span class="px-4 py-2 bg-amber-500/10 text-amber-500 rounded-2xl text-xs font-bold border border-amber-500/20">
                {{ $pendingSubmissions->count() }} Menunggu Review
            </span>
        </div>
    </div>

    {{-- Tabel Utama (Pending) --}}
    <div class="organic-card overflow-hidden">
        <div class="p-6 border-b border-white/5 bg-white/5">
            <h3 class="text-white font-header">Antrean Persetujuan</h3>
        </div>
        <table class="w-full text-left">
            <thead class="text-slate-500 text-[10px] uppercase tracking-widest bg-darkCard">
                <tr>
                    <th class="p-5 font-medium">Nama Staff</th>
                    <th class="p-5 font-medium">Tanggal Input</th>
                    <th class="p-5 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-slate-300 divide-y divide-white/5">
                @forelse($pendingSubmissions as $s)
                <tr class="hover:bg-white/[0.02] transition">
                    <td class="p-5">
                        <p class="font-bold text-white">{{ $s->user->name }}</p>
                        <p class="text-[10px] text-slate-500">Divisi: {{ $s->user->division->name }}</p>
                    </td>
                    <td class="p-5 text-sm">{{ $s->created_at->format('d M Y, H:i') }}</td>
                    <td class="p-5 text-right">
                        <button onclick="openModal('modal-{{ $s->id }}')" class="bg-primary/10 text-primary hover:bg-primary hover:text-white px-5 py-2 rounded-xl text-xs font-bold transition-all shadow-lg shadow-primary/5">
                            Review Detail
                        </button>
                    </td>
                </tr>

                {{-- MODAL DETAIL --}}
                <div id="modal-{{ $s->id }}" class="fixed inset-0 z-[60] hidden overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 py-10">
                        <div class="fixed inset-0 bg-secondary/90 backdrop-blur-sm" onclick="closeModal('modal-{{ $s->id }}')"></div>
                        <div class="relative bg-darkCard w-full max-w-2xl rounded-[40px] border border-white/10 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
                            <form action="{{ route('manager.approval.process', $s->id) }}" method="POST">
                                @csrf
                                <div class="p-8 max-h-[80vh] overflow-y-auto custom-scrollbar">
                                    <div class="mb-6">
                                        <h4 class="text-2xl font-header text-white mb-1">Review Laporan</h4>
                                        <p class="text-slate-400 text-sm">Staff: <span class="text-primary font-bold">{{ $s->user->name }}</span></p>
                                    </div>

                                    {{-- 1. DAFTAR TIKET (Dari KpiCaseLog) --}}
                                    <div class="mb-8">
                                        <h5 class="text-[10px] text-primary uppercase font-bold tracking-widest mb-4 flex items-center gap-2">
                                            <i class="fas fa-ticket-alt"></i> Daftar Case Yang Dikerjakan
                                        </h5>
                                        <div class="space-y-3">
                                            @forelse($s->caseLogs as $log)
                                            <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <span class="text-[10px] text-slate-500 uppercase block">Nomor Tiket</span>
                                                        <span class="text-sm font-bold text-white">{{ $log->ticket_number }}</span>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="text-[10px] text-slate-500 uppercase block">Response Time</span>
                                                        @if($log->is_problem_detected_by_staff)
                                                        <span class="text-[10px] bg-red-500/20 text-red-500 px-2 py-0.5 rounded font-bold italic">Problem Detected</span>
                                                        @else
                                                        <span class="text-sm font-bold text-primary">{{ $log->response_time_minutes }} Menit</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="text-center py-6 bg-white/5 rounded-2xl border border-dashed border-white/10 text-slate-500 text-xs italic">
                                                Tidak ada log tiket.
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    {{-- 3. FORM KEPUTUSAN MANAGER --}}
                                    <div class="bg-secondary/50 p-6 rounded-3xl border border-primary/20 space-y-4">
                                        <h5 class="text-[10px] text-primary uppercase font-bold tracking-widest mb-2 flex items-center gap-2">
                                            <i class="fas fa-gavel"></i> Keputusan Penilaian
                                        </h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-[10px] text-slate-500 uppercase font-bold ml-2">Tepat Waktu?</label>
                                                <select name="is_on_time" class="w-full bg-darkCard border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none mt-1">
                                                    <option value="1">Ya (On-Time)</option>
                                                    <option value="0">Terlambat</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-slate-500 uppercase font-bold ml-2">Ada Revisi?</label>
                                                <select name="needs_revision" class="w-full bg-darkCard border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none mt-1">
                                                    <option value="0">Tidak</option>
                                                    <option value="1">Ya (Butuh Perbaikan)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-[10px] text-slate-500 uppercase font-bold ml-2">Catatan / Feedback</label>
                                            <textarea name="manager_feedback" rows="2" class="w-full bg-darkCard border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none mt-1" placeholder="Masukkan saran atau feedback..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white/5 p-6 flex gap-4">
                                    <button type="submit" name="status" value="approved" class="flex-1 bg-primary text-white font-bold py-4 rounded-2xl hover:scale-105 transition-all">
                                        Setujui Laporan
                                    </button>
                                    <button type="submit" name="status" value="rejected" class="flex-1 bg-red-500/10 text-red-500 font-bold py-4 rounded-2xl hover:bg-red-500 hover:text-white transition-all">
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="3" class="p-10 text-center text-slate-500 italic">Antrean kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Riwayat --}}
    <div class="organic-card p-6 opacity-80 hover:opacity-100 transition-opacity">
        <h4 class="font-header text-white mb-4 italic flex items-center gap-2 text-sm">
            <i class="fas fa-history text-slate-500"></i> Riwayat Terakhir
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($historySubmissions as $h)
            <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-2 h-2 rounded-full {{ $h->status == 'approved' ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                    <div>
                        <p class="text-sm text-white font-bold">{{ $h->user->name }}</p>
                        <p class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($h->approved_at)->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded {{ $h->status == 'approved' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                        {{ $h->status }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.2);
        border-radius: 10px;
    }
</style>
@endsection