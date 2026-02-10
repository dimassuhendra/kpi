@extends('layouts.manager')

@section('content')
<div x-data="{ activeTab: 'tac' }" class="space-y-10">

    {{-- 1. HEADER & FILTER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-header font-bold text-white tracking-tight uppercase">
                Mission <span class="text-primary italic">Control</span>
            </h1>
            <p class="text-slate-400 text-sm italic">
                Monitoring performa divisi: <span class="text-primary font-bold uppercase" x-text="activeTab"></span>
            </p>
        </div>

        {{-- BUTTON SWITCHER --}}
        <div class="flex items-center gap-1 bg-slate-800/40 p-1 rounded-3xl border border-white/5">
            <button @click="activeTab = 'all'"
                :class="activeTab === 'all' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200'"
                class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all">
                All Division
            </button>

            <button @click="activeTab = 'tac'"
                :class="activeTab === 'tac' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200'"
                class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all">
                TAC
            </button>

            <button @click="activeTab = 'infra'"
                :class="activeTab === 'infra' ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200'"
                class="px-6 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all">
                Infrastructure
            </button>
        </div>
    </div>

    {{-- 2. CONTENT WRAPPER --}}
    <div class="relative">
        {{-- Section: ALL (Dalam Pengembangan) --}}
        <div x-show="activeTab === 'all'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            @include('manager.partials.content-all')
        </div>

        {{-- Section: TAC (Konten Eksisting) --}}
        <div x-show="activeTab === 'tac'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            @include('manager.partials.content-tac')
        </div>

        {{-- Section: INFRA (Dalam Pengembangan) --}}
        <div x-show="activeTab === 'infra'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            @include('manager.partials.content-infra')
        </div>
    </div>

</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endsection