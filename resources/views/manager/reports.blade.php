@extends('layouts.manager')

@section('content')
    <div class="max-w-5xl mx-auto space-y-8" x-data="{ showPreview: false }">

        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase">
                    Intelligence <span class="text-emerald-600">Reports</span>
                </h1>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mt-1">
                    Ekspor data performa & validasi KPI Tim Lapangan
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner">
                    <i class="fas fa-file-invoice text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <i class="fas fa-filter text-6xl text-slate-900"></i>
            </div>

            <form id="filterForm" action="{{ route('manager.reports.export') }}" method="GET"
                class="relative z-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="md:col-span-1">
                        <label class="text-[10px] uppercase font-black text-slate-400 mb-3 block ml-1 tracking-widest">Pilih
                            Staff</label>
                        <div class="relative">
                            <select name="user_id" id="user_id"
                                class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-700 focus:border-emerald-500 focus:bg-white outline-none appearance-none transition-all">
                                <option value="">Semua Staff</option>
                                @foreach ($staffs->groupBy('divisi.nama_divisi') as $divisiName => $group)
                                    <optgroup label="DIVISI {{ strtoupper($divisiName) }}"
                                        class="text-emerald-600 font-black">
                                        @foreach ($group as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama_lengkap }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <i
                                class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-black text-slate-400 mb-3 block ml-1 tracking-widest">Dari
                            Tanggal</label>
                        <input type="date" name="start_date" id="start_date" required value="{{ date('Y-m-01') }}"
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3 text-sm font-bold text-slate-700 focus:border-emerald-500 focus:bg-white outline-none transition-all">
                    </div>

                    <div>
                        <label
                            class="text-[10px] uppercase font-black text-slate-400 mb-3 block ml-1 tracking-widest">Sampai
                            Tanggal</label>
                        <input type="date" name="end_date" id="end_date" required value="{{ date('Y-m-d') }}"
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3 text-sm font-bold text-slate-700 focus:border-emerald-500 focus:bg-white outline-none transition-all">
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-6 border-t border-slate-50">
                    <button type="button" id="btnResetData"
                        class="flex-none px-6 bg-rose-50 hover:bg-rose-100 text-rose-600 py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all flex items-center justify-center gap-3 border border-rose-100">
                        <i class="fas fa-trash-alt text-xs"></i>
                        Reset Data
                    </button>

                    <button type="button" id="btnPreview"
                        class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-search text-xs"></i>
                        Preview Data
                    </button>

                    <button type="submit"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all shadow-lg shadow-emerald-200 flex items-center justify-center gap-3">
                        <i class="fas fa-file-excel text-sm"></i>
                        Download Excel
                    </button>
                </div>
            </form>
        </div>

        <div id="previewContainer" class="hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        Preview Laporan Tervalidasi
                    </h3>
                    <span id="previewCount"
                        class="text-[10px] font-black px-3 py-1 bg-white rounded-full text-emerald-600 border border-emerald-100">0
                        Data ditemukan</span>
                </div>
                <div class="overflow-x-auto max-h-[400px] custom-scrollbar">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="sticky top-0 bg-white z-10 shadow-sm">
                                <th class="px-6 py-4 text-[9px] uppercase font-black text-slate-400 tracking-widest">Tgl
                                </th>
                                <th class="px-6 py-4 text-[9px] uppercase font-black text-slate-400 tracking-widest">Staff
                                </th>
                                <th
                                    class="px-4 py-4 text-[9px] uppercase font-black text-slate-400 tracking-widest text-center">
                                    Divisi</th>
                                <th class="px-6 py-4 text-[9px] uppercase font-black text-slate-400 tracking-widest">
                                    Tipe/Kategori</th>
                                <th class="px-6 py-4 text-[9px] uppercase font-black text-slate-400 tracking-widest">Judul
                                    Kegiatan</th>
                                <th
                                    class="px-4 py-4 text-[9px] uppercase font-black text-slate-400 tracking-widest text-center">
                                    Inisiatif</th>
                                <th
                                    class="px-4 py-4 text-[9px] uppercase font-black text-slate-400 tracking-widest text-center">
                                    Mandiri</th>
                                <th
                                    class="px-4 py-4 text-[9px] uppercase font-black text-slate-400 tracking-widest text-center">
                                    Durasi</th>
                            </tr>
                        </thead>
                        <tbody id="previewBody" class="divide-y divide-slate-50">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-emerald-50/50 border border-emerald-100 p-6 rounded-[2rem] flex items-start gap-4">
            <div
                class="w-10 h-10 rounded-xl bg-white border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                <i class="fas fa-lightbulb"></i>
            </div>
            <div>
                <p class="text-[11px] text-emerald-800/80 leading-relaxed font-bold uppercase tracking-tight">
                    Informasi Penting
                </p>
                <p class="text-[10px] text-emerald-700/60 mt-1 italic uppercase tracking-wider">
                    System hanya akan mengekspor data yang sudah melewati tahap <span
                        class="font-black text-emerald-600 underline">Validation (Approved)</span> oleh Manager.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnPreview').addEventListener('click', function() {
            const userId = document.getElementById('user_id').value;
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            const container = document.getElementById('previewContainer');
            const body = document.getElementById('previewBody');
            const countLabel = document.getElementById('previewCount');
            const btn = this;

            // Loading state
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            // Gunakan URL Helper Laravel agar link route tidak pecah
            const url = new URL("{{ route('manager.reports.preview') }}", window.location.origin);
            url.searchParams.append('user_id', userId);
            url.searchParams.append('start_date', start);
            url.searchParams.append('end_date', end);

            fetch(url)
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.error || 'Server Error');
                    return data;
                })
                .then(data => {
                    body.innerHTML = '';
                    container.classList.remove('hidden');
                    countLabel.innerText = `${data.length} Data ditemukan`;

                    if (data.length === 0) {
                        body.innerHTML =
                            `<tr><td colspan="4" class="px-8 py-10 text-center text-[10px] font-black text-slate-300 uppercase italic tracking-widest">Tidak ada data ditemukan</td></tr>`;
                    } else {
                        data.forEach(item => {
                            // 1. Pengaman untuk tanggal
                            const tgl = item.created_at ? new Date(item.created_at) : new Date();
                            const formattedDate = tgl.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });

                            // 2. Pengaman untuk Tipe (Menghindari error toUpperCase)
                            const tipeLaporan = item.tipe ? item.tipe.toUpperCase() : 'N/A';

                            // 3. Warna badge berdasarkan tipe
                            const badgeColor = tipeLaporan === 'MANDIRI' ? 'bg-blue-50 text-blue-600' :
                                'bg-amber-50 text-amber-600';

                            body.innerHTML += `
                                <tr class="hover:bg-slate-50 transition-all border-b border-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-[11px] font-bold text-slate-700">${item.tanggal}</td>
                                    <td class="px-6 py-4 text-[11px] font-bold text-slate-600 uppercase">${item.nama_staff}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black ${item.divisi === 'TAC' ? 'bg-purple-50 text-purple-600' : 'bg-orange-50 text-orange-600'}">
                                            ${item.divisi}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-[10px] font-medium text-slate-500 italic">${item.col_5}</td>
                                    <td class="px-6 py-4 text-[11px] text-slate-700 font-medium">${item.judul}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-bold ${item.inisiatif === 'Ya' ? 'text-emerald-500' : 'text-slate-300'}">${item.inisiatif}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-bold ${item.mandiri === 'Ya' ? 'text-emerald-500' : 'text-slate-300'}">${item.mandiri}</td>
                                    <td class="px-4 py-4 text-center text-[11px] font-black text-slate-700">${item.durasi}</td>
                                </tr>
                            `;
                        });
                    }
                })
                .catch(error => {
                    console.error('Preview Error:', error);
                    alert('Gagal mengambil data: ' + error.message);
                })
                .finally(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                });
        });
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
