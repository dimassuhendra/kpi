@extends('layouts.manager')

@section('content')
<div class="space-y-8">
    {{-- Header & Division Switcher --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-6">
        <div class="space-y-4 w-full md:w-auto">
            <div>
                <h2 class="font-header text-3xl text-white italic">KPI <span class="text-primary">Management</span></h2>
                <p class="text-slate-500 text-sm">Konfigurasi parameter penilaian per divisi.</p>
            </div>

            {{-- Switcher Divisi --}}
            <form action="{{ route('manager.variables.index') }}" method="GET" id="divSwitcher" class="relative max-w-xs">
                <label class="text-[10px] text-primary uppercase font-bold mb-1 block ml-1 tracking-widest">Pilih Divisi:</label>
                <div class="relative">
                    <select name="division_id" onchange="this.form.submit()"
                        class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 outline-none focus:border-primary appearance-none cursor-pointer transition-all hover:bg-white/10">
                        @foreach($allDivisions as $div)
                        <option value="{{ $div->id }}" {{ $selectedDivisionId == $div->id ? 'selected' : '' }} class="bg-darkCard">
                            {{ $div->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </form>
        </div>

        <button onclick="openModal('addVarModal')" class="bg-primary text-white px-6 py-3 rounded-2xl font-bold text-sm hover:scale-105 transition shadow-lg shadow-primary/20">
            <i class="fas fa-plus mr-2"></i> Tambah Variabel ke {{ $allDivisions->find($selectedDivisionId)->name }}
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Matriks Penilaian --}}
        <div class="lg:col-span-2">
            <div class="organic-card overflow-hidden">
                <form action="{{ route('manager.variables.updateWeights') }}" method="POST">
                    @csrf
                    <div class="p-6 border-b border-white/5 bg-white/5 flex justify-between items-center">
                        <h3 class="text-white font-header italic">Scoring Matrix: <span class="text-primary">{{ $allDivisions->find($selectedDivisionId)->name }}</span></h3>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 px-4 py-2 rounded-xl text-xs font-bold hover:bg-emerald-500 hover:text-white transition-all">
                                <i class="fas fa-save mr-1"></i> Simpan Bobot
                            </button>
                        </div>
                    </div>

                    <table class="w-full text-left">
                        <thead class="text-slate-500 text-[10px] uppercase tracking-widest bg-darkCard">
                            <tr>
                                <th class="p-5 font-medium text-primary">Nama Variabel</th>
                                <th class="p-5 font-medium w-32">Bobot (%)</th>
                                <th class="p-5 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($variables as $v)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="p-5 text-white font-medium">{{ $v->variable_name }}</td>
                                <td class="p-5">
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="weights[{{ $v->id }}]" value="{{ $v->weight }}"
                                            class="w-20 bg-secondary border border-white/10 rounded-lg px-2 py-1 text-white text-sm focus:border-primary outline-none transition-all">
                                        <span class="text-slate-600 text-xs">%</span>
                                    </div>
                                </td>
                                <td class="p-5 text-right">
                                    <button type="button" onclick="confirmDelete('{{ route('manager.variables.destroy', $v->id) }}')" class="text-red-500/50 hover:text-red-500 transition-colors p-2">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-10 text-center text-slate-500 italic">
                                    Belum ada variabel untuk divisi ini. Silahkan tambah variabel baru.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        {{-- Panel Kontrol Bobot --}}
        <div class="space-y-6">
            <div class="organic-card p-6 border-t-4 {{ $totalWeight != 100 ? 'border-amber-500' : 'border-primary' }}">
                <h4 class="text-white font-bold mb-4">Total Bobot Divisi</h4>
                <div class="flex items-end gap-2 mb-2">
                    <span class="text-5xl font-header {{ $totalWeight != 100 ? 'text-amber-500' : 'text-primary' }}">
                        {{ number_format($totalWeight, 0) }}
                    </span>
                    <span class="text-slate-500 mb-2">/ 100%</span>
                </div>

                {{-- Progress Bar --}}
                <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden mb-4">
                    <div class="{{ $totalWeight != 100 ? 'bg-amber-500' : 'bg-primary' }} h-full transition-all duration-500"
                        style="width: {{ min($totalWeight, 100) }}%"></div>
                </div>

                {{-- Status Alert --}}
                @if($totalWeight != 100)
                <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl mb-6">
                    <p class="text-[10px] text-amber-500 leading-tight italic text-center">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Total bobot saat ini {{ $totalWeight }}%. Pastikan total bobot adalah 100% agar perhitungan KPI akurat.
                    </p>
                </div>
                @endif

                <form action="{{ route('manager.variables.autoAverage') }}" method="POST">
                    @csrf
                    {{-- Hidden input agar controller tahu divisi mana yang dirata-ratakan --}}
                    <input type="hidden" name="division_id" value="{{ $selectedDivisionId }}">
                    <button type="submit" class="w-full py-4 bg-white/5 border border-white/10 rounded-2xl text-white text-xs font-bold hover:bg-primary hover:border-primary transition-all shadow-sm">
                        <i class="fas fa-magic mr-2"></i> Bagi Rata Bobot (AVG)
                    </button>
                </form>
                <p class="text-[10px] text-slate-500 mt-3 italic text-center leading-relaxed">
                    Fitur ini akan menghitung otomatis bobot rata untuk {{ $variables->count() }} variabel di divisi ini.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH VARIABEL --}}
<div id="addVarModal" class="fixed inset-0 z-[70] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-secondary/90 backdrop-blur-sm" onclick="closeModal('addVarModal')"></div>
        <div class="relative bg-darkCard w-full max-w-md rounded-[30px] border border-white/10 p-8 shadow-2xl animate-in zoom-in duration-200">
            <h4 class="text-xl font-header text-white mb-2">Variabel Baru</h4>
            <p class="text-xs text-slate-400 mb-6">Menambahkan metrik untuk divisi <span class="text-primary font-bold">{{ $allDivisions->find($selectedDivisionId)->name }}</span></p>

            <form action="{{ route('manager.variables.store') }}" method="POST">
                @csrf
                {{-- Input hidden division_id --}}
                <input type="hidden" name="division_id" value="{{ $selectedDivisionId }}">

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] text-slate-500 uppercase font-bold ml-2 tracking-widest">Nama Variabel / Item</label>
                        <input type="text" name="variable_name" required placeholder="Contoh: Kecepatan Respon"
                            class="w-full bg-secondary border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none mt-1 focus:border-primary transition-all">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeModal('addVarModal')" class="flex-1 px-4 py-3 text-slate-400 text-sm font-bold hover:text-white transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-primary text-white font-bold py-3 rounded-xl hover:scale-105 active:scale-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden Form for Delete --}}
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmDelete(url) {
        if (confirm('Hapus variabel ini? Tindakan ini akan mempengaruhi perhitungan KPI staff di divisi ini.')) {
            const form = document.getElementById('deleteForm');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection