<div class="space-y-8 animate-fadeIn">
    {{-- Header Detail --}}
    <div class="flex justify-between items-end border-b border-white/5 pb-6">
        <div>
            <h3 class="text-2xl font-bold text-white">{{ $report->user->nama_lengkap }}</h3>
            <p class="text-slate-400 text-sm italic">{{ \Carbon\Carbon::parse($report->tanggal)->format('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- SECTION 1: TECHNICAL CASES --}}
    @if ($cases->count() > 0)
        <div>
            <h4 class="text-xs font-bold text-primary uppercase tracking-widest mb-4 flex items-center">
                <i class="fas fa-microchip mr-2"></i> Technical Cases
            </h4>
            <div class="space-y-3">
                @foreach ($cases as $case)
                    <div
                        class="bg-secondary/30 border border-white/5 rounded-2xl p-4 flex justify-between items-center group hover:bg-secondary/50 transition">
                        <div class="flex-grow">
                            <p class="text-slate-200 font-medium leading-relaxed">{{ $case->deskripsi_kegiatan }}</p>
                            <div class="flex gap-4 mt-2">
                                <span class="text-[10px] text-slate-500 uppercase">
                                    <i class="far fa-clock mr-1"></i> {{ $case->value_raw }} Mins Response
                                </span>
                                @if ($case->temuan_sendiri)
                                    <span class="text-[10px] text-emerald-500 uppercase font-bold">
                                        <i class="fas fa-eye mr-1"></i> Proaktif
                                    </span>
                                @endif
                                <span
                                    class="text-[10px] {{ $case->is_mandiri ? 'text-blue-400' : 'text-amber-400' }} uppercase font-bold">
                                    <i class="fas {{ $case->is_mandiri ? 'fa-user-check' : 'fa-users' }} mr-1"></i>
                                    {{ $case->is_mandiri ? 'Mandiri' : 'Bantuan: ' . $case->pic_name }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <span class="text-xs font-mono text-primary font-bold">+{{ $case->nilai_akhir }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- SECTION 2: GENERAL ACTIVITIES --}}
    @if ($activities->count() > 0)
        <div>
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                <i class="fas fa-tasks mr-2"></i> General Activities
            </h4>
            <div class="grid grid-cols-1 gap-3">
                @foreach ($activities as $act)
                    <div class="bg-slate-800/20 border border-white/5 border-l-2 border-l-slate-600 rounded-xl p-4">
                        <p class="text-slate-400 text-sm italic">"{{ $act->deskripsi_kegiatan }}"</p>
                        <span class="text-[9px] text-slate-600 uppercase mt-2 block">Non-KPI Activity</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ACTION BUTTONS --}}
    <div class="pt-8 flex gap-4">
        {{-- Tombol Approve menggunakan route manager.approval.store --}}
        <form action="{{ route('manager.approval.store') }}" method="POST" class="flex-grow">
            @csrf
            <input type="hidden" name="report_id" value="{{ $report->id }}">
            <input type="hidden" name="status" value="approved">
            
            <button type="submit"
                class="w-full bg-primary hover:bg-accent text-secondary font-bold py-4 rounded-2xl transition-all shadow-lg shadow-primary/10 flex items-center justify-center group">
                <i class="fas fa-check-double mr-2 group-hover:scale-110 transition"></i> Approve Mission
            </button>
        </form>

        {{-- Tombol Reject (Jika ingin simpel tanpa SweetAlert dulu, bisa pakai form yang sama) --}}
        <form action="{{ route('manager.approval.store') }}" method="POST">
            @csrf
            <input type="hidden" name="report_id" value="{{ $report->id }}">
            <input type="hidden" name="status" value="rejected">
            <button type="submit"
                class="h-full bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white px-6 rounded-2xl transition-all border border-rose-500/20">
                <i class="fas fa-times"></i>
            </button>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>
