@extends('layouts.manager')

@section('content')
    {{-- Inisialisasi Alpine.js Component --}}
    <div x-data="dashboardManager()" class="space-y-8 relative pb-10">

        {{-- LOADER OVERLAY (Muncul saat AJAX Fetching) --}}
        <div x-show="isLoading" x-cloak
            class="absolute inset-0 z-50 flex items-center justify-center bg-slate-50/60 backdrop-blur-sm rounded-3xl transition-opacity">
            <div class="bg-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 border border-slate-100">
                <i class="fas fa-circle-notch fa-spin text-amber-500 text-2xl"></i>
                <span class="text-sm font-bold text-slate-700 tracking-wider uppercase">Menyinkronkan Data...</span>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 1. HEADER & DIVISION SWITCHER --}}
        {{-- ========================================================= --}}
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-3xl font-header font-black text-slate-800 tracking-tight uppercase">
                    Mission <span class="text-primary italic">Control</span>
                </h1>
                <p class="text-slate-500 text-xs tracking-widest font-bold uppercase mt-1">
                    Monitoring Performa:
                    <span class="text-indigo-600">
                        @if ($selectedDivisi == '1')
                            TAC
                        @elseif($selectedDivisi == '2')
                            INFRASTRUCTURE
                        @elseif($selectedDivisi == '3')
                            BOT
                        @elseif($selectedDivisi == '4')
                            PURCHASING
                        @else
                            UMUM
                        @endif
                    </span>
                </p>
            </div>

            {{-- BUTTON SWITCHER DIVISI --}}
            <div class="flex flex-wrap items-center gap-2 bg-primary p-1.5 rounded-2xl border border-slate-200">
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => '1']) }}'"
                    class="px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300 {{ $selectedDivisi == '1' ? 'bg-secondary text-white shadow-md' : 'text-white hover:bg-accent' }}">
                    TAC
                </button>
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => '2']) }}'"
                    class="px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300 {{ $selectedDivisi == '2' ? 'bg-secondary text-white shadow-md' : 'text-white hover:bg-accent' }}">
                    Infra
                </button>
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => '6']) }}'"
                    class="px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300 {{ $selectedDivisi == '6' ? 'bg-secondary text-white shadow-md' : 'text-white hover:bg-accent' }}">
                    BOT
                </button>
                <button @click="window.location.href='{{ route('manager.dashboard', ['divisi_id' => '8']) }}'"
                    class="px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300 {{ $selectedDivisi == '8' ? 'bg-secondary text-white shadow-md' : 'text-white hover:bg-accent' }}">
                    Purchasing
                </button>
            </div>
        </div>
        {{-- INI ADALAH PENUTUP KOTAK HEADER YANG KEMARIN TERHAPUS --}}

        {{-- ========================================================= --}}
        {{-- 2. FILTER SECTION (AJAX - TANPA RELOAD) --}}
        {{-- ========================================================= --}}
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-end">
            {{-- Filter Staff --}}
            <div class="w-full md:w-64">
                <label class="text-slate-400 text-[10px] uppercase mb-2 block font-bold tracking-widest"><i
                        class="fas fa-user-tie mr-1"></i> Filter Staff</label>
                <select x-model="filterUser"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-700 font-bold outline-none focus:border-amber-500 transition-colors">
                    <option value="all">Semua Staff</option>
                    @foreach ($allStaffs as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Waktu --}}
            <div class="w-full md:w-64">
                <label class="text-slate-400 text-[10px] uppercase mb-2 block font-bold tracking-widest"><i
                        class="fas fa-calendar-alt mr-1"></i> Rentang Waktu</label>
                <select x-model="filterTime"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-700 font-bold outline-none focus:border-amber-500 transition-colors">
                    <option value="today">Hari Ini</option>
                    <option value="yesterday">Kemarin</option>
                    <option value="weekly">7 Hari Terakhir</option>
                    <option value="monthly">Bulan Ini</option>
                    <option value="custom">Custom Tanggal</option>
                </select>
            </div>

            {{-- Custom Date Picker (Muncul jika pilih Custom) --}}
            <div x-show="filterTime === 'custom'" x-cloak class="w-full md:flex-1 flex gap-2 items-end transition-all">
                <div class="flex-1">
                    <label class="text-slate-400 text-[9px] uppercase mb-1 block font-bold">Mulai</label>
                    <input type="date" x-model="startDate"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm outline-none focus:border-amber-500">
                </div>
                <div class="flex-1">
                    <label class="text-slate-400 text-[9px] uppercase mb-1 block font-bold">Sampai</label>
                    <input type="date" x-model="endDate"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm outline-none focus:border-amber-500">
                </div>
                <button @click="applyCustomDate()"
                    class="bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-700 transition">
                    Terapkan
                </button>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 3. CONTENT WRAPPER (Data Injector) --}}
        {{-- ========================================================= --}}
        <div class="space-y-8">

            {{-- Executive Summary (Selalu Muncul Paling Atas) --}}
            @include('manager.partials.content-executive')

            {{-- Konten Dinamis Berdasarkan Divisi (Akan Muncul Di Bawah Executive) --}}
            @if ($selectedDivisi == '1')
                @include('manager.partials.content-tac')
            @elseif ($selectedDivisi == '2')
                @include('manager.partials.content-infra')
            @elseif ($selectedDivisi >= '3')
                @include('manager.partials.content-bo')
            @endif

        </div>
    </div>

    {{-- CDN ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardManager', () => ({
                activeDivisi: '{{ $selectedDivisi }}',
                filterTime: 'monthly', // Default Bulan Ini
                filterUser: 'all',
                startDate: '',
                endDate: '',
                isLoading: false,
                chartData: @json($chartData), // Inisialisasi data dari Load Halaman Pertama

                init() {
                    // Pantau perubahan Dropdown (Kecuali Custom Date yang butuh tombol terapkan)
                    this.$watch('filterTime', value => {
                        if (value !== 'custom') this.fetchData();
                    });
                    this.$watch('filterUser', () => this.fetchData());

                    // Render Chart Pertama Kali
                    this.$nextTick(() => {
                        window.dispatchEvent(new CustomEvent('render-charts', {
                            detail: this.chartData
                        }));
                    });
                },

                fetchData() {
                    this.isLoading = true;

                    // Mengambil base URL dari halaman saat ini tanpa query parameter
                    let url = new URL(window.location.origin + window.location.pathname);
                    url.searchParams.append('divisi_id', this.activeDivisi);
                    url.searchParams.append('filter', this.filterTime);
                    url.searchParams.append('user_id', this.filterUser);

                    if (this.filterTime === 'custom') {
                        if (!this.startDate || !this.endDate) {
                            this.isLoading = false;
                            return; // Jangan fetch jika tanggal kosong
                        }
                        url.searchParams.append('start_date', this.startDate);
                        url.searchParams.append('end_date', this.endDate);
                    }

                    // AJAX Fetch
                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success') {
                                this.chartData = res.data;
                                // Pancarkan event agar script di partials-blade merender ulang datanya
                                window.dispatchEvent(new CustomEvent('update-charts', {
                                    detail: res.data
                                }));
                            }
                        })
                        .catch(err => console.error("Gagal menarik data:", err))
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                applyCustomDate() {
                    if (this.startDate && this.endDate) {
                        this.fetchData();
                    } else {
                        alert("Pilih tanggal mulai dan sampai terlebih dahulu.");
                    }
                }
            }));
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom Styling untuk Select Dropdown agar lebih rapi */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg fill='black' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
            background-repeat: no-repeat;
            background-position-x: 95%;
            background-position-y: 50%;
        }
    </style>
@endsection
