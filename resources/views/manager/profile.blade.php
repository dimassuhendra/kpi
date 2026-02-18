@extends('layouts.manager')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Account Settings</h2>
            <p class="text-[10px] font-bold text-imperial-500 uppercase tracking-widest">Manage your personal information and security</p>
        </div>
        <div class="w-12 h-12 bg-white border-2 border-gold-400 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="fas fa-user-shield text-imperial-500"></i>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-xs font-bold rounded-r-xl animate-bounce">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('manager.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-3">
                <i class="fas fa-id-card text-gold-500 text-xs"></i>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Basic Information</span>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-imperial-500 focus:border-transparent transition-all text-sm font-bold text-slate-700">
                    </div>
                    @error('nama_lengkap') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Username</label>
                    <div class="relative">
                        <i class="fas fa-at absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-imperial-500 focus:border-transparent transition-all text-sm font-bold text-slate-700">
                    </div>
                    @error('username') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-imperial-500 focus:border-transparent transition-all text-sm font-bold text-slate-700">
                    </div>
                    @error('email') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-3">
                <i class="fas fa-key text-gold-500 text-xs"></i>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Security & Password</span>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 mb-4">
                    <p class="text-[10px] text-amber-700 font-bold leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i> Biarkan kosong jika Anda tidak ingin mengganti password.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Current Password</label>
                        <input type="password" name="current_password" 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-imperial-500 focus:border-transparent transition-all text-sm">
                        @error('current_password') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                        <input type="password" name="new_password" 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-imperial-500 focus:border-transparent transition-all text-sm">
                        @error('new_password') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-imperial-500 focus:border-transparent transition-all text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" 
                class="px-10 py-4 bg-imperial-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-red-100 hover:bg-imperial-600 hover:-translate-y-1 transition-all active:scale-95">
                Save Changes <i class="fas fa-save ml-2"></i>
            </button>
        </div>
    </form>
</div>
@endsection