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
                <div class="info-card overflow-hidden transition-all duration-300 border-l-4 border-l-slate-200 bg-white shadow-sm rounded-xl"
                    id="wrapper-{{ $rp->id }}">

                    {{-- Header Accordion (Clickable) --}}
                    <div onclick="toggleAccordion({{ $rp->id }})"
                        class="p-5 cursor-pointer flex items-center justify-between hover:bg-slate-50/80 transition-colors">

                        <div class="flex items-start sm:items-center gap-5">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-100 flex-shrink-0 flex items-center justify-center text-emerald-700 font-black text-lg">
                                {{ strtoupper(substr($rp->user->nama_lengkap, 0, 1)) }}
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-header font-black text-slate-800 uppercase tracking-tight">
                                        {{ $rp->user->nama_lengkap }}
                                    </h3>
                                    <span
                                        class="hidden sm:inline-block text-[9px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded uppercase font-bold tracking-widest">
                                        {{ $rp->user->divisi->nama_divisi ?? 'Divisi' }}
                                    </span>
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-3 mt-1.5 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                    <span class="flex items-center"><i class="far fa-calendar-alt mr-1 text-slate-300"></i>
                                        {{ \Carbon\Carbon::parse($rp->tanggal)->format('d M Y') }}</span>
                                    @if ($rp->shift)
                                        <span class="flex items-center"><i
                                                class="fas fa-user-clock mr-1 text-slate-300"></i>
                                            {{ $rp->shift->nama_shift }}</span>
                                    @endif
                                </div>

                                @if ($rp->user->divisi_id == 1)
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @if ($rp->is_gps_ontime)
                                            <span
                                                class="text-[9px] bg-emerald-50 text-emerald-600 border border-emerald-100 px-1.5 py-0.5 rounded-md font-bold flex items-center gap-1"><i
                                                    class="fas fa-map-marker-alt"></i> Report GPS Sesuai Jadwal</span>
                                        @else
                                            <span
                                                class="text-[9px] bg-rose-50 text-rose-600 border border-rose-100 px-1.5 py-0.5 rounded-md font-bold flex items-center gap-1"><i
                                                    class="fas fa-map-marker-alt"></i> Report GPS Terlambat/Tidak Ada</span>
                                        @endif

                                        @if ($rp->is_dashboard_ontime)
                                            <span
                                                class="text-[9px] bg-indigo-50 text-indigo-600 border border-indigo-100 px-1.5 py-0.5 rounded-md font-bold flex items-center gap-1"><i
                                                    class="fas fa-chart-line"></i> Report Dashboard Sesuai Jadwal</span>
                                        @else
                                            <span
                                                class="text-[9px] bg-rose-50 text-rose-600 border border-rose-100 px-1.5 py-0.5 rounded-md font-bold flex items-center gap-1"><i
                                                    class="fas fa-chart-line"></i> Report Dashboard Terlambat</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-6 flex-shrink-0">
                            <div class="text-right hidden md:block">
                                <span
                                    class="block text-[9px] font-black text-slate-400 uppercase tracking-tighter mb-0.5">Status</span>
                                <span
                                    class="text-[10px] bg-amber-100 text-amber-600 px-2 py-1 rounded-md font-black uppercase tracking-wider">Waiting</span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center">
                                <i id="icon-{{ $rp->id }}"
                                    class="fas fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Content Accordion --}}
                    <div id="content-{{ $rp->id }}"
                        class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="p-6 border-t border-slate-100" id="detail-{{ $rp->id }}">
                            <div class="flex justify-center py-8">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="animate-spin h-8 w-8 border-4 border-emerald-500 border-t-transparent rounded-full mb-3">
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest animate-pulse">
                                        Memuat Data Laporan...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="info-card p-20 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white shadow-sm mb-4">
                        <i class="fas fa-clipboard-check text-4xl text-emerald-400"></i>
                    </div>
                    <h3 class="font-header font-black text-slate-800 uppercase tracking-tight text-xl">Semua Beres!</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Tidak ada laporan yang perlu
                        divalidasi saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL PREVIEW GAMBAR --}}
    <div id="imagePreviewModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="relative max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col">
            {{-- Header Modal --}}
            <div class="flex justify-between items-center p-4 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-700 uppercase tracking-widest text-xs"><i
                        class="fas fa-image text-indigo-500 mr-2"></i> Preview Bukti</h3>
                <button onclick="closeImageModal()"
                    class="text-slate-400 hover:text-rose-500 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-rose-50">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>
            {{-- Body Modal (Menampilkan Gambar) --}}
            <div class="p-4 flex justify-center items-center bg-slate-800/5 min-h-[50vh]">
                <img id="modalImgContent" src="" alt="Bukti Lampiran"
                    class="max-h-[70vh] w-auto object-contain rounded-lg shadow-sm">
            </div>
        </div>
    </div>

    <script>
        function toggleAccordion(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);
            const wrapper = document.getElementById('wrapper-' + id);
            const detailContainer = document.getElementById('detail-' + id);

            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                icon.style.transform = "rotate(0deg)";
                wrapper.classList.replace('border-l-emerald-500', 'border-l-slate-200');
            } else {
                icon.style.transform = "rotate(180deg)";
                wrapper.classList.replace('border-l-slate-200', 'border-l-emerald-500');

                if (detailContainer.getAttribute('data-loaded') !== 'true') {
                    content.style.maxHeight = "200px";

                    fetch(`/manager/validation/${id}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Gagal memuat data');
                            return res.text();
                        })
                        .then(html => {
                            detailContainer.innerHTML = html;
                            detailContainer.setAttribute('data-loaded', 'true');
                            setTimeout(() => {
                                content.style.maxHeight = detailContainer.scrollHeight + 500 + "px";
                            }, 50);
                        })
                        .catch(err => {
                            detailContainer.innerHTML =
                                `<div class="text-center text-rose-500 font-bold py-8"><i class="fas fa-exclamation-triangle mr-2"></i>Terjadi kesalahan sistem. Coba muat ulang halaman.</div>`;
                            content.style.maxHeight = "150px";
                        });
                } else {
                    content.style.maxHeight = detailContainer.scrollHeight + 500 + "px";
                }
            }
        }

        // FUNGSI UNTUK MENGENDALIKAN MODAL GAMBAR
        function openImageModal(imageUrl) {
            document.getElementById('modalImgContent').src = imageUrl;
            document.getElementById('imagePreviewModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imagePreviewModal').classList.add('hidden');
            setTimeout(() => {
                document.getElementById('modalImgContent').src = ''; // Clear src after animation
            }, 300);
        }

        // Tutup modal jika user klik di luar gambar (area background hitam)
        document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    </script>
@endsection
