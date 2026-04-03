@extends('layouts.staff')

@section('content')
    {{-- TAMBAHKAN activeTab: 'laporan' PADA X-DATA --}}
    <div class="container mx-auto px-4 md:px-0" x-data="{ editModal: false, activeCase: {}, activeTab: 'laporan' }">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-header font-bold text-slate-800">Log Aktivitas</h1>
                <p class="text-slate-500 font-body text-xs md:text-sm">Rekam jejak dan riwayat input data Anda.</p>
            </div>

            {{-- TAB SWITCHER --}}
            @if (auth()->user()->divisi_id == 1)
                <div
                    class="bg-slate-200/50 p-1.5 rounded-2xl flex gap-2 w-full md:w-max border border-slate-200 shadow-inner">
                    <button type="button" @click="activeTab = 'laporan'"
                        :class="activeTab === 'laporan' ? 'bg-white text-indigo-600 shadow-md font-black' :
                            'text-slate-500 font-bold hover:text-slate-700'"
                        class="flex-1 md:px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                        <i class="fas fa-calendar-check mr-1"></i> Laporan
                    </button>
                    <button type="button" @click="activeTab = 'rating'"
                        :class="activeTab === 'rating' ? 'bg-white text-amber-500 shadow-md font-black' :
                            'text-slate-500 font-bold hover:text-slate-700'"
                        class="flex-1 md:px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                        <i class="fas fa-star mr-1"></i> Rating
                    </button>
                    <button type="button" @click="activeTab = 'kuis'"
                        :class="activeTab === 'kuis' ? 'bg-white text-emerald-500 shadow-md font-black' :
                            'text-slate-500 font-bold hover:text-slate-700'"
                        class="flex-1 md:px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                        <i class="fas fa-award mr-1"></i> Kuis
                    </button>
                </div>
            @endif
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl mb-6 flex items-center shadow-sm">
                <i class="fas fa-check-circle mr-3 text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif

        {{-- ========================================== --}}
        {{-- TAB 1: LOG LAPORAN HARIAN                 --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'laporan'" x-transition.opacity.duration.500ms>

            {{-- Filter Section --}}
            <div class="bg-white p-5 mb-6 rounded-2xl border border-slate-200 shadow-sm">

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Filter Laporan</h3>
                        <p class="text-xs text-slate-500">Sesuaikan data yang ingin Anda lihat atau unduh.</p>
                    </div>

                    <form action="{{ route('staff.logs.export.excel') }}" method="GET" class="w-full md:w-auto">
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                        <button type="submit"
                            class="w-full md:w-auto flex items-center justify-center gap-2 bg-emerald-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-emerald-600 transition shadow-md shadow-emerald-200">
                            <i class="fas fa-file-excel"></i> Unduh Excel
                        </button>
                    </form>
                </div>

                <hr class="border-slate-100 mb-5">

                <form action="{{ route('staff.kpi.logs') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

                        <div>
                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition">
                        </div>

                        <div>
                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition">
                        </div>

                        <div>
                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Status Validasi</label>
                            <select name="status"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 outline-none focus:border-amber-500 transition">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Cari Deskripsi</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Contoh: perbaikan..."
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition">
                        </div>

                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <a href="{{ route('staff.kpi.logs') }}"
                            class="w-full sm:w-auto text-center bg-slate-200 text-slate-600 text-sm py-2 px-6 rounded-xl hover:bg-slate-300 transition font-bold">
                            Reset Filter
                        </a>
                        <button type="submit"
                            class="w-full sm:w-auto bg-amber-500 text-white px-8 py-2 rounded-xl font-bold text-sm hover:bg-amber-600 transition shadow-md shadow-amber-200 flex items-center justify-center gap-2">
                            <i class="fas fa-search"></i> Terapkan
                        </button>
                    </div>
                </form>

            </div>

            {{-- Table Section --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
                <table class="hidden md:table w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest border-b border-slate-200">
                            <th class="p-5">Tanggal Laporan</th>
                            <th class="p-5 text-center">Jumlah Item</th>
                            <th class="p-5">Status</th>
                            <th class="p-5 text-right">Aksi</th>
                        </tr>
                    </thead>

                    @forelse($logs as $log)
                        <tbody class="text-slate-600 border-t border-slate-100" x-data="{ expanded: false }">
                            <tr class="hover:bg-slate-50 transition-all cursor-pointer" @click="expanded = !expanded">
                                <td class="p-5 font-bold text-slate-800">
                                    <div class="flex items-center group">
                                        <i class="fas fa-chevron-right text-[10px] mr-3 transition-transform text-slate-400"
                                            :class="expanded ? 'rotate-90 text-amber-500' : ''"></i>
                                        {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}
                                    </div>
                                </td>
                                <td class="p-5 text-center">
                                    <span
                                        class="bg-slate-100 border border-slate-200 px-3 py-1 rounded-full text-xs font-bold text-slate-600">
                                        {{ $log->details->count() }} Kegiatan
                                    </span>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="w-fit text-[10px] px-3 py-1 rounded-full border uppercase font-bold tracking-tighter
                                    {{ $log->status == 'pending' ? 'text-amber-600 bg-amber-50 border-amber-200' : '' }}
                                    {{ $log->status == 'rejected' ? 'text-rose-600 bg-rose-50 border-rose-200' : '' }}
                                    {{ $log->status == 'approved' ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : '' }}">
                                        {{ $log->status == 'pending' ? 'Menunggu' : ($log->status == 'rejected' ? 'Ditolak' : 'Disetujui') }}
                                    </span>
                                </td>
                                <td class="p-5 text-right" @click.stop>
                                    @if ($log->status == 'pending' || $log->status == 'rejected')
                                        <form action="{{ route('staff.kpi.destroy', $log->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Hapus seluruh laporan tanggal ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-slate-400 hover:text-rose-500 p-2 transition">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @else
                                        <i class="fas fa-check-double text-emerald-400 text-xs"></i>
                                    @endif
                                </td>
                            </tr>

                            {{-- Expanded Details Desktop --}}
                            <tr x-show="expanded" x-cloak class="bg-slate-50/50" x-transition>
                                <td colspan="4" class="p-6 border-t border-slate-100 shadow-inner">

                                    {{-- JIKA ADA CATATAN MANAGER (REVISI) --}}
                                    @if ($log->catatan_manager)
                                        <div
                                            class="mb-5 p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-start gap-3">
                                            <i class="fas fa-exclamation-circle text-rose-500 mt-0.5"></i>
                                            <div>
                                                <p class="text-xs font-bold text-rose-700 uppercase tracking-widest mb-1">
                                                    Catatan Manager (Revisi)</p>
                                                <p class="text-sm text-rose-600 italic">"{{ $log->catatan_manager }}"</p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- SUMMARY REPORTING (Khusus TAC) --}}
                                    @if ($log->user->divisi_id == 1)
                                        <div
                                            class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5 p-4 rounded-xl border border-indigo-100 bg-indigo-50/30">
                                            {{-- Shift --}}
                                            <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                                                <span
                                                    class="block text-[9px] text-slate-400 uppercase font-black mb-1">Shift
                                                    Kerja</span>
                                                @if ($log->shift)
                                                    <span
                                                        class="font-bold text-xs text-slate-700 block">{{ $log->shift->nama_shift }}</span>
                                                    <span
                                                        class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($log->shift->jam_masuk)->format('H:i') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($log->shift->jam_pulang)->format('H:i') }}</span>
                                                @else
                                                    <span class="text-xs text-rose-500 font-bold">Tidak diisi</span>
                                                @endif
                                            </div>
                                            {{-- GPS --}}
                                            <div
                                                class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm flex flex-col justify-between">
                                                <div>
                                                    <span
                                                        class="block text-[9px] text-slate-400 uppercase font-black mb-1">Status
                                                        GPS</span>
                                                    @if ($log->is_gps_ontime)
                                                        <span class="text-emerald-600 text-[10px] font-bold"><i
                                                                class="fas fa-check-circle mr-1"></i> Tepat Waktu</span>
                                                    @else
                                                        <span class="text-rose-500 text-[10px] font-bold"><i
                                                                class="fas fa-times-circle mr-1"></i> Late / None</span>
                                                    @endif
                                                </div>
                                                @if ($log->bukti_report_gps)
                                                    <button type="button"
                                                        onclick="openImageModal('{{ asset('storage/' . $log->bukti_report_gps) }}')"
                                                        class="mt-2 text-left text-[10px] text-indigo-500 font-bold hover:underline"><i
                                                            class="fas fa-image"></i> Lihat Bukti</button>
                                                @endif
                                            </div>
                                            {{-- Dashboard --}}
                                            <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                                                <span
                                                    class="block text-[9px] text-slate-400 uppercase font-black mb-1">Status
                                                    Dashboard KPI</span>
                                                @if ($log->is_dashboard_ontime)
                                                    <span class="text-emerald-600 text-[10px] font-bold block"><i
                                                            class="fas fa-check-circle mr-1"></i> Tepat Waktu</span>
                                                @else
                                                    <span class="text-rose-500 text-[10px] font-bold block"><i
                                                            class="fas fa-times-circle mr-1"></i> Terlambat</span>
                                                @endif
                                                <span class="text-[9px] text-slate-400 font-bold mt-1 block">Submit:
                                                    {{ $log->created_at->timezone('Asia/Jakarta')->format('H:i') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- LIST KEGIATAN --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($log->details as $detail)
                                            <div
                                                class="bg-white p-4 rounded-xl border border-l-4 {{ $detail->kategori == 'Network' ? 'border-l-amber-500' : 'border-l-emerald-500' }} border-slate-200 shadow-sm flex flex-col justify-between">

                                                <div>
                                                    <div class="flex justify-between items-start mb-2">
                                                        <span
                                                            class="text-[9px] uppercase font-black px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 inline-block">
                                                            {{ $detail->kategori ?? 'Aktivitas' }}
                                                        </span>
                                                        @if ($log->status == 'pending' || $log->status == 'rejected')
                                                            <div class="flex items-center gap-3">
                                                                {{-- Tombol Edit --}}
                                                                <button
                                                                    @click="editModal = true; activeCase = {{ json_encode($detail) }}"
                                                                    class="text-amber-500 hover:text-amber-700 text-xs font-bold">
                                                                    <i class="fas fa-pen"></i> Edit
                                                                </button>

                                                                {{-- Tombol Hapus --}}
                                                                <form
                                                                    action="{{ route('staff.kpi.case-destroy', $detail->id) }}"
                                                                    method="POST" class="inline"
                                                                    onsubmit="return confirm('Yakin ingin menghapus item kegiatan ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                                                                        <i class="fas fa-trash"></i> Hapus
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <h4 class="text-slate-800 text-sm font-bold mb-1 leading-relaxed">
                                                        {{ $detail->deskripsi_kegiatan }}
                                                    </h4>

                                                    @if ($detail->nomor_tiket)
                                                        <p class="text-[10px] text-indigo-600 font-bold mb-2"><i
                                                                class="fas fa-ticket-alt mr-1"></i>
                                                            {{ $detail->nomor_tiket }}
                                                        </p>
                                                    @endif

                                                    {{-- DETAIL METRIK KHUSUS TAC --}}
                                                    @if (
                                                        $log->user->divisi_id == 1 &&
                                                            $detail->kategori == 'Network' &&
                                                            strtolower($detail->deskripsi_kegiatan) !== 'monitoring network')
                                                        <div
                                                            class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-100 text-xs">
                                                            @if ($detail->temuan_sendiri)
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <span
                                                                        class="text-slate-500 font-bold text-[10px]">Tipe:</span>
                                                                    <span
                                                                        class="text-rose-500 font-bold text-[10px]">Deteksi
                                                                        Dini</span>
                                                                </div>
                                                                @if ($detail->bukti_deteksi_dini)
                                                                    <button type="button"
                                                                        onclick="openImageModal('{{ asset('storage/' . $detail->bukti_deteksi_dini) }}')"
                                                                        class="text-[10px] text-rose-500 font-bold hover:underline mb-2 block"><i
                                                                            class="fas fa-search"></i> Lihat Bukti
                                                                        Deteksi</button>
                                                                @endif
                                                            @else
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <span
                                                                        class="text-slate-500 font-bold text-[10px]">Respons:</span>
                                                                    <span
                                                                        class="text-amber-600 font-bold text-[10px]">{{ $detail->waktu_respon_menit }}
                                                                        Menit</span>
                                                                </div>
                                                                @if ($detail->bukti_respon_time)
                                                                    <button type="button"
                                                                        onclick="openImageModal('{{ asset('storage/' . $detail->bukti_respon_time) }}')"
                                                                        class="text-[10px] text-amber-600 font-bold hover:underline mb-2 block"><i
                                                                            class="fas fa-image"></i> Lihat Bukti
                                                                        Respons</button>
                                                                @endif
                                                            @endif

                                                            <div
                                                                class="flex justify-between items-start pt-2 border-t border-slate-200">
                                                                <span
                                                                    class="text-slate-500 font-bold text-[10px]">Penyelesaian:</span>
                                                                @if ($detail->is_mandiri)
                                                                    <span
                                                                        class="text-emerald-600 font-bold text-[10px]">Mandiri</span>
                                                                @else
                                                                    <span
                                                                        class="text-purple-600 font-bold text-[10px] text-right">Eskalasi<br><span
                                                                            class="text-[9px] text-slate-400">PIC:
                                                                            {{ $detail->pic_name }}</span></span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- DETAIL GPS KHUSUS TAC --}}
                                                    @if ($log->user->divisi_id == 1 && $detail->kategori == 'GPS' && $detail->value_raw !== '0')
                                                        <p class="text-[10px] text-slate-500 font-bold mt-2">Total
                                                            Kendaraan:
                                                            <span class="text-slate-800">{{ $detail->value_raw }}</span>
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- DETAIL BUKTI FOTO KHUSUS INFRA --}}
                                                @if ($log->user->divisi_id == 2 && $detail->foto_dokumentasi)
                                                    <div class="mt-4 pt-3 border-t border-slate-100">
                                                        <button type="button"
                                                            onclick="openImageModal('{{ asset('storage/' . $detail->foto_dokumentasi) }}')"
                                                            class="text-xs text-indigo-500 font-bold hover:text-indigo-700 transition">
                                                            <i class="fas fa-image mr-1"></i> Lihat Foto Dokumentasi
                                                        </button>
                                                    </div>
                                                @endif

                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="4" class="p-10 text-center text-slate-400 italic">Belum ada laporan.</td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>

                {{-- Mobile Version --}}
                <div class="md:hidden divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <div x-data="{ expanded: false }" class="p-4">
                            <div class="flex justify-between items-start mb-2" @click="expanded = !expanded">
                                <div>
                                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-1">
                                        {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d M Y') }}
                                    </p>
                                    <p class="text-slate-800 font-bold flex items-center">
                                        <i class="fas fa-chevron-right text-[10px] mr-2 transition-transform text-slate-400"
                                            :class="expanded ? 'rotate-90 text-amber-500' : ''"></i>
                                        {{ $log->details->count() }} Kegiatan
                                    </p>
                                </div>
                                <span
                                    class="w-fit text-[10px] px-3 py-1 rounded-full border uppercase font-bold tracking-tighter
                                {{ $log->status == 'pending' ? 'text-amber-600 bg-amber-50 border-amber-200' : '' }}
                                {{ $log->status == 'rejected' ? 'text-rose-600 bg-rose-50 border-rose-200' : '' }}
                                {{ $log->status == 'approved' ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : '' }}">
                                    {{ $log->status == 'pending' ? 'Pending' : ($log->status == 'rejected' ? 'Ditolak' : 'Disetujui') }}
                                </span>
                            </div>

                            {{-- Expanded Isi Mobile --}}
                            <div x-show="expanded" x-cloak x-transition
                                class="mt-4 space-y-3 pt-4 border-t border-slate-100">
                                {{-- Manager Note Mobile --}}
                                @if ($log->catatan_manager)
                                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl mb-3">
                                        <p class="text-[10px] font-bold text-rose-700 uppercase mb-1"><i
                                                class="fas fa-exclamation-circle mr-1"></i>Catatan Manager</p>
                                        <p class="text-xs text-rose-600 italic">"{{ $log->catatan_manager }}"</p>
                                    </div>
                                @endif

                                @foreach ($log->details as $detail)
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <span
                                                class="text-[9px] text-slate-500 font-bold uppercase">{{ $detail->kategori ?? 'Aktivitas' }}</span>

                                            {{-- Aksi Edit & Hapus Mobile --}}
                                            @if ($log->status == 'pending' || $log->status == 'rejected')
                                                <div class="flex items-center gap-3">
                                                    <button
                                                        @click="editModal = true; activeCase = {{ json_encode($detail) }}"
                                                        class="text-amber-500 hover:text-amber-700 text-[10px] font-bold">
                                                        <i class="fas fa-pen"></i> Edit
                                                    </button>
                                                    <form action="{{ route('staff.kpi.case-destroy', $detail->id) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirm('Hapus kegiatan ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-rose-500 hover:text-rose-700 text-[10px] font-bold">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                        <h4 class="text-slate-800 text-sm font-bold mb-2">
                                            {{ $detail->deskripsi_kegiatan }}</h4>

                                        {{-- KHUSUS TAC MOBILE --}}
                                        @if (
                                            $log->user->divisi_id == 1 &&
                                                $detail->kategori == 'Network' &&
                                                strtolower($detail->deskripsi_kegiatan) !== 'monitoring network')
                                            <div class="text-[10px] space-y-1 text-slate-500">
                                                @if ($detail->temuan_sendiri)
                                                    <p>Tipe: <span class="font-bold text-rose-500">Deteksi Dini</span></p>
                                                @else
                                                    <p>Respons: <span
                                                            class="font-bold text-amber-600">{{ $detail->waktu_respon_menit }}
                                                            Menit</span></p>
                                                @endif
                                                <p>Penyelesaian: <span
                                                        class="font-bold text-slate-700">{{ $detail->is_mandiri ? 'Mandiri' : 'Eskalasi (' . $detail->pic_name . ')' }}</span>
                                                </p>
                                            </div>
                                        @endif

                                        {{-- BUKTI FOTO INFRA MOBILE --}}
                                        @if ($log->user->divisi_id == 2 && $detail->foto_dokumentasi)
                                            <div class="mt-3 pt-2 border-t border-slate-200">
                                                <button type="button"
                                                    onclick="openImageModal('{{ asset('storage/' . $detail->foto_dokumentasi) }}')"
                                                    class="text-[10px] text-indigo-500 font-bold hover:text-indigo-700">
                                                    <i class="fas fa-image mr-1"></i> Lihat Foto Dokumentasi
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 italic text-sm">Data tidak ditemukan...</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- TAB 2: LOG RATING PELANGGAN                --}}
        {{-- ========================================== --}}
        @if (auth()->user()->divisi_id == 1)
            <div x-show="activeTab === 'rating'" style="display: none;" x-transition.opacity.duration.500ms>
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase tracking-widest text-slate-400">
                                    <th class="p-4 font-black">Detail Tiket</th>
                                    <th class="p-4 font-black text-center">Rating</th>
                                    <th class="p-4 font-black text-center">Waktu Survey</th>
                                    <th class="p-4 font-black text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($feedbacks as $fb)
                                    <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
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
                                            <span
                                                class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md">
                                                {{ \Carbon\Carbon::parse($fb->tanggal_survey)->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-center">
                                            <button type="button"
                                                onclick="openImageModal('{{ asset('storage/' . $fb->bukti_survey) }}')"
                                                class="text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 p-2 rounded-xl transition">
                                                <i class="fas fa-image fa-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-slate-400 text-sm italic">Belum ada
                                            data rating masuk.</td>
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
                                    <th class="p-4 font-black text-center">Periode</th>
                                    <th class="p-4 font-black text-center">Skor Global</th>
                                    <th class="p-4 font-black text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($assessments as $quiz)
                                    <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                        <td class="p-4 text-center">
                                            <span
                                                class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
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
                                                class="text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 p-2 rounded-xl transition">
                                                <i class="fas fa-image fa-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-slate-400 text-sm italic">Belum ada
                                            data kuis masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Edit Case --}}
        <div x-show="editModal" class="fixed inset-0 z-[60] flex items-end md:items-center justify-center p-0 md:p-4"
            style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editModal = false"></div>
            <div
                class="bg-white border-t md:border border-slate-200 rounded-t-3xl md:rounded-2xl p-6 md:p-8 max-w-lg w-full z-[70] shadow-2xl relative max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold text-slate-800 mb-1">Edit Laporan</h2>
                <p class="text-slate-400 text-xs mb-6 uppercase tracking-widest font-bold">Perbarui detail laporan Anda</p>

                <form :action="'{{ url('/staff/kpi/case-update') }}/' + activeCase.id" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        {{-- SEMUA KATEGORI: DESKRIPSI --}}
                        <div>
                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Deskripsi
                                Kegiatan</label>
                            <textarea name="deskripsi" x-model="activeCase.deskripsi_kegiatan" rows="3"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none text-sm"
                                required></textarea>
                        </div>

                        {{-- KHUSUS INFRA (FORM UPLOAD FOTO) --}}
                        <template x-if="{{ auth()->user()->divisi_id }} == 2">
                            <div class="space-y-4 pt-3 border-t border-slate-100">
                                <div>
                                    <label
                                        class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Kategori</label>
                                    <select name="kategori" x-model="activeCase.kategori"
                                        class="w-full border border-slate-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500 text-sm font-bold text-slate-700 bg-white">
                                        <option value="Network">Network</option>
                                        <option value="CCTV">CCTV</option>
                                        <option value="GPS">GPS</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Ganti Foto
                                        Dokumentasi (Max 2MB)</label>
                                    <input type="file" name="foto_dokumentasi" accept="image/*"
                                        class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 cursor-pointer">
                                    <span class="text-[9px] text-slate-400 block mt-1 italic">*Kosongkan jika tidak ingin
                                        mengganti foto</span>
                                </div>
                            </div>
                        </template>

                        {{-- KHUSUS NETWORK (TAC) --}}
                        <template
                            x-if="{{ auth()->user()->divisi_id }} == 1 && activeCase.kategori == 'Network' && activeCase.deskripsi_kegiatan !== 'Monitoring Network'">
                            <div class="space-y-4 pt-3 border-t border-slate-100">
                                {{-- Tiket --}}
                                <div>
                                    <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">No. Tiket
                                        (Opsional)</label>
                                    <input type="text" name="nomor_tiket" x-model="activeCase.nomor_tiket"
                                        class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none text-sm">
                                </div>

                                {{-- Temuan Sendiri --}}
                                <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-slate-200">
                                    <input type="checkbox" name="temuan_sendiri" value="1"
                                        :checked="activeCase.temuan_sendiri == 1"
                                        @change="activeCase.temuan_sendiri = $event.target.checked ? 1 : 0"
                                        class="w-5 h-5 rounded text-amber-500 focus:ring-amber-500 border-slate-300">
                                    <label class="text-xs font-bold text-slate-600">Deteksi Dini / Temuan Sendiri</label>
                                </div>

                                {{-- Waktu Respon & Bukti (Jika bukan temuan) --}}
                                <template x-if="activeCase.temuan_sendiri == 0">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Respons
                                                (Menit)</label>
                                            <input type="number" name="waktu_respon_menit"
                                                x-model="activeCase.waktu_respon_menit"
                                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:border-amber-500 outline-none text-sm">
                                        </div>
                                        <div>
                                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Ganti
                                                Bukti Respon</label>
                                            <input type="file" name="bukti_respon_time" accept="image/*"
                                                class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-amber-100 file:text-amber-700">
                                            <span class="text-[8px] text-slate-400 block mt-1">*Kosongkan jika tidak
                                                diganti</span>
                                        </div>
                                    </div>
                                </template>

                                {{-- Bukti Deteksi Dini (Jika temuan) --}}
                                <template x-if="activeCase.temuan_sendiri == 1">
                                    <div>
                                        <label class="text-rose-400 text-[10px] uppercase mb-1 block font-bold">Ganti Bukti
                                            Deteksi Dini</label>
                                        <input type="file" name="bukti_deteksi_dini" accept="image/*"
                                            class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-rose-50 file:text-rose-600">
                                        <span class="text-[8px] text-slate-400 block mt-1">*Kosongkan jika tidak
                                            diganti</span>
                                    </div>
                                </template>

                                {{-- Status Penyelesaian --}}
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                    <label class="text-slate-500 text-[10px] uppercase mb-2 block font-bold">Status
                                        Penyelesaian</label>
                                    <select name="is_mandiri" x-model="activeCase.is_mandiri"
                                        class="w-full border border-slate-300 rounded-lg px-3 py-2 outline-none focus:border-amber-500 text-xs font-bold text-slate-700 mb-3 bg-white">
                                        <option value="1">Penyelesaian Mandiri</option>
                                        <option value="0">Eskalasi / Bantuan Tim Lain</option>
                                    </select>

                                    <template x-if="activeCase.is_mandiri == 0">
                                        <div>
                                            <label class="text-rose-400 text-[10px] uppercase mb-1 block font-bold">Nama
                                                PIC / Tim Bantuan</label>
                                            <input type="text" name="pic_name" x-model="activeCase.pic_name"
                                                class="w-full bg-white border border-rose-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-rose-500">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- KHUSUS GPS (TAC) --}}
                        <template
                            x-if="{{ auth()->user()->divisi_id }} == 1 && activeCase.kategori == 'GPS' && activeCase.deskripsi_kegiatan !== 'Monitoring GPS'">
                            <div class="pt-3 border-t border-slate-100">
                                <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Jumlah
                                    Kendaraan</label>
                                <input type="text" name="value_raw" x-model="activeCase.value_raw"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none text-sm">
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-col md:flex-row justify-end gap-3 mt-8">
                        <button type="button" @click="editModal = false"
                            class="order-2 md:order-1 px-5 py-2 text-slate-500 font-bold text-sm">Batal</button>
                        <button type="submit"
                            class="order-1 md:order-2 bg-amber-500 text-white font-bold px-6 py-2 rounded-xl hover:bg-amber-600 transition shadow-lg shadow-amber-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- MODAL PREVIEW GAMBAR --}}
    <div id="imagePreviewModal"
        class="fixed inset-0 z-[100] hidden bg-slate-900/90 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
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

    <script>
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
