<div class="animate-fadeIn">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Technical Cases --}}
        <div>
            <h4 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-4 flex items-center">
                <i class="fas fa-microchip mr-2"></i> Technical Cases
            </h4>
            <div class="space-y-3">
                @forelse ($cases as $case)
                    {{-- Di dalam @forelse ($cases as $case) --}}
                    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            {{-- Deskripsi: Hanya Judul (Misal: Monitoring GPS atau Perbaikan GPS) --}}
                            <p class="text-xs font-bold text-slate-700 leading-relaxed">
                                {{ $case->deskripsi_kegiatan }}
                            </p>
                            <span
                                class="text-[10px] font-mono text-emerald-600 font-black bg-emerald-50 px-2 py-1 rounded-lg">
                                +{{ $case->nilai_akhir }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-3">
                            @php
                                $isInfra = $report->user->divisi_id == 2;
                                // Cek kategori GPS dari database
                                $isGps = $case->kategori == 'GPS';
                            @endphp

                            @if ($isInfra)
                                {{-- Badge INFRA --}}
                                <span class="text-[9px] text-blue-500 font-bold uppercase bg-blue-50 px-2 py-1 rounded">
                                    <i class="fas fa-layer-group mr-1"></i> Kategori: {{ $case->kategori }}
                                </span>
                            @elseif($isGps)
                                {{-- Badge GPS: Menampilkan Jumlah Kendaraan dari value_raw --}}
                                <span
                                    class="text-[9px] text-emerald-600 font-bold uppercase bg-emerald-50 px-2 py-1 rounded flex items-center w-fit">
                                    <i class="fas fa-car mr-1"></i>
                                    {{-- Jika value_raw berisi 'ALL', tampilkan 'ALL Kendaraan', jika angka tampilkan '10 Kendaraan' --}}
                                    {{ $case->value_raw }} Kendaraan
                                </span>

                                {{-- Tambahkan Label Monitoring jika perlu --}}
                                @if ($case->value_raw === 'ALL')
                                    <span
                                        class="text-[9px] text-slate-400 font-bold uppercase bg-slate-100 px-2 py-1 rounded">
                                        Monitoring Rutin
                                    </span>
                                @endif
                            @else
                                {{-- Badge Network TAC --}}
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        class="text-[9px] text-slate-400 font-bold uppercase bg-slate-50 px-2 py-1 rounded">
                                        <i class="far fa-clock mr-1 text-emerald-500"></i> {{ $case->value_raw }}m
                                    </span>

                                    @if ($case->temuan_sendiri)
                                        <span
                                            class="text-[9px] text-amber-500 font-bold uppercase bg-amber-50 px-2 py-1 rounded">
                                            Temuan TAC
                                        </span>
                                    @endif

                                    {{-- PERBAIKAN DI SINI: Logika pengecekan Mandiri vs Bantuan --}}
                                    @if ($case->is_mandiri == 1)
                                        <span
                                            class="text-[9px] text-emerald-600 font-bold uppercase bg-emerald-50 px-2 py-1 rounded">
                                            <i class="fas fa-user mr-1"></i> Penyelesaian TAC
                                        </span>
                                    @else
                                        <span
                                            class="text-[9px] text-blue-600 font-bold uppercase bg-blue-50 px-2 py-1 rounded">
                                            <i class="fas fa-hands-helping mr-1"></i> Assist:
                                            {{ $case->pic_name ?? 'N/A' }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-[10px] text-slate-300 font-bold italic uppercase">No technical cases reported</p>
                @endforelse
            </div>
        </div>

        {{-- General Activities --}}
        <div>
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center">
                <i class="fas fa-tasks mr-2"></i> General Activities
            </h4>
            <div class="space-y-2">
                @forelse ($activities as $act)
                    <div class="p-3 bg-slate-100/50 border-l-2 border-slate-200 rounded-r-xl">
                        {{-- Sesuai permintaan: Hanya teks deskripsi tanpa tulisan apapun di bawahnya --}}
                        <p class="text-xs text-slate-500 italic">"{{ $act->deskripsi_kegiatan }}"</p>
                    </div>
                @empty
                    <p class="text-[10px] text-slate-300 font-bold italic uppercase">No general activities</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Action Footer tetap sama --}}
    <div class="mt-8 pt-6 border-t border-slate-100">
        <form action="{{ route('manager.approval.store') }}" method="POST"
            class="flex flex-col md:flex-row items-end md:items-center justify-between gap-4">
            @csrf
            <input type="hidden" name="report_id" value="{{ $report->id }}">

            <div class="w-full md:max-w-xs">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Manager's Note
                    (Optional)</label>
                <input type="text" name="keterangan_manager" placeholder="Alasan reject atau apresiasi..."
                    class="w-full text-xs border-slate-200 bg-slate-50 rounded-xl px-4 py-2.5 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" name="status" value="rejected"
                    class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                    Reject
                </button>
                <button type="submit" name="status" value="approved"
                    class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-100 transition-all flex items-center gap-2">
                    <i class="fas fa-check-double"></i> Approve Mission
                </button>
            </div>
        </form>
    </div>
</div>
