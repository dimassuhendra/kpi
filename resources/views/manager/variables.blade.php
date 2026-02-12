@extends('layouts.manager')

@section('content')
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-header font-bold uppercase" style="color: var(--text-main)">
                    Activity <span style="color: var(--primary)">Categories</span>
                </h1>
                <p class="text-[10px] italic font-bold uppercase tracking-widest opacity-70" style="color: var(--text-muted)">
                    Manajemen kategori aktivitas pekerjaan divisi TAC
                </p>
            </div>
            <button onclick="openModal('create')"
                class="px-6 py-2 rounded-xl text-xs font-bold uppercase transition-all shadow-lg hover:opacity-90 active:scale-95"
                style="background: var(--primary); color: white;">
                + Tambah Kategori
            </button>
        </div>

        {{-- TABLE --}}
        <div class="organic-card overflow-hidden border border-white/5">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] uppercase font-bold tracking-widest border-b border-white/5"
                        style="background: rgba(0,0,0,0.1); color: var(--text-muted)">
                        <th class="px-6 py-4">Nama Kategori Aktivitas</th>
                        <th class="px-6 py-4 text-center">Divisi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($variables as $v)
                        <tr class="hover:bg-white/[0.02] transition-all group">
                            <td class="px-6 py-4 font-bold uppercase tracking-tight text-sm"
                                style="color: var(--text-main)">
                                {{ $v->nama_variabel }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[9px] px-3 py-1 rounded-full font-bold uppercase border border-white/5"
                                    style="background: rgba(255,255,255,0.05); color: var(--text-muted)">
                                    {{ $v->divisi->nama_divisi ?? 'TAC' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-4">
                                    <button onclick="openModal('edit', {{ $v->id }}, '{{ $v->nama_variabel }}')"
                                        class="text-[10px] font-bold uppercase transition-colors hover:opacity-70"
                                        style="color: var(--text-muted)">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>

                                    <form action="{{ route('manager.variables.destroy', $v->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus kategori ini? Aktivitas yang sudah tercatat mungkin akan terpengaruh.')"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-400 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL POPUP --}}
    <div id="modalOverlay"
        style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl border border-white/10"
            style="background: var(--dark-card)" onclick="event.stopPropagation()">

            <h3 id="modalTitle" class="text-xl font-header font-bold uppercase mb-6 text-center"
                style="color: var(--primary)">
                Tambah Kategori
            </h3>

            <form id="modalForm" method="POST">
                @csrf
                <div id="methodPlaceholder"></div>
                <input type="hidden" name="divisi_id" value="1">

                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] uppercase font-bold mb-2 block ml-1" style="color: var(--text-muted)">
                            Nama Kategori / Variabel
                        </label>
                        <input type="text" name="nama_variabel" id="input_nama" required
                            placeholder="Contoh: Maintenance Server"
                            class="w-full border border-white/5 rounded-2xl px-5 py-4 text-sm focus:border-primary outline-none transition-all"
                            style="background: rgba(0,0,0,0.2); color: var(--text-main)">
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="closeModal()"
                            class="flex-1 py-4 rounded-2xl text-[10px] font-bold uppercase transition-all"
                            style="background: rgba(255,255,255,0.05); color: var(--text-main)">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmit"
                            class="flex-[2] py-4 rounded-2xl text-[10px] font-bold uppercase shadow-lg transition-all hover:opacity-90"
                            style="background: var(--primary); color: white">
                            Simpan Kategori
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const overlay = document.getElementById('modalOverlay');
        const form = document.getElementById('modalForm');
        const btnSubmit = document.getElementById('btnSubmit');

        function openModal(type, id = null, nama = '') {
            // Menggunakan flex agar center
            overlay.style.display = 'flex';

            // Ambil warna tema secara dinamis untuk tombol
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim();
            const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim();

            if (type === 'create') {
                document.getElementById('modalTitle').innerText = "Tambah Kategori Baru";
                document.getElementById('modalTitle').style.color = "var(--primary)";
                form.action = "{{ route('manager.variables.store') }}";
                document.getElementById('methodPlaceholder').innerHTML = "";
                document.getElementById('input_nama').value = "";
                btnSubmit.innerText = "Simpan Kategori";
                btnSubmit.style.backgroundColor = "var(--primary)";
            } else {
                document.getElementById('modalTitle').innerText = "Edit Kategori";
                document.getElementById('modalTitle').style.color = "var(--accent)";
                form.action = "/manager/variables/" + id;
                document.getElementById('methodPlaceholder').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('input_nama').value = nama;
                btnSubmit.innerText = "Update Kategori";
                // Gunakan warna accent (biasanya amber/emas) untuk mode edit agar ada perbedaan visual
                btnSubmit.style.backgroundColor = "var(--accent)";
            }
        }

        function closeModal() {
            overlay.style.display = 'none';
        }

        // Menutup modal jika klik di area overlay (luar modal)
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeModal();
        });
    </script>
@endsection
