@extends('layouts.staff')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-10">
        <h2 class="font-header text-3xl text-primary">Pengaturan Profil</h2>
        <p class="text-slate-400 font-body">Kelola informasi akun dan keamanan password Anda.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl font-bold">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-8">

        {{-- Section 1: Informasi Dasar --}}
        <div class="bg-darkCard rounded-[2rem] border border-white/5 overflow-hidden">
            <div class="px-8 py-6 border-b border-white/5 bg-white/5">
                <h3 class="font-header text-xl text-white flex items-center">
                    <i class="fas fa-user-circle mr-3 text-primary"></i> Informasi Dasar
                </h3>
            </div>
            <div class="p-8">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-400 uppercase tracking-wider ml-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full rounded-xl border-none bg-slate-900/50 p-4 focus:ring-2 focus:ring-primary text-slate-200">
                            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2 opacity-60">
                            <label class="text-sm font-bold text-slate-400 uppercase tracking-wider ml-1">Email (Read Only)</label>
                            <input type="email" value="{{ $user->email }}" disabled
                                class="w-full rounded-xl border-none bg-slate-800 p-4 text-slate-400 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="bg-primary hover:bg-emerald-600 text-white px-8 py-3 rounded-xl font-header transition-all transform active:scale-95 shadow-lg shadow-primary/20">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Section 2: Keamanan (Ganti Password) --}}
        <div class="bg-darkCard rounded-[2rem] border border-white/5 overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-white/5 bg-white/5">
                <h3 class="font-header text-xl text-white flex items-center">
                    <i class="fas fa-lock mr-3 text-primary"></i> Perbarui Password
                </h3>
            </div>
            <div class="p-8">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6 max-w-xl">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-400 uppercase tracking-wider ml-1">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                class="w-full rounded-xl border-none bg-slate-900/50 p-4 focus:ring-2 focus:ring-primary text-slate-200">
                            @error('current_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-400 uppercase tracking-wider ml-1">Password Baru</label>
                                <input type="password" name="password"
                                    class="w-full rounded-xl border-none bg-slate-900/50 p-4 focus:ring-2 focus:ring-primary text-slate-200">
                                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-400 uppercase tracking-wider ml-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full rounded-xl border-none bg-slate-900/50 p-4 focus:ring-2 focus:ring-primary text-slate-200">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white px-8 py-3 rounded-xl font-header transition-all transform active:scale-95 shadow-lg">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection