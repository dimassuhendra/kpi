<div>
    <div class="mb-10">
        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
            <span
                class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center mr-2 text-sm font-black">1A</span>
            Technical Cases: Network
        </h2>
        <div class="space-y-4">
            <template x-for="(row, index) in rows_network" :key="index">
                <div :class="row.is_monitoring ? 'bg-slate-50 border-slate-200' : 'bg-white border-slate-200 border-l-amber-500'"
                    class="border rounded-2xl p-5 border-l-4 relative shadow-sm transition-all">

                    <button x-show="!row.is_default" type="button" @click="removeNetwork(index)"
                        class="absolute top-2 right-2 text-rose-400 hover:text-rose-600 p-2 rounded-xl">
                        <i class="fas fa-trash-alt"></i>
                    </button>

                    <input type="hidden" :name="'case_network[' + index + '][is_monitoring]'"
                        :value="row.is_monitoring ? 1 : 0">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
                        <div :class="row.is_monitoring ? 'lg:col-span-12' : 'lg:col-span-4'">
                            <label
                                class="text-[10px] uppercase text-slate-400 font-black ml-1 tracking-widest">Deskripsi
                                Kegiatan</label>
                            <input type="text" :name="'case_network[' + index + '][deskripsi]'"
                                x-model="row.deskripsi" :readonly="row.is_monitoring"
                                :class="row.is_monitoring ? 'bg-transparent border-none font-bold text-slate-700' :
                                    'bg-white border-slate-200'"
                                class="w-full border rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        </div>

                        <template x-if="!row.is_monitoring">
                            <div class="lg:col-span-8 grid grid-cols-1 lg:grid-cols-12 gap-5">
                                <div class="lg:col-span-5 grid grid-cols-2 gap-4">
                                    <div class="w-full">
                                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1">Respons
                                            (Min)</label>
                                        <input type="number" :name="'case_network[' + index + '][respons]'"
                                            x-model="row.respons" :readonly="row.temuan_sendiri"
                                            :class="row.temuan_sendiri ? 'bg-slate-100 text-slate-400' : 'bg-white'"
                                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <label
                                            class="text-[10px] uppercase text-slate-400 font-black mb-1">Temuan</label>
                                        <input type="checkbox" :name="'case_network[' + index + '][temuan_sendiri]'"
                                            x-model="row.temuan_sendiri"
                                            @change="if(row.temuan_sendiri) row.respons = 0"
                                            class="w-6 h-6 rounded-lg text-amber-500 focus:ring-amber-500 border-slate-300">
                                    </div>
                                </div>
                                <div class="lg:col-span-7">
                                    <label
                                        class="text-[10px] uppercase text-slate-400 font-black ml-1">Penyelesaian</label>
                                    <div class="flex gap-2">
                                        <select x-model="row.is_mandiri"
                                            :name="'case_network[' + index + '][is_mandiri]'"
                                            class="border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold outline-none focus:border-amber-500">
                                            <option value="1">Mandiri</option>
                                            <option value="0">Bantuan</option>
                                        </select>
                                        <input type="text" :name="'case_network[' + index + '][pic_name]'"
                                            x-model="row.pic_name" x-show="row.is_mandiri == 0"
                                            class="flex-grow border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:border-amber-500"
                                            placeholder="Nama PIC Pemberi Bantuan">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <button type="button" @click="addNetworkRow()"
                class="w-full py-4 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 hover:border-amber-400 hover:text-amber-500 hover:bg-amber-50/30 transition-all font-bold text-sm uppercase tracking-widest">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Network Case
            </button>
        </div>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
            <span
                class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center mr-2 text-sm font-black">1B</span>
            Technical Cases: GPS
        </h2>
        <div class="space-y-4">
            <template x-for="(row, index) in rows_gps" :key="index">
                <div :class="row.is_monitoring ? 'bg-slate-50 border-slate-200' : 'bg-white border-slate-200 border-l-emerald-500'"
                    class="border rounded-2xl p-5 border-l-4 relative shadow-sm">

                    <button x-show="!row.is_default" type="button" @click="removeGps(index)"
                        class="absolute top-2 right-2 text-rose-400 hover:text-rose-600 p-2 rounded-xl">
                        <i class="fas fa-trash-alt"></i>
                    </button>

                    <input type="hidden" :name="'case_gps[' + index + '][is_monitoring]'"
                        :value="row.is_monitoring ? 1 : 0">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black ml-1">Nama Kegiatan</label>
                            <input type="text" :name="'case_gps[' + index + '][nama_kegiatan]'"
                                x-model="row.nama_kegiatan" :readonly="row.is_monitoring"
                                :class="row.is_monitoring ? 'bg-transparent border-none font-bold text-slate-700' :
                                    'bg-white border-slate-200'"
                                class="w-full border rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500 transition">
                        </div>
                        <template x-if="!row.is_monitoring">
                            <div>
                                <label class="text-[10px] uppercase text-slate-400 font-black ml-1">Jumlah
                                    Kendaraan</label>
                                <input type="text" :name="'case_gps[' + index + '][jumlah_kendaraan]'"
                                    x-model="row.jumlah_kendaraan"
                                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500"
                                    placeholder="Contoh: 5">
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <button type="button" @click="addGpsRow()"
                class="w-full py-4 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 hover:border-emerald-400 hover:text-emerald-500 hover:bg-emerald-50/30 transition-all font-bold text-sm uppercase tracking-widest">
                <i class="fas fa-plus-circle mr-2"></i> Tambah GPS Case
            </button>
        </div>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-bold text-slate-700 mb-4 flex items-center">
            <span
                class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center mr-2 text-sm font-black">2</span>
            General Activities
        </h2>

        <div class="space-y-3">
            <template x-for="(act, index) in activities" :key="index">
                <div
                    class="bg-white border border-slate-200 p-4 border-l-4 border-slate-300 rounded-2xl flex gap-4 items-end shadow-sm group">
                    <div class="flex-grow">
                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1">Deskripsi Kegiatan
                            Lainnya</label>
                        <input type="text" :name="'activity[' + index + '][deskripsi]'" x-model="act.deskripsi"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 outline-none focus:bg-white focus:border-slate-400 transition">
                    </div>
                    <button type="button" @click="removeActivity(index)"
                        class="text-rose-400 p-2.5 hover:bg-rose-50 rounded-xl transition">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </template>
            <button type="button" @click="addActivity()"
                class="text-xs text-amber-600 font-black uppercase tracking-tighter hover:text-amber-700 mt-2 ml-1">
                + Tambah Baris Kegiatan
            </button>
        </div>
    </div>
</div>
