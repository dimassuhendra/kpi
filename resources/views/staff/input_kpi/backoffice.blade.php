<div class="mb-10">
    <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
        <i class="fas fa-briefcase mr-3 text-amber-500"></i> Backoffice Daily Activities
    </h2>
    <div class="space-y-4">
        <template x-for="(bo, index) in bo_activities" :key="index">
            <div
                class="bg-white border border-slate-200 rounded-2xl p-6 border-l-4 border-l-amber-500 relative shadow-sm">
                <button type="button" @click="removeBoActivity(index)"
                    class="absolute top-4 right-4 text-red-500 hover:bg-red-50 p-2 rounded-xl transition">
                    <i class="fas fa-times-circle text-xl"></i>
                </button>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-[10px] uppercase text-slate-500 font-bold ml-1">Judul Kegiatan</label>
                        <input type="text" :name="'bo_activity[' + index + '][judul]'" x-model="bo.judul"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-slate-800 outline-none focus:border-amber-500 transition"
                            required>
                    </div>
                    <div>
                        <label class="text-[10px] uppercase text-slate-500 font-bold ml-1">Detail Activity</label>
                        <textarea :name="'bo_activity[' + index + '][deskripsi]'" x-model="bo.deskripsi" rows="3"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-slate-800 outline-none focus:border-amber-500 resize-none transition"></textarea>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
