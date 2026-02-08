@extends('layouts.staff')

@section('content')
<div class="container mx-auto px-4 lg:px-0" x-data="kpiForm()">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-header font-bold text-white">Daily Case Reporting</h1>
            <p class="text-slate-400 font-body text-sm md:text-base">Input aktivitas case harian - {{ date('d M Y') }}</p>
        </div>
        <button type="button" @click="addRow()" class="w-full md:w-auto bg-primary hover:bg-accent text-secondary font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i> Tambah Case
        </button>
    </div>

    <form action="{{ route('staff.kpi.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <template x-for="(row, index) in rows" :key="index">
                <div class="organic-card p-5 border-l-4 border-primary relative">

                    <button type="button" @click="removeRow(index)" class="absolute top-2 right-2 text-red-500 hover:bg-red-500/10 p-2 rounded-xl transition">
                        <i class="fas fa-trash-alt"></i>
                    </button>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start lg:items-center">

                        <div class="lg:col-span-4">
                            <label class="text-[10px] uppercase text-primary font-bold ml-1">Deskripsi / Judul Case</label>
                            <input type="text" :name="'case['+index+'][deskripsi]'" x-model="row.deskripsi"
                                class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white focus:border-primary outline-none shadow-inner"
                                placeholder="Contoh: Perbaikan Jaringan Lt.2" required>
                        </div>

                        <div class="grid grid-cols-2 lg:flex lg:col-span-3 gap-4">
                            <div class="w-full">
                                <label class="text-[10px] uppercase text-primary font-bold ml-1">Respons (Min)</label>
                                <input type="number" :name="'case['+index+'][respons]'" x-model="row.respons"
                                    :disabled="row.temuan_sendiri"
                                    class="w-full bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white focus:border-primary outline-none disabled:opacity-30"
                                    placeholder="0" required>
                            </div>
                            <div class="w-full flex flex-col items-center justify-center">
                                <label class="text-[10px] uppercase text-primary font-bold mb-1 text-center">Temuan Sendiri</label>
                                <input type="checkbox" :name="'case['+index+'][temuan_sendiri]'" x-model="row.temuan_sendiri"
                                    @change="if(row.temuan_sendiri) row.respons = 0"
                                    class="w-6 h-6 lg:w-8 lg:h-8 rounded-lg border-white/10 text-primary focus:ring-primary bg-secondary/50">
                            </div>
                        </div>

                        <div class="lg:col-span-5">
                            <label class="text-[10px] uppercase text-primary font-bold ml-1">Penyelesaian Mandiri / Nama PIC</label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <select x-model="row.is_mandiri" :name="'case['+index+'][is_mandiri]'"
                                    class="w-full sm:w-32 bg-secondary/50 border border-white/10 rounded-xl px-3 py-2 text-white text-sm outline-none">
                                    <option value="1">Mandiri</option>
                                    <option value="0">Bantuan</option>
                                </select>
                                <input type="text" :name="'case['+index+'][pic_name]'" x-model="row.pic_name"
                                    x-show="row.is_mandiri == 0"
                                    class="flex-grow bg-secondary/50 border border-white/10 rounded-xl px-4 py-2 text-white focus:border-primary outline-none"
                                    placeholder="Nama PIC yang dihubungi">
                            </div>
                        </div>

                    </div>
                </div>
            </template>
        </div>

        <div class="mt-10 flex flex-col-reverse md:flex-row gap-4 justify-end border-t border-white/5 pt-8 mb-10">
            <button type="submit" name="submit_type" value="draft" class="w-full md:w-auto px-8 py-4 rounded-xl bg-slate-700 text-white font-bold hover:bg-slate-600 transition">
                Simpan Draft
            </button>
            <button type="submit" name="submit_type" value="final" class="w-full md:w-auto px-10 py-4 rounded-xl bg-primary text-secondary font-bold hover:bg-accent transition shadow-lg shadow-primary/20">
                Submit Laporan
            </button>
        </div>
    </form>
</div>

<script>
    function kpiForm() {
        return {
            rows: @json($formattedRows),

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
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            }
        }
    }
</script>
@endsection