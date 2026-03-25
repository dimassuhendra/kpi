{{-- SECTION: EXECUTIVE SUMMARY (INFO CARDS) --}}
<div class="mb-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- CARD 1: PENDING VALIDASI (CLICKABLE) --}}
        {{-- Ganti URL route di bawah ini dengan nama route halaman validasi Anda --}}
        <a href="{{ url('/manager/validation') }}"
            class="block bg-gradient-to-br from-rose-500 to-rose-600 p-6 rounded-3xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-20 group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-clipboard-list text-8xl text-white"></i>
            </div>
            <div class="relative z-10">
                <p class="text-white/80 text-[10px] font-bold uppercase tracking-widest mb-1">Butuh Tindakan Manager</p>
                {{-- Mengambil jumlah data pending langsung dari Model (Ringkas & Aman) --}}
                @php
                    $pendingCount = \App\Models\DailyReport::where('status', 'pending')->count();
                @endphp
                <h3 class="text-4xl font-black text-white mb-4">{{ $pendingCount }} <span
                        class="text-lg font-bold">Laporan</span></h3>
                <div
                    class="flex items-center gap-2 text-white bg-white/20 w-max px-4 py-2 rounded-xl backdrop-blur-sm text-xs font-bold">
                    Cek & Validasi Sekarang <i class="fas fa-arrow-right"></i>
                </div>
            </div>
        </a>

        {{-- CARD 2: INFO STAF AKTIF --}}
        <div
            class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-center relative overflow-hidden">
            <div class="absolute right-4 top-4 text-slate-100">
                <i class="fas fa-users text-6xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Total Staf dalam Filter
                </p>
                <h3 class="text-4xl font-black text-slate-800"
                    x-text="chartData.staff_labels ? chartData.staff_labels.length : 0">0</h3>
                <p class="text-slate-400 text-xs mt-2 font-medium">Berdasarkan divisi yang dipilih</p>
            </div>
        </div>

        {{-- CARD 3: TOTAL VOLUME AKTIVITAS (Tersaring) --}}
        <div
            class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-center relative overflow-hidden">
            <div class="absolute right-4 top-4 text-slate-100">
                <i class="fas fa-chart-line text-6xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Volume Pekerjaan
                    Tersaring</p>
                {{-- Menghitung total data dari Trend Activity dan Case yang didapat dari server --}}
                <h3 class="text-4xl font-black text-amber-500"
                    x-text="chartData.tac && chartData.tac.trend_case ? (chartData.tac.trend_case.reduce((a,b)=>a+b,0) + chartData.tac.trend_activity.reduce((a,b)=>a+b,0)) : (chartData.infra && chartData.infra.donut_kategori ? chartData.infra.donut_kategori.reduce((a,b)=>a+b,0) : 0)">
                    0
                </h3>
                <p class="text-slate-400 text-xs mt-2 font-medium">Sesuai rentang waktu di atas</p>
            </div>
        </div>

    </div>
</div>
