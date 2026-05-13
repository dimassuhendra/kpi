<div class="space-y-10">
    <div>
        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
            <i class="fas fa-briefcase mr-3 text-amber-500"></i>
            {{ auth()->user()->divisi->nama_divisi ?? 'BOT' }} Daily Activities
        </h2>
        <div class="space-y-4">
            <template x-for="(bo, index) in bo_activities" :key="index">
                <div
                    class="bg-white border border-slate-200 rounded-2xl p-6 border-l-4 border-l-amber-500 relative shadow-sm">
                    <button type="button" @click="removeBo(index)"
                        class="absolute top-4 right-4 text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition">
                        <i class="fas fa-times-circle text-xl"></i>
                    </button>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black ml-1">Judul Kegiatan</label>
                            <input type="text" :name="'bo_activity[' + index + '][judul]'" x-model="bo.judul"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-amber-500 transition"
                                required>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black ml-1">Detail Activity
                                (Opsional)</label>
                            <textarea :name="'bo_activity[' + index + '][deskripsi]'" x-model="bo.deskripsi" rows="3"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-amber-500 resize-none transition"
                                placeholder="Jelaskan secara detail..."></textarea>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    @if (auth()->user()->divisi_id == 4)
        <div class="pt-8 border-t border-slate-200" x-data="{
            hasNotulen: {{ $report && $report->meetingNote ? 'true' : 'false' }},
            judul: '{{ $report->meetingNote->judul_briefing ?? '' }}',
            isi: `{!! $report->meetingNote->isi_notulen ?? '' !!}`
        }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center">
                    <i class="fas fa-file-alt mr-3 text-indigo-500"></i> Notulen Briefing
                </h2>
                <button type="button" @click="hasNotulen = !hasNotulen"
                    class="text-xs font-bold px-4 py-2 rounded-lg transition"
                    :class="hasNotulen ? 'bg-rose-50 text-rose-600' : 'bg-indigo-50 text-indigo-600'">
                    <span x-text="hasNotulen ? '- Batalkan Notulen' : '+ Tambah Notulen Briefing'"></span>
                </button>
            </div>

            <div x-show="hasNotulen" x-transition class="bg-indigo-50/30 border border-indigo-100 rounded-2xl p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-[10px] uppercase text-indigo-400 font-black ml-1">Judul / Tanggal
                            Briefing</label>
                        <input type="text" name="notulen_judul" x-model="judul" :required="hasNotulen"
                            placeholder="Contoh: Briefing Harian {{ date('d/m/Y') }}"
                            class="w-full bg-white border border-indigo-100 rounded-xl px-4 py-3 text-slate-800 font-bold outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="text-[10px] uppercase text-indigo-400 font-black ml-1">Isi Notulen
                            (Poin-poin)</label>
                        <textarea name="notulen_isi" x-model="isi" rows="10" :required="hasNotulen"
                            class="w-full bg-white border border-indigo-100 rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-indigo-500 resize-none transition font-mono text-sm"
                            placeholder="Batu Makmur&#10;- issue FO Cut..."></textarea>
                    </div>
                </div>
            </div>
            <template x-if="!hasNotulen">
                <div>
                    <input type="hidden" name="notulen_judul" value="">
                    <input type="hidden" name="notulen_isi" value="">
                </div>
            </template>
        </div>
    @endif
</div>
