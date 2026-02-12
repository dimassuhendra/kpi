@extends('layouts.manager')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Header Section --}}
        <div class="flex justify-between items-end mb-8 ml-2">
            <div>
                <h2 class="text-2xl font-header font-black text-slate-800 uppercase tracking-tight">
                    Pending <span class="text-emerald-600">Validations</span>
                </h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">
                    Tinjau dan setujui laporan aktivitas harian staff
                </p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Queue</span>
                <p class="text-2xl font-header font-black text-slate-800">{{ $pendingReports->count() }}</p>
            </div>
        </div>

        {{-- Accordion Container --}}
        <div class="space-y-4">
            @forelse($pendingReports as $rp)
                <div class="info-card overflow-hidden transition-all duration-300 border-l-4 border-l-slate-200"
                    id="wrapper-{{ $rp->id }}">
                    {{-- Header Accordion (Clickable) --}}
                    <div onclick="toggleAccordion({{ $rp->id }})"
                        class="p-5 cursor-pointer flex items-center justify-between hover:bg-slate-50/50 transition-colors">

                        <div class="flex items-center gap-5">
                            {{-- Avatar/Initial --}}
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-700 font-black text-lg">
                                {{ strtoupper(substr($rp->user->nama_lengkap, 0, 1)) }}
                            </div>

                            <div>
                                <h3 class="font-header font-black text-slate-800 uppercase tracking-tight">
                                    {{ $rp->user->nama_lengkap }}</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ \Carbon\Carbon::parse($rp->tanggal)->format('l, d M Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right hidden md:block">
                                <span
                                    class="block text-[9px] font-black text-slate-400 uppercase tracking-tighter">Status</span>
                                <span
                                    class="text-[10px] bg-amber-100 text-amber-600 px-2 py-0.5 rounded-md font-black uppercase">Waiting</span>
                            </div>
                            <i id="icon-{{ $rp->id }}"
                                class="fas fa-chevron-down text-slate-300 transition-transform duration-300"></i>
                        </div>
                    </div>

                    {{-- Content Accordion (Hidden by default) --}}
                    <div id="content-{{ $rp->id }}"
                        class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/30">
                        <div class="p-6 border-t border-slate-100" id="detail-{{ $rp->id }}">
                            {{-- Data akan di-load di sini via AJAX --}}
                            <div class="flex justify-center py-8">
                                <div
                                    class="animate-spin h-6 w-6 border-4 border-emerald-500 border-t-transparent rounded-full">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="info-card p-20 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-4">
                        <i class="fas fa-check-double text-3xl text-slate-200"></i>
                    </div>
                    <h3 class="font-header font-black text-slate-800 uppercase tracking-tight">Semua Beres!</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Tidak ada laporan yang perlu
                        divalidasi saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function toggleAccordion(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);
            const wrapper = document.getElementById('wrapper-' + id);
            const detailContainer = document.getElementById('detail-' + id);

            // Close others (Optional: Jika ingin sistem 1 terbuka yang lain tertutup)
            // document.querySelectorAll('[id^="content-"]').forEach(...)

            if (content.style.maxHeight) {
                // Close
                content.style.maxHeight = null;
                icon.style.transform = "rotate(0deg)";
                wrapper.classList.replace('border-l-emerald-500', 'border-l-slate-200');
            } else {
                // Open
                content.style.maxHeight = content.scrollHeight + "px";
                icon.style.transform = "rotate(180deg)";
                wrapper.classList.replace('border-l-slate-200', 'border-l-emerald-500');

                // Load data if empty
                if (detailContainer.getAttribute('data-loaded') !== 'true') {
                    fetch(`/manager/validation/${id}`)
                        .then(res => res.text())
                        .then(html => {
                            detailContainer.innerHTML = html;
                            detailContainer.setAttribute('data-loaded', 'true');
                            // Recalculate height
                            content.style.maxHeight = content.scrollHeight + 500 + "px";
                        });
                }
            }
        }
    </script>
@endsection
