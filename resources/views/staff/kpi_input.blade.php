@extends('layouts.staff')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="{ 
    tickets: [{ number: '', time: '', detected: false }], 
    showModal: false,
    updateTime(index) {
        if(this.tickets[index].detected) {
            this.tickets[index].time = 0;
        }
    }
}">

    {{-- Notifikasi System --}}
    <div class="mb-6">
        @if (session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-500 p-4 rounded-2xl flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-2xl flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-2xl">
            <div class="font-bold mb-1">Terjadi kesalahan validasi:</div>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-10 gap-4">
        <div>
            <h2 class="font-header text-2xl md:text-4xl text-primary">Input Laporan Harian</h2>
            <p class="font-body text-slate-400 text-sm md:text-base opacity-80">Input daftar tiket yang Anda kerjakan hari ini.</p>
        </div>
        <div class="bg-darkCard px-5 py-2.5 rounded-2xl border border-white/5 shadow-lg">
            <span class="text-primary font-bold text-xs md:text-sm">
                <i class="fas fa-calendar-alt mr-2"></i> {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <form x-ref="kpiForm" action="{{ route('staff.kpi.store') }}" method="POST">
        @csrf

        {{-- Section 1: Tiket (Daily Activity) --}}
        <div class="organic-card bg-darkCard overflow-hidden mb-8">
            <div class="bg-white/5 px-6 md:px-8 py-4 border-b border-white/5">
                <h3 class="font-header text-lg md:text-xl text-primary flex items-center">
                    <i class="fas fa-ticket-alt mr-3 text-sm md:text-base"></i> Daily Activity
                </h3>
            </div>

            <div class="p-4 md:p-8">
                {{-- Desktop View Table --}}
                <div class="hidden md:block">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-500 text-[10px] uppercase tracking-[0.2em] font-bold">
                                <th class="pb-4 px-2">Nama Case / Nomor Tiket</th>
                                <th class="pb-4 px-2">Response Time (Min)</th>
                                <th class="pb-4 px-2 text-center">Problem Detected?</th>
                                <th class="pb-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(ticket, index) in tickets" :key="index">
                                <tr class="group border-b border-white/5 last:border-none">
                                    <td class="py-4 px-2">
                                        <input type="text" :name="`tickets[${index}][number]`" x-model="ticket.number"
                                            class="w-full rounded-xl border-none bg-slate-900/50 p-3 focus:ring-2 focus:ring-primary text-slate-200 placeholder-slate-600" placeholder="Contoh: TKT-001 atau Nama Client" required>
                                    </td>
                                    <td class="py-4 px-2">
                                        <input type="number" :name="`tickets[${index}][time]`" x-model="ticket.time"
                                            :readonly="ticket.detected"
                                            :class="ticket.detected ? 'bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20' : 'bg-slate-900/50 text-slate-200'"
                                            class="w-full rounded-xl border-none p-3 focus:ring-2 focus:ring-primary transition-all duration-300" placeholder="15" required>
                                    </td>
                                    <td class="py-4 px-2 text-center">
                                        <input type="checkbox" :name="`tickets[${index}][detected]`" x-model="ticket.detected"
                                            @change="updateTime(index)"
                                            class="w-6 h-6 rounded-lg border-none bg-slate-900 text-primary focus:ring-0 focus:ring-offset-0 cursor-pointer">
                                    </td>
                                    <td class="py-4 px-2 text-right">
                                        <button type="button" @click="tickets.splice(index, 1)" x-show="tickets.length > 1"
                                            class="text-slate-500 hover:text-red-400 transition-colors p-2">
                                            <i class="fas fa-minus-circle text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View Card List --}}
                <div class="md:hidden space-y-4">
                    <template x-for="(ticket, index) in tickets" :key="index">
                        <div class="p-4 bg-slate-900/30 rounded-2xl border border-white/5 relative" :class="ticket.detected ? 'border-emerald-500/30 bg-emerald-500/5' : ''">
                            <button type="button" @click="tickets.splice(index, 1)" x-show="tickets.length > 1"
                                class="absolute top-2 right-2 text-red-500/50 p-2">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-[10px] uppercase text-slate-500 font-bold mb-1 block">Nama Case</label>
                                    <input type="text" x-model="ticket.number" :name="`tickets[${index}][number]`"
                                        class="w-full rounded-lg border-none bg-darkCard p-2.5 text-sm text-slate-200" placeholder="TKT-001" required>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] uppercase text-slate-500 font-bold mb-1 block">Time (Min)</label>
                                        <input type="number" x-model="ticket.time" :name="`tickets[${index}][time]`"
                                            :readonly="ticket.detected"
                                            :class="ticket.detected ? 'text-emerald-400 font-bold' : 'text-slate-200'"
                                            class="w-full rounded-lg border-none bg-darkCard p-2.5 text-sm" placeholder="15" required>
                                    </div>
                                    <div class="flex flex-col items-center justify-center bg-darkCard rounded-xl border border-white/5">
                                        <label class="text-[10px] uppercase text-slate-500 font-bold mb-1 block text-center">Problem?</label>
                                        <input type="checkbox" x-model="ticket.detected" :name="`tickets[${index}][detected]`"
                                            @change="updateTime(index)"
                                            class="w-6 h-6 rounded border-none bg-slate-800 text-primary">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="tickets.push({ number: '', time: '', detected: false })"
                    class="mt-6 flex items-center justify-center w-full py-3 md:py-4 rounded-2xl border-2 border-dashed border-primary/30 text-primary font-bold hover:bg-primary/5 transition-all text-sm uppercase tracking-widest">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Baris Tiket
                </button>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col md:flex-row gap-3 md:gap-4 mb-10">
            <button type="button" @click="showModal = true" class="order-1 md:order-2 flex-1 bg-primary text-white px-8 py-4 md:py-5 rounded-3xl font-header text-lg md:text-2xl shadow-xl shadow-primary/20 hover:bg-emerald-600 transition-all transform active:scale-95 flex items-center justify-center">
                KIRIM LAPORAN <i class="fas fa-paper-plane ml-3 text-sm md:text-xl"></i>
            </button>
            <a href="{{ route('staff.dashboard') }}" class="order-2 md:order-1 px-8 py-4 md:py-5 rounded-3xl font-header text-lg md:text-2xl text-slate-500 hover:bg-white/5 transition text-center uppercase tracking-widest flex items-center justify-center">
                BATAL
            </a>
        </div>

        {{-- Modal Konfirmasi --}}
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">

            <div class="bg-darkCard border border-white/10 rounded-[2.5rem] max-w-md w-full p-6 md:p-10 shadow-2xl relative overflow-hidden"
                @click.away="showModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-10"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent"></div>

                <div class="text-center">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-heart text-3xl md:text-4xl text-primary animate-pulse"></i>
                    </div>
                    <h3 class="font-header text-xl md:text-2xl text-white mb-2">Konfirmasi Laporan</h3>
                    <p class="font-body text-slate-400 text-sm md:text-base mb-8 leading-relaxed">
                        Data tiket Anda akan dikirim ke Manager untuk divalidasi dan dinilai. Pastikan data sudah benar.
                    </p>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="button" @click="$refs.kpiForm.submit()" class="w-full bg-primary text-white py-4 rounded-2xl font-header text-lg hover:bg-emerald-600 transition-all shadow-lg shadow-primary/20">
                        Ya, Kirim Sekarang
                    </button>
                    <button type="button" @click="showModal = false" class="w-full bg-white/5 text-slate-500 py-4 rounded-2xl font-header text-lg hover:bg-white/10 transition-all">
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

    input[type='number']::-webkit-inner-spin-button,
    input[type='number']::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    @media (max-width: 640px) {

        input,
        select {
            font-size: 14px !important;
        }
    }

    .organic-card {
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 2rem;
    }
</style>

{{-- Pastikan Alpine.js terload dengan benar --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection