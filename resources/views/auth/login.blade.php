@extends('layouts.app')

@section('content')
<div class="flex min-h-screen items-center justify-center p-4">
    <div class="flex flex-col lg:flex-row w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl">

        <div class="flex w-full lg:w-1/2 flex-col items-center justify-center bg-white p-8 lg:p-12">
            <div class="w-40 h-40 lg:w-64 lg:h-64 flex items-center justify-center rounded-2xl mb-4 lg:mb-8">
                <img src="{{ asset('img/logo.png') }}" alt="Your Logo" class="max-w-full h-auto" onerror="this.src='https://placehold.co/400x400/09637E/EBF4F6?text=YOUR+LOGO'">
            </div>
            <h2 class="text-2xl lg:text-3xl text-primary font-header text-center">Sistem Monitoring KPI</h2>
            <p class="text-secondary opacity-80 font-body text-sm lg:text-base">TAC & Infrastruktur IT</p>
        </div>

        <div class="w-full lg:w-1/2 bg-primary p-8 lg:p-12 text-white">
            <h1 class="mb-2 text-4xl lg:text-5xl font-header">Welcome!</h1>
            <p class="mb-8 font-light text-background opacity-70 font-body">Silakan login untuk melanjutkan</p>

            <div class="mb-8 flex justify-center">
                <div class="relative flex w-full rounded-full bg-black/20 p-1">
                    <button type="button" id="btn-staff" class="z-10 w-1/2 rounded-full py-2 font-body text-white transition" onclick="setRole('staff')">Staff Access</button>
                    <button type="button" id="btn-manager" class="z-10 w-1/2 rounded-full py-2 font-body text-white opacity-50 transition" onclick="setRole('manager')">Manager Access</button>
                    <div id="toggle-bg" class="absolute h-10 w-1/2 rounded-full bg-secondary shadow-lg transition-all duration-300"></div>
                </div>
            </div>

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <input type="hidden" name="role" id="selected-role" value="{{ old('role', 'staff') }}">

                @if($errors->any())
                <div class="mb-4 p-3 bg-red-500 text-white rounded-lg text-sm font-body">
                    {{ $errors->first() }}
                </div>
                @endif

                <div class="mb-5">
                    <div class="flex items-center rounded-xl bg-background/10 border border-white/20 px-4 py-3 focus-within:bg-white focus-within:text-primary transition">
                        <i class="fas fa-user-circle mr-3"></i>
                        <input type="email" name="email" placeholder="Email Address" class="w-full bg-transparent outline-none placeholder:text-white/50 focus:text-primary font-body" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="flex items-center rounded-xl bg-background/10 border border-white/20 px-4 py-3 focus-within:bg-white focus-within:text-primary transition">
                        <i class="fas fa-key mr-3"></i>
                        <input type="password" name="password" placeholder="Password" class="w-full bg-transparent outline-none placeholder:text-white/50 focus:text-primary font-body" required>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" class="w-full rounded-xl bg-secondary px-6 py-4 font-header text-xl lg:text-2xl shadow-lg hover:bg-accent hover:text-primary transition-all active:scale-95">
                        LOGIN
                    </button>
                    <div class="text-center">
                        <a href="#" class="text-xs font-body text-background opacity-50 hover:opacity-100 transition">Lupa password? Hubungi Manager IT</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script tetap sama seperti sebelumnya
    window.onload = function() {
        const currentRole = document.getElementById('selected-role').value;
        setRole(currentRole);
    };

    function setRole(role) {
        document.getElementById('selected-role').value = role;
        const bg = document.getElementById('toggle-bg');
        const btnStaff = document.getElementById('btn-staff');
        const btnManager = document.getElementById('btn-manager');

        if (role === 'manager' || role === 'gm') {
            bg.style.left = '50%';
            bg.style.transform = 'translateX(0)';
            btnManager.classList.remove('opacity-50');
            btnStaff.classList.add('opacity-50');
        } else {
            bg.style.left = '4px';
            bg.style.transform = 'translateX(0)';
            btnStaff.classList.remove('opacity-50');
            btnManager.classList.add('opacity-50');
        }
    }
</script>
@endsection