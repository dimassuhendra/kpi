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
                @else
                    <button type="button" @click="addBoActivity()"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i> + Tambah Kegiatan Backoffice
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
                @else
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
            {{-- TAB 3: FORM NILAI KUIS                     --}}
            {{-- ========================================== --}}
            <div x-show="activeTab === 'kuis'" x-cloak style="display: none;" x-transition.opacity.duration.500ms
                class="mb-20">
                <div class="bg-white border border-slate-200 p-6 rounded-3xl shadow-sm">
                    <h2
                        class="text-lg font-black text-slate-800 uppercase tracking-widest mb-1 border-l-4 border-emerald-500 pl-3">
                        Input Nilai <span class="text-emerald-500">Asesmen Kuis</span></h2>
                    <p class="text-xs text-slate-500 mb-6 pl-4">Masukkan rekap nilai jika Anda baru saja menyelesaikan kuis
                        teknikal bulanan.</p>
                    <form action="{{ route('staff.assessment.store') }}" method="POST" enctype="multipart/form-data"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pl-4">
                        @csrf
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Bulan <span
                                    class="text-rose-500">*</span></label>
                            <select name="periode_bulan" required
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-emerald-500 text-sm font-bold text-slate-700">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Tahun <span
                                    class="text-rose-500">*</span></label>
                            <input type="number" name="periode_tahun" required value="{{ date('Y') }}"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-emerald-500 text-sm font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Jumlah Soal <span
                                    class="text-rose-500">*</span></label>
                            <input type="number" name="jumlah_soal" required min="1" placeholder="Contoh: 50"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-emerald-500 text-sm font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Jumlah Benar <span
                                    class="text-rose-500">*</span></label>
                            <input type="number" name="jumlah_benar" required min="0" placeholder="Contoh: 45"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-slate-50 outline-none focus:border-emerald-500 text-sm font-bold text-slate-700">
                        </div>
                        <div class="lg:col-span-4">
                            <label class="text-[10px] uppercase text-slate-400 font-black mb-2 block">Bukti Screenshot Kuis
                                <span class="text-rose-500">*</span></label>
                            <input type="file" name="bukti_kuis" accept="image/*" required
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-emerald-100 file:text-emerald-700 cursor-pointer">
                        </div>
                        <div class="lg:col-span-4 text-right mt-2 border-t pt-4">
                            <button type="submit"
                                class="bg-emerald-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-emerald-600 transition">Simpan
                                Nilai Kuis</button>
                        </div>
                    </form>
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
                activeTab: 'daily', // Tambahan state untuk Tab Switcher
                // Data disuntikkan dari Controller
                rows_network: @json($formattedRows['network']),
                rows_gps: @json($formattedRows['gps']),
                activities: @json($formattedRows['activities']),
                infra_activities: @json($formattedRows['infra']),
                bo_activities: @json($formattedRows['bo']),

                addNetworkRow() {
                    this.rows_network.push({
                        deskripsi: '',
                        respons: '',
                        temuan_sendiri: false,
                        is_mandiri: 1,
                        pic_name: '',
                        is_monitoring: false,
                        is_default: false
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
                        deskripsi: ''
                    });
                },
                addBoActivity() {
                    this.bo_activities.push({
                        judul: '',
                        deskripsi: ''
                    });
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
