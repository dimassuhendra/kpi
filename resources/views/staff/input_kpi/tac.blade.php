<div class="mb-10" x-show="rows.length > 0">
    <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
        <span class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center mr-2 text-sm">1</span>
        Technical Cases (KPI)
    </h2>
    <div class="space-y-6">
        <template x-for="(row, index) in rows" :key="index">
            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 border-l-4 border-l-amber-500 relative shadow-sm">
                <button type="button" @click="removeRow(index)"
                    class="absolute top-2 right-2 text-red-500 hover:bg-red-50 p-2 rounded-xl transition">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start lg:items-center">
                    <div class="lg:col-span-4">
                        <label class="text-[10px] uppercase text-slate-500 font-bold ml-1">Deskripsi Case</label>
                        <input type="text" :name="'case[' + index + '][deskripsi]'" x-model="row.deskripsi"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition"
                            placeholder="Deskripsi perbaikan..." required>
                    </div>
                    <div class="grid grid-cols-2 lg:flex lg:col-span-3 gap-4">
                        <div class="w-full">
                            <label class="text-[10px] uppercase text-slate-500 font-bold ml-1">Respons (Min)</label>
                            <input type="number" :name="'case[' + index + '][respons]'" x-model="row.respons"
                                :readonly="row.temuan_sendiri"
                                :class="row.temuan_sendiri ? 'bg-slate-200 text-slate-400' : 'bg-slate-50 text-slate-800'"
                                class="w-full border border-slate-300 rounded-xl px-4 py-2 outline-none focus:border-amber-500 transition"
                                required>
                        </div>
                        <div class="w-full flex flex-col items-center justify-center">
                            <label
                                class="text-[10px] uppercase text-slate-500 font-bold mb-1 text-center">Temuan</label>
                            <input type="checkbox" :name="'case[' + index + '][temuan_sendiri]'"
                                x-model="row.temuan_sendiri" @change="if(row.temuan_sendiri) row.respons = 0"
                                class="w-6 h-6 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                        </div>
                    </div>
                    <div class="lg:col-span-5">
                        <label class="text-[10px] uppercase text-slate-500 font-bold ml-1">Penyelesaian</label>
                        <div class="flex gap-2">
                            <select x-model="row.is_mandiri" :name="'case[' + index + '][is_mandiri]'"
                                class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-800 text-sm focus:border-amber-500 outline-none">
                                <option value="1">Mandiri</option>
                                <option value="0">Bantuan</option>
                            </select>
                            <input type="text" :name="'case[' + index + '][pic_name]'" x-model="row.pic_name"
                                x-show="row.is_mandiri == 0"
                                class="flex-grow bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-slate-800 outline-none focus:border-amber-500 transition"
                                placeholder="Nama PIC">
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<div class="mb-10">
    <h2 class="text-xl font-bold text-slate-700 mb-4 flex items-center">
        <span
            class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center mr-2 text-sm">2</span>
        General Activities
    </h2>
    <div class="space-y-4">
        <template x-for="(act, index) in activities" :key="index">
            <div
                class="bg-white border border-slate-200 p-4 border-l-4 border-slate-400 rounded-xl flex gap-4 items-end shadow-sm">
                <div class="flex-grow">
                    <label class="text-[10px] uppercase text-slate-500 font-bold ml-1">Deskripsi</label>
                    <input type="text" :name="'activity[' + index + '][deskripsi]'" x-model="act.deskripsi"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-slate-800 outline-none focus:border-amber-500 transition"
                        required>
                </div>
                <button type="button" @click="removeActivity(index)"
                    class="text-red-500 p-2 hover:bg-red-50 rounded-lg">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </template>
    </div>
</div>
