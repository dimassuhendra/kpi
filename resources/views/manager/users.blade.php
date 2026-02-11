@extends('layouts.manager')

@section('content')
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-header font-bold uppercase" style="color: var(--text-main)">
                    User <span style="color: var(--primary)">Management</span>
                </h1>
                <p class="text-[10px] italic font-bold uppercase tracking-widest opacity-70" style="color: var(--text-muted)">
                    Manajemen akun & monitoring performa staff
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="openUserModal('create')"
                    class="px-6 py-2 rounded-xl text-xs font-bold uppercase transition-all shadow-lg hover:opacity-90 active:scale-95"
                    style="background: var(--primary); color: white; shadow-color: var(--primary)">
                    + Tambah Staff
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="organic-card overflow-hidden border border-white/5">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] uppercase font-bold tracking-widest border-b border-white/5"
                        style="background: rgba(0,0,0,0.1); color: var(--text-muted)">
                        <th class="px-6 py-4">Nama Staff</th>
                        <th class="px-6 py-4 text-center">Email</th>
                        <th class="px-6 py-4 text-center">Divisi</th>
                        <th class="px-6 py-4 text-center">Terakhir Update</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($users as $u)
                        <tr class="hover:bg-white/[0.02] transition-all group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold" style="color: var(--text-main)">{{ $u->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold" style="color: var(--text-main)">{{ $u->email }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold" style="color: var(--text-main)">{{ $u->divisi->nama_divisi }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($u->latestReport)
                                    <span class="font-bold" style="color: var(--text-main)">
                                        {{ $u->latestReport->created_at->diffForHumans() }}
                                    </span>
                                    <br>
                                    <small class="text-muted" style="font-size: 0.75rem; opacity: 0.7;">
                                        {{ $u->latestReport->created_at->format('d M Y, H:i') }}
                                    </small>
                                @else
                                    <span class="text-gray-400">Belum melapor</span>
                                @endif
                            </td>
                            {{-- <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-4">
                                    <div class="text-center">
                                        <span
                                            class="font-bold block leading-none text-emerald-500">{{ $u->mandiri_count }}</span>
                                        <span class="text-[8px] uppercase font-bold opacity-60"
                                            style="color: var(--text-muted)">Mandiri</span>
                                    </div>
                                    <div class="text-center">
                                        <span
                                            class="font-bold block leading-none text-amber-500">{{ $u->inisiatif_count }}</span>
                                        <span class="text-[8px] uppercase font-bold opacity-60"
                                            style="color: var(--text-muted)">Temuan</span>
                                    </div>
                                </div>
                            </td> --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    {{-- Tombol Edit --}}
                                    <button
                                        onclick="openUserModal('edit', {{ $u->id }}, '{{ $u->nama_lengkap }}', '{{ $u->email }}', {{ $u->divisi_id }})"
                                        class="transition-colors opacity-50 hover:opacity-100"
                                        style="color: var(--text-main)">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('manager.users.destroy', $u->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus staff ini?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-rose-500 hover:text-rose-400 transition-colors opacity-80 hover:opacity-100">
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
    <div id="userModalOverlay"
        style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl border border-white/10"
            style="background: var(--dark-card)">
            <h3 id="modalTitle" class="text-xl font-header font-bold uppercase mb-6 text-center"
                style="color: var(--primary)">
                Tambah Staff
            </h3>

            <form id="userForm" method="POST">
                @csrf
                <div id="methodPlaceholder"></div>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] uppercase font-bold mb-2 block ml-1" style="color: var(--text-muted)">Nama
                            Lengkap</label>
                        <input type="text" name="nama_lengkap" id="u_nama_lengkap" required
                            class="w-full border border-white/5 rounded-2xl px-5 py-3 text-sm focus:border-primary outline-none transition-all disabled:opacity-50"
                            style="background: rgba(0,0,0,0.2); color: var(--text-main)">
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-bold mb-2 block ml-1"
                            style="color: var(--text-muted)">Email Access</label>
                        <input type="email" name="email" id="u_email" required
                            class="w-full border border-white/5 rounded-2xl px-5 py-3 text-sm focus:border-primary outline-none transition-all disabled:opacity-50"
                            style="background: rgba(0,0,0,0.2); color: var(--text-main)">
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-bold mb-2 block ml-1"
                            style="color: var(--text-muted)">Divisi Assignment</label>
                        <select name="divisi_id" id="u_divisi"
                            class="w-full border border-white/5 rounded-2xl px-5 py-3 text-sm focus:border-primary outline-none transition-all appearance-none"
                            style="background: rgba(0,0,0,0.2); color: var(--text-main)">
                            @foreach ($divisis as $d)
                                <option value="{{ $d->id }}" style="background: var(--dark-card)">
                                    {{ $d->nama_divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="passwordContainer">
                        <label class="text-[10px] uppercase font-bold mb-2 block ml-1"
                            style="color: var(--text-muted)">Password</label>
                        <input type="password" name="password" id="u_password"
                            class="w-full border border-white/5 rounded-2xl px-5 py-3 text-sm focus:border-primary outline-none transition-all"
                            style="background: rgba(0,0,0,0.2); color: var(--text-main)">
                    </div>

                    <div class="flex gap-3 pt-6">
                        <button type="button" onclick="closeUserModal()"
                            class="flex-1 py-4 rounded-2xl text-xs font-bold uppercase transition-all"
                            style="background: rgba(255,255,255,0.05); color: var(--text-main)">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-[2] py-4 rounded-2xl text-xs font-bold uppercase shadow-lg transition-all hover:opacity-90"
                            style="background: var(--primary); color: white">
                            Simpan Akun
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const overlay = document.getElementById('userModalOverlay');
        const form = document.getElementById('userForm');

        function openUserModal(type, id = null, nama_lengkap = '', email = '', divisiId = '') {
            const inputNama = document.getElementById('u_nama_lengkap');
            const inputEmail = document.getElementById('u_email');
            const passwordContainer = document.getElementById('passwordContainer');
            const inputPassword = document.getElementById('u_password');

            overlay.style.display = 'flex';

            if (type === 'create') {
                document.getElementById('modalTitle').innerText = "Tambah Staff Baru";
                form.action = "{{ route('manager.users.store') }}";
                document.getElementById('methodPlaceholder').innerHTML = "";
                inputNama.readOnly = false;
                inputEmail.readOnly = false;
                passwordContainer.style.display = 'block';
                inputPassword.required = true;
                form.reset();
            } else {
                document.getElementById('modalTitle').innerText = "Ubah Divisi Staff";
                form.action = "/manager/users/" + id;
                document.getElementById('methodPlaceholder').innerHTML = '@method('PUT')';
                inputNama.value = nama_lengkap;
                inputEmail.value = email;
                document.getElementById('u_divisi').value = divisiId;
                inputNama.readOnly = true;
                inputEmail.readOnly = true;
                passwordContainer.style.display = 'none';
                inputPassword.required = false;
            }
        }

        function closeUserModal() {
            overlay.style.display = 'none';
        }

        // Menutup modal jika klik di luar area modal
        window.onclick = function(event) {
            if (event.target == overlay) {
                closeUserModal();
            }
        }
    </script>
@endsection
