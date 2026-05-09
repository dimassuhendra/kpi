<div class="space-y-6">

    {{-- SECTION 1: DOCUMENTATION & REPORTING (Hanya untuk TAC) --}}
    @if ($report->user->divisi_id == 1)
        <div class="bg-indigo-50/50 rounded-xl border border-indigo-100 p-5">
            <h4 class="font-bold text-slate-800 mb-4 text-xs uppercase tracking-widest flex items-center">
                <i class="fas fa-file-signature text-indigo-500 mr-2 text-base"></i> Documentation & Reporting
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                    <span class="block text-[10px] text-slate-400 uppercase font-black mb-1">Shift Kerja</span>
                    @if ($report->shift)
                        <span class="font-bold text-sm text-slate-700 block">{{ $report->shift->nama_shift }}</span>
                        <span class="text-xs text-slate-500">
                            {{ \Carbon\Carbon::parse($report->shift->jam_masuk)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($report->shift->jam_pulang)->format('H:i') }}
                        </span>
                    @else
                        <span class="text-xs text-rose-500 font-bold">Shift tidak diisi</span>
                    @endif
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="block text-[10px] text-slate-400 uppercase font-black mb-1">Report GPS</span>
                        @if ($report->is_gps_ontime)
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold"><i
                                    class="fas fa-check-circle"></i> Report Sesuai Jadwal</span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-rose-100 text-rose-700 text-xs font-bold"><i
                                    class="fas fa-times-circle"></i> Terlambat/Tidak Ada</span>
                        @endif
                    </div>
                    @if ($report->bukti_report_gps)
                        <button type="button"
                            onclick="openImageModal('{{ asset('storage/' . $report->bukti_report_gps) }}')"
                            class="mt-3 w-full text-center py-2 rounded bg-indigo-100 text-indigo-600 hover:bg-indigo-200 font-bold text-[10px] uppercase tracking-wider transition">
                            <i class="fas fa-image mr-1"></i> Lihat Bukti GPS
                        </button>
                    @endif
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="block text-[10px] text-slate-400 uppercase font-black mb-1">Input Dashboard
                            KPI</span>
                        @if ($report->is_dashboard_ontime)
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold"><i
                                    class="fas fa-check-circle"></i> Tepat Waktu (< 2 Jam)</span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-rose-100 text-rose-700 text-xs font-bold"><i
                                            class="fas fa-times-circle"></i> Terlambat</span>
                        @endif
                    </div>
                    <span class="block mt-3 text-[10px] font-bold text-slate-400">
                        <i class="fas fa-clock"></i> Disubmit pada:
                        {{ $report->created_at->timezone('Asia/Jakarta')->translatedFormat('l, d F Y, H:i') }} WIB
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- SECTION 2: TECHNICAL CASES / AKTIVITAS UTAMA --}}
    @if ($cases->count() > 0)
        <div>
            <h4 class="font-bold text-slate-800 mb-3 text-xs uppercase tracking-widest">
                <i class="fas fa-network-wired text-amber-500 mr-2"></i>Technical / Main Activities
            </h4>
            <div class="space-y-3">
                @foreach ($cases as $case)
                    @if ($report->user->divisi_id == 1)
                        <div
                            class="bg-white p-4 rounded-xl border {{ $case->kategori == 'Network' ? 'border-l-4 border-l-amber-500 border-slate-200' : 'border-l-4 border-l-emerald-500 border-slate-200' }} shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <div class="flex-grow">
                                    <span
                                        class="text-[10px] uppercase font-black px-2 py-0.5 rounded-md {{ $case->kategori == 'Network' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }} mb-2 inline-block">{{ $case->kategori }}</span>
                                    <p class="text-sm font-bold text-slate-800">{{ $case->deskripsi_kegiatan }}</p>
                                    @if ($case->nomor_tiket)
                                        <p class="text-xs text-indigo-600 font-bold mt-1.5"><i
                                                class="fas fa-ticket-alt mr-1"></i> Tiket: {{ $case->nomor_tiket }}</p>
                                    @endif
                                    @if ($case->kategori == 'GPS')
                                        <p class="text-xs text-slate-500 font-semibold mt-2">Jumlah Kendaraan: <span
                                                class="text-slate-800 font-bold">{{ $case->value_raw == '0' ? 'ALL' : $case->value_raw }}</span>
                                        </p>
                                    @endif
                                </div>
                                @if ($case->kategori == 'Network' && strtolower($case->deskripsi_kegiatan) !== 'monitoring network')
                                    <div
                                        class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-xs min-w-[250px]">
                                        @if ($case->temuan_sendiri)
                                            <div
                                                class="flex justify-between items-center mb-2 pb-2 border-b border-slate-200">
                                                <span class="font-bold text-slate-500">Kategori Case:</span><span
                                                    class="font-black text-rose-500">Deteksi Dini</span>
                                            </div>
                                            @if ($case->bukti_deteksi_dini)
                                                <button type="button"
                                                    onclick="openImageModal('{{ asset('storage/' . $case->bukti_deteksi_dini) }}')"
                                                    class="w-full text-center py-1.5 rounded bg-rose-100 text-rose-600 hover:bg-rose-200 font-bold text-[10px] uppercase tracking-wider mb-2 transition"><i
                                                        class="fas fa-search mr-1"></i> Lihat Bukti Deteksi</button>
                                            @endif
                                        @else
                                            <div
                                                class="flex justify-between items-center mb-2 pb-2 border-b border-slate-200">
                                                <span class="font-bold text-slate-500">Waktu Respon:</span><span
                                                    class="font-black text-amber-600">{{ $case->waktu_respon_menit }}
                                                    Menit</span>
                                            </div>
                                            @if ($case->bukti_respon_time)
                                                <button type="button"
                                                    onclick="openImageModal('{{ asset('storage/' . $case->bukti_respon_time) }}')"
                                                    class="w-full text-center py-1.5 rounded bg-amber-100 text-amber-600 hover:bg-amber-200 font-bold text-[10px] uppercase tracking-wider mb-2 transition"><i
                                                        class="fas fa-image mr-1"></i> Lihat Bukti Respon</button>
                                            @endif
                                        @endif
                                        <div class="flex justify-between items-start mt-2">
                                            <span class="font-bold text-slate-500">Penyelesaian:</span>
                                            @if ($case->is_mandiri)
                                                <span class="font-bold text-emerald-600 text-right">Mandiri</span>
                                            @else
                                                <span class="font-bold text-rose-500 text-right">Eskalasi <br> <span
                                                        class="text-[10px] text-slate-500 block leading-tight mt-0.5">Bantuan:
                                                        {{ $case->pic_name }}</span></span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- BLOK NON-TAC (Bot, Purchasing, Infra) YANG SUDAH DIRAPIKAN SAMA SEPERTI GENERAL ACTIVITY --}}
                        @php
                            $judul = $case->nama_kegiatan ?? '';
                            $deskripsi = $case->deskripsi_kegiatan ?? '';

                            if (empty($judul) && !empty($deskripsi)) {
                                if (strpos($deskripsi, ': ') !== false) {
                                    $parts = explode(': ', $deskripsi, 2);
                                    $judul = $parts[0];
                                    $deskripsi = trim($parts[1]);
                                } else {
                                    $judul = $deskripsi;
                                    $deskripsi = '';
                                }
                            }
                            if (trim($deskripsi) === '-') {
                                $deskripsi = '';
                            }
                        @endphp

                        <div
                            class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
                            <div>
                                <span
                                    class="text-[10px] uppercase font-black px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 mb-3 inline-block tracking-wider">
                                    {{ $case->kategori ?? 'Aktivitas' }}
                                </span>
                                <h4 class="text-sm font-bold text-slate-800 leading-snug">
                                    {{ $judul }}
                                </h4>
                                @if (!empty($deskripsi))
                                    <div
                                        class="mt-3 p-3 bg-slate-50/50 backdrop-blur-sm rounded-xl border border-slate-100">
                                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                            {{ $deskripsi }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            @if (!empty($case->foto_dokumentasi))
                                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-start">
                                    <button type="button"
                                        onclick="openImageModal('{{ asset('storage/' . $case->foto_dokumentasi) }}')"
                                        class="px-4 py-1.5 rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-200 hover:text-slate-800 font-bold text-[10px] uppercase tracking-wider transition-all duration-300 flex items-center">
                                        <i class="fas fa-image mr-2 text-sm"></i> Lihat Dokumentasi
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- SECTION 3: GENERAL ACTIVITIES --}}
    @if ($activities->count() > 0)
        <div>
            <h4 class="font-bold text-slate-800 mb-3 text-xs uppercase tracking-widest"><i
                    class="fas fa-tasks text-slate-500 mr-2"></i>General / Other Activities</h4>
            <div class="space-y-3">
                @foreach ($activities as $act)
                    @php
                        // Memperbaiki bug text $judul tidak muncul dengan mendefinisikannya di dalam loop
                        $judul = $act->nama_kegiatan ?? '';
                        $deskripsi = $act->deskripsi_kegiatan ?? '';

                        if (empty($judul) && !empty($deskripsi)) {
                            if (strpos($deskripsi, ': ') !== false) {
                                $parts = explode(': ', $deskripsi, 2);
                                $judul = $parts[0];
                                $deskripsi = trim($parts[1]);
                            } else {
                                $judul = $deskripsi;
                                $deskripsi = '';
                            }
                        }
                        if (trim($deskripsi) === '-') {
                            $deskripsi = '';
                        }
                    @endphp
                    <div
                        class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
                        <div>
                            <span
                                class="text-[10px] uppercase font-black px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 mb-3 inline-block tracking-wider">
                                {{ $act->kategori ?? 'Aktivitas Umum' }}
                            </span>
                            <h4 class="text-sm font-bold text-slate-800 leading-snug">
                                {{ $judul }}
                            </h4>
                            @if (!empty($deskripsi))
                                <div
                                    class="mt-3 p-3 bg-slate-50/50 backdrop-blur-sm rounded-xl border border-slate-100">
                                    <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                        {{ $deskripsi }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        @if (!empty($act->foto_dokumentasi))
                            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-start">
                                <button type="button"
                                    onclick="openImageModal('{{ asset('storage/' . $act->foto_dokumentasi) }}')"
                                    class="px-4 py-1.5 rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-200 hover:text-slate-800 font-bold text-[10px] uppercase tracking-wider transition-all duration-300 flex items-center">
                                    <i class="fas fa-image mr-2 text-sm"></i> Lihat Dokumentasi
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    {{-- ========================================== --}}
    {{-- SECTION 4: LEMBUR (DENGAN HITUNGAN DURASI) --}}
    {{-- ========================================== --}}
    @if ($report->lemburReport && $report->lemburReport->count() > 0)
        <div class="mt-6 pt-6 border-t-2 border-dashed border-slate-200">
            <h4 class="font-bold text-slate-800 mb-3 text-xs uppercase tracking-widest">
                <i class="fas fa-moon text-indigo-500 mr-2"></i>Pekerjaan Lembur
            </h4>
            <div class="space-y-4">
                @foreach ($report->lemburReport as $lembur)
                    {{-- LOGIKA HITUNG DURASI --}}
                    @php
                        $mulai = \Carbon\Carbon::parse($lembur->waktu_mulai);
                        $selesai = \Carbon\Carbon::parse($lembur->waktu_selesai);

                        // 1. Dapatkan total keseluruhan menit lembur
                        $totalMenit = $mulai->diffInMinutes($selesai);

                        // 2. Bagi 60 dan bulatkan ke bawah untuk dapat Jam bulat (contoh 48/60 = 0)
                        $jam = floor($totalMenit / 60);

                        // 3. Sisa baginya adalah Menit
                        $menit = $totalMenit % 60;

                        $teksDurasi = '';

                        // Karena $jam sudah dibulatkan (pasti angka 0, 1, 2, dst)
                        if ($jam > 0) {
                            $teksDurasi .= $jam . ' Jam ';
                        }

                        if ($menit > 0) {
                            $teksDurasi .= $menit . ' Menit';
                        }

                        // Menghapus spasi berlebih
                        $teksDurasi = trim($teksDurasi);

                        if ($teksDurasi == '') {
                            $teksDurasi = '0 Menit';
                        }
                    @endphp

                    <div
                        class="bg-white p-4 rounded-xl border border-l-4 border-l-indigo-600 border-slate-200 shadow-sm relative overflow-hidden">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mt-1">
                            <div class="flex-grow">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span
                                        class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md border border-slate-200">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $mulai->format('H:i') }} WIB - {{ $selesai->format('H:i') }} WIB
                                    </span>
                                    {{-- BADGE DURASI LEMBUR --}}
                                    <span
                                        class="text-[10px] font-black text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded-md shadow-sm">
                                        <i class="fas fa-hourglass-half mr-1"></i> Durasi: {{ $teksDurasi }}
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-slate-800 leading-relaxed">
                                    {{ $lembur->detail_pekerjaan }}</p>
                            </div>
                        </div>

                        @if ($lembur->foto_dokumentasi)
                            <div class="mt-3 pt-3 border-t border-slate-100 flex justify-start">
                                <button type="button"
                                    onclick="openImageModal('{{ asset('storage/' . $lembur->foto_dokumentasi) }}')"
                                    class="px-4 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 font-bold text-[10px] uppercase tracking-wider transition-all flex items-center">
                                    <i class="fas fa-image mr-1.5 text-sm"></i> Lihat Bukti Lembur
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- SATU FORM PERSETUJUAN UTAMA UNTUK SEMUANYA --}}
    {{-- ========================================== --}}
    @if ($report->status === 'pending')
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-inner mt-8">
            <h5 class="font-black text-slate-700 uppercase tracking-widest text-xs mb-4">
                <i class="fas fa-clipboard-check text-emerald-500 mr-2"></i>Validasi Keseluruhan (Harian & Lembur)
            </h5>
            <form action="{{ route('manager.approval.store') }}" method="POST">
                @csrf
                <input type="hidden" name="report_id" value="{{ $report->id }}">

                <div class="mb-4">
                    <textarea name="catatan_manager" rows="2"
                        class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all bg-white"
                        placeholder="Berikan catatan jika ada revisi untuk harian/lembur..."></textarea>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="status" value="approved"
                        class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-widest text-sm py-4 rounded-xl transition-colors shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-check-circle mr-2"></i> Approve Semua
                    </button>
                    <button type="submit" name="status" value="rejected"
                        class="flex-1 bg-rose-500 hover:bg-rose-600 text-white font-black uppercase tracking-widest text-sm py-4 rounded-xl transition-colors shadow-lg shadow-rose-500/30">
                        <i class="fas fa-times-circle mr-2"></i> Reject (Revisi)
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
