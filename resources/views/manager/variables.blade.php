@extends('layouts.manager')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-header font-bold text-white uppercase">Activity <span class="text-primary">Categories</span></h1>
            <p class="text-slate-400 text-xs italic font-bold uppercase tracking-widest">Manajemen kategori aktivitas pekerjaan divisi TAC</p>
        </div>
        <button onclick="openModal('create')" class="bg-primary hover:bg-primary/80 text-white px-6 py-2 rounded-xl text-xs font-bold uppercase transition-all shadow-lg shadow-primary/20">
            + Tambah Kategori
        </button>
    </div>

    {{-- TABLE --}}
    <div class="organic-card overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/5 text-[10px] text-slate-500 uppercase font-bold tracking-widest">
                    <th class="px-6 py-4">Nama Kategori Aktivitas</th>
                    <th class="px-6 py-4 text-center">Divisi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($variables as $v)
                <tr class="hover:bg-white/[0.02] transition-all group">
                    <td class="px-6 py-4 font-bold text-slate-200 uppercase tracking-tight text-sm">{{ $v->nama_variabel }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-[10px] bg-slate-800 text-slate-400 px-3 py-1 rounded-full font-bold">{{ $v->divisi->nama_divisi ?? 'TAC' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3">
                            <button onclick="openModal('edit', {{ $v->id }}, '{{ $v->nama_variabel }}')" class="text-amber-500 hover:text-amber-400 text-xs font-bold uppercase">Edit</button>
                            <form action="{{ route('manager.variables.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Aktivitas yang sudah tercatat mungkin akan terpengaruh.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-400"><i class="fas fa-trash"></i></button>
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
<div id="modalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
    <div class="bg-slate-900 border border-white/10 w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl" onclick="event.stopPropagation()">
        <h3 id="modalTitle" class="text-xl font-header font-bold text-white uppercase mb-6 text-center text-primary">Tambah Kategori</h3>

        <form id="modalForm" method="POST">
            @csrf
            <div id="methodPlaceholder"></div>
            <input type="hidden" name="divisi_id" value="1">

            <div class="space-y-6">
                <div>
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-1">Nama Kategori / Variabel</label>
                    <input type="text" name="nama_variabel" id="input_nama" required
                        placeholder="Contoh: Maintenance Server"
                        class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-4 text-sm text-white focus:border-primary outline-none transition-all">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal()" class="flex-1 py-4 rounded-2xl bg-slate-800 text-white text-[10px] font-bold uppercase hover:bg-slate-700 transition-all">Batal</button>
                    <button type="submit" id="btnSubmit" class="flex-[2] py-4 rounded-2xl bg-primary text-white text-[10px] font-bold uppercase hover:shadow-lg hover:shadow-primary/30 transition-all">Simpan Kategori</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const overlay = document.getElementById('modalOverlay');
    const form = document.getElementById('modalForm');

    function openModal(type, id = null, nama = '') {
        overlay.style.setProperty('display', 'flex', 'important');

        if (type === 'create') {
            document.getElementById('modalTitle').innerText = "Tambah Kategori Baru";
            form.action = "{{ route('manager.variables.store') }}";
            document.getElementById('methodPlaceholder').innerHTML = "";
            document.getElementById('input_nama').value = "";
            document.getElementById('btnSubmit').innerText = "Simpan Kategori";
            document.getElementById('btnSubmit').style.backgroundColor = '#3b82f6';
        } else {
            document.getElementById('modalTitle').innerText = "Edit Kategori";
            form.action = "/manager/variables/" + id;
            document.getElementById('methodPlaceholder').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('input_nama').value = nama;
            document.getElementById('btnSubmit').innerText = "Update Kategori";
            document.getElementById('btnSubmit').style.backgroundColor = '#f59e0b';
        }
    }

    function closeModal() {
        overlay.style.display = 'none';
    }

    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });
</script>
@endsection