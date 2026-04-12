<div class="mb-10">

    {{-- BUTTON SWITCH --}}
    <div
        class="flex p-1 mb-8 space-x-1 bg-slate-100/80 border border-slate-200 rounded-2xl max-w-sm mx-auto shadow-inner">
        <button type="button" @click="infraTab = 'harian'"
            :class="infraTab === 'harian' ? 'bg-white text-amber-600 shadow-sm ring-1 ring-slate-900/5' :
                'text-slate-500 hover:text-slate-700 hover:bg-slate-50/50'"
            class="flex-1 py-2.5 text-sm font-bold rounded-xl transition-all duration-200 flex items-center justify-center">
            <i class="fas fa-sun mr-2"></i> Harian
        </button>
        <button type="button" @click="infraTab = 'lembur'"
            :class="infraTab === 'lembur' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-900/5' :
                'text-slate-500 hover:text-slate-700 hover:bg-slate-50/50'"
            class="flex-1 py-2.5 text-sm font-bold rounded-xl transition-all duration-200 flex items-center justify-center">
            <i class="fas fa-moon mr-2"></i> Lembur
        </button>
    </div>

    {{-- Input hidden penanda lembur --}}
    <input type="hidden" name="is_lembur" :value="hasLemburData() ? '1' : '0'">

    {{-- ========================================== --}}
    {{-- 1. VIEW FORM HARIAN --}}
    {{-- ========================================== --}}
    <div x-show="infraTab === 'harian'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="space-y-6">

        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
            <i class="fas fa-tools mr-3 text-amber-500"></i> Infrastruktur Daily Activities
        </h2>

        <template x-for="(infra, index) in infra_activities" :key="index">
            <div
                class="bg-white border border-slate-200 rounded-2xl p-6 border-l-4 border-l-amber-500 relative shadow-sm">
                <button type="button" @click="removeInfra(index)"
                    class="absolute top-4 right-4 text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition">
                    <i class="fas fa-times-circle text-xl"></i>
                </button>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    <div class="md:col-span-3">
                        <label
                            class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Kategori</label>
                        <select :name="'infra_activity[' + index + '][kategori]'" x-model="infra.kategori"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 font-bold outline-none focus:border-amber-500 transition">
                            <option value="Network">Network</option>
                            <option value="CCTV">CCTV</option>
                            <option value="GPS">GPS</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="md:col-span-5">
                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Nama
                            Kegiatan</label>
                        {{-- FIX: required diganti menjadi dinamis sesuai tab aktif --}}
                        <input type="text" :name="'infra_activity[' + index + '][nama_kegiatan]'"
                            x-model="infra.nama_kegiatan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 outline-none focus:border-amber-500 transition"
                            placeholder="Contoh: Pemasangan NVR Baru" :required="infraTab === 'harian'">
                    </div>

                    <div class="md:col-span-4">
                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Foto
                            Dokumentasi (Opsional)</label>
                        <input type="hidden" :name="'infra_activity[' + index + '][foto_dokumentasi_path]'"
                            x-model="infra.foto_dokumentasi_path">
                        <input type="file" accept="image/*"
                            @change="infra.isUploading = true; uploadFile($event, 'infra', (path) => { infra.foto_dokumentasi_path = path; infra.isUploading = false; })"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-700 cursor-pointer transition">

                        <p x-show="infra.isUploading" class="text-[9px] text-amber-500 mt-1 animate-pulse"><i
                                class="fas fa-spinner fa-spin mr-1"></i> Uploading...</p>
                        <p x-show="!infra.isUploading && infra.foto_dokumentasi_path"
                            class="text-[9px] text-emerald-500 mt-1 font-bold"><i class="fas fa-check mr-1"></i> File OK
                        </p>
                    </div>

                    <div class="md:col-span-12 mt-1">
                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Detail
                            Pekerjaan</label>
                        {{-- FIX: required diganti menjadi dinamis sesuai tab aktif --}}
                        <textarea :name="'infra_activity[' + index + '][deskripsi]'" x-model="infra.deskripsi" rows="3"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-amber-500 resize-none transition"
                            placeholder="Jelaskan detail pekerjaan yang dilakukan hari ini..." :required="infraTab === 'harian'"></textarea>
                    </div>
                </div>
            </div>
        </template>

        <button type="button" @click="addInfraActivity()"
            class="w-full py-4 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 hover:border-amber-400 hover:text-amber-500 hover:bg-amber-50/30 transition-all font-bold text-sm uppercase tracking-widest mt-4">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Activity Harian
        </button>
    </div>

    {{-- ========================================== --}}
    {{-- 2. VIEW FORM LEMBUR --}}
    {{-- ========================================== --}}
    <div x-show="infraTab === 'lembur'" style="display: none;" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="space-y-6">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-slate-800 flex items-center">
                <i class="fas fa-clock mr-3 text-indigo-500"></i> Laporan Pekerjaan Lembur
            </h2>

            <button type="button" @click="clearLembur()"
                class="text-xs font-bold text-rose-500 hover:text-rose-600 bg-rose-50 px-3 py-1.5 rounded-lg transition">
                <i class="fas fa-trash-alt mr-1"></i> Bersihkan & Batal Lembur
            </button>
        </div>

        <template x-for="(lemburItem, index) in lembur_activities" :key="index">
            <div
                class="bg-white border border-slate-200 rounded-2xl p-6 border-l-4 border-l-indigo-500 relative shadow-sm">

                <button type="button" @click="removeLembur(index)"
                    class="absolute top-4 right-4 text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition">
                    <i class="fas fa-times-circle text-xl"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    <div class="md:col-span-4">
                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Waktu Mulai
                            Lembur</label>
                        {{-- FIX: required mengecek apakah tab lembur sedang aktif --}}
                        <input type="datetime-local" :name="'lembur_activity[' + index + '][waktu_mulai]'"
                            x-model="lemburItem.waktu_mulai"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 outline-none focus:border-indigo-500 transition"
                            :required="infraTab === 'lembur' && (lemburItem.waktu_selesai !== '' || lemburItem
                                .detail !== '' || lemburItem.foto_path !== '')">
                    </div>

                    <div class="md:col-span-4">
                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Waktu
                            Selesai Lembur</label>
                        <input type="datetime-local" :name="'lembur_activity[' + index + '][waktu_selesai]'"
                            x-model="lemburItem.waktu_selesai"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 outline-none focus:border-indigo-500 transition"
                            :required="infraTab === 'lembur' && (lemburItem.waktu_mulai !== '' || lemburItem
                                .detail !== '' || lemburItem.foto_path !== '')">
                    </div>

                    <div class="md:col-span-4">
                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Bukti Foto
                            Lembur (Wajib maks: 1MB)</label>
                        <input type="hidden" :name="'lembur_activity[' + index + '][foto_path]'"
                            x-model="lemburItem.foto_path">
                        <input type="file" accept="image/*"
                            @change="lemburItem.isUploading = true; uploadFile($event, 'lembur', (path) => { lemburItem.foto_path = path; lemburItem.isUploading = false; })"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-100 file:text-indigo-700 cursor-pointer transition"
                            :required="infraTab === 'lembur' && (lemburItem.waktu_mulai !== '' || lemburItem
                                    .waktu_selesai !== '' || lemburItem.detail !== '') && !lemburItem
                                .foto_path">

                        <p x-show="lemburItem.isUploading" class="text-[9px] text-indigo-500 mt-1 animate-pulse"><i
                                class="fas fa-spinner fa-spin mr-1"></i> Uploading...</p>
                        <p x-show="!lemburItem.isUploading && lemburItem.foto_path"
                            class="text-[9px] text-emerald-500 mt-1 font-bold"><i class="fas fa-check mr-1"></i> File
                            OK</p>
                    </div>

                    <div class="md:col-span-12 mt-1">
                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Detail
                            Pekerjaan Lembur</label>
                        <textarea :name="'lembur_activity[' + index + '][detail]'" x-model="lemburItem.detail" rows="3"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-indigo-500 resize-none transition"
                            placeholder="Jelaskan secara rinci apa yang diselesaikan pada jam lembur ini..."
                            :required="infraTab === 'lembur' && (lemburItem.waktu_mulai !== '' || lemburItem
                                .waktu_selesai !== '' || lemburItem.foto_path !== '')"></textarea>
                    </div>
                </div>
            </div>
        </template>

        <button type="button" @click="addLemburActivity()"
            class="w-full py-4 border-2 border-dashed border-indigo-200 rounded-2xl text-indigo-400 hover:border-indigo-400 hover:text-indigo-500 hover:bg-indigo-50/30 transition-all font-bold text-sm uppercase tracking-widest mt-4">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Kegiatan Lembur
        </button>
    </div>
</div>
