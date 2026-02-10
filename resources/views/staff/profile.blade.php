@extends('layouts.staff')

@section('content')
<div class="container mx-auto max-w-4xl px-4 md:px-0">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-header font-bold text-white text-center md:text-left">Pengaturan Profil</h1>
        <p class="text-slate-400 font-body text-xs md:text-sm text-center md:text-left">Kelola informasi akun dan keamanan Anda.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm flex items-center shadow-lg">
        <i class="fas fa-check-circle mr-3"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        <div class="space-y-6">
            <div class="organic-card p-6 text-center">
                <div class="relative w-24 h-24 mx-auto mb-4">
                    <div class="w-24 h-24 bg-primary/20 rounded-full flex items-center justify-center text-primary text-4xl font-bold border-4 border-slate-800 shadow-xl">
                        {{ substr($user->nama_lengkap, 0, 1) }}
                    </div>
                    <div class="absolute bottom-0 right-0 w-6 h-6 bg-emerald-500 border-4 border-slate-900 rounded-full"></div>
                </div>

                <h2 class="text-white font-bold text-lg leading-tight">{{ $user->nama_lengkap }}</h2>
                <p class="text-slate-500 text-xs mt-1 lowercase">{{ '@' . $user->username }}</p>

                <div class="mt-6 pt-6 border-t border-slate-700/50 space-y-4 text-left">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Status</span>
                        <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full border border-emerald-500/30 font-bold">ACTIVE</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Role</span>
                        <span class="text-[10px] text-slate-300 font-bold uppercase">Staff TAC</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Bergabung</span>
                        <span class="text-[10px] text-slate-300 font-bold">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <form action="{{ route('staff.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="organic-card p-5 md:p-8">
                    <h3 class="text-white font-header text-sm mb-6 flex items-center border-b border-slate-700/50 pb-4 md:border-0 md:pb-0">
                        <i class="fas fa-user-circle mr-3 text-primary text-lg"></i> Informasi Dasar
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                                class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition text-sm">
                            @error('nama_lengkap') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition text-sm">
                            @error('username') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Email Perusahaan</label>
                            <div class="flex">
                                <input type="text" name="email_prefix" value="{{ old('email_prefix', explode('@', $user->email)[0]) }}"
                                    class="flex-1 min-w-0 bg-slate-900/50 border border-slate-700 rounded-l-lg px-4 py-3 text-white focus:outline-none focus:border-primary transition text-sm">
                                <span class="bg-slate-800 border border-l-0 border-slate-700 rounded-r-lg px-3 md:px-4 py-3 text-slate-400 text-[11px] md:text-sm flex items-center whitespace-nowrap">
                                    @mybolo.com
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-500 italic font-medium">Hanya prefix email yang dapat diubah.</p>
                        </div>
                    </div>
                </div>

                <div class="organic-card p-5 md:p-8">
                    <h3 class="text-white font-header text-sm mb-6 flex items-center border-b border-slate-700/50 pb-4 md:border-0 md:pb-0">
                        <i class="fas fa-shield-alt mr-3 text-primary text-lg"></i> Keamanan Akun
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Password Baru</label>
                            <input type="password" name="password" placeholder="••••••••"
                                class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-primary transition text-sm">
                            <p class="text-[10px] text-slate-500 mt-1 italic">Kosongkan jika tidak ingin diubah.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••"
                                class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-primary transition text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-end pt-2 pb-8">
                    <button type="submit" class="w-full md:w-auto bg-primary hover:bg-emerald-400 text-slate-900 font-bold py-4 px-10 rounded-xl shadow-lg shadow-emerald-500/10 transition-all active:scale-95 flex items-center justify-center">
                        <i class="fas fa-save mr-2 text-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection