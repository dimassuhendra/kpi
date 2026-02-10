@extends('layouts.manager')

@section('content')
    {{-- 1. SCOPE TUNGGAL: Inisialisasi activeTab sesuai $selectedDivisi dari Server --}}
    <div x-data="{
        activeTab: '{{ $selectedDivisi == '1' ? 'tac' : ($selectedDivisi == '2' ? 'infra' : 'all') }}'
    }" class="space-y-10">

        {{-- HEADER & FILTER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-header font-bold text-white tracking-tight uppercase">
                    Mission <span class="text-primary italic">Control</span>
                </h1>
                <p class="text-slate-400 text-sm italic">
                    Monitoring performa divisi: <span class="text-primary font-bold uppercase" x-text="activeTab"></span>
                </p>
            </div>

            {{-- BUTTON SWITCHER (Dihapus x-data di sini agar nyambung ke atas) --}}
            <div class="flex items-center gap-1 bg-slate-800/40 p-1 rounded-3xl border border-white/5">

                {{-- Button All Division --}}
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => 'all']) }}'"
                    :class="activeTab === 'all' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200'"
                    class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all">
                    All Division
                </button>

                {{-- Button TAC --}}
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => '1']) }}'"
                    :class="activeTab === 'tac' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200'"
                    class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all">
                    TAC
                </button>

                {{-- Button Infrastructure --}}
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => '2']) }}'"
                    :class="activeTab === 'infra' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200'"
                    class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all">
                    Infrastructure
                </button>
            </div>
        </div>

        {{-- 2. CONTENT WRAPPER --}}
        <div class="relative">
            {{-- Section: ALL --}}
            <div x-show="activeTab === 'all'" x-cloak>
                @if ($selectedDivisi == 'all')
                    @include('manager.partials.content-all')
                @endif
            </div>

            {{-- Section: TAC --}}
            <div x-show="activeTab === 'tac'" x-cloak>
                @if ($selectedDivisi == '1')
                    @include('manager.partials.content-tac')
                @endif
            </div>

            {{-- Section: INFRA --}}
            <div x-show="activeTab === 'infra'" x-cloak>
                @if ($selectedDivisi == '2')
                    @include('manager.partials.content-infra')
                @endif
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
