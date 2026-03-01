@extends('layouts.staff')

@section('content')
    <div class="container mx-auto px-4 lg:px-0" x-data="kpiForm()">
        {{-- HEADER & BUTTONS --}}
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900">Daily Reporting</h1>
                <p class="text-slate-600 font-body text-sm md:text-base">
                    Unit: <span
                        class="text-amber-600 font-bold">{{ auth()->user()->divisi->nama_divisi ?? 'General' }}</span>
                    <span class="text-slate-400"> - {{ date('d M Y') }}</span>
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                @if (auth()->user()->divisi_id == 1)
                    <button type="button" @click="addActivity()"
                        class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-tasks mr-2 text-amber-400"></i> + Activity Umum
                    </button>
                    <button type="button" @click="addRow()"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i> + Technical Case
                    </button>
                @elseif (auth()->user()->divisi_id == 2)
                    <button type="button" @click="addInfraActivity()"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-tools mr-2"></i> + Tambah Kegiatan Infra
                    </button>
                @else
                    <button type="button" @click="addBoActivity()"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i> + Tambah Kegiatan Backoffice
                    </button>
                @endif
            </div>
        </div>

        <form action="{{ route('staff.kpi.store') }}" method="POST">
            @csrf

            {{-- 1. VIEW TAC (DIVISI 1) --}}
            @if (auth()->user()->divisi_id == 1)
                @include('staff.input_kpi.tac')
                {{-- 2. VIEW INFRASTRUKTUR --}}
            @elseif (auth()->user()->divisi_id == 2)
                @include('staff.input_kpi.infra')
                {{-- 3. VIEW BACKOFFICE --}}
            @else
                @include('staff.input_kpi.backoffice')
            @endif

            {{-- SUBMIT BUTTON --}}
            <div class="mt-10 flex justify-end border-t border-slate-200 pt-8 mb-20">
                <button type="submit"
                    class="w-full md:w-auto px-12 py-4 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition shadow-lg shadow-amber-200 flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Semua Laporan
                </button>
            </div>
        </form>
    </div>

    <script>
        function kpiForm() {
            return {
                rows: @json($formattedRows ?? []),
                activities: [],
                infra_activities: [],
                bo_activities: [],
                init() {
                    const divisiId = {{ auth()->user()->divisi_id }};
                    if (divisiId == 2 && this.infra_activities.length === 0) this.addInfraActivity();
                    else if (divisiId != 1 && divisiId != 2 && this.bo_activities.length === 0) this.addBoActivity();
                },
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
