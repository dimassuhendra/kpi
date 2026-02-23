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
                                    {{ date('d F Y', strtotime($log->tanggal)) }}
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($log->details as $detail)
                                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                                            <div class="flex justify-between items-start mb-3">
                                                <span
                                                    class="text-[9px] bg-slate-800 text-white px-2 py-0.5 rounded uppercase font-bold tracking-widest">
                                                    Item #{{ $loop->iteration }}
                                                </span>
                                                @if ($log->status == 'pending' || $log->status == 'rejected')
                                                    <button
                                                        @click="editModal = true; activeCase = {{ json_encode($detail) }}"
                                                        class="text-amber-600 hover:text-amber-700 text-xs font-bold flex items-center">
                                                        <i class="fas fa-pen mr-1"></i> Edit
                                                    </button>
                                                @endif
                                            </div>
                                            <h4 class="text-slate-800 text-sm font-bold mb-2 leading-relaxed">
                                                {{ $detail->deskripsi_kegiatan }}
                                            </h4>

                                            <div
                                                class="flex flex-wrap gap-3 text-[10px] text-slate-500 uppercase tracking-wide border-t border-slate-100 pt-3 mt-3 font-bold">
                                                {{-- LOGIKA PERBAIKAN DISINI --}}
                                                @if ($log->user->divisi_id == 1)
                                                    {{-- Divisi TAC (Pakai Menit) --}}
                                                    <span><i class="far fa-clock mr-1 text-amber-500"></i>
                                                        {{ $detail->value_raw }} Menit</span>
                                                    <span
                                                        class="{{ $detail->is_mandiri ? 'text-blue-600' : 'text-purple-600' }}">
                                                        <i class="fas fa-user-tag mr-1"></i>
                                                        {{ $detail->is_mandiri ? 'Mandiri' : 'Bantuan' }}
                                                    </span>
                                                @else
                                                    {{-- Divisi Infra & Backoffice (Pakai Kategori) --}}
                                                    <span
                                                        class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                                        <i class="fas fa-tag text-amber-500"></i>
                                                        {{ $detail->kategori ?? ($log->user->divisi_id == 2 ? 'Infrastruktur' : 'General') }}
                                                    </span>
                                                @endif

                                                @if ($detail->temuan_sendiri)
                                                    <span class="text-orange-600"><i class="fas fa-search mr-1"></i>
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
                                    {{ date('d M Y', strtotime($log->tanggal)) }}
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
                                {{ $log->status == 'pending' ? 'Pending' : ($log->status == 'rejected' ? 'No' : 'Approved') }}
                            </span>
                        </div>

                        <div x-show="expanded" x-cloak x-transition class="mt-4 space-y-3 pt-4 border-t border-slate-100">
                            @foreach ($log->details as $detail)
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-[9px] text-slate-500 font-bold uppercase">Item
                                            #{{ $loop->iteration }}</span>
                                    </div>
                                    <h4 class="text-slate-800 text-sm font-bold mb-3">{{ $detail->deskripsi_kegiatan }}
                                    </h4>
                                    <div class="flex flex-wrap gap-3 text-[9px] font-bold uppercase text-slate-500">
                                        @if ($log->user->divisi_id == 1)
                                            <span><i class="far fa-clock text-amber-500"></i> {{ $detail->value_raw }}
                                                Menit</span>
                                        @else
                                            <span><i class="fas fa-tag text-amber-500"></i>
                                                {{ $detail->kategori ?? 'Kegiatan' }}</span>
                                        @endif
                                    </div>
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
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 md:p-4"
            style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editModal = false"></div>
            <div
                class="bg-white border-t md:border border-slate-200 rounded-t-3xl md:rounded-2xl p-6 md:p-8 max-w-lg w-full z-50 shadow-2xl">
                <h2 class="text-xl font-bold text-slate-800 mb-1">Edit Laporan</h2>
                <p class="text-slate-400 text-xs mb-6 uppercase tracking-widest font-bold">Perbarui data laporan Anda</p>

                <form :action="'{{ url('/staff/kpi/case-update') }}/' + activeCase.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Deskripsi
                                Kegiatan</label>
                            <textarea name="deskripsi" x-model="activeCase.deskripsi_kegiatan" rows="4"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none text-sm"
                                required></textarea>
                        </div>
                        <template x-if="{{ auth()->user()->divisi_id }} == 1">
                            <div>
                                <label class="text-slate-500 text-[10px] uppercase mb-1 block font-bold">Waktu Respon
                                    (Menit)</label>
                                <input type="number" name="respon" x-model="activeCase.value_raw"
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
@endsection
