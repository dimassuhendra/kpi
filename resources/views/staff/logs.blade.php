@extends('layouts.staff')

@section('content')
    <div class="container mx-auto px-4 md:px-0" x-data="{ editModal: false, activeCase: {} }">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-header font-bold text-white">Log Aktivitas</h1>
                <p class="text-slate-400 font-body text-xs md:text-sm">Rekam jejak dan detail pengerjaan case kamu.</p>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="organic-card p-4 mb-6 bg-slate-800/30 rounded-xl border border-slate-700">
            <form action="{{ route('staff.kpi.logs') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:flex-1">
                    <label class="text-slate-500 text-[10px] uppercase mb-1 block">Cari Deskripsi</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari keyword kegiatan..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div class="w-full md:w-40">
                    <label class="text-slate-500 text-[10px] uppercase mb-1 block">Status</label>
                    <select name="status"
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white outline-none">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="w-full md:w-auto flex gap-2">
                    <button type="submit"
                        class="flex-1 md:flex-none bg-emerald-500 text-slate-900 px-6 py-2 rounded-lg font-bold text-sm hover:bg-emerald-400 transition">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('staff.kpi.logs') }}"
                        class="flex-1 md:flex-none text-center bg-slate-800 text-slate-400 text-sm py-2 px-4 rounded-lg hover:text-white transition">Reset</a>
                </div>
            </form>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div
                class="bg-emerald-500/20 border border-emerald-500 text-emerald-400 p-4 rounded-xl mb-6 shadow-lg shadow-emerald-900/10">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-rose-500/20 border border-rose-500 text-rose-400 p-4 rounded-xl mb-6 shadow-lg shadow-rose-900/10">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Table Section --}}
        <div class="organic-card overflow-hidden bg-slate-900/50 border border-slate-700 rounded-2xl shadow-xl">
            <table class="hidden md:table w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800/50 text-slate-400 text-[10px] uppercase tracking-widest">
                        <th class="p-5">Tanggal Laporan</th>
                        <th class="p-5 text-center">Volume Case</th>
                        <th class="p-5">Status Validasi</th>
                        <th class="p-5 text-right">Aksi</th>
                    </tr>
                </thead>

                @forelse($logs as $log)
                    <tbody class="text-slate-300 border-t border-slate-700/50" x-data="{ expanded: false }">
                        <tr class="hover:bg-slate-800/20 transition-all cursor-pointer" @click="expanded = !expanded">
                            <td class="p-5 font-medium text-white">
                                <div class="flex items-center group">
                                    <i class="fas fa-chevron-right text-[10px] mr-3 transition-transform"
                                        :class="expanded ? 'rotate-90 text-emerald-400' : ''"></i>
                                    {{ date('d F Y', strtotime($log->tanggal)) }}
                                </div>
                            </td>
                            <td class="p-5 text-center">
                                <span
                                    class="bg-slate-800 border border-slate-700 px-3 py-1 rounded-full text-xs text-slate-300">
                                    {{ $log->details->count() }} Case Aktif
                                </span>
                            </td>
                            <td class="p-5">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="w-fit text-[10px] px-3 py-1 rounded-full border uppercase font-bold tracking-tighter
                                            {{ $log->status == 'pending' ? 'text-amber-400 bg-amber-400/5 border-amber-400/20' : '' }}
                                            {{ $log->status == 'rejected' ? 'text-rose-400 bg-rose-400/5 border-rose-400/20' : '' }}
                                            {{ $log->status == 'approved' ? 'text-emerald-400 bg-emerald-400/5 border-emerald-400/20' : '' }}">
                                        {{ $log->status == 'pending' ? 'Menunggu' : ($log->status == 'rejected' ? 'Ditolak' : 'Selesai') }}
                                    </span>
                                    @if ($log->status == 'rejected' && $log->keterangan_manager)
                                        <span class="text-[9px] text-rose-300 italic max-w-[150px] truncate"
                                            title="{{ $log->keterangan_manager }}">
                                            Note: {{ $log->keterangan_manager }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-5 text-right" @click.stop>
                                @if ($log->status == 'pending' || $log->status == 'rejected')
                                    <form action="{{ route('staff.kpi.destroy', $log->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus seluruh laporan tanggal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-400 p-2 transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <i class="fas fa-check-double text-emerald-500/50 text-xs"></i>
                                @endif
                            </td>
                        </tr>

                        {{-- Expanded Details Desktop --}}
                        <tr x-show="expanded" x-cloak class="bg-slate-900/80" x-transition>
                            <td colspan="4" class="p-6 border-t border-slate-800 shadow-inner">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($log->details as $detail)
                                        <div class="bg-slate-800/40 p-5 rounded-xl border border-slate-700/50">
                                            <div class="flex justify-between items-start mb-3">
                                                <span
                                                    class="text-[9px] bg-slate-700 text-slate-300 px-2 py-0.5 rounded uppercase tracking-tighter">
                                                    Case #{{ $loop->iteration }}
                                                </span>
                                                @if ($log->status == 'pending' || $log->status == 'rejected')
                                                    <button
                                                        @click="editModal = true; activeCase = {{ json_encode($detail) }}"
                                                        class="text-blue-400 hover:text-blue-300 text-xs font-bold">
                                                        <i class="fas fa-pen mr-1"></i> Edit
                                                    </button>
                                                @endif
                                            </div>
                                            <h4 class="text-white text-sm font-semibold mb-2 leading-relaxed">
                                                {{ $detail->deskripsi_kegiatan }}
                                            </h4>
                                            <div
                                                class="flex flex-wrap gap-4 text-[10px] text-slate-400 uppercase tracking-wide border-t border-slate-700/50 pt-3 mt-3 font-bold">
                                                <span><i class="far fa-clock mr-1 text-emerald-400"></i>
                                                    {{ $detail->value_raw }} Menit</span>
                                                <span
                                                    class="{{ $detail->is_mandiri ? 'text-emerald-400' : 'text-blue-400' }}">
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $detail->is_mandiri ? 'Mandiri' : 'Bantuan' }}
                                                </span>
                                                @if ($detail->temuan_sendiri)
                                                    <span class="text-amber-400"><i class="fas fa-bolt mr-1"></i>
                                                        Temuan</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody class="text-slate-300">
                        <tr>
                            <td colspan="4" class="p-10 text-center text-slate-500 italic">Data tidak ditemukan...</td>
                        </tr>
                    </tbody>
                @endforelse
            </table>

            {{-- Mobile Version --}}
            <div class="md:hidden divide-y divide-slate-700/50">
                @forelse($logs as $log)
                    <div x-data="{ expanded: false }" class="p-4">
                        <div class="flex justify-between items-start mb-2" @click="expanded = !expanded">
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-1">
                                    {{ date('d M Y', strtotime($log->tanggal)) }}
                                </p>
                                <p class="text-white font-bold flex items-center">
                                    <i class="fas fa-chevron-right text-[10px] mr-2 transition-transform"
                                        :class="expanded ? 'rotate-90 text-emerald-400' : ''"></i>
                                    {{ $log->details->count() }} Case Aktif
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span
                                    class="w-fit text-[10px] px-3 py-1 rounded-full border uppercase font-bold tracking-tighter
                                    {{ $log->status == 'pending' ? 'text-amber-400 bg-amber-400/5 border-amber-400/20' : '' }}
                                    {{ $log->status == 'rejected' ? 'text-rose-400 bg-rose-400/5 border-rose-400/20' : '' }}
                                    {{ $log->status == 'approved' ? 'text-emerald-400 bg-emerald-400/5 border-emerald-400/20' : '' }}">
                                    {{ $log->status == 'pending' ? 'Menunggu' : ($log->status == 'rejected' ? 'Ditolak' : 'Selesai') }}
                                </span>
                                @if ($log->status == 'pending' || $log->status == 'rejected')
                                    <form action="{{ route('staff.kpi.destroy', $log->id) }}" method="POST" @click.stop
                                        onsubmit="return confirm('Hapus laporan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-500 text-xs">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Expanded Details Mobile --}}
                        <div x-show="expanded" x-cloak x-transition
                            class="mt-4 space-y-3 pt-4 border-t border-slate-700/50">
                            @foreach ($log->details as $detail)
                                <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-700/50">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-[9px] text-slate-500 uppercase">Case
                                            #{{ $loop->iteration }}</span>
                                        @if ($log->status == 'pending' || $log->status == 'rejected')
                                            <button @click="editModal = true; activeCase = {{ json_encode($detail) }}"
                                                class="text-blue-400 text-[10px] font-bold">
                                                <i class="fas fa-pen"></i> Edit
                                            </button>
                                        @endif
                                    </div>
                                    <h4 class="text-white text-sm mb-3 leading-snug">{{ $detail->deskripsi_kegiatan }}
                                    </h4>
                                    <div class="flex flex-wrap gap-3 text-[9px] font-bold uppercase text-slate-400">
                                        {{-- 1. Selalu Tampilkan Menit --}}
                                        <span>
                                            <i class="far fa-clock text-emerald-400"></i> {{ $detail->value_raw }} Menit
                                        </span>

                                        {{-- 2. Selalu Tampilkan Status Mandiri/Bantuan --}}
                                        <span class="{{ $detail->is_mandiri ? 'text-emerald-400' : 'text-blue-400' }}">
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $detail->is_mandiri ? 'Mandiri' : 'Bantuan' }}
                                        </span>

                                        {{-- 3. Jika Divisi BUKAN TAC, Tambahkan info Kategori --}}
                                        @if ($log->user->divisi->nama_divisi != 'TAC')
                                            <span
                                                class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-800/50 text-slate-300 border border-slate-700">
                                                <i class="fas fa-layer-group text-emerald-400"></i>
                                                {{ $detail->kategori ?? 'Lainnya' }}
                                            </span>
                                        @endif

                                        {{-- 4. Tampilkan badge Temuan jika ada --}}
                                        @if ($detail->temuan_sendiri)
                                            <span class="text-amber-400"><i class="fas fa-bolt mr-1"></i> Temuan</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-500 italic text-sm">Data tidak ditemukan...</div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $logs->links() }}
        </div>

        {{-- Edit Modal --}}
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 md:p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">

            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="editModal = false"></div>

            <div class="bg-slate-900 border-t md:border border-slate-700 rounded-t-3xl md:rounded-2xl p-6 md:p-8 max-w-lg w-full z-50 shadow-2xl overflow-y-auto max-h-[90vh]"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-y-full md:translate-y-10 md:scale-95"
                x-transition:enter-end="translate-y-0 md:scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-y-0 md:scale-100"
                x-transition:leave-end="translate-y-full md:translate-y-10 md:scale-95">

                <div class="w-12 h-1 bg-slate-700 rounded-full mx-auto mb-6 md:hidden"></div>

                <h2 class="text-xl font-bold text-white mb-1">Edit Detail Case</h2>
                <p class="text-slate-400 text-xs mb-6 uppercase tracking-widest">Perbarui rincian aktivitas Anda</p>

                {{-- Action Form menggunakan JavaScript String Template untuk ID --}}
                <form :action="'{{ url('/staff/kpi/case-update') }}/' + activeCase.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Deskripsi
                                Kegiatan</label>
                            <textarea name="deskripsi" x-model="activeCase.deskripsi_kegiatan" rows="4"
                                class="w-full bg-slate-800 border border-slate-700 rounded-lg p-3 text-white focus:ring-1 focus:ring-emerald-500 outline-none text-sm"
                                required></textarea>
                        </div>
                        <div>
                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Waktu Respon
                                (Menit)</label>
                            <input type="number" name="respon" x-model="activeCase.value_raw"
                                class="w-full bg-slate-800 border border-slate-700 rounded-lg p-3 text-white focus:ring-1 focus:ring-emerald-500 outline-none text-sm"
                                required>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-end gap-3 mt-8">
                        <button type="button" @click="editModal = false"
                            class="order-2 md:order-1 px-5 py-4 md:py-2 text-slate-400 hover:text-white transition text-sm text-center font-bold">
                            Batal
                        </button>
                        <button type="submit"
                            class="order-1 md:order-2 bg-emerald-500 text-slate-900 font-bold px-6 py-4 md:py-2 rounded-xl hover:bg-emerald-400 transition text-sm shadow-lg">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
