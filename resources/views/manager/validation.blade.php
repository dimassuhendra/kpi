@extends('layouts.manager')

@section('content')
    {{-- WRAPPER UTAMA DENGAN ALPINE.JS --}}
    <div class="max-w-6xl mx-auto space-y-6 pb-20" x-data="{ activeTab: 'pending' }">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 ml-2 gap-4">
            <div>
                <h2 class="text-2xl font-header font-black text-slate-800 uppercase tracking-tight">
                    Monitoring <span class="text-primary">& Validations</span>
                </h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">
                    Pusat persetujuan laporan dan log aktivitas
                </p>
            </div>

            {{-- TAB SWITCHER MANAGER --}}
            <div
                class="bg-slate-200/50 p-1.5 rounded-2xl flex flex-wrap gap-2 w-full md:w-max border border-slate-200 shadow-inner">
                <button type="button" @click="activeTab = 'pending'"
                    :class="activeTab === 'pending' ? 'bg-white text-primary shadow-md font-black' :
                        'text-secondary font-bold hover:text-slate-700'"
                    class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                    <i class="fas fa-clipboard-check mr-2"></i> Pending
                    <span
                        class="ml-2 bg-accent text-primary py-0.5 px-2 rounded-md">{{ $pendingReports->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'rating'"
                    :class="activeTab === 'rating' ? 'bg-white text-amber-500 shadow-md font-black' :
                        'text-slate-500 font-bold hover:text-slate-700'"
                    class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                    <i class="fas fa-star mr-2"></i> Log Rating
                </button>
                <button type="button" @click="activeTab = 'kuis'"
                    :class="activeTab === 'kuis' ? 'bg-white text-indigo-500 shadow-md font-black' :
                        'text-slate-500 font-bold hover:text-slate-700'"
                    class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                    <i class="fas fa-award mr-2"></i> Log Kuis
                </button>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- TAB 1: PENDING VALIDATIONS (KODE LAMA)     --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'pending'" x-transition.opacity.duration.500ms class="space-y-4">
            @forelse($pendingReports as $rp)
                {{-- PASTE KODE ACCORDION VALIDATION ANDA SEBELUMNYA DI SINI --}}
                <div class="info-card overflow-hidden transition-all duration-300 border-l-4 border-l-slate-200 bg-white shadow-sm rounded-xl"
                    id="wrapper-{{ $rp->id }}">
                    <div onclick="toggleAccordion({{ $rp->id }})"
                        class="p-5 cursor-pointer flex items-center justify-between hover:bg-slate-50/80 transition-colors">
                        {{-- (Isi Accordion Header biarkan sama persis seperti kode Anda) --}}
                        <div class="flex items-start sm:items-center gap-5">
                            <div
                                class="w-12 h-12 rounded-2xl bg-accent flex-shrink-0 flex items-center justify-center text-primary font-black text-lg">
                                {{ strtoupper(substr($rp->user->nama_lengkap, 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-header font-black text-slate-800 uppercase tracking-tight">
                                        {{ $rp->user->nama_lengkap }}</h3>
                                    <span
                                        class="hidden sm:inline-block text-[9px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded uppercase font-bold tracking-widest">{{ $rp->user->divisi->nama_divisi ?? 'Divisi' }}</span>
                                </div>
                                <div
                                    class="flex flex-wrap items-center gap-3 mt-1.5 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                    <span class="flex items-center"><i
                                            class="far fa-calendar-alt mr-1 text-slate-300"></i>{{ \Carbon\Carbon::parse($rp->tanggal)->format('d M Y') }}</span>
                                    @if ($rp->shift)
                                        <span class="flex items-center"><i
                                                class="fas fa-user-clock mr-1 text-slate-300"></i>{{ $rp->shift->nama_shift }}</span>
                                    @endif
                                </div>
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

        {{-- ========================================== --}}
        {{-- TAB 2: LOG RATING PELANGGAN                --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'rating'" style="display: none;" x-transition.opacity.duration.500ms>
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase tracking-widest text-slate-400">
                                <th class="p-4 font-black">Staff / TAC</th>
                                <th class="p-4 font-black">Detail Tiket</th>
                                <th class="p-4 font-black text-center">Rating</th>
                                <th class="p-4 font-black text-center">Waktu Survey</th>
                                <th class="p-4 font-black text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($feedbacks as $fb)
                                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                    <td class="p-4">
                                        <p class="font-bold text-slate-700">{{ $fb->user->nama_lengkap }}</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-slate-800">{{ $fb->nomor_tiket }}</p>
                                        <p class="text-xs text-slate-500">{{ $fb->nama_pelanggan }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center gap-0.5 text-amber-400 text-xs">
                                            @for ($i = 0; $i < $fb->rating; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor
                                            @for ($i = 0; $i < 5 - $fb->rating; $i++)
                                                <i class="far fa-star text-slate-300"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md">
                                            {{ \Carbon\Carbon::parse($fb->tanggal_survey)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button type="button"
                                            onclick="openImageModal('{{ asset('storage/' . $fb->bukti_survey) }}')"
                                            class="text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 p-2 rounded-xl transition tooltip"
                                            title="Lihat Bukti SS">
                                            <i class="fas fa-image fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 text-sm italic">Belum ada data
                                        rating masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- TAB 3: LOG NILAI KUIS / ASESMEN            --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'kuis'" style="display: none;" x-transition.opacity.duration.500ms>
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase tracking-widest text-slate-400">
                                <th class="p-4 font-black">Staff / TAC</th>
                                <th class="p-4 font-black text-center">Periode</th>
                                <th class="p-4 font-black text-center">Skor Global</th>
                                <th class="p-4 font-black text-center">Bukti SS</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($assessments as $quiz)
                                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                    <td class="p-4">
                                        <p class="font-bold text-slate-700">{{ $quiz->user->nama_lengkap }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100">
                                            {{ date('F', mktime(0, 0, 0, $quiz->periode_bulan, 1)) }}
                                            {{ $quiz->periode_tahun }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $percent =
                                                $quiz->jumlah_soal > 0
                                                    ? round(($quiz->jumlah_benar / $quiz->jumlah_soal) * 100)
                                                    : 0;
                                            $color =
                                                $percent >= 80
                                                    ? 'text-emerald-500'
                                                    : ($percent >= 60
                                                        ? 'text-amber-500'
                                                        : 'text-rose-500');
                                        @endphp
                                        <p class="font-black text-xl {{ $color }}">{{ $percent }}%</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Benar
                                            {{ $quiz->jumlah_benar }}/{{ $quiz->jumlah_soal }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button type="button"
                                            onclick="openImageModal('{{ asset('storage/' . $quiz->bukti_kuis) }}')"
                                            class="text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 p-2 rounded-xl transition tooltip"
                                            title="Lihat Bukti Kuis">
                                            <i class="fas fa-image fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 text-sm italic">Belum ada
                                        data kuis masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL PREVIEW GAMBAR (KODE LAMA ANDA) --}}
    <div id="imagePreviewModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="relative max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-4 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-700 uppercase tracking-widest text-xs"><i
                        class="fas fa-image text-indigo-500 mr-2"></i> Preview Bukti</h3>
                <button onclick="closeImageModal()"
                    class="text-slate-400 hover:text-rose-500 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-rose-50">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>
            <div class="p-4 flex justify-center items-center bg-slate-800/5 min-h-[50vh]">
                <img id="modalImgContent" src="" alt="Bukti Lampiran"
                    class="max-h-[70vh] w-auto object-contain rounded-lg shadow-sm">
            </div>
        </div>
    </div>

    {{-- SCRIPT ACCORDION & MODAL (KODE LAMA ANDA) --}}
    <script>
        function toggleAccordion(id) {
            // (Isi fungsi toggleAccordion biarkan sama persis seperti kode Anda)
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
                document.getElementById('modalImgContent').src = '';
            }, 300);
        }

        document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    </script>
@endsection
