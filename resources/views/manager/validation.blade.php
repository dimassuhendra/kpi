@extends('layouts.manager')

@section('content')
    <div class="h-[calc(100vh-120px)] flex flex-col md:flex-row gap-6">

        {{-- LEFT SIDE: LIST PENDING --}}
        <div class="w-full md:w-1/3 flex flex-col space-y-4">
            <h2 class="text-xl font-header font-bold text-white uppercase tracking-wider">
                Pending <span class="text-primary">Queue</span>
            </h2>

            <div class="flex-grow overflow-y-auto pr-2 custom-scrollbar space-y-3">
                @forelse($pendingReports as $rp)
                    <div onclick="loadDetail({{ $rp->id }})" id="card-{{ $rp->id }}"
                        class="report-card organic-card p-4 cursor-pointer border-l-4 border-transparent hover:border-primary transition-all group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-bold text-slate-200 group-hover:text-primary transition-colors">
                                    {{ $rp->user->nama_lengkap }}
                                </p>
                                <p class="text-[10px] text-slate-500 uppercase mt-1">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ \Carbon\Carbon::parse($rp->tanggal)->format('d M Y') }}
                                </p>
                            </div>
                            <span
                                class="text-[9px] bg-amber-500/10 text-amber-500 px-2 py-1 rounded font-bold italic tracking-tighter">WAITING</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-10 bg-slate-900/50 rounded-3xl border border-white/5">
                        <i class="fas fa-check-circle text-3xl text-slate-700 mb-3"></i>
                        <p class="text-slate-500 text-xs italic uppercase tracking-widest">Antrean Bersih</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT SIDE: DETAIL REVIEW --}}
        <div class="w-full md:w-2/3 organic-card overflow-hidden flex flex-col border-white/10 shadow-2xl relative">
            <div id="detail-container" class="flex-grow overflow-y-auto p-8 custom-scrollbar">
                <div class="h-full flex flex-col justify-center items-center text-slate-600">
                    <div class="relative mb-6">
                        <i class="fas fa-file-signature text-6xl opacity-20"></i>
                        <div class="absolute -top-2 -right-2 w-4 h-4 bg-primary rounded-full animate-ping"></div>
                    </div>
                    <p class="text-sm italic uppercase tracking-[0.2em] font-bold opacity-30">Review Mission Logs</p>
                    <p class="text-[10px] text-slate-500 mt-2 uppercase">Pilih laporan di sisi kiri untuk memvalidasi
                        aktivitas</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;
        }

        .report-card.active {
            background: rgba(59, 130, 246, 0.05);
            border-left-color: #3b82f6;
        }
    </style>

    <script>
        function loadDetail(id) {
            // UI Feedback: Highlight active card
            document.querySelectorAll('.report-card').forEach(c => c.classList.remove('active'));
            document.getElementById('card-' + id).classList.add('active');

            const container = document.getElementById('detail-container');

            // Loader State
            container.innerHTML = `
            <div class="h-full flex flex-col justify-center items-center">
                <div class="animate-spin h-10 w-10 border-4 border-primary border-t-transparent rounded-full mb-4"></div>
                <p class="text-[10px] text-slate-500 uppercase tracking-widest animate-pulse">Fetching Mission Data...</p>
            </div>
        `;

            fetch(`/manager/validation/${id}`)
                .then(res => {
                    if (!res.ok) {
                        // Ini akan mencetak error detail ke console browser
                        console.error("Server Error:", res.statusText);
                        throw new Error('Gagal memuat data dari server');
                    }
                    return res.text();
                })
                .then(html => {
                    container.innerHTML = html;
                })
                .catch(err => {
                    container.innerHTML = `
                    <div class="h-full flex flex-col justify-center items-center text-rose-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                        <p class="text-xs font-bold uppercase">Gagal memuat data</p>
                    </div>
                `;
                });
        }
    </script>
@endsection
