@extends('layouts.manager')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-header text-3xl text-white italic text-primary">Team <span class="text-white">Directory</span></h2>
            <p class="text-slate-500 text-sm">Kelola akses dan keanggotaan tim IT {{ Auth::user()->division->name }}.</p>
        </div>

        <button onclick="document.getElementById('modal-staff').classList.remove('hidden')"
            class="bg-primary hover:bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold text-xs transition shadow-lg shadow-primary/20">
            <i class="fas fa-plus mr-2"></i> Daftarkan Staff Baru
        </button>
    </div>

    {{-- Table Staff --}}
    <div class="organic-card overflow-hidden">
        <table class="w-full text-left">
            <thead class="text-slate-500 text-[10px] uppercase tracking-widest bg-darkCard">
                <tr>
                    <th class="p-5 font-medium">Informasi Staff</th>
                    <th class="p-5 font-medium">Divisi</th>
                    <th class="p-5 font-medium">Kontak</th>
                    <th class="p-5 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            {{-- Ganti bagian Tbody pada Tabel --}}
            <tbody class="divide-y divide-white/5 text-slate-300">
                @foreach($team as $member)
                <tr class="hover:bg-white/[0.02] transition">
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <span class="text-white font-bold">{{ $member->name }}</span>
                        </div>
                    </td>
                    <td class="p-5 text-sm">
                        <span class="bg-indigo-500/10 text-indigo-400 px-3 py-1 rounded-full border border-indigo-500/20 text-[10px] uppercase font-bold">
                            {{ $member->division->name ?? 'No Division' }}
                        </span>
                    </td>
                    <td class="p-5 text-sm text-slate-400 italic">{{ $member->email }}</td>
                    <td class="p-5 text-right">
                        <div class="flex justify-end gap-2">
                            {{-- Edit Button --}}
                            <button onclick="openEditModal({{ json_encode($member) }})"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white transition">
                                <i class="fas fa-edit text-xs"></i>
                            </button>

                            {{-- Delete Button --}}
                            <form action="{{ route('manager.staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus staff ini? Semua data KPI terkait mungkin akan hilang.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition">
                                    <i class="fas fa-trash text-xs"></i>
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

{{-- Modal Tambah Staff --}}
<div id="modal-staff" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-secondary/80 backdrop-blur-sm p-4">
    <div class="bg-darkCard border border-white/10 w-full max-w-md rounded-[40px] p-8 shadow-2xl animate-in fade-in zoom-in duration-300">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-header text-xl text-white">Register <span class="text-primary">New Staff</span></h3>
            <button onclick="document.getElementById('modal-staff').classList.add('hidden')" class="text-slate-500 hover:text-white"><i class="fas fa-times"></i></button>
        </div>

        {{-- Ganti bagian form di dalam modal-staff dengan ini --}}
        <form action="{{ route('manager.staff.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-2">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full bg-secondary border border-white/5 rounded-2xl px-5 py-3 text-white focus:border-primary outline-none transition">
            </div>

            {{-- Penambahan Dropdown Divisi --}}
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-2">Penempatan Divisi</label>
                <select name="division_id" required class="w-full bg-secondary border border-white/5 rounded-2xl px-5 py-3 text-white focus:border-primary outline-none transition appearance-none">
                    <option value="" disabled selected>Pilih Divisi...</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-secondary border border-white/5 rounded-2xl px-5 py-3 text-white focus:border-primary outline-none transition">
            </div>

            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-2">Password</label>
                <input type="password" name="password" required class="w-full bg-secondary border border-white/5 rounded-2xl px-5 py-3 text-white focus:border-primary outline-none transition">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-primary py-4 rounded-2xl font-bold text-white shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">
                    Simpan & Aktifkan Akun
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Staff --}}
<div id="modal-edit-staff" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-secondary/80 backdrop-blur-sm p-4">
    <div class="bg-darkCard border border-white/10 w-full max-w-md rounded-[40px] p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-header text-xl text-white">Update <span class="text-primary">Staff Info</span></h3>
            <button onclick="closeEditModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times"></i></button>
        </div>

        <form id="form-edit" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-2">Nama Lengkap</label>
                <input type="text" name="name" id="edit-name" required class="w-full bg-secondary border border-white/5 rounded-2xl px-5 py-3 text-white focus:border-primary outline-none transition">
            </div>

            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-2">Ubah Divisi</label>
                <select name="division_id" id="edit-division" required class="w-full bg-secondary border border-white/5 rounded-2xl px-5 py-3 text-white focus:border-primary outline-none transition">
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-[10px] text-slate-500 uppercase font-bold mb-2 block ml-2">Email Address</label>
                <input type="email" name="email" id="edit-email" required class="w-full bg-secondary border border-white/5 rounded-2xl px-5 py-3 text-white focus:border-primary outline-none transition">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-amber-500 py-4 rounded-2xl font-bold text-white shadow-lg shadow-amber-500/20 hover:scale-[1.02] transition-transform">
                    Update Staff Profile
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(user) {
        const modal = document.getElementById('modal-edit-staff');
        const form = document.getElementById('form-edit');

        // Set Action URL secara dinamis
        form.action = `/manager/staff/${user.id}`;

        // Isi data ke input
        document.getElementById('edit-name').value = user.name;
        document.getElementById('edit-email').value = user.email;
        document.getElementById('edit-division').value = user.division_id;

        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modal-edit-staff').classList.add('hidden');
    }
</script>
@endsection