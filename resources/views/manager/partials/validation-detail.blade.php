<form action="{{ route('manager.approval.store') }}" method="POST" class="space-y-8">
    @csrf
    <input type="hidden" name="report_id" value="{{ $report->id }}">

    <div class="flex justify-between items-end border-b border-white/5 pb-6">
        <div>
            <span class="text-[10px] text-primary font-bold uppercase tracking-[0.2em]">Reviewing Report</span>
            <h3 class="text-2xl font-header font-bold text-white uppercase">{{ $report->user->nama_lengkap }}</h3>
            <p class="text-sm text-slate-500 italic">Dikirim {{ $report->created_at->diffForHumans() }}</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] text-slate-500 uppercase font-bold mb-1 tracking-tighter">Current Score</p>
            <span class="text-4xl font-header font-bold text-white">{{ number_format($report->total_nilai_harian, 1) }}</span>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($report->details as $item)
        <div class="bg-slate-900/40 p-5 rounded-[2rem] border border-white/5 hover:border-white/10 transition-all">
            <div class="flex justify-between items-start mb-3">
                <div class="px-3 py-1 bg-primary/10 rounded-lg">
                    <span class="text-[10px] text-primary font-bold uppercase italic">{{ $item->variabelKpi->nama_variabel }}</span>
                </div>
                <div class="flex items-center gap-3 bg-slate-800/50 p-1.5 rounded-xl border border-white/5">
                    <span class="text-[9px] text-slate-500 font-bold uppercase ml-2">Adjust Score:</span>
                    <input type="number" name="details[{{ $item->id }}][score]"
                        value="{{ $item->nilai_akhir }}" step="0.1"
                        class="w-16 bg-slate-950 text-white text-xs text-center font-bold py-1 rounded-lg border border-primary/30 focus:border-primary outline-none">
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

    <div class="pt-6 border-t border-white/5 space-y-4">
        <div>
            <label class="text-[10px] text-slate-500 uppercase font-bold tracking-widest ml-1">Catatan Manager (Opsional)</label>
            <textarea name="catatan" rows="2" placeholder="Berikan alasan jika menolak..."
                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl p-4 text-sm text-white focus:border-primary outline-none mt-2"></textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" name="status" value="rejected" class="flex-1 py-4 rounded-2xl border border-rose-500/30 text-rose-500 font-bold text-xs uppercase hover:bg-rose-500 hover:text-white transition-all">
                Reject & Revise
            </button>
            <button type="submit" name="status" value="approved" class="flex-[2] py-4 rounded-2xl bg-primary text-white font-bold text-xs uppercase hover:shadow-lg hover:shadow-primary/40 transition-all">
                Approve & Finalize Score
            </button>
        </div>
    </div>
</form>