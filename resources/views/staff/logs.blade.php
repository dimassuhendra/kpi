@extends('layouts.staff')

@section('content')
    <div class="container mx-auto px-4 md:px-0" x-data="{ editModal: false, activeCase: {} }">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-header font-bold text-slate-800">Log Aktivitas</h1>
                <p class="text-slate-500 font-body text-xs md:text-sm">Rekam jejak dan detail pengerjaan laporan harian Anda.
                </p>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white p-5 mb-6 rounded-2xl border border-slate-200 shadow-sm">
            <form action="{{ route('staff.kpi.logs') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:flex-1">
                    <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Cari Deskripsi</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari keyword kegiatan..."
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition">
                </div>
                <div class="w-full md:w-48">
                    <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Status Validasi</label>
                    <select name="status"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 outline-none focus:border-amber-500 transition">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                    </select>
                </div>
                <div class="w-full md:w-auto flex gap-2">
                    <button type="submit"
                        class="flex-1 md:flex-none bg-amber-500 text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-amber-600 transition shadow-md shadow-amber-200">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('staff.kpi.logs') }}"
                        class="flex-1 md:flex-none text-center bg-slate-200 text-slate-600 text-sm py-2 px-4 rounded-xl hover:bg-slate-300 transition font-bold">Reset</a>
                </div>
            </form>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl mb-6 flex items-center shadow-sm">
                <i class="fas fa-check-circle mr-3 text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Table Section --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <table class="hidden md:table w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest border-b border-slate-200">
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
                                    <form action="{{ route('staff.kpi.destroy', $log->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus seluruh laporan tanggal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-rose-500 p-2 transition">
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
                                            <span class="block text-[9px] text-slate-400 uppercase font-black mb-1">Shift
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
                                            <span class="block text-[9px] text-slate-400 uppercase font-black mb-1">Status
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
                                            class="bg-white p-4 rounded-xl border border-l-4 {{ $detail->kategori == 'Network' ? 'border-l-amber-500' : 'border-l-emerald-500' }} border-slate-200 shadow-sm">

                                            <div class="flex justify-between items-start mb-2">
                                                <span
                                                    class="text-[9px] uppercase font-black px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 inline-block">
                                                    {{ $detail->kategori ?? 'Aktivitas' }}
                                                </span>
                                                @if ($log->status == 'pending' || $log->status == 'rejected')
                                                    <button
                                                        @click="editModal = true; activeCase = {{ json_encode($detail) }}"
                                                        class="text-amber-500 hover:text-amber-700 text-xs font-bold">
                                                        <i class="fas fa-pen"></i> Edit
                                                    </button>
                                                @endif
                                            </div>

                                            <h4 class="text-slate-800 text-sm font-bold mb-1 leading-relaxed">
                                                {{ $detail->deskripsi_kegiatan }}
                                            </h4>

                                            @if ($detail->nomor_tiket)
                                                <p class="text-[10px] text-indigo-600 font-bold mb-2"><i
                                                        class="fas fa-ticket-alt mr-1"></i> {{ $detail->nomor_tiket }}</p>
                                            @endif

                                            {{-- DETAIL METRIK (KHUSUS NETWORK) --}}
                                            @if ($detail->kategori == 'Network' && strtolower($detail->deskripsi_kegiatan) !== 'monitoring network')
                                                <div
                                                    class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-100 text-xs">
                                                    @if ($detail->temuan_sendiri)
                                                        <div class="flex justify-between items-center mb-1">
                                                            <span class="text-slate-500 font-bold text-[10px]">Tipe:</span>
                                                            <span class="text-rose-500 font-bold text-[10px]">Deteksi
                                                                Dini</span>
                                                        </div>
                                                        @if ($detail->bukti_deteksi_dini)
                                                            <button type="button"
                                                                onclick="openImageModal('{{ asset('storage/' . $detail->bukti_deteksi_dini) }}')"
                                                                class="text-[10px] text-rose-500 font-bold hover:underline mb-2 block"><i
                                                                    class="fas fa-search"></i> Lihat Bukti Deteksi</button>
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
                                                                    class="fas fa-image"></i> Lihat Bukti Respons</button>
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

                                            {{-- DETAIL GPS --}}
                                            @if ($detail->kategori == 'GPS' && $detail->value_raw !== '0')
                                                <p class="text-[10px] text-slate-500 font-bold mt-2">Total Kendaraan: <span
                                                        class="text-slate-800">{{ $detail->value_raw }}</span></p>
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
                        <div x-show="expanded" x-cloak x-transition class="mt-4 space-y-3 pt-4 border-t border-slate-100">
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
                                    <div class="flex justify-between mb-2">
                                        <span
                                            class="text-[9px] text-slate-500 font-bold uppercase">{{ $detail->kategori ?? 'Aktivitas' }}</span>
                                    </div>
                                    <h4 class="text-slate-800 text-sm font-bold mb-2">{{ $detail->deskripsi_kegiatan }}
                                    </h4>

                                    @if ($detail->kategori == 'Network' && strtolower($detail->deskripsi_kegiatan) !== 'monitoring network')
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
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 italic text-sm">Data tidak ditemukan...</div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $logs->links() }}
        </div>

        {{-- Edit Modal --}}
        {{-- Edit Modal --}}
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
