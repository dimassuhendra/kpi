@extends('layouts.staff')

@section('content')
    <div class="container mx-auto px-4 lg:px-0" x-data="kpiForm()">
        {{-- HEADER & BUTTONS --}}
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-header font-bold text-white">Daily Reporting</h1>
                <p class="text-slate-400 font-body text-sm md:text-base">
                    Unit: <span class="text-primary font-bold">{{ auth()->user()->divisi->nama_divisi ?? 'General' }}</span>
                    - {{ date('d M Y') }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                @if (auth()->user()->divisi_id == 1)
                    {{-- TOMBOL KHUSUS TAC --}}
                    <button type="button" @click="addActivity()"
                        class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg flex items-center justify-center">
                        <i class="fas fa-tasks mr-2"></i> + Activity Umum
                    </button>
                    <button type="button" @click="addRow()"
                        class="bg-primary hover:bg-accent text-secondary font-bold py-3 px-6 rounded-xl transition-all shadow-lg flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i> + Technical Case
                    </button>
                @elseif (auth()->user()->divisi_id == 2)
                    {{-- TOMBOL KHUSUS INFRA --}}
                    <button type="button" @click="addInfraActivity()"
                        class="bg-primary hover:bg-accent text-secondary font-bold py-3 px-6 rounded-xl transition-all shadow-lg flex items-center justify-center">
                        <i class="fas fa-tools mr-2"></i> + Tambah Kegiatan Infra
                    </button>
                @else
                    {{-- TOMBOL KHUSUS BACKOFFICE --}}
                    <button type="button" @click="addBoActivity()"
                        class="bg-primary hover:bg-accent text-secondary font-bold py-3 px-6 rounded-xl transition-all shadow-lg flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i> + Tambah Kegiatan Backoffice
                    </button>
                @endif
            </div>
        </div>

        <form action="{{ route('staff.kpi.store') }}" method="POST">
            @csrf

            {{-- 1. VIEW TAC (DIVISI 1) --}}
            @if (auth()->user()->divisi_id == 1)
                {{-- SECTION 1: TECHNICAL CASES --}}
                <div class="mb-10" x-show="rows.length > 0">
                    <h2 class="text-xl font-bold text-primary mb-4 flex items-center">
                        <span
                            class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center mr-2 text-sm">1</span>
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
                                        <label class="text-[10px] uppercase text-primary font-bold ml-1">Deskripsi
                                            Case</label>
                                        <input type="text" :name="'case[' + index + '][deskripsi]'"
                                            x-model="row.deskripsi"
                                            class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white focus:border-primary outline-none"
                                            placeholder="Deskripsi perbaikan..." required>
                                    </div>
                                    <div class="grid grid-cols-2 lg:flex lg:col-span-3 gap-4">
                                        <div class="w-full">
                                            <label class="text-[10px] uppercase text-primary font-bold ml-1">Respons
                                                (Min)</label>
                                            <input type="number" :name="'case[' + index + '][respons]'"
                                                x-model="row.respons" :readonly="row.temuan_sendiri"
                                                :class="row.temuan_sendiri ? 'opacity-30 pointer-events-none' : ''"
                                                class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white outline-none"
                                                required>
                                        </div>
                                        <div class="w-full flex flex-col items-center justify-center">
                                            <label
                                                class="text-[10px] uppercase text-primary font-bold mb-1 text-center">Temuan</label>
                                            <input type="checkbox" :name="'case[' + index + '][temuan_sendiri]'"
                                                x-model="row.temuan_sendiri"
                                                @change="if(row.temuan_sendiri) row.respons = 0"
                                                class="w-6 h-6 rounded-lg border-white/10 text-primary bg-secondary/50">
                                        </div>
                                    </div>
                                    <div class="lg:col-span-5">
                                        <label
                                            class="text-[10px] uppercase text-primary font-bold ml-1">Penyelesaian</label>
                                        <div class="flex gap-2">
                                            <select x-model="row.is_mandiri" :name="'case[' + index + '][is_mandiri]'"
                                                class="bg-secondary/50 border border-white/10 rounded-xl px-3 py-2 text-white text-sm">
                                                <option value="1">Mandiri</option>
                                                <option value="0">Bantuan</option>
                                            </select>
                                            <input type="text" :name="'case[' + index + '][pic_name]'"
                                                x-model="row.pic_name" x-show="row.is_mandiri == 0"
                                                class="flex-grow bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white outline-none"
                                                placeholder="Nama PIC">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- SECTION 2: ACTIVITY UMUM TAC --}}
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-slate-300 mb-4 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-slate-700 flex items-center justify-center mr-2 text-sm">2</span>
                        General Activities
                    </h2>
                    <div class="space-y-4">
                        <template x-for="(act, index) in activities" :key="index">
                            <div class="organic-card p-4 border-l-4 border-slate-500 flex gap-4 items-end">
                                <div class="flex-grow">
                                    <label class="text-[10px] uppercase text-slate-400 font-bold ml-1">Deskripsi</label>
                                    <input type="text" :name="'activity[' + index + '][deskripsi]'"
                                        x-model="act.deskripsi"
                                        class="w-full bg-secondary/30 border border-white/5 rounded-xl px-4 py-2 text-white outline-none"
                                        required>
                                </div>
                                <button type="button" @click="removeActivity(index)" class="text-red-500 p-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 2. VIEW INFRASTRUKTUR (DIVISI 2) --}}
            @elseif (auth()->user()->divisi_id == 2)
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-primary mb-4 flex items-center">
                        <i class="fas fa-tools mr-3"></i> Infrastruktur Daily Activities
                    </h2>
                    <div class="space-y-6">
                        <template x-for="(infra, index) in infra_activities" :key="index">
                            <div class="organic-card p-6 border-l-4 border-primary relative animate-fadeIn">
                                <button type="button" @click="removeInfraActivity(index)"
                                    class="absolute top-4 right-4 text-rose-500 hover:bg-rose-500/10 p-2 rounded-xl transition">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </button>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                    <div class="md:col-span-4 space-y-4">
                                        <div>
                                            <label
                                                class="text-[10px] uppercase text-primary font-bold ml-1 tracking-widest">Kategori</label>
                                            <select :name="'infra_activity[' + index + '][kategori]'"
                                                x-model="infra.kategori"
                                                class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-primary transition">
                                                <option value="Network">Network</option>
                                                <option value="CCTV">CCTV</option>
                                                <option value="GPS">GPS Tracking</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="text-[10px] uppercase text-primary font-bold ml-1 tracking-widest">Nama
                                                Kegiatan</label>
                                            <input type="text" :name="'infra_activity[' + index + '][nama_kegiatan]'"
                                                x-model="infra.nama_kegiatan"
                                                class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-primary"
                                                placeholder="Contoh: Pemasangan NVR Baru" required>
                                        </div>
                                    </div>
                                    <div class="md:col-span-8">
                                        <label
                                            class="text-[10px] uppercase text-primary font-bold ml-1 tracking-widest">Deskripsi
                                            Detail Pekerjaan</label>
                                        <textarea :name="'infra_activity[' + index + '][deskripsi]'" x-model="infra.deskripsi" rows="4"
                                            class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-3 text-white outline-none focus:border-primary resize-none"
                                            placeholder="Jelaskan secara detail apa yang dikerjakan..." required></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="infra_activities.length === 0"
                            class="text-center py-20 border-2 border-dashed border-white/5 rounded-3xl">
                            <i class="fas fa-hard-hat text-4xl text-slate-700 mb-4 block"></i>
                            <p class="text-slate-500 italic uppercase text-xs tracking-widest">Belum ada laporan kegiatan
                                infrastruktur</p>
                        </div>
                    </div>
                </div>

                {{-- 3. VIEW BACKOFFICE (DIVISI LAINNYA/3) --}}
            @else
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-primary mb-4 flex items-center">
                        <i class="fas fa-briefcase mr-3"></i> Backoffice Daily Activities
                    </h2>
                    <div class="space-y-4">
                        <template x-for="(bo, index) in bo_activities" :key="index">
                            <div class="organic-card p-6 border-l-4 border-primary relative animate-fadeIn">
                                <button type="button" @click="removeBoActivity(index)"
                                    class="absolute top-4 right-4 text-rose-500 hover:bg-rose-500/10 p-2 rounded-xl transition">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </button>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="text-[10px] uppercase text-primary font-bold ml-1">Judul
                                            Kegiatan</label>
                                        <input type="text" :name="'bo_activity[' + index + '][judul]'"
                                            x-model="bo.judul"
                                            class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white outline-none focus:border-primary"
                                            placeholder="Contoh: Rekap Data Absensi" required>
                                    </div>
                                    <div>
                                        <label class="text-[10px] uppercase text-primary font-bold ml-1">Detail
                                            Activity</label>
                                        <textarea :name="'bo_activity[' + index + '][deskripsi]'" x-model="bo.deskripsi" rows="3"
                                            class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white outline-none focus:border-primary resize-none"
                                            placeholder="Jelaskan detail apa yang dikerjakan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="bo_activities.length === 0"
                            class="text-center py-20 border-2 border-dashed border-white/5 rounded-3xl">
                            <i class="fas fa-folder-open text-4xl text-slate-700 mb-4 block"></i>
                            <p class="text-slate-500 italic uppercase text-xs tracking-widest">Belum ada laporan kegiatan
                                backoffice</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SUBMIT BUTTON --}}
            <div class="mt-10 flex justify-end border-t border-white/5 pt-8 mb-20">
                <button type="submit"
                    class="w-full md:w-auto px-12 py-4 rounded-2xl bg-primary text-secondary font-bold hover:bg-accent transition shadow-xl shadow-primary/20 flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Semua Laporan Hari Ini
                </button>
            </div>
        </form>
    </div>

    <script>
        function kpiForm() {
            return {
                // State
                rows: @json($formattedRows ?? []),
                activities: [],
                infra_activities: [],
                bo_activities: [],

                init() {
                    const divisiId = {{ auth()->user()->divisi_id }};
                    // Auto-tambah baris jika masih kosong saat load
                    if (divisiId == 2 && this.infra_activities.length === 0) {
                        this.addInfraActivity();
                    } else if (divisiId != 1 && divisiId != 2 && this.bo_activities.length === 0) {
                        this.addBoActivity();
                    }
                },

                // Method TAC
                addRow() {
                    this.rows.push({
                        deskripsi: '',
                        respons: 0,
                        temuan_sendiri: false,
                        is_mandiri: 1,
                        pic_name: ''
                    });
                },
                removeRow(index) {
                    this.rows.splice(index, 1);
                },
                addActivity() {
                    this.activities.push({
                        deskripsi: ''
                    });
                },
                removeActivity(index) {
                    this.activities.splice(index, 1);
                },

                // Method INFRA
                addInfraActivity() {
                    this.infra_activities.push({
                        kategori: 'Network',
                        nama_kegiatan: '',
                        deskripsi: ''
                    });
                },
                removeInfraActivity(index) {
                    this.infra_activities.splice(index, 1);
                },

                // Method BACKOFFICE
                addBoActivity() {
                    this.bo_activities.push({
                        judul: '',
                        deskripsi: ''
                    });
                },
                removeBoActivity(index) {
                    this.bo_activities.splice(index, 1);
                }
            }
        }
    </script>
@endsection
