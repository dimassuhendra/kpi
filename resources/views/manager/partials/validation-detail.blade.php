<form action="{{ route('manager.approval.store') }}" method="POST" class="space-y-8">
    @csrf
    <input type="hidden" name="report_id" value="{{ $report->id }}">

    {{-- HEADER DETAIL --}}
    <div class="flex justify-between items-end border-b border-white/5 pb-6">
        <div>
            <span class="text-[10px] text-primary font-bold uppercase tracking-[0.2em]">Reviewing Mission Log</span>
            <h3 class="text-2xl font-header font-bold text-white uppercase">{{ $report->user->nama_lengkap }}</h3>
            <p class="text-sm text-slate-500 italic">Dikirim {{ $report->created_at->diffForHumans() }}</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] text-slate-500 uppercase font-bold mb-1 tracking-tighter">Report Date</p>
            <span class="text-xl font-header font-bold text-white">{{ \Carbon\Carbon::parse($report->tanggal)->format('d M Y') }}</span>
        </div>
    </div>

    {{-- LIST ITEM KEGIATAN --}}
    <div class="space-y-4">
        @foreach($report->details as $item)
        <div class="bg-slate-900/40 p-5 rounded-[2rem] border border-white/5 hover:border-white/10 transition-all">
            <div class="flex justify-between items-start mb-3">
                <div class="px-3 py-1 bg-primary/10 rounded-lg">
                    <span class="text-[10px] text-primary font-bold uppercase italic">{{ $item->variabelKpi->nama_variabel }}</span>
                </div>

                {{-- DURASI PEKERJAAN (Pengganti Score) --}}
                <div class="flex items-center gap-2 bg-slate-800/50 px-4 py-2 rounded-xl border border-white/5">
                    <i class="far fa-clock text-primary text-xs"></i>
                    <span class="text-xs text-white font-mono font-bold">{{ $item->value_raw ?? '0' }} Hours</span>
                </div>
            </div>

            <p class="text-slate-300 text-sm leading-relaxed mb-4">"{{ $item->deskripsi_kegiatan }}"</p>

            <div class="flex gap-4">
                <div class="flex items-center gap-1.5 {{ $item->is_mandiri ? 'text-emerald-500' : 'text-slate-600' }}">
                    <i class="fas fa-check-circle text-[10px]"></i>
                    <span class="text-[9px] font-bold uppercase">Mandiri</span>
                </div>
                <div class="flex items-center gap-1.5 {{ $item->temuan_sendiri ? 'text-amber-500' : 'text-slate-600' }}">
                    <i class="fas fa-lightbulb text-[10px]"></i>
                    <span class="text-[9px] font-bold uppercase">Inisiatif</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- FOOTER FORM --}}
    <div class="pt-6 border-t border-white/5 space-y-4">
        <div>
            <label class="text-[10px] text-slate-500 uppercase font-bold tracking-widest ml-1">Catatan Manager (Opsional)</label>
            <textarea name="catatan" rows="2" placeholder="Berikan instruksi jika ada perbaikan..."
                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl p-4 text-sm text-white focus:border-primary outline-none mt-2"></textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" name="status" value="rejected" class="flex-1 py-4 rounded-2xl border border-rose-500/30 text-rose-500 font-bold text-xs uppercase hover:bg-rose-500 hover:text-white transition-all">
                Reject & Revise
            </button>
            <button type="submit" name="status" value="approved" class="flex-[2] py-4 rounded-2xl bg-primary text-white font-bold text-xs uppercase hover:shadow-lg hover:shadow-primary/40 transition-all">
                Approve & Finalize
            </button>
        </div>
    </div>
</form>