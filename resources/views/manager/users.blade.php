@extends('layouts.manager')

@section('content')
    <div class="p-6 space-y-8 max-w-7xl mx-auto">
        {{-- Alert Error Validasi --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4 shadow-sm">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="font-bold">Terjadi Kesalahan:</span>
                </div>
                <ul class="list-disc list-inside text-xs font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol
                        class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        <li class="inline-flex items-center">Admin</li>
                        <li><i class="fas fa-chevron-right mx-2 text-[8px]"></i></li>
                        <li class="text-emerald-600">Users</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black tracking-tight text-slate-800 uppercase">
                    User <span class="text-primary">Management</span>
                </h1>
                <p class="text-sm font-medium text-slate-500 mt-1">
                    Total <span class="text-slate-800 font-bold">{{ $users->count() }}</span> staff terdaftar dalam sistem.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <button onclick="openUserModal('create_managerial')"
                    class="group flex-1 lg:flex-none px-5 py-3.5 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl text-xs font-bold uppercase tracking-widest transition-all shadow-lg active:scale-95 flex items-center justify-center gap-3">
                    <div class="bg-white/20 p-1.5 rounded-lg group-hover:rotate-90 transition-transform">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <span>Managerial</span>
                </button>

                <button onclick="openUserModal('create')"
                    class="group flex-1 lg:flex-none px-5 py-3.5 bg-primary hover:bg-accent text-white rounded-2xl text-xs font-bold uppercase tracking-widest transition-all shadow-[0_10px_20px_-10px_rgba(16,185,129,0.5)] active:scale-95 flex items-center justify-center gap-3">
                    <div class="bg-white/20 p-1.5 rounded-lg group-hover:rotate-90 transition-transform">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>Tambah Staff</span>
                </button>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="pl-8 py-6 text-[11px] uppercase font-black text-slate-500 tracking-[0.15em]">
                                Informasi Staff</th>
                            <th
                                class="px-6 py-6 text-[11px] uppercase font-black text-slate-500 tracking-[0.15em] text-center">
                                Divisi & Akses</th>
                            <th
                                class="px-6 py-6 text-[11px] uppercase font-black text-slate-500 tracking-[0.15em] text-center">
                                Aktivitas Terakhir</th>
                            <th
                                class="pr-8 py-6 text-[11px] uppercase font-black text-slate-500 tracking-[0.15em] text-right">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $u)
                            <tr class="hover:bg-slate-50/50 transition-all duration-300 group">
                                <td class="pl-8 py-5">
                                    <div class="flex items-center gap-4 pointer-events-none">
                                        <div class="relative">
                                            <div
                                                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-emerald-100">
                                                {{ substr($u->nama_lengkap, 0, 2) }}
                                            </div>
                                            <div
                                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-white rounded-full flex items-center justify-center shadow-sm">
                                                <div
                                                    class="w-2 h-2 rounded-full {{ $u->latestReport ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div
                                                class="font-bold text-slate-800 text-base leading-tight group-hover:text-emerald-700 transition-colors">
                                                {{ $u->nama_lengkap }}
                                            </div>
                                            <div class="text-xs font-medium text-slate-400 mt-0.5">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span
                                            class="px-4 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-wider border border-emerald-100/50">
                                            {{ $u->divisi->nama_divisi }}
                                        </span>
                                        <span
                                            class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $u->role }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if (in_array($u->role, ['manager', 'gm']))
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="px-4 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-wider border border-emerald-100/50 shadow-sm">
                                                Akun Manajemen
                                            </span>
                                        </div>
                                    @else
                                        @if ($u->latestReport)
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                                    <i class="far fa-clock text-emerald-500"></i>
                                                    {{ $u->latestReport->created_at->diffForHumans() }}
                                                </span>
                                                <span
                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-tight mt-1">
                                                    {{ $u->latestReport->created_at->format('d M Y • H:i') }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center opacity-50">
                                                <i class="fas fa-user-slash text-slate-300 text-lg"></i>
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-widest italic mt-1">
                                                    No Activity
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="pr-8 py-5 text-right">
                                    <div class="flex justify-end gap-2 transition-all">
                                        <button
                                            onclick="openUserModal('edit', {{ $u->id }}, '{{ $u->nama_lengkap }}', '{{ $u->email }}', {{ $u->divisi_id }}, '{{ $u->role }}')"
                                            class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:border-emerald-500 hover:text-emerald-600 hover:shadow-lg transition-all flex items-center justify-center">
                                            <i class="fas fa-pen text-xs"></i>
                                        </button>
                                        <form action="{{ route('manager.users.destroy', $u->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:border-rose-500 hover:text-rose-500 hover:shadow-lg transition-all flex items-center justify-center">
                                                <i class="fas fa-trash-alt text-xs"></i>
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
    </div>

    {{-- Modal Overlay Formulir (Create/Edit) --}}
    <div id="userModalOverlay"
        class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-md z-[100] items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-[3rem] shadow-2xl border border-white p-2 transform transition-all">
            <div class="bg-slate-50 rounded-[2.5rem] p-8">
                <div class="text-center mb-8">
                    <div
                        class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-3xl mx-auto flex items-center justify-center mb-4 shadow-inner">
                        <i class="fas fa-user-shield text-2xl"></i>
                    </div>
                    <h3 id="modalTitle" class="text-xl font-black text-slate-800 uppercase tracking-tighter">Registration
                    </h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-1">Sistem Otentikasi User
                    </p>
                </div>

                <form id="userForm" method="POST" class="space-y-5">
                    @csrf
                    <div id="methodPlaceholder"></div>

                    <div class="space-y-4">
                        {{-- Field Role --}}
                        <div id="roleSelectionContainer" class="hidden relative">
                            <label
                                class="text-[10px] uppercase font-black text-slate-400 mb-1.5 ml-4 block tracking-widest">Akses
                                Level</label>
                            <div class="relative">
                                <select name="role" id="u_role"
                                    class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3.5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all appearance-none font-bold text-slate-700">
                                    <option value="staff">STAFF</option>
                                    <option value="manager">MANAGER</option>
                                    <option value="gm">GENERAL MANAGER (GM)</option>
                                </select>
                                <i
                                    class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                            </div>
                        </div>

                        {{-- Field Nama --}}
                        <div class="relative">
                            <label
                                class="text-[10px] uppercase font-black text-slate-400 mb-1.5 ml-4 block tracking-widest">Nama
                                Lengkap</label>
                            <input type="text" name="nama_lengkap" id="u_nama_lengkap" required
                                placeholder="John Doe"
                                class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3.5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all placeholder:text-slate-300 font-medium">
                        </div>

                        {{-- Field Email --}}
                        <div class="relative">
                            <label
                                class="text-[10px] uppercase font-black text-slate-400 mb-1.5 ml-4 block tracking-widest">Email
                                Access</label>
                            <input type="email" name="email" id="u_email" required placeholder="john@company.com"
                                class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3.5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium">
                        </div>

                        {{-- Field Divisi --}}
                        <div id="divisiContainer" class="relative">
                            <label
                                class="text-[10px] uppercase font-black text-slate-400 mb-1.5 ml-4 block tracking-widest">Divisi
                                Assignment</label>
                            <div class="relative">
                                <select name="divisi_id" id="u_divisi"
                                    class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3.5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all appearance-none font-bold text-slate-700">
                                    @foreach ($divisis as $d)
                                        <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                    @endforeach
                                </select>
                                <i
                                    class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                            </div>
                        </div>

                        {{-- Field Password --}}
                        <div id="passwordContainer" class="relative">
                            <label
                                class="text-[10px] uppercase font-black text-slate-400 mb-1.5 ml-4 block tracking-widest">Master
                                Password</label>
                            <input type="password" name="password" id="u_password" placeholder="••••••••"
                                class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3.5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium">
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 pt-4">
                        <button type="submit"
                            class="w-full py-4 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all active:scale-[0.98]">
                            Simpan Data
                        </button>
                        <button type="button" onclick="closeUserModal()"
                            class="w-full py-4 bg-transparent text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:text-slate-600 transition-all">
                            Batalkan & Kembali
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Info Modal Overlay (Glassmorphism & Clean Blue/White Palette) --}}
    <div id="infoModalOverlay"
        class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-md z-[150] items-center justify-center p-4 transition-all opacity-0 duration-300">
        <div
            class="w-full max-w-2xl bg-white/95 border border-white/60 shadow-2xl rounded-[2rem] p-8 relative overflow-hidden backdrop-blur-xl">
            <div class="absolute top-0 right-0 p-6">
                <button onclick="closeInfoModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-rose-100 hover:text-rose-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex items-center gap-6 mb-8 border-b border-slate-100 pb-8 mt-2">
                <div class="w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-black text-3xl shadow-lg shadow-blue-200"
                    id="infoAvatar">
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight" id="infoName">Loading...</h2>
                    <p class="text-blue-600 font-bold text-[11px] uppercase tracking-[0.2em] mt-1" id="infoDivisi"></p>
                    <div class="flex items-center gap-2 mt-2">
                        <i class="fas fa-envelope text-slate-400 text-xs"></i>
                        <p class="text-slate-500 text-sm font-medium" id="infoEmail"></p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Statistik Kinerja Bulan
                    Ini</h3>
                <div class="grid grid-cols-2 gap-4" id="infoStatsContainer">
                </div>
            </div>
        </div>
    </div>

    <script>
        const overlay = document.getElementById('userModalOverlay');
        const form = document.getElementById('userForm');

        function openUserModal(type, id = null, nama_lengkap = '', email = '', divisiId = '', role = 'staff') {
            const inputNama = document.getElementById('u_nama_lengkap');
            const inputEmail = document.getElementById('u_email');
            const inputDivisi = document.getElementById('u_divisi');
            const inputRole = document.getElementById('u_role');
            const roleContainer = document.getElementById('roleSelectionContainer');
            const divisiContainer = document.getElementById('divisiContainer');
            const passwordContainer = document.getElementById('passwordContainer');
            const inputPassword = document.getElementById('u_password');

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            // Reset form ke awal
            form.reset();
            document.getElementById('methodPlaceholder').innerHTML = "";

            if (type === 'create') {
                document.getElementById('modalTitle').innerText = "Register New Staff";
                form.action = "{{ route('manager.users.store') }}";

                roleContainer.classList.add('hidden');
                inputRole.value = 'staff';

                divisiContainer.classList.remove('hidden');
                inputNama.readOnly = false;
                inputEmail.readOnly = false;
                passwordContainer.style.display = 'block';
                inputPassword.required = true;
                inputPassword.placeholder = "••••••••";

            } else if (type === 'create_managerial') {
                document.getElementById('modalTitle').innerText = "Managerial Access";
                form.action = "{{ route('manager.users.store') }}";

                roleContainer.classList.remove('hidden');
                inputRole.value = 'manager';

                // Set default Backoffice (ID 3)
                inputDivisi.value = "3";
                divisiContainer.classList.add('hidden');

                inputNama.readOnly = false;
                inputEmail.readOnly = false;
                passwordContainer.style.display = 'block';
                inputPassword.required = true;
                inputPassword.placeholder = "••••••••";

            } else {
                // MODE EDIT
                document.getElementById('modalTitle').innerText = "Update Profile";
                form.action = "/manager/users/" + id;
                document.getElementById('methodPlaceholder').innerHTML = '@method('PUT')';

                inputNama.value = nama_lengkap;
                inputEmail.value = email;
                inputDivisi.value = divisiId;
                inputRole.value = role;

                roleContainer.classList.add('hidden');
                divisiContainer.classList.remove('hidden');

                // Manager bisa update nama, email dan password
                inputNama.readOnly = false;
                inputEmail.readOnly = false;
                passwordContainer.style.display = 'block';
                inputPassword.required = false;
                inputPassword.placeholder = "Kosongkan jika tidak ubah password";
            }
        }

        function closeUserModal() {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }

        // Fungsi khusus untuk modal staff
        async function openStaffInfo(id) {
            const modal = document.getElementById('infoModalOverlay');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Animasi fade in
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
            }, 10);

            document.getElementById('infoName').innerText = "Mencari data...";
            document.getElementById('infoDivisi').innerText = "";
            document.getElementById('infoEmail').innerText = "";
            document.getElementById('infoAvatar').innerText = "?";
            document.getElementById('infoStatsContainer').innerHTML = `
                <div class="col-span-2 flex justify-center py-8">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-blue-500"></i>
                </div>
            `;

            try {
                const response = await fetch(`/manager/users/${id}/info`);
                const result = await response.json();
                const user = result.user;
                const stats = result.stats;

                document.getElementById('infoName').innerText = user.nama_lengkap;
                document.getElementById('infoDivisi').innerText = user.divisi.nama_divisi;
                document.getElementById('infoEmail').innerText = user.email;
                document.getElementById('infoAvatar').innerText = user.nama_lengkap.substring(0, 2).toUpperCase();

                let statsHtml = '';

                // Desain kartu untuk statistik (menyesuaikan divisi)
                if (user.divisi_id == 2) {
                    const s = stats.infraStats;
                    statsHtml = `
                        <div class="bg-blue-50/70 rounded-2xl p-5 border border-blue-100/50 backdrop-blur-sm">
                            <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Total Case Terselesaikan</div>
                            <div class="text-4xl font-black text-blue-600">${stats.total_case}</div>
                        </div>
                        <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-100 backdrop-blur-sm">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Distribusi Kategori</div>
                            <div class="text-sm font-bold text-slate-700 flex flex-col gap-2">
                                <div class="flex justify-between items-center"><span class="text-slate-500">Network</span> <span class="bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-sm">${s.Network}</span></div>
                                <div class="flex justify-between items-center"><span class="text-slate-500">CCTV</span> <span class="bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-sm">${s.CCTV}</span></div>
                                <div class="flex justify-between items-center"><span class="text-slate-500">GPS</span> <span class="bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-sm">${s.GPS}</span></div>
                            </div>
                        </div>
                    `;
                } else {
                    const s = stats.tacStats;
                    statsHtml = `
                        <div class="bg-blue-50/70 rounded-2xl p-5 border border-blue-100/50">
                            <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Total Case</div>
                            <div class="text-4xl font-black text-blue-600">${s.total_case}</div>
                        </div>
                        <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-100">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Aktivitas</div>
                            <div class="text-4xl font-black text-slate-700">${s.total_activity}</div>
                        </div>
                        <div class="bg-emerald-50/70 rounded-2xl p-5 border border-emerald-100/50">
                            <div class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2">Temuan Sendiri</div>
                            <div class="text-3xl font-black text-emerald-600">${s.temuan_sendiri}</div>
                        </div>
                        <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-100">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Rata-rata Waktu</div>
                            <div class="text-2xl font-black text-slate-700 mt-1">${s.avg_time} <span class="text-xs font-bold text-slate-400">Menit</span></div>
                        </div>
                    `;
                }
                document.getElementById('infoStatsContainer').innerHTML = statsHtml;

            } catch (error) {
                console.error("Error memuat data", error);
                document.getElementById('infoStatsContainer').innerHTML =
                    `<div class="col-span-2 p-4 rounded-xl bg-red-50 text-red-500 font-bold text-center text-sm border border-red-100">Gagal menarik data statistik dari server.</div>`;
            }
        }

        function closeInfoModal() {
            const modal = document.getElementById('infoModalOverlay');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == overlay) closeUserModal();
            if (event.target == document.getElementById('infoModalOverlay')) closeInfoModal();
        }
    </script>
@endsection
