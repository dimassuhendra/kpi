<div class="space-y-6">

    {{-- SECTION 1: DOCUMENTATION & REPORTING (Hanya untuk TAC) --}}
    @if ($report->user->divisi_id == 1)
        <div class="bg-indigo-50/50 rounded-xl border border-indigo-100 p-5">
            <h4 class="font-bold text-slate-800 mb-4 text-xs uppercase tracking-widest flex items-center">
                <i class="fas fa-file-signature text-indigo-500 mr-2 text-base"></i> Documentation & Reporting
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Info Shift --}}
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

                {{-- Status & Bukti GPS --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="block text-[10px] text-slate-400 uppercase font-black mb-1">Report GPS</span>
                        @if ($report->is_gps_ontime)
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold"><i
                                    class="fas fa-check-circle"></i> Report GPS Sesuai Jadwal</span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-rose-100 text-rose-700 text-xs font-bold"><i
                                    class="fas fa-times-circle"></i> Report GPS Terlambat/Tidak Ada</span>
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

                {{-- Status Waktu Submit Dashboard KPI --}}
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
                    {{-- JIKA DIVISI 1 (TAC) -> TAMPILKAN FORMAT LENGKAP --}}
                    @if ($report->user->divisi_id == 1)
                        <div
                            class="bg-white p-4 rounded-xl border {{ $case->kategori == 'Network' ? 'border-l-4 border-l-amber-500 border-slate-200' : 'border-l-4 border-l-emerald-500 border-slate-200' }} shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">

                                {{-- Info Utama Case --}}
                                <div class="flex-grow">
                                    <span
                                        class="text-[10px] uppercase font-black px-2 py-0.5 rounded-md {{ $case->kategori == 'Network' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }} mb-2 inline-block">
                                        {{ $case->kategori }}
                                    </span>

                                    <p class="text-sm font-bold text-slate-800">{{ $case->deskripsi_kegiatan }}</p>

                                    @if ($case->nomor_tiket)
                                        <p class="text-xs text-indigo-600 font-bold mt-1.5"><i
                                                class="fas fa-ticket-alt mr-1"></i> Tiket: {{ $case->nomor_tiket }}</p>
                                    @endif

                                    @if ($case->kategori == 'GPS')
                                        <p class="text-xs text-slate-500 font-semibold mt-2">
                                            Jumlah Kendaraan:
                                            <span class="text-slate-800 font-bold">
                                                {{ $case->value_raw == '0' ? 'ALL' : $case->value_raw }}
                                            </span>
                                        </p>
                                    @endif
                                </div>

                                {{-- Info Metrik & Bukti (Khusus Network) --}}
                                @if ($case->kategori == 'Network' && strtolower($case->deskripsi_kegiatan) !== 'monitoring network')
                                    <div
                                        class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-xs min-w-[250px]">

                                        @if ($case->temuan_sendiri)
                                            <div
                                                class="flex justify-between items-center mb-2 pb-2 border-b border-slate-200">
                                                <span class="font-bold text-slate-500">Kategori Case:</span>
                                                <span class="font-black text-rose-500">Deteksi Dini</span>
                                            </div>
                                            @if ($case->bukti_deteksi_dini)
                                                <button type="button"
                                                    onclick="openImageModal('{{ asset('storage/' . $case->bukti_deteksi_dini) }}')"
                                                    class="w-full text-center py-1.5 rounded bg-rose-100 text-rose-600 hover:bg-rose-200 font-bold text-[10px] uppercase tracking-wider mb-2 transition">
                                                    <i class="fas fa-search mr-1"></i> Lihat Bukti Deteksi
                                                </button>
                                            @endif
                                        @else
                                            <div
                                                class="flex justify-between items-center mb-2 pb-2 border-b border-slate-200">
                                                <span class="font-bold text-slate-500">Waktu Respon:</span>
                                                <span class="font-black text-amber-600">{{ $case->waktu_respon_menit }}
                                                    Menit</span>
                                            </div>
                                            @if ($case->bukti_respon_time)
                                                <button type="button"
                                                    onclick="openImageModal('{{ asset('storage/' . $case->bukti_respon_time) }}')"
                                                    class="w-full text-center py-1.5 rounded bg-amber-100 text-amber-600 hover:bg-amber-200 font-bold text-[10px] uppercase tracking-wider mb-2 transition">
                                                    <i class="fas fa-image mr-1"></i> Lihat Bukti Respon
                                                </button>
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

                        {{-- JIKA BUKAN DIVISI 1 (INFRA DLL) -> TAMPILKAN FORMAT SEDERHANA --}}
                    @else
                        <div
                            class="bg-white p-4 rounded-xl border border-l-4 border-l-indigo-500 border-slate-200 shadow-sm flex flex-col justify-between">
                            <div>
                                <span
                                    class="text-[10px] uppercase font-black px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-600 mb-2 inline-block">
                                    {{ $case->kategori ?? 'Aktivitas' }}
                                </span>
                                <p class="text-sm font-bold text-slate-800 leading-relaxed pb-2">
                                    {{ $case->deskripsi_kegiatan }}</p>
                            </div>

                            @if ($case->foto_dokumentasi)
                                <div class="mt-2 pt-3 border-t border-slate-100 flex justify-start">
                                    <button type="button"
                                        onclick="openImageModal('{{ asset('storage/' . $case->foto_dokumentasi) }}')"
                                        class="px-4 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 font-bold text-[10px] uppercase tracking-wider transition-all flex items-center">
                                        <i class="fas fa-image mr-1.5 text-sm"></i> Lihat Dokumentasi
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
            <div class="space-y-2">
                @foreach ($activities as $act)
                    <div
                        class="bg-white p-3 rounded-xl border border-l-4 border-l-slate-400 border-slate-200 shadow-sm">
                        <p class="text-sm font-bold text-slate-700">{{ $act->deskripsi_kegiatan }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- FORM PERSETUJUAN --}}
    <div class="mt-8 pt-6 border-t border-slate-200">
        <form action="{{ route('manager.approval.store') }}" method="POST">
            @csrf
            <input type="hidden" name="report_id" value="{{ $report->id }}">

            <div class="mb-5">
                <label class="block text-xs uppercase font-black text-slate-500 mb-2">Catatan Manager (Opsional)</label>
                <textarea name="catatan_manager" rows="3"
                    class="w-full border border-slate-300 rounded-xl p-4 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all bg-white"
                    placeholder="Berikan feedback atau alasan jika rejected..."></textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" name="status" value="approved"
                    class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-widest text-sm py-4 rounded-xl transition-colors shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-check-circle mr-2"></i> Approve Laporan
                </button>
                <button type="submit" name="status" value="rejected"
                    class="flex-1 bg-rose-500 hover:bg-rose-600 text-white font-black uppercase tracking-widest text-sm py-4 rounded-xl transition-colors shadow-lg shadow-rose-500/30">
                    <i class="fas fa-times-circle mr-2"></i> Reject (Revisi)
                </button>
            </div>
        </form>
    </div>

</div>
