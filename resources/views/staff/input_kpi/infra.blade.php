<div class="mb-10">
    <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
        <i class="fas fa-tools mr-3 text-amber-500"></i> Infrastruktur Daily Activities
    </h2>
    <div class="space-y-6">
        <template x-for="(infra, index) in infra_activities" :key="index">
            <div
                class="bg-white border border-slate-200 rounded-2xl p-6 border-l-4 border-l-amber-500 relative shadow-sm">
                <button type="button" @click="removeInfraActivity(index)"
                    class="absolute top-4 right-4 text-red-500 hover:bg-red-50 p-2 rounded-xl transition">
                    <i class="fas fa-times-circle text-xl"></i>
                </button>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-4 space-y-4">
                        <div>
                            <label
                                class="text-[10px] uppercase text-slate-500 font-bold ml-1 tracking-widest">Kategori</label>
                            <select :name="'infra_activity[' + index + '][kategori]'" x-model="infra.kategori"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-amber-500 transition">
                                <option value="Network">Network</option>
                                <option value="CCTV">CCTV</option>
                                <option value="GPS">GPS Tracking</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-500 font-bold ml-1 tracking-widest">Nama
                                Kegiatan</label>
                            <input type="text" :name="'infra_activity[' + index + '][nama_kegiatan]'"
                                x-model="infra.nama_kegiatan"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-amber-500 transition"
                                placeholder="Pemasangan NVR Baru" required>
                        </div>
                    </div>
                    <div class="md:col-span-8">
                        <label class="text-[10px] uppercase text-slate-500 font-bold ml-1 tracking-widest">Detail
                            Pekerjaan</label>
                        <textarea :name="'infra_activity[' + index + '][deskripsi]'" x-model="infra.deskripsi" rows="4"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-amber-500 resize-none transition"
                            placeholder="Jelaskan detail pekerjaan..." required></textarea>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
