@extends('layouts.staff')

@section('content')
    <div class="container mx-auto px-4 lg:px-0" x-data="kpiForm()">
        {{-- HEADER & BUTTONS --}}
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-header font-bold text-white">Daily Reporting</h1>
                <p class="text-slate-400 font-body text-sm md:text-base">Input aktivitas harian - {{ date('d M Y') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <button type="button" @click="addActivity()"
                    class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg flex items-center justify-center">
                    <i class="fas fa-tasks mr-2"></i> + Activity Umum
                </button>
                <button type="button" @click="addRow()"
                    class="bg-primary hover:bg-accent text-secondary font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i> + Technical Case
                </button>
            </div>
        </div>

        <form action="{{ route('staff.kpi.store') }}" method="POST">
            @csrf

            {{-- SECTION 1: TECHNICAL CASES --}}
            <div class="mb-10">
                <h2 class="text-xl font-bold text-primary mb-4 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center mr-2 text-sm">1</span>
                    Technical Cases (KPI)
                </h2>
                <div class="space-y-6">
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="organic-card p-5 border-l-4 border-primary relative">
                            <button type="button" @click="removeRow(index)"
                                class="absolute top-2 right-2 text-red-500 hover:bg-red-500/10 p-2 rounded-xl transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start lg:items-center">
                                <div class="lg:col-span-4">
                                    <label class="text-[10px] uppercase text-primary font-bold ml-1">Deskripsi / Judul
                                        Case</label>
                                    <input type="text" :name="'case[' + index + '][deskripsi]'" x-model="row.deskripsi"
                                        class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white focus:border-primary outline-none shadow-inner"
                                        placeholder="Contoh: Perbaikan Jaringan Lt.2" required>
                                </div>

                                <div class="grid grid-cols-2 lg:flex lg:col-span-3 gap-4">
                                    <div class="w-full">
                                        <label class="text-[10px] uppercase text-primary font-bold ml-1">Respons
                                            (Min)</label>
                                        <input type="number" :name="'case[' + index + '][respons]'" x-model="row.respons"
                                            :readonly="row.temuan_sendiri"
                                            :class="row.temuan_sendiri ? 'opacity-30 pointer-events-none' : ''"
                                            class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white focus:border-primary outline-none"
                                            placeholder="0" required>
                                    </div>
                                    <div class="w-full flex flex-col items-center justify-center">
                                        <label class="text-[10px] uppercase text-primary font-bold mb-1 text-center">Temuan
                                            Sendiri</label>
                                        <input type="checkbox" :name="'case[' + index + '][temuan_sendiri]'"
                                            x-model="row.temuan_sendiri" @change="if(row.temuan_sendiri) row.respons = 0"
                                            class="w-6 h-6 lg:w-8 lg:h-8 rounded-lg border-white/10 text-primary focus:ring-primary bg-secondary/50">
                                    </div>
                                </div>

                                <div class="lg:col-span-5">
                                    <label class="text-[10px] uppercase text-primary font-bold ml-1">Penyelesaian Mandiri /
                                        Nama PIC</label>
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <select x-model="row.is_mandiri" :name="'case[' + index + '][is_mandiri]'"
                                            class="w-full sm:w-32 bg-secondary/50 border border-white/10 rounded-xl px-3 py-2 text-white text-sm outline-none">
                                            <option value="1">Mandiri</option>
                                            <option value="0">Bantuan</option>
                                        </select>
                                        <input type="text" :name="'case[' + index + '][pic_name]'" x-model="row.pic_name"
                                            x-show="row.is_mandiri == 0"
                                            class="flex-grow bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white focus:border-primary outline-none"
                                            placeholder="Nama PIC yang dihubungi">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- SECTION 2: GENERAL ACTIVITIES --}}
            <div class="mb-10">
                <h2 class="text-xl font-bold text-slate-300 mb-4 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-slate-700 flex items-center justify-center mr-2 text-sm">2</span>
                    General Activities (Non-KPI)
                </h2>
                <div class="space-y-4">
                    <template x-for="(act, index) in activities" :key="index">
                        <div
                            class="organic-card p-4 border-l-4 border-slate-500 relative flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex-grow w-full">
                                <label class="text-[10px] uppercase text-slate-400 font-bold ml-1">Deskripsi Kegiatan
                                    Activity</label>
                                <input type="text" :name="'activity[' + index + '][deskripsi]'" x-model="act.deskripsi"
                                    class="w-full bg-secondary/30 border border-white/5 rounded-xl px-4 py-2 text-white focus:border-slate-500 outline-none"
                                    placeholder="Contoh: Merapikan kabel server atau Maintenance rutin bulanan" required>
                            </div>
                            <button type="button" @click="removeActivity(index)"
                                class="bg-red-500/10 text-red-500 p-2.5 rounded-xl hover:bg-red-500 hover:text-white transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </template>
                    <div x-show="activities.length === 0"
                        class="text-center py-8 border-2 border-dashed border-white/5 rounded-2xl text-slate-500">
                        Belum ada activity umum yang ditambahkan.
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-end border-t border-white/5 pt-8 mb-10">
                <button type="submit"
                    class="w-full md:w-auto px-10 py-4 rounded-xl bg-primary text-secondary font-bold hover:bg-accent transition shadow-lg shadow-primary/20">
                    Submit Semua Laporan
                </button>
            </div>
        </form>
    </div>

    <script>
        function kpiForm() {
            return {
                
                rows: [],
                activities: [],

                addRow() {
                    this.rows.push({
                        deskripsi: '',
                        respons: '',
                        temuan_sendiri: false,
                        is_mandiri: 1,
                        pic_name: ''
                    });
                },
                removeRow(index) {
                    if (this.rows.length > 0) {
                        this.rows.splice(index, 1);
                    }
                },

                addActivity() {
                    this.activities.push({
                        deskripsi: ''
                    });
                },
                removeActivity(index) {
                    this.activities.splice(index, 1);
                }
            }
        }
    </script>
@endsection
