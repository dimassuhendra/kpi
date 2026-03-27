<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - MyBolo Console</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0065cf',
                        secondary: '#2f8de4',
                        accent: '#7fb5f0',
                        background: '#EBF4F6',
                        slate: {
                            850: '#1e293b',
                        }
                    },
                    fontFamily: {
                        header: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --primary: #0065cf;
            --bg-body: #EBF4F6;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            overflow-x: hidden;
        }

        .nav-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 2px solid rgba(0, 101, 207, 0.1);
        }

        .nav-link {
            position: relative;
            transition: all 0.3s ease;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        .nav-link.active {
            color: var(--primary);
        }

        @media (min-width: 1024px) {
            .nav-link.active::after {
                content: '';
                position: absolute;
                bottom: -28px;
                left: 0;
                width: 100%;
                height: 4px;
                background: var(--primary);
                border-radius: 50px 50px 0 0;
            }
        }

        .hero-container {
            position: relative;
            height: auto;
            min-height: 200px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(0, 101, 207, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .hero-image {
            background: url("https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=2015&auto=format&fit=crop") no-repeat center center;
            background-size: cover;
            position: absolute;
            inset: 0;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 101, 207, 0.9) 0%, rgba(30, 41, 59, 0.8) 100%);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased" x-data="{ mobileMenu: false }">

    <nav class="nav-glass sticky top-0 z-[60] w-full">
        <div class="max-w-[1440px] mx-auto px-4 lg:px-10">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-4 lg:gap-12">
                    <a href="#" class="flex items-center group">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform overflow-hidden">
                            <img src="{{ asset('img/logo-new.png') }}" alt="Logo MyBolo" class="w-7 h-7 object-contain">
                        </div>
                        <span class="ml-3 font-header font-black text-lg tracking-tighter text-slate-800 uppercase">
                            MyBolo<span class="text-primary">Console</span>
                        </span>
                    </a>

                    <div class="hidden lg:flex items-center space-x-8 mt-1">
                        <a href="{{ route('manager.dashboard') }}"
                            class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }} uppercase">Dashboard</a>
                        @if (Auth::user()->role !== 'gm')
                            <a href="{{ route('manager.approval.index') }}"
                                class="nav-link {{ request()->routeIs('manager.approval.*') ? 'active' : '' }} uppercase">Validation</a>
                            <a href="{{ route('manager.users.index') }}"
                                class="nav-link {{ request()->routeIs('manager.users.*') ? 'active' : '' }} uppercase">Users</a>
                        @endif
                        <a href="{{ route('manager.reports.index') }}"
                            class="nav-link {{ request()->routeIs('manager.reports.*') ? 'active' : '' }} uppercase">Archive</a>
                        <a href="{{ route('manager.profile.index') }}"
                            class="nav-link {{ request()->routeIs('manager.profile.*') ? 'active' : '' }} uppercase">Profile</a>
                        <a href="{{ route('updates.index') }}" onclick="markUpdateRead()"
                            class="nav-link {{ request()->routeIs('updates.index') ? 'active' : '' }} uppercase flex items-center gap-1">
                            Pembaruan
                            <span id="update-badge" class="hidden h-2 w-2 rounded-full bg-blue-400 animate-ping"></span>
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3 lg:gap-6">
                    <div class="hidden sm:block text-right border-r border-slate-200 pr-6">
                        <p class="text-[9px] text-primary font-black uppercase tracking-widest leading-none mb-1">
                            System Management
                        </p>
                        <p class="text-xs font-bold text-slate-800 tracking-tight">{{ Auth::user()->nama_lengkap }}</p>
                    </div>

                    <div class="hidden lg:block">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-wider border border-slate-200 hover:bg-primary hover:text-white transition-all">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>

                    <button @click="mobileMenu = !mobileMenu"
                        class="lg:hidden w-10 h-10 flex items-center justify-center text-slate-600 text-xl">
                        <i class="fas" :class="mobileMenu ? 'fa-times' : 'fa-bars-staggered'"></i>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="lg:hidden bg-white border-t border-primary/20 px-4 py-6 shadow-xl relative z-[70]">

            <div class="flex flex-col space-y-3">
                <a href="{{ route('manager.dashboard') }}"
                    class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('manager.dashboard') ? 'bg-blue-50 text-primary border border-blue-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                    <span class="flex items-center gap-3">
                        <i
                            class="fas fa-th-large {{ request()->routeIs('manager.dashboard') ? 'text-primary' : 'text-slate-400' }}"></i>
                        Dashboard
                    </span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                @if (Auth::user()->role !== 'gm')
                    <a href="{{ route('manager.approval.index') }}"
                        class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('manager.approval.*') ? 'bg-blue-50 text-primary border border-blue-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                        <span class="flex items-center gap-3">
                            <i
                                class="fas fa-check-double {{ request()->routeIs('manager.approval.*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Validation
                        </span>
                        <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                    </a>

                    <a href="{{ route('manager.users.index') }}"
                        class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('manager.users.*') ? 'bg-blue-50 text-primary border border-blue-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                        <span class="flex items-center gap-3">
                            <i
                                class="fas fa-users {{ request()->routeIs('manager.users.*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Users
                        </span>
                        <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                    </a>
                @endif

                <a href="{{ route('manager.reports.index') }}"
                    class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('manager.reports.*') ? 'bg-blue-50 text-primary border border-blue-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                    <span class="flex items-center gap-3">
                        <i
                            class="fas fa-box-archive {{ request()->routeIs('manager.reports.*') ? 'text-primary' : 'text-slate-400' }}"></i>
                        Archive
                    </span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                <a href="{{ route('manager.profile.index') }}"
                    class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('manager.profile.*') ? 'bg-blue-50 text-primary border border-blue-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                    <span class="flex items-center gap-3">
                        <i
                            class="fas fa-user-circle {{ request()->routeIs('manager.profile.*') ? 'text-primary' : 'text-slate-400' }}"></i>
                        Profile
                    </span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                <a href="{{ route('updates.index') }}" onclick="markUpdateRead()"
                    class="flex items-center justify-between p-4 rounded-2xl {{ request()->routeIs('updates.index') ? 'bg-blue-50 text-primary' : 'text-slate-600' }} font-bold text-sm uppercase">
                    <span class="flex items-center gap-3">
                        Pembaruan
                        <span id="update-badge-mobile"
                            class="hidden text-[9px] bg-accent text-white px-1.5 py-0.5 rounded-full">Baru</span>
                    </span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                <div class="pt-4 mt-2 border-t border-slate-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-3 p-4 bg-primary hover:bg-blue-700 text-white rounded-2xl font-black text-sm uppercase shadow-lg shadow-blue-200 transition-all">
                            <i class="fas fa-power-off"></i> Sign Out System
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow relative z-10">
        <div class="max-w-[1440px] mx-auto px-4 lg:px-10 py-6">

            <div class="hero-container mb-6 lg:mb-10">
                <div class="hero-image"></div>
                <div class="hero-overlay"></div>

                <div class="relative z-10 h-full p-6 lg:p-10 flex flex-col justify-between gap-8">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-2 lg:gap-3">
                            <div
                                class="px-3 py-1.5 rounded-xl lg:rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white flex items-center gap-2 w-fit">
                                <i class="fas fa-microchip text-accent text-[10px]"></i>
                                <div class="flex flex-col leading-none">
                                    <span
                                        class="text-accent text-[7px] lg:text-[8px] font-black uppercase tracking-wider mb-0.5">System
                                        Status</span>
                                    <span
                                        class="text-[9px] lg:text-[10px] font-bold text-white uppercase whitespace-nowrap">Operational</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right text-white">
                            <div id="digital-clock"
                                class="font-mono text-xl lg:text-3xl font-bold tracking-tighter text-white">
                                00:00:00
                            </div>
                            <div id="current-date"
                                class="text-[9px] font-black text-accent uppercase tracking-widest mt-1 opacity-80">
                                Loading...
                            </div>
                        </div>
                    </div>

                    <div>
                        <h1 class="text-xl lg:text-3xl font-black text-white tracking-tight uppercase">
                            Selamat Datang Kembali, </br><span
                                class="text-accent">{{ Auth::user()->nama_lengkap }}</span>
                        </h1>
                        <p class="text-slate-300 text-[9px] font-bold uppercase tracking-[0.2em] mt-1">
                            Monitor dan kelola infrastruktur jaringan Anda secara real-time.
                        </p>
                    </div>
                </div>
            </div>

            <div class="min-h-[400px]">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="mt-auto py-8 border-t border-slate-200 bg-white relative z-10">
        <div class="max-w-[1440px] mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <p class="text-[10px] font-black text-slate-400 tracking-widest uppercase">
                    &copy; 2026 MyBolo <span class="text-primary">Console</span>
                </p>
            </div>
            <div class="text-[10px] font-bold text-primary uppercase tracking-widest flex items-center gap-2">
                Server Online <div class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Digital Clock
        function updateTime() {
            const now = new Date();
            document.getElementById('digital-clock').textContent = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Check Update Logic
        const SYSTEM_VERSION = "1.3.26";

        function checkSystemUpdate() {
            const lastSeenVersion = localStorage.getItem('last_seen_version');
            if (lastSeenVersion !== SYSTEM_VERSION) {
                document.querySelectorAll('[id^="update-badge"]').forEach(el => el.classList.remove('hidden'));
            }
        }

        function markUpdateRead() {
            localStorage.setItem('last_seen_version', SYSTEM_VERSION);
            document.querySelectorAll('[id^="update-badge"]').forEach(el => el.classList.add('hidden'));
        }
        document.addEventListener('DOMContentLoaded', checkSystemUpdate);

        // =============================================================
        // Js untuk session timeout warning
        // =============================================================
        const sessionLifetimeMs = {{ config('session.lifetime') }} * 60 * 1000;

        const warningTimeMs = sessionLifetimeMs - (60 * 60 * 1000);

        let warningTimer;

        function startSessionTimers() {
            clearTimeout(warningTimer);

            warningTimer = setTimeout(function() {
                Swal.fire({
                    title: 'Apakah Anda masih di sana?',
                    text: 'Sesi login Anda sudah lama tidak ada aktivitas. Klik tombol di bawah untuk tetap login.',
                    icon: 'question', // Mengganti ikon menjadi pertanyaan
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tetap Login',
                    cancelButtonText: 'Keluar',
                    allowOutsideClick: false, // Wajib diklik tombolnya
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('keep-alive') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json' // Penting: Agar server membalas pesan error dengan format JSON jika sesi sudah terlanjur habis
                            }
                        }).then(response => {
                            if (response.ok) {
                                Swal.fire({
                                    title: 'Sesi Diperpanjang!',
                                    text: 'Anda bisa melanjutkan pekerjaan kembali.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                startSessionTimers();
                            } else {
                                Swal.fire({
                                    title: 'Sesi Berakhir',
                                    text: 'Maaf, Anda terlalu lama merespon. Silakan login kembali untuk keamanan.',
                                    icon: 'error'
                                }).then(() => {
                                    window.location
                                        .reload(); // Ini akan otomatis melempar ke halaman login
                                });
                            }
                        }).catch(error => {
                            window.location.reload();
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location.reload();
                    }
                });
            }, warningTimeMs);
        }

        startSessionTimers();
    </script>
</body>

</html>
