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
            <div onclick="loadDetail({{ $rp->id }})"
                id="card-{{ $rp->id }}"
                class="report-card organic-card p-4 cursor-pointer border-l-4 border-transparent hover:border-primary transition-all group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-bold text-slate-200 group-hover:text-primary transition-colors">{{ $rp->user->nama_lengkap }}</p>
                        <p class="text-[10px] text-slate-500 uppercase mt-1">
                            {{ \Carbon\Carbon::parse($rp->tanggal)->format('d M Y') }}
                        </p>
                    </div>
                    <span class="text-[9px] bg-slate-800 text-slate-400 px-2 py-1 rounded font-bold italic">PENDING</span>
                </div>
            </div>
            @empty
            <div class="text-center p-10 bg-slate-900/50 rounded-3xl border border-white/5">
                <p class="text-slate-500 text-xs italic italic">Tidak ada antrean laporan.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- RIGHT SIDE: DETAIL REVIEW --}}
    <div class="w-full md:w-2/3 organic-card overflow-hidden flex flex-col border-white/10 shadow-2xl">
        <div id="detail-container" class="flex-grow overflow-y-auto p-8 custom-scrollbar">
            <div class="h-full flex flex-col justify-center items-center text-slate-600">
                <i class="fas fa-file-signature text-5xl mb-4 opacity-20"></i>
                <p class="text-sm italic uppercase tracking-widest font-bold opacity-30">Pilih laporan di sisi kiri untuk divalidasi</p>
            </div>
        </div>
    </div>
</div>

<script>
    function loadDetail(id) {
        // Highlight active card
        document.querySelectorAll('.report-card').forEach(c => c.classList.remove('border-primary', 'bg-white/5'));
        document.getElementById('card-' + id).classList.add('border-primary', 'bg-white/5');

        const container = document.getElementById('detail-container');
        container.innerHTML = `<div class="h-full flex justify-center items-center"><div class="animate-spin h-10 w-10 border-4 border-primary border-t-transparent rounded-full"></div></div>`;

        fetch(`/manager/validation/${id}`)
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
            });
    }
</script>
@endsection