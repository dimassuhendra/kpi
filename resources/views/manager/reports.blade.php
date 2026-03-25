@extends('layouts.manager')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase">Intelligence <span
                        class="text-emerald-600">Reports</span></h1>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mt-1">Pilih staff untuk mengunduh
                    analisa performa mandiri</p>
            </div>
            <div class="bg-emerald-50 w-12 h-12 rounded-2xl flex items-center justify-center text-emerald-600"><i
                    class="fas fa-file-export"></i></div>
        </div>

        {{-- Filter Rentang Tanggal --}}
        <div class="bg-slate-900 p-6 rounded-[2.5rem] shadow-xl">
            <form action="{{ route('manager.reports.index') }}" method="GET"
                class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="text-[9px] uppercase font-black text-slate-400 mb-2 block ml-1">Periode Awal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>
                <div class="flex-1">
                    <label class="text-[9px] uppercase font-black text-slate-400 mb-2 block ml-1">Periode Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                    Update List
                </button>
            </form>
        </div>

        {{-- Daftar Staff Per Divisi --}}
        @foreach ($staffs as $divisi => $group)
            <div class="space-y-4">
                <div class="flex items-center gap-4 ml-2">
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-500">{{ $divisi ?? 'Tanpa Divisi' }}
                    </h2>
                    <div class="h-px bg-slate-200 flex-1"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($group as $s)
                        <div
                            class="bg-white border border-slate-100 p-6 rounded-[2rem] shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                            <div
                                class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 transform scale-0 group-hover:scale-150">
                            </div>

                            <div class="relative z-10">
                                <div class="flex items-start justify-between mb-4">
                                    <div
                                        class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <span
                                        class="text-[9px] font-black px-3 py-1 bg-slate-50 rounded-full text-slate-400 uppercase tracking-tighter">
                                        {{ $s->daily_reports_count }} Approved Reports
                                    </span>
                                </div>

                                <h3 class="font-bold text-slate-800 text-lg leading-tight mb-1">{{ $s->nama_lengkap }}</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-6">{{ $s->divisi->nama_divisi ?? 'Tanpa Divisi' }}</p>

                                <div class="flex gap-3 w-full">
                                    <a href="{{ route('manager.reports.export', ['user_id' => $s->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-center py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all flex items-center justify-center gap-2">
                                        <i class="fas fa-file-excel text-sm"></i>
                                        <span>Excel</span>
                                    </a>

                                    <button onclick="exportUserPdf({{ $s->id }})"
                                        class="flex-1 bg-rose-600 hover:bg-rose-700 text-white text-center py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-100 transition-all flex items-center justify-center gap-2">
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
