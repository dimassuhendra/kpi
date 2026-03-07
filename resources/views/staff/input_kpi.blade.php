@extends('layouts.staff')

@section('content')
    <div class="container mx-auto px-4 lg:px-0" x-data="kpiForm()">

        {{-- MODAL REJECTED ALERT --}}
        {{-- Bagian Modal Alert --}}
        @if ($isRejected)
            <div class="mb-8 p-6 bg-rose-50 border-2 border-rose-200 rounded-3xl">
                <div class="flex items-center gap-4 text-rose-700 mb-2">
                    <div class="w-10 h-10 bg-rose-500 text-white rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-black uppercase">Laporan Perlu Revisi</h3>
                </div>
                <div class="ml-14">
                    <p class="text-rose-600 text-sm italic">
                        "{{ $catatanManager }}" {{-- Ini akan menampilkan isi catatan dari manager --}}
                    </p>
                </div>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">Daily Reporting</h1>
                <p class="text-slate-600 font-body text-sm md:text-base">
                    Unit: <span
                        class="text-amber-600 font-bold">{{ auth()->user()->divisi->nama_divisi ?? 'General' }}</span>
                    <span class="text-slate-400 ml-2">| {{ date('d M Y') }}</span>
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                @if (auth()->user()->divisi_id == 1)
                    <button type="button" @click="addActivity()"
                        class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-tasks mr-2 text-amber-400"></i> + Activity Umum
                    </button>
                    <button type="button" @click="addNetworkRow()"
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

            @if (auth()->user()->divisi_id == 1)
                @include('staff.input_kpi.tac')
            @elseif (auth()->user()->divisi_id == 2)
                @include('staff.input_kpi.infra')
            @else
                @include('staff.input_kpi.backoffice')
            @endif

            <div class="mt-10 flex justify-end border-t border-slate-200 pt-8 mb-20">
                <button type="submit"
                    class="w-full md:w-auto px-12 py-4 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition shadow-lg shadow-amber-200 flex items-center justify-center gap-3 group">
                    <span>Kirim Laporan Hari Ini</span>
                    <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>

    <script>
        function kpiForm() {
            return {
                // Data disuntikkan dari Controller
                rows_network: @json($formattedRows['network']),
                rows_gps: @json($formattedRows['gps']),
                activities: @json($formattedRows['activities']),
                infra_activities: @json($formattedRows['infra']),
                bo_activities: @json($formattedRows['bo']),

                addNetworkRow() {
                    this.rows_network.push({
                        deskripsi: '',
                        respons: '',
                        temuan_sendiri: false,
                        is_mandiri: 1,
                        pic_name: '',
                        is_monitoring: false,
                        is_default: false
                    });
                },
                addGpsRow() {
                    this.rows_gps.push({
                        nama_kegiatan: '',
                        jumlah_kendaraan: '',
                        is_monitoring: false,
                        is_default: false
                    });
                },
                addActivity() {
                    this.activities.push({
                        deskripsi: ''
                    });
                },
                addInfraActivity() {
                    this.infra_activities.push({
                        kategori: 'Network',
                        nama_kegiatan: '',
                        deskripsi: ''
                    });
                },
                addBoActivity() {
                    this.bo_activities.push({
                        judul: '',
                        deskripsi: ''
                    });
                },
                removeNetwork(index) {
                    this.rows_network.splice(index, 1);
                },
                removeGps(index) {
                    this.rows_gps.splice(index, 1);
                },
                removeActivity(index) {
                    this.activities.splice(index, 1);
                },
                removeInfra(index) {
                    this.infra_activities.splice(index, 1);
                },
                removeBo(index) {
                    this.bo_activities.splice(index, 1);
                }
            }
        }
    </script>
@endsection
