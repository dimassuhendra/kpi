@extends('layouts.manager')

@section('content')
    {{-- 1. SCOPE TUNGGAL: Inisialisasi activeTab sesuai $selectedDivisi dari Server --}}
    <div x-data="{
        activeTab: '{{ $selectedDivisi == '2' ? 'infra' : 'tac' }}'
    }" class="space-y-10">

        {{-- HEADER & FILTER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-header font-bold text-[var(--text-main)] tracking-tight uppercase">
                    Mission <span class="text-[var(--primary)] italic">Control</span>
                </h1>
                <p class="text-[var(--text-muted)] text-sm italic">
                    Monitoring performa divisi: <span class="text-[var(--primary)] font-bold uppercase"
                        x-text="activeTab"></span>
                </p>
            </div>

            {{-- BUTTON SWITCHER --}}
            <div
                class="flex items-center gap-1 bg-[var(--bg-main)]/40 p-1 rounded-3xl border border-[var(--text-muted)]/10 shadow-inner">

                {{-- <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => 'all']) }}'"
                    :class="activeTab === 'all' ? 'bg-[var(--primary)] text-white shadow-lg' :
                        'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
                    class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300">
                    All Division
                </button> --}}

                {{-- Button TAC --}}
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => '1']) }}'"
                    :class="activeTab === 'tac' ? 'bg-[var(--primary)] text-white shadow-lg' :
                        'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
                    class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300">
                    TAC
                </button>

                {{-- Button Infrastructure --}}
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => '2']) }}'"
                    :class="activeTab === 'infra' ? 'bg-[var(--primary)] text-white shadow-lg' :
                        'text-[var(--text-muted)] hover:text-[var(--text-main)]'"
                    class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300">
                    Infrastructure
                </button>
            </div>
        </div>

        {{-- 2. CONTENT WRAPPER --}}
        <div class="relative min-h-[400px]">
            {{-- Section: ALL --}}
            <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-cloak>
                @if ($selectedDivisi == 'all')
                    @include('manager.partials.content-all')
                @endif
            </div>

            {{-- Section: TAC --}}
            <div x-show="activeTab === 'tac'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-cloak>
                @if ($selectedDivisi == '1')
                    @include('manager.partials.content-tac')
                @endif
            </div>

            {{-- Section: INFRA --}}
            <div x-show="activeTab === 'infra'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-cloak>
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

        /* Tambahan agar saat ganti tab transisinya smooth */
        .relative>div {
            width: 100%;
        }
    </style>
@endsection
