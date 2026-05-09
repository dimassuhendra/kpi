@extends('layouts.manager')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Logika untuk Menentukan Judul Laporan --}}
        @php
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);

            // Cek apakah rentang tanggal adalah exactly 1 bulan penuh
            $isFullMonth =
                $start->copy()->startOfMonth()->isSameDay($start) &&
                $end->copy()->endOfMonth()->isSameDay($end) &&
                $start->isSameMonth($end);

            if ($isFullMonth) {
                $judulLaporan = 'Rapot Pekerjaan Bulan ' . $start->translatedFormat('F Y');
            } else {
                $judulLaporan = 'Rapot Pekerjaan Periode ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
            }
        @endphp

        {{-- Header --}}
        <div
            class="bg-white p-8 rounded-[2.5rem] border border-background shadow-sm flex justify-between items-center font-body">
            <div>
                {{-- Judul Dinamis --}}
                <h1 class="text-2xl font-black tracking-tighter text-primary uppercase font-header">
                    {{ $judulLaporan }}
                </h1>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-secondary mt-1">
                    Evaluasi Performa & Intelligence Reports
                </p>
            </div>
            <div class="bg-background w-12 h-12 rounded-2xl flex items-center justify-center text-primary">
                <i class="fas fa-file-export text-xl"></i>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-primary p-6 rounded-[2.5rem] shadow-xl shadow-accent/30 space-y-4 font-body">

            {{-- Quick Filter Pills (Bulan Sebelumnya) --}}
            <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
                @php
                    // Bikin array 3 bulan terakhir untuk tombol cepat
                    $months = [
                        now()->startOfMonth(),
                        now()->subMonth()->startOfMonth(),
                        now()->subMonths(2)->startOfMonth(),
                    ];
                @endphp

                @foreach ($months as $m)
                    <a href="{{ route('manager.reports.index', [
                        'start_date' => $m->copy()->startOfMonth()->format('Y-m-d'),
                        'end_date' => $m->copy()->endOfMonth()->format('Y-m-d'),
                    ]) }}"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap
                              {{ $start->isSameMonth($m) && $isFullMonth
                                  ? 'bg-white text-primary shadow-lg shadow-black/10'
                                  : 'bg-secondary text-white hover:bg-accent hover:text-white border border-secondary' }}">
                        {{ $m->translatedFormat('F Y') }}
                    </a>
                @endforeach
            </div>

            {{-- Filter Rentang Tanggal Manual (Custom) --}}
            <form action="{{ route('manager.reports.index') }}" method="GET"
                class="flex flex-col md:flex-row gap-4 items-end border-t border-secondary pt-4">
                <div class="flex-1">
                    <label class="text-[9px] uppercase font-black text-accent mb-2 block ml-1">Periode Awal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full bg-secondary border-none rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-background focus:outline-none transition-all">
                </div>
                <div class="flex-1">
                    <label class="text-[9px] uppercase font-black text-accent mb-2 block ml-1">Periode Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full bg-secondary border-none rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-background focus:outline-none transition-all">
                </div>
                <button type="submit"
                    class="bg-accent hover:bg-white text-primary px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-md">
                    Terapkan Custom
                </button>
            </form>
        </div>

        {{-- Daftar Staff Per Divisi --}}
        @foreach ($staffs as $divisi => $group)
            <div class="space-y-4 font-body">
                {{-- Header Divisi dengan Tombol Export --}}
                <div class="flex items-center justify-between ml-2 mb-6">
                    <div class="flex items-center gap-4 flex-1">
                        <h2 class="text-xs font-black uppercase tracking-[0.3em] text-primary font-header">
                            {{ $divisi ?? 'Tanpa Divisi' }}
                        </h2>
                        <div class="h-px bg-background flex-1 mr-4"></div>
                    </div>

                    {{-- Tombol Export Excel Per Divisi --}}
                    <a href="{{ route('manager.reports.export.divisi', ['divisi' => $divisi, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                        class="bg-secondary hover:bg-primary text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md shadow-accent/50 transition-all flex items-center gap-2">
                        <i class="fas fa-users"></i>
                        <span>Export Divisi</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($group as $s)
                        <div
                            class="bg-white border border-background p-6 rounded-[2rem] shadow-sm hover:shadow-md transition-all group relative overflow-hidden">

                            {{-- Bubble Background on Hover --}}
                            <div
                                class="absolute -right-4 -top-4 w-20 h-20 bg-background rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 transform scale-0 group-hover:scale-150">
                            </div>

                            <div class="relative z-10">
                                <div class="flex items-start justify-between mb-4">
                                    <div
                                        class="w-12 h-12 bg-background rounded-2xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <span
                                        class="text-[9px] font-black px-3 py-1 bg-background rounded-full text-primary uppercase tracking-tighter">
                                        {{ $s->daily_reports_count }} Approved Reports
                                    </span>
                                </div>

                                <h3 class="font-bold text-primary font-header text-lg leading-tight mb-1">
                                    {{ $s->nama_lengkap }}
                                </h3>
                                <p class="text-[10px] text-secondary font-bold uppercase tracking-widest mb-6">
                                    {{ $s->divisi->nama_divisi ?? 'Tanpa Divisi' }}
                                </p>

                                <div class="flex gap-3 w-full">
                                    <a href="{{ route('manager.reports.export', ['user_id' => $s->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                        class="flex-1 bg-primary hover:bg-secondary text-white text-center py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-accent/40 transition-all flex items-center justify-center gap-2">
                                        <i class="fas fa-file-excel text-sm"></i>
                                        <span>Excel</span>
                                    </a>

                                    <button onclick="exportUserPdf({{ $s->id }})" disabled
                                        class="flex-1 bg-rose-500 text-white text-center py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-200 transition-all flex items-center justify-center gap-2 disabled:opacity-90 disabled:cursor-not-allowed">
                                        <i class="fas fa-file-pdf text-sm"></i>
                                        <span>PDF</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Tombol Reset Range (Berbahaya - tetap letakkan di bagian bawah/terpisah) --}}
        <div class="pt-10 border-t border-slate-100 flex justify-center">
            <button id="btnResetData"
                class="text-rose-400 hover:text-rose-600 text-[10px] font-black uppercase tracking-[0.2em] transition-all">
                <i class="fas fa-exclamation-triangle mr-2"></i> Wipe Historical Data In Range
            </button>
        </div>
    </div>

    {{-- Script tetap gunakan Fetch untuk Preview atau Reset Data --}}
    <script>
        function exportUserPdf(userId) {
            // 1. Ambil nilai tanggal dari input filter yang ada di halaman
            const startDate = document.getElementsByName('start_date')[0].value;
            const endDate = document.getElementsByName('end_date')[0].value;

            if (!startDate || !endDate) {
                alert('Silakan pilih rentang tanggal terlebih dahulu!');
                return;
            }

            // 2. Buat Form secara dinamis
            const formExport = document.createElement('form');
            formExport.method = 'POST';
            formExport.action = "{{ route('manager.export.pdf') }}";
            formExport.target = '_blank';

            // 3. Tambahkan CSRF Token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = "{{ csrf_token() }}";
            formExport.appendChild(csrfInput);

            // 4. Tambahkan User ID
            const userInput = document.createElement('input');
            userInput.type = 'hidden';
            userInput.name = 'user_id';
            userInput.value = userId;
            formExport.appendChild(userInput);

            // 5. Tambahkan Filter Tanggal agar PDF sinkron dengan pilihan Manager
            const startInput = document.createElement('input');
            startInput.type = 'hidden';
            startInput.name = 'start_date';
            startInput.value = startDate;
            formExport.appendChild(startInput);

            const endInput = document.createElement('input');
            endInput.type = 'hidden';
            endInput.name = 'end_date';
            endInput.value = endDate;
            formExport.appendChild(endInput);

            // 6. Eksekusi
            document.body.appendChild(formExport);
            formExport.submit();
            document.body.removeChild(formExport);
        }

        document.getElementById('btnResetData').addEventListener('click', function() {
            const userId = document.getElementById('user_id').value;
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            const staffName = document.getElementById('user_id').options[document.getElementById('user_id')
                .selectedIndex].text;

            if (!start || !end) return alert('Pilih rentang tanggal penghapusan!');

            const confirmMsg =
                `PERINGATAN KERAS!\n\nAnda akan menghapus SEMUA data laporan dari:\nTarget: ${staffName}\nPeriode: ${start} s/d ${end}\n\nData yang dihapus TIDAK DAPAT dikembalikan. Lanjutkan?`;

            if (confirm(confirmMsg)) {
                const secondConfirm = confirm("Konfirmasi terakhir: Anda benar-benar yakin?");
                if (!secondConfirm) return;

                const btn = this;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                btn.disabled = true;

                fetch("{{ route('manager.reports.destroy') }}", {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            start_date: start,
                            end_date: end
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Refresh untuk melihat perubahan
                        } else {
                            alert(data.error || data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Reset Error:', error);
                        alert('Terjadi kesalahan sistem.');
                    })
                    .finally(() => {
                        btn.innerHTML = '<i class="fas fa-trash-alt text-xs"></i> Reset Data';
                        btn.disabled = false;
                    });
            }
        });
    </script>
@endsection
