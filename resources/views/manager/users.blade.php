@extends('layouts.manager')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-header font-bold text-white uppercase">User <span class="text-primary">Management</span></h1>
            <p class="text-slate-400 text-xs italic font-bold uppercase tracking-widest">Manajemen akun & monitoring performa staff</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openUserModal('create')" class="bg-primary hover:bg-primary/80 text-white px-6 py-2 rounded-xl text-xs font-bold uppercase transition-all shadow-lg shadow-primary/20">
                + Tambah Staff
            </button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="organic-card overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/5 text-[10px] text-slate-500 uppercase font-bold tracking-widest">
                    <th class="px-6 py-4">Informasi Staff</th>
                    <th class="px-6 py-4 text-center">Avg KPI</th>
                    <th class="px-6 py-4 text-center">Total Case</th>
                    <th class="px-6 py-4 text-center">Autonomy</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($users as $u)
                <tr class="hover:bg-white/[0.02] transition-all group">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-200">{{ $u->nama_lengkap }}</span>
                            <span class="text-[10px] text-primary uppercase font-bold italic">{{ $u->divisi->nama_divisi ?? 'No Division' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-block px-3 py-1 rounded-full bg-primary/10 border border-primary/20">
                            <span class="font-header font-bold text-primary">{{ number_format($u->avg_kpi ?? 0, 1) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-white font-bold">{{ $u->total_case }}</span>
                        <span class="text-[10px] text-slate-500 block uppercase">Cases</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-4">
                            <div class="text-center">
                                <span class="text-emerald-500 font-bold block leading-none">{{ $u->mandiri_count }}</span>
                                <span class="text-[8px] text-slate-500 uppercase font-bold">Penyelesaian Sendiri</span>
                            </div>
                            <div class="text-center">
                                <span class="text-amber-500 font-bold block leading-none">{{ $u->inisiatif_count }}</span>
                                <span class="text-[8px] text-slate-500 uppercase font-bold">Temuan Sendiri</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3">
                            {{-- Tombol Edit --}}
                            <button onclick="openUserModal('edit', {{ $u->id }}, '{{ $u->nama_lengkap }}', '{{ $u->email }}', {{ $u->divisi_id }})" class="text-slate-400 hover:text-white transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('manager.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus staff ini? Semua data laporan dan nilai KPI-nya akan hilang permanen!')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-400 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
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

{{-- MODAL USER --}}
<div id="userModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
    <div class="bg-slate-900 border border-white/10 w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl">
        <h3 id="modalTitle" class="text-xl font-header font-bold text-white uppercase mb-6 text-center text-primary">Tambah Staff</h3>

        <form id="userForm" method="POST">
            @csrf
            <div id="methodPlaceholder"></div>
            <div class="space-y-4">
                {{-- Ganti bagian input nama di Modal lu --}}
                <div>
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="u_nama_lengkap" required
                        class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-3 text-sm text-white focus:border-primary outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                </div>
                <div>
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-1">Email Access</label>
                    <input type="email" name="email" id="u_email" required
                        class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-3 text-sm text-white focus:border-primary outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                </div>
                <div>
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-1">Divisi Assignment</label>
                    <select name="divisi_id" id="u_divisi" class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-3 text-sm text-white focus:border-primary outline-none transition-all appearance-none">
                        @foreach($divisis as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="passwordContainer">
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-1">Password</label>
                    <input type="password" name="password" id="u_password"
                        class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-3 text-sm text-white focus:border-primary outline-none transition-all">
                </div>

                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="closeUserModal()" class="flex-1 py-4 rounded-2xl bg-slate-800 text-white text-xs font-bold uppercase">Batal</button>
                    <button type="submit" class="flex-[2] py-4 rounded-2xl bg-primary text-white text-xs font-bold uppercase shadow-lg shadow-primary/30">Simpan Akun</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const overlay = document.getElementById('userModalOverlay');
    const form = document.getElementById('userForm');

    function openUserModal(type, id = null, nama_lengkap = '', email = '', divisiId = '') {
        const overlay = document.getElementById('userModalOverlay');
        const form = document.getElementById('userForm');
        const inputNama = document.getElementById('u_nama_lengkap');
        const inputEmail = document.getElementById('u_email');
        const passwordContainer = document.getElementById('passwordContainer');
        const inputPassword = document.getElementById('u_password');

        overlay.style.display = 'flex';

        if (type === 'create') {
            document.getElementById('modalTitle').innerText = "Tambah Staff Baru";
            form.action = "{{ route('manager.users.store') }}";
            document.getElementById('methodPlaceholder').innerHTML = "";

            // Aktifkan input kembali
            inputNama.readOnly = false;
            inputEmail.readOnly = false;
            passwordContainer.style.display = 'block';
            inputPassword.required = true;

            form.reset();
        } else {
            document.getElementById('modalTitle').innerText = "Ubah Divisi Staff";
            form.action = "/manager/users/" + id;
            document.getElementById('methodPlaceholder').innerHTML = '@method("PUT")';

            // Isi data
            inputNama.value = nama_lengkap;
            inputEmail.value = email;
            document.getElementById('u_divisi').value = divisiId;

            // Kunci input (Hanya Divisi yang bisa diubah)
            inputNama.readOnly = true;
            inputEmail.readOnly = true;

            // Sembunyikan field password saat edit (opsional, agar fokus hanya ke divisi)
            passwordContainer.style.display = 'none';
            inputPassword.required = false;
        }
    }

    function closeUserModal() {
        overlay.style.display = 'none';
    }
</script>
@endsection