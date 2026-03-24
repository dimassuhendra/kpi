<div>
    <div class="mb-10" x-data="{ ada_laporan_gps: {{ isset($report) && $report->bukti_report_gps ? 'true' : 'false' }} }">
        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
            <span
                class="w-8 h-8 rounded-lg bg-indigo-500 text-white flex items-center justify-center mr-2 text-sm font-black">1</span>
            Documentation & Reporting
        </h2>

        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm space-y-6">

            <div>
                <label class="text-xs uppercase text-slate-500 font-black block mb-2">Shift Kerja Hari Ini <span
                        class="text-red-500">*</span></label>
                <select name="shift_id" required
                    class="w-full lg:w-1/2 border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-indigo-500 font-bold text-slate-700">
                    <option value="">-- Pilih Shift Anda --</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}"
                            {{ isset($report) && $report->shift_id == $shift->id ? 'selected' : '' }}>
                            {{ $shift->nama_shift }} ({{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <hr class="border-slate-100">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-slate-800 text-sm">Ada Laporan GPS Hari Ini?</p>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="ada_laporan_gps" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500">
                            </div>
                        </label>
                    </div>

                    <div x-show="ada_laporan_gps" x-transition class="mt-4 pt-4 border-t border-slate-200 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-700 text-sm">Report GPS on Time?</p>
                                <p class="text-[10px] text-slate-400">Sesuai jam pelaporan divisi</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_gps_ontime" value="1" class="sr-only peer"
                                    {{ isset($report) && $report->is_gps_ontime ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500">
                                </div>
                            </label>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-1 block">Upload Bukti
                                Report GPS</label>
                            <input type="file" name="bukti_report_gps" accept="image/*"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-center">
                    <div class="mb-2">
                        <p class="font-bold text-slate-800 text-sm mb-2">Input Dashboard KPI</p>
                        <div
                            class="bg-indigo-50/50 text-indigo-600 border border-indigo-100 p-3 rounded-lg text-xs leading-relaxed">
                            <i class="fas fa-clock mr-1"></i> Ketepatan waktu input Dashboard KPI Anda (Maks. 2 Jam
                            setelah jam pulang Shift) <strong>akan terekam secara otomatis</strong> oleh sistem saat
                            Anda menekan tombol Submit Laporan di bawah. Tidak perlu melampirkan bukti gambar.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
            <span
                class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center mr-2 text-sm font-black">2A</span>
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

                    <div class="grid grid-cols-1 gap-5">

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                            <div :class="row.is_monitoring ? 'lg:col-span-12' : 'lg:col-span-8'">
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
                                <div class="lg:col-span-4">
                                    <label class="text-[10px] uppercase text-slate-400 font-black ml-1">No. Tiket
                                        (Opsional)</label>
                                    <input type="text" :name="'case_network[' + index + '][nomor_tiket]'"
                                        x-model="row.nomor_tiket"
                                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:border-amber-500"
                                        placeholder="#TKT-1234">
                                </div>
                            </template>
                        </div>

                        <template x-if="!row.is_monitoring">
                            <div class="lg:col-span-5 grid grid-cols-1 gap-3 border-r border-slate-200 pr-4">

                                <div class="flex items-center gap-3 bg-white p-2 rounded-lg border border-slate-200">
                                    <input type="checkbox" :name="'case_network[' + index + '][temuan_sendiri]'"
                                        x-model="row.temuan_sendiri" @change="if(row.temuan_sendiri) row.respons = 0"
                                        class="w-5 h-5 rounded text-amber-500 focus:ring-amber-500 border-slate-300">
                                    <label class="text-xs font-bold text-slate-600">Deteksi Dini / Temuan
                                        Sendiri</label>
                                </div>

                                <div x-show="!row.temuan_sendiri" class="grid grid-cols-3 gap-2 items-center mt-1">
                                    <div class="col-span-1">
                                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1">Respons
                                            (Min)</label>
                                        <input type="number" :name="'case_network[' + index + '][respons]'"
                                            x-model="row.respons"
                                            class="w-full border border-slate-200 rounded-lg px-3 py-2 outline-none">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="text-[10px] uppercase text-slate-400 font-black ml-1">Bukti SS
                                            Respon</label>
                                        <input type="file" :name="'case_network[' + index + '][bukti_respon_time]'"
                                            accept="image/*"
                                            class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                                    </div>
                                </div>

                                <div x-show="row.temuan_sendiri" class="mt-1">
                                    <label class="text-[10px] uppercase text-rose-400 font-black ml-1">Bukti SS Deteksi
                                        Dini</label>
                                    <input type="file" :name="'case_network[' + index + '][bukti_deteksi_dini]'"
                                        accept="image/*"
                                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-600">
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
                class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center mr-2 text-sm font-black">2B</span>
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
                class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center mr-2 text-sm font-black">3</span>
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
