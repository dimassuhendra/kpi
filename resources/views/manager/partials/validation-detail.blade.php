<div class="animate-fadeIn">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Technical Cases --}}
        <div>
            <h4 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-4 flex items-center">
                <i class="fas fa-microchip mr-2"></i> Technical Cases
            </h4>
            <div class="space-y-3">
                @forelse ($cases as $case)
                    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-bold text-slate-700 leading-relaxed">{{ $case->deskripsi_kegiatan }}
                            </p>
                            <span
                                class="text-[10px] font-mono text-emerald-600 font-black bg-emerald-50 px-2 py-1 rounded-lg">
                                +{{ $case->nilai_akhir }}
                            </span>
                        </div>
                        <div class="flex gap-3 mt-3">
                            <span class="text-[9px] text-slate-400 font-bold uppercase bg-slate-50 px-2 py-1 rounded">
                                <i class="far fa-clock mr-1 text-emerald-500"></i> {{ $case->value_raw }}m
                            </span>
                            @if ($case->temuan_sendiri)
                                <span
                                    class="text-[9px] text-amber-500 font-bold uppercase bg-amber-50 px-2 py-1 rounded">
                                    <i class="fas fa-bolt mr-1"></i> Proaktif
                                </span>
                            @endif
                            <span class="text-[9px] text-slate-400 font-bold uppercase bg-slate-50 px-2 py-1 rounded">
                                <i class="fas {{ $case->is_mandiri ? 'fa-user' : 'fa-users' }} mr-1"></i>
                                {{ $case->is_mandiri ? 'Solo' : 'Assist: ' . $case->pic_name }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-[10px] text-slate-300 font-bold italic uppercase">No technical cases reported</p>
                @endforelse
            </div>
        </div>

        {{-- General Activities --}}
        <div>
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center">
                <i class="fas fa-tasks mr-2"></i> General Activities
            </h4>
            <div class="space-y-2">
                @forelse ($activities as $act)
                    <div class="p-3 bg-slate-100/50 border-l-2 border-slate-200 rounded-r-xl">
                        <p class="text-xs text-slate-500 italic">"{{ $act->deskripsi_kegiatan }}"</p>
                    </div>
                @empty
                    <p class="text-[10px] text-slate-300 font-bold italic uppercase">No general activities</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Action Footer --}}
    <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
        <form action="{{ route('manager.approval.store') }}" method="POST">
            @csrf
            <input type="hidden" name="report_id" value="{{ $report->id }}">
            <input type="hidden" name="status" value="rejected">
            <button type="submit"
                class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                Reject
            </button>
        </form>

        <form action="{{ route('manager.approval.store') }}" method="POST">
            @csrf
            <input type="hidden" name="report_id" value="{{ $report->id }}">
            <input type="hidden" name="status" value="approved">
            <button type="submit"
                class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center gap-2">
                <i class="fas fa-check-double"></i> Approve Mission
            </button>
        </form>
    </div>
</div>
