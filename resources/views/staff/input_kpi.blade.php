@extends('layouts.staff')

@section('content')
    {{-- TAMBAHKAN activeTab: 'daily' pada inisialisasi Alpine --}}
    <div class="container mx-auto px-4 lg:px-0" x-data="kpiForm()">

        {{-- MODAL REJECTED ALERT --}}
        @if ($isRejected)
            <div class="mb-8 p-6 bg-rose-50 border-2 border-rose-200 rounded-3xl">
                <div class="flex items-center gap-4 text-rose-700 mb-2">
                    <div class="w-10 h-10 bg-rose-500 text-white rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-black uppercase">Laporan Perlu Revisi</h3>
                </div>
                <div class="ml-14">
                    <p class="text-rose-600 text-sm italic">
                        "{{ $catatanManager }}"
                    </p>
                </div>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">Daily Reporting</h1>
                <p class="text-slate-600 font-body text-sm md:text-base">
                    Unit: <span
                        class="text-amber-600 font-bold">{{ auth()->user()->divisi->nama_divisi ?? 'General' }}</span>
                    <span class="text-slate-400 ml-2">| {{ date('d M Y') }}</span>
                </p>
            </div>

            {{-- TOMBOL TAMBAH BARIS (Hanya Tampil di Tab Daily) --}}
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto" x-show="activeTab === 'daily'">
                @if (auth()->user()->divisi_id == 1)
                    <button type="button" @click="addActivity()"
                        class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-tasks mr-2 text-amber-400"></i> + Activity Umum
                    </button>
                    <button type="button" @click="addNetworkRow()"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i> + Technical Case
                    </button>
                @elseif (auth()->user()->divisi_id == 2)
                    <button type="button" @click="addInfraActivity()"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-tools mr-2"></i> + Tambah Kegiatan Infra
                    </button>
                @elseif (in_array(auth()->user()->divisi_id, [4, 5]))
                    <button type="button" @click="addBoActivity()"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i> + Tambah Kegiatan
                        {{ auth()->user()->divisi_id == 5 ? 'Purchasing' : 'BOT' }}
                    </button>
                @endif
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- TAB SWITCHER (KHUSUS DIVISI TAC = 1)       --}}
        {{-- ========================================== --}}
        @if (auth()->user()->divisi_id == 1)
            <div class="flex items-center justify-center mb-8">
                <div
                    class="bg-slate-200/50 p-1.5 rounded-2xl flex flex-col md:flex-row gap-2 w-full border border-slate-200 shadow-inner">
                    <button type="button" @click="activeTab = 'daily'"
                        :class="activeTab === 'daily' ? 'bg-white text-indigo-600 shadow-md font-black' :
                            'text-slate-500 font-bold hover:text-slate-700'"
                        class="flex-1 px-4 py-3 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                        <i class="fas fa-calendar-check mr-2"></i> Laporan Harian
                    </button>
                    <button type="button" @click="activeTab = 'rating'"
                        :class="activeTab === 'rating' ? 'bg-white text-amber-500 shadow-md font-black' :
                            'text-slate-500 font-bold hover:text-slate-700'"
                        class="flex-1 px-4 py-3 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                        <i class="fas fa-star mr-2"></i> Rating Pelanggan
                    </button>
                    <button type="button" @click="activeTab = 'kuis'"
                        :class="activeTab === 'kuis' ? 'bg-white text-emerald-500 shadow-md font-black' :
                            'text-slate-500 font-bold hover:text-slate-700'"
                        class="flex-1 px-4 py-3 rounded-xl text-xs uppercase tracking-widest transition-all duration-300">
                        <i class="fas fa-award mr-2"></i> Nilai Kuis
                    </button>
                </div>
            </div>
        @endif

        {{-- ========================================== --}}
        {{-- TAB 1: FORM DAILY REPORT UTAMA             --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'daily'" x-transition.opacity.duration.500ms>
            <form id="formInputKpi" action="{{ route('staff.kpi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if (auth()->user()->divisi_id == 1)
                    @include('staff.input_kpi.tac')
                @elseif (auth()->user()->divisi_id == 2)
                    @include('staff.input_kpi.infra')
                @elseif (in_array(auth()->user()->divisi_id, [4, 5]))
                    @include('staff.input_kpi.backoffice')
                @endif

                <div class="mt-10 flex justify-end border-t border-slate-200 pt-8 mb-20">
                    <button type="submit"
                        class="w-full md:w-auto px-12 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-lg flex items-center justify-center gap-3 group">
                        <span>Kirim Laporan Hari Ini</span>
                        <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- ========================================== --}}
        {{-- TAB 2: FORM RATING PELANGGAN               --}}
        {{-- ========================================== --}}
        @if (auth()->user()->divisi_id == 1)
            <div x-show="activeTab === 'rating'" x-cloak style="display: none;" x-transition.opacity.duration.500ms
                class="mb-20">
                <div class="bg-white border border-slate-200 p-6 rounded-3xl shadow-sm">
                    <h2
                        class="text-lg font-black text-slate-800 uppercase tracking-widest mb-1 border-l-4 border-amber-500 pl-3">
                        Input Rating <span class="text-amber-500">Pelanggan</span></h2>
                    <p class="text-xs text-slate-500 mb-6 pl-4">Masukkan data rating jika Anda baru saja menyelesaikan
                        tiket.</p>
                    <form action="{{ route('staff.feedback.store') }}" method="POST" enctype="multipart/form-data"
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-4">
                        @csrf
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Nomor Tiket <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="nomor_tiket" required placeholder="#TKT-..."
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-amber-500 text-sm font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Nama Pelanggan <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="nama_pelanggan" required placeholder="Nama pelanggan..."
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-amber-500 text-sm font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Tanggal Survey <span
                                    class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_survey" required value="{{ date('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-amber-500 text-sm font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Rating Diberikan (1-5)
                                <span class="text-rose-500">*</span></label>
                            <select name="rating" required
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-amber-500 text-sm font-bold text-slate-700">
                                <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Puas)</option>
                                <option value="4">⭐⭐⭐⭐ (4 - Puas)</option>
                                <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                <option value="2">⭐⭐ (2 - Kurang)</option>
                                <option value="1">⭐ (1 - Kecewa)</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Bukti SS Survey / Chat
                                <span class="text-rose-500">*</span></label>
                            <input type="file" name="bukti_survey" accept="image/*" required
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-amber-100 file:text-amber-700 cursor-pointer">
                        </div>
                        <div class="md:col-span-2 text-right mt-2 border-t pt-4">
                            <button type="submit"
                                class="bg-amber-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-amber-600 transition">Simpan
                                Rating</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- TAB 3: RIWAYAT NILAI KUIS                  --}}
            {{-- ========================================== --}}
            <div x-show="activeTab === 'kuis'" x-cloak style="display: none;" x-transition.opacity.duration.500ms
                class="mb-20">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <h2
                            class="text-lg font-black text-slate-800 uppercase tracking-widest mb-1 border-l-4 border-emerald-500 pl-3">
                            Riwayat Nilai <span class="text-emerald-500">Asesmen Kuis</span>
                        </h2>
                        <p class="text-xs text-slate-500 pl-4">Daftar nilai kuis yang telah diinput oleh Manager.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase tracking-widest text-slate-400">
                                    <th class="p-4 font-black text-center">Periode</th>
                                    <th class="p-4 font-black text-center">Skor Global</th>
                                    <th class="p-4 font-black text-center">Bukti SS</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($myAssessments as $quiz)
                                    <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                        <td class="p-4 text-center">
                                            <span
                                                class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100">
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
                                            @if ($quiz->bukti_kuis)
                                                <a href="{{ asset('storage/' . $quiz->bukti_kuis) }}" target="_blank"
                                                    class="text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 p-2 rounded-xl transition tooltip"
                                                    title="Lihat Bukti Kuis">
                                                    <i class="fas fa-image fa-lg"></i>
                                                </a>
                                            @else
                                                <span class="text-[10px] text-slate-400 italic">Tidak ada bukti</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3"
                                            class="p-8 text-center text-slate-400 text-sm italic border-t border-slate-100">
                                            Belum ada data kuis yang dinilai oleh manager.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- MODAL UPLOAD PROGRESS --}}
    <div id="uploadProgressModal"
        class="fixed inset-0 z-[999] hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-indigo-50">
                <div id="progressIndeterminate" class="h-full bg-indigo-500 w-1/3 animate-pulse"></div>
            </div>

            <div class="mb-5 mt-2">
                <i class="fas fa-cloud-upload-alt text-5xl text-indigo-500 animate-bounce"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Mengunggah Laporan...</h3>
            <p class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-6">Mohon jangan tutup halaman ini
            </p>

            <div class="w-full bg-slate-100 rounded-full h-4 mb-2 overflow-hidden border border-slate-200 shadow-inner">
                <div id="progressBar"
                    class="bg-gradient-to-r from-indigo-500 to-indigo-400 h-full rounded-full transition-all duration-300 ease-out flex items-center justify-end px-2"
                    style="width: 0%">
                    <span id="progressText" class="text-[9px] font-black text-white">0%</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function kpiForm() {
            return {
                activeTab: 'daily',
                infraTab: 'harian',

                lembur_activities: [{
                    waktu_mulai: '',
                    waktu_selesai: '',
                    detail: '',
                    foto_path: '',
                }],

                // Memetakan ulang format bawaan backend agar state upload default ter-set
                rows_network: @json($formattedRows['network']).map(row => ({
                    ...row,
                    bukti_respon_time_path: '',
                    bukti_deteksi_dini_path: '',
                    isUploadingRespon: false,
                    isUploadingDeteksi: false
                })),
                rows_gps: @json($formattedRows['gps']),
                activities: @json($formattedRows['activities']),
                infra_activities: @json($formattedRows['infra']).map(row => ({
                    ...row,
                    foto_dokumentasi_path: '',
                })),
                bo_activities: @json($formattedRows['bo'] ?? []),

                // AJAX UPLOAD FUNCTION
                async uploadFile(event, folderName, callback) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validasi ukuran Client Side
                    if (file.size > 2 * 1024 * 1024) {
                        alert("File terlalu besar! Maksimal 2MB per gambar.");
                        event.target.value = "";
                        callback(''); // Kosongkan state
                        return;
                    }

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('folder', folderName);
                    // Ambil token dari meta tag csrf atau dari form
                    formData.append('_token', document.querySelector('input[name="_token"]').value);

                    try {
                        const response = await fetch('{{ route('staff.upload.async') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            callback(data.path); // Set hidden input & matikan loading via callback
                        } else {
                            alert("Gagal mengunggah: " + (data.message || data.error));
                            event.target.value = "";
                            callback('');
                        }
                    } catch (error) {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan koneksi saat mengunggah.");
                        event.target.value = "";
                        callback('');
                    }
                },

                addNetworkRow() {
                    this.rows_network.push({
                        deskripsi: '',
                        respons: '',
                        temuan_sendiri: false,
                        is_mandiri: 1,
                        pic_name: '',
                        is_monitoring: false,
                        is_default: false,
                        // Inisialisasi state upload
                        bukti_respon_time_path: '',
                        bukti_deteksi_dini_path: '',
                        isUploadingRespon: false,
                        isUploadingDeteksi: false
                    });
                },
                addGpsRow() {
                    this.rows_gps.push({
                        nama_kegiatan: '',
                        jumlah_kendaraan: '',
                        is_monitoring: false,
                        is_default: false
                    });
                },
                addActivity() {
                    this.activities.push({
                        deskripsi: ''
                    });
                },
                addInfraActivity() {
                    this.infra_activities.push({
                        kategori: 'Network',
                        nama_kegiatan: '',
                        deskripsi: '',
                        foto_dokumentasi_path: '',
                    });
                },
                addBoActivity() {
                    this.bo_activities.push({
                        judul: '',
                        deskripsi: ''
                    });
                },
                addLemburActivity() {
                    this.lembur_activities.push({
                        waktu_mulai: '',
                        waktu_selesai: '',
                        detail: '',
                        foto_path: '',
                    });
                },
                removeLembur(index) {
                    this.lembur_activities.splice(index, 1);
                    if (this.lembur_activities.length === 0) {
                        this.clearLembur();
                    }
                },
                clearLembur() {
                    this.lembur_activities = [{
                        waktu_mulai: '',
                        waktu_selesai: '',
                        detail: '',
                        foto_path: '',
                        isUploading: false
                    }];
                    this.infraTab = 'harian'; // <-- UPDATE: gunakan infraTab
                },
                hasLemburData() {
                    return this.lembur_activities.some(item =>
                        item.waktu_mulai !== '' || item.waktu_selesai !== '' || item.detail !== '' || item.foto_path !==
                        ''
                    );
                },
                removeNetwork(index) {
                    this.rows_network.splice(index, 1);
                },
                removeGps(index) {
                    this.rows_gps.splice(index, 1);
                },
                removeActivity(index) {
                    this.activities.splice(index, 1);
                },
                removeInfra(index) {
                    this.infra_activities.splice(index, 1);
                },
                removeBo(index) {
                    this.bo_activities.splice(index, 1);
                }
            }
        }

        // Js untuk progress bar upload dengan AJAX (tanpa reload halaman)
        document.addEventListener('DOMContentLoaded', function() {
            const formInput = document.getElementById('formInputKpi');

            if (formInput) {
                formInput.addEventListener('submit', function(e) {
                    e.preventDefault(); // Mencegah submit normal

                    const modal = document.getElementById('uploadProgressModal');
                    const progressBar = document.getElementById('progressBar');
                    const progressText = document.getElementById('progressText');
                    const btnSubmit = formInput.querySelector('button[type="submit"]');

                    // Cegah klik 2 kali dan tampilkan modal
                    if (btnSubmit) btnSubmit.disabled = true;
                    modal.classList.remove('hidden');

                    let formData = new FormData(this);
                    let xhr = new XMLHttpRequest();

                    xhr.open('POST', this.action, true);
                    // Beritahu Laravel ini adalah request AJAX & minta balasan berupa JSON
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');

                    // Event Tracking Progress File Upload
                    xhr.upload.onprogress = function(e) {
                        if (e.lengthComputable) {
                            let percent = Math.round((e.loaded / e.total) * 100);
                            progressBar.style.width = percent + '%';
                            progressText.innerText = percent + '%';
                        }
                    };

                    // Event Saat Selesai
                    xhr.onload = function() {
                        if (xhr.status === 200 || xhr.status === 201) {
                            // Sukses! Redirect ke URL yang diberikan controller
                            let response = JSON.parse(xhr.responseText);
                            window.location.href = response.redirect;
                        } else if (xhr.status === 422) {
                            // Error Validasi Laravel (misal file > 2MB atau tidak sesuai format)
                            modal.classList.add('hidden');
                            if (btnSubmit) btnSubmit.disabled = false;

                            let response = JSON.parse(xhr.responseText);
                            let errorMsg = "Gagal! Ada isian yang tidak sesuai:\n\n";
                            for (let field in response.errors) {
                                errorMsg += "- " + response.errors[field][0] + "\n";
                            }
                            alert(errorMsg);
                        } else {
                            // Error Server 500 dll
                            modal.classList.add('hidden');
                            if (btnSubmit) btnSubmit.disabled = false;
                            alert('Terjadi kesalahan pada server (Error ' + xhr.status +
                                '). Coba lagi nanti.');
                        }
                    };

                    // Event Saat Jaringan Putus
                    xhr.onerror = function() {
                        modal.classList.add('hidden');
                        if (btnSubmit) btnSubmit.disabled = false;
                        alert('Koneksi terputus. Silakan periksa jaringan internet Anda.');
                    };

                    // Jalankan Upload
                    xhr.send(formData);
                });
            }
        });
    </script>
@endsection
