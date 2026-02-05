@extends('layouts.staff')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ tickets: [{ number: '', time: '', detected: false }], showModal: false }">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="font-header text-4xl text-primary">Input Laporan Harian</h2>
            <p class="font-body text-secondary opacity-70">Lengkapi data pengerjaan tiket Anda hari ini.</p>
        </div>
        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-accent/20">
            <span class="text-primary font-bold"><i class="fas fa-calendar-alt mr-2"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <form x-ref="kpiForm" action="{{ route('staff.kpi.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-6 border border-white">
            <div class="bg-primary/5 px-8 py-4 border-b border-accent/10">
                <h3 class="font-header text-xl text-primary flex items-center">
                    <i class="fas fa-ticket-alt mr-3"></i> Daftar Tiket / Case TAC
                </h3>
            </div>
            <div class="p-8">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-secondary opacity-50 text-xs uppercase tracking-wider">
                            <th class="pb-4 px-2">Nama Case</th>
                            <th class="pb-4 px-2">Response Time (Min)</th>
                            <th class="pb-4 px-2 text-center">Problem Detected?</th>
                            <th class="pb-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(ticket, index) in tickets" :key="index">
                            <tr class="group">
                                <td class="py-2 px-2">
                                    <input type="text" :name="`tickets[${index}][number]`" x-model="ticket.number"
                                        class="w-full rounded-xl border-none bg-background p-3 focus:ring-2 focus:ring-secondary font-body" placeholder="TKT-001" required>
                                </td>
                                <td class="py-2 px-2">
                                    <input type="number" :name="`tickets[${index}][time]`" x-model="ticket.time"
                                        class="w-full rounded-xl border-none bg-background p-3 focus:ring-2 focus:ring-secondary font-body" placeholder="15" required>
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <input type="checkbox" :name="`tickets[${index}][detected]`" x-model="ticket.detected"
                                        class="w-6 h-6 rounded-lg border-none bg-background text-secondary focus:ring-0">
                                </td>
                                <td class="py-2 px-2 text-right">
                                    <button type="button" @click="tickets.splice(index, 1)" x-show="tickets.length > 1"
                                        class="text-red-400 hover:text-red-600 transition">
                                        <i class="fas fa-times-circle text-xl"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <button type="button" @click="tickets.push({ number: '', time: '', detected: false })"
                    class="mt-6 flex items-center justify-center w-full py-3 rounded-xl border-2 border-dashed border-accent text-secondary font-bold hover:bg-accent/5 transition">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Baris Tiket
                </button>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 mb-8 border border-white">
            <h3 class="font-header text-xl text-primary mb-6 flex items-center">
                <i class="fas fa-tasks mr-3"></i> Variabel Pendukung
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($variables->whereIn('input_type', ['dropdown', 'boolean']) as $var)
                <div class="space-y-2">
                    <label class="font-bold text-primary block ml-1">{{ $var->variable_name }}</label>
                    @if($var->input_type == 'dropdown')
                    <select name="vars[{{ $var->id }}]" class="w-full rounded-xl border-none bg-background p-4 focus:ring-2 focus:ring-secondary font-body">
                        @foreach($var->scoring_matrix as $key => $value)
                        <option value="{{ $key }}">{{ str_replace('_', ' ', ucfirst($key)) }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
            <button type="button" @click="showModal = true" class="flex-1 bg-primary text-white px-10 py-5 rounded-2xl font-header text-2xl shadow-xl hover:bg-secondary transition-all transform active:scale-95">
                KIRIM LAPORAN <i class="fas fa-paper-plane ml-3"></i>
            </button>
            <a href="{{ route('staff.dashboard') }}" class="px-10 py-5 rounded-2xl font-header text-2xl text-primary/50 hover:bg-gray-200 transition text-center">
                BATAL
            </a>
        </div>

        <div x-show="showModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl transform transition-all"
                @click.away="showModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100">

                <div class="text-center">
                    <div class="w-20 h-20 bg-background rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-question-circle text-4xl text-secondary"></i>
                    </div>
                    <h3 class="font-header text-2xl text-primary mb-2">Konfirmasi Laporan</h3>
                    <p class="font-body text-gray-500 mb-8">
                        Apakah Anda yakin data yang dimasukkan sudah benar? Laporan yang dikirim akan diproses oleh Manager.
                    </p>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="button" @click="$refs.kpiForm.submit()" class="w-full bg-secondary text-white py-4 rounded-xl font-header text-lg shadow-lg hover:bg-primary transition">
                        Ya, Kirim Sekarang
                    </button>
                    <button type="button" @click="showModal = false" class="w-full bg-gray-100 text-gray-500 py-4 rounded-xl font-header text-lg hover:bg-gray-200 transition">
                        Periksa Kembali
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection