<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - KPI System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Bree+Serif:wght@400;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'var(--primary)',
                        secondary: 'var(--secondary)',
                        accent: 'var(--accent)',
                        darkCard: 'var(--dark-card)',
                        bgMain: 'var(--bg-main)',
                    },
                    fontFamily: {
                        header: ['"Fredoka"'],
                        body: ['"Bree Serif"'],
                    }
                }
            }
        }
    </script>

    <style>
        /* PEMETAAN 4 WARNA PER TEMA 
           1. Primary | 2. Accent | 3. DarkCard | 4. BG-Main
        */
        :root {
            /* Tema 4: Original Dark (Default) */
            --primary: #6366f1;
            --accent: #818cf8;
            --dark-card: #1e293b;
            --bg-main: #0f172a;
            --secondary: #0f172a;
            /* Untuk Nav */
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
        }

        [data-theme="tema-1"] {
            --primary: #C40C0C;
            /* Merah */
            --accent: #FF6500;
            /* Oranye Bright */
            --dark-card: #CC561E;
            /* Cokelat Oranye */
            --bg-main: #F6CE71;
            /* Kuning Krem */
            --secondary: #C40C0C;
            --text-main: #210000;
            --text-muted: #4a0505;
        }

        [data-theme="tema-2"] {
            --primary: #016B61;
            /* Teal Gelap */
            --accent: #70B2B2;
            /* Teal Muda */
            --dark-card: #9ECFD4;
            /* Biru Toska Muda */
            --bg-main: #E5E9C5;
            /* Hijau Krem Muda */
            --secondary: #016B61;
            --text-main: #002b28;
            --text-muted: #3d5a57;
        }

        [data-theme="tema-3"] {
            --primary: #504B38;
            /* Hitam Earthy */
            --accent: #B9B28A;
            /* Khaki */
            --dark-card: #EBE5C2;
            /* Krem */
            --bg-main: #F8F3D9;
            /* Putih Tulang */
            --secondary: #504B38;
            --text-main: #504B38;
            --text-muted: #7a7356;
        }

        body {
            font-family: 'Bree Serif', serif;
            scroll-behavior: smooth;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: all 0.4s ease;
        }

        .font-header {
            font-family: 'Fredoka', sans-serif;
        }

        .logo-silhouette {
            transition: all 0.3s ease;
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        :root:not([data-theme]) .logo-silhouette {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        [data-theme="tema-1"] .logo-silhouette,
        [data-theme="tema-2"] .logo-silhouette,
        [data-theme="tema-3"] .logo-silhouette {
            filter: none;
            /* Kembali ke warna asli file logo.png Anda */
            opacity: 1;
        }

        /* KARTU DINAMIS */
        .organic-card {
            background: var(--dark-card);
            color: var(--text-main);
            border-radius: 40px 15px 40px 15px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        /* NAVIGASI DINAMIS */
        .glass-nav {
            background: var(--secondary);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .nav-link {
            position: relative;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.7);
            /* Default putih transparan di nav gelap */
        }

        /* Jika tema aktif, pastikan teks nav menyesuaikan */
        [data-theme] .nav-link {
            color: var(--bg-main);
            opacity: 0.8;
        }

        [data-theme] .nav-link.active,
        [data-theme] .nav-link:hover {
            color: var(--accent);
            opacity: 1;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background-color: var(--accent);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 60%;
        }

        /* TOMBOL DINAMIS */
        .btn-primary {
            background-color: var(--primary);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--accent);
            transform: translateY(-2px);
        }

        #mobile-menu {
            transition: all 0.4s ease;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            background: var(--secondary);
        }

        #mobile-menu.menu-open {
            max-height: 100vh;
            opacity: 1;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <nav class="glass-nav sticky top-0 z-50 w-full px-6 lg:px-12">
        <div class="flex justify-between h-20 items-center">

            {{-- Brand --}}
            <div class="flex items-center gap-8">
                <a href="{{ route('manager.dashboard') }}" class="flex-shrink-0 flex items-center group">
                    <img class="h-10 w-auto logo-silhouette group-hover:rotate-12 transition-transform"
                        src="{{ asset('img/logo.png') }}" alt="Logo">

                    <span class="ml-3 font-header font-bold text-xl tracking-tighter uppercase"
                        style="color: var(--text-main)">
                        MyBolo <span style="color: var(--primary)" class="italic">Console</span>
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden lg:flex lg:space-x-1 items-center">
                    <a href="{{ route('manager.dashboard') }}"
                        class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all">
                        <i class="fas fa-layer-group mr-2 text-xs"></i> Overview
                    </a>

                    <a href="{{ route('manager.approval.index') }}"
                        class="nav-link {{ request()->is('manager/validation*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all relative">
                        <i class="fas fa-shield-check mr-2 text-xs"></i> Validation
                    </a>

                    <a href="{{ route('manager.users.index') }}"
                        class="nav-link {{ request()->is('manager/users*') || request()->is('manager/divisi*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all">
                        <i class="fas fa-users-cog mr-2 text-xs"></i> User Management
                    </a>

                    <a href="{{ route('manager.variables.index') }}"
                        class="nav-link {{ request()->is('manager/variables*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all">
                        <i class="fas fa-sliders-h mr-2 text-xs"></i> KPI Config
                    </a>

                    <a href="{{ route('manager.reports.index') }}"
                        class="nav-link {{ request()->is('manager/reports*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all">
                        <i class="fas fa-file-download mr-2 text-xs"></i> Reports
                    </a>
                </div>
            </div>

            {{-- Right Side Profile & Theme Switcher --}}
            <div class="hidden md:flex items-center space-x-4">

                {{-- Theme Switcher Dots --}}
                <div class="flex bg-black/30 p-1.5 rounded-full gap-2 mr-2 border border-white/10">
                    <button onclick="setTheme('default')" title="Tema Gelap" class="w-4 h-4 rounded-full bg-[#6366f1] border border-white/20 hover:scale-125 transition-all"></button>
                    <button onclick="setTheme('tema-1')" title="Tema Merah" class="w-4 h-4 rounded-full bg-[#C40C0C] border border-white/20 hover:scale-125 transition-all"></button>
                    <button onclick="setTheme('tema-2')" title="Tema Teal" class="w-4 h-4 rounded-full bg-[#016B61] border border-white/20 hover:scale-125 transition-all"></button>
                    <button onclick="setTheme('tema-3')" title="Tema Earth" class="w-4 h-4 rounded-full bg-[#504B38] border border-white/20 hover:scale-125 transition-all"></button>
                </div>

                <div class="flex flex-col items-end mr-2 text-right">
                    <span class="text-[9px] uppercase tracking-[0.2em] font-bold leading-none mb-1" style="color: var(--accent)">Access Level: Manager</span>
                    <span class="text-sm font-header font-semibold text-white">{{ Auth::user()->name }}</span>
                </div>

                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/10 group">
                    <i class="fas fa-user-shield text-white group-hover:scale-110 transition-transform"></i>
                </div>

                <div class="h-6 w-[1px] bg-white/10"></div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white transition-all duration-300">
                        <i class="fas fa-power-off text-sm"></i>
                    </button>
                </form>
            </div>

            {{-- Mobile Toggle --}}
            <div class="flex items-center lg:hidden">
                <button id="menu-btn" class="p-2 rounded-xl bg-white/5 text-white">
                    <i id="menu-icon" class="fas fa-bars-staggered text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="lg:hidden">
            <div class="py-6 space-y-1 px-4">
                <a href="{{ route('manager.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl text-white bg-white/10">
                    <i class="fas fa-home w-8 text-xs"></i> Overview
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="max-w-[1600px] mx-auto py-10 px-6 lg:px-12">
            @yield('content')
        </div>
    </main>

    <footer class="p-8 text-center" style="background: rgba(0,0,0,0.05)">
        <p class="text-[10px] font-medium italic uppercase tracking-widest opacity-60">
            &copy; 2026 <span style="color: var(--primary)" class="font-bold">MyBolo Console</span> • Manager Intelligence Interface
        </p>
    </footer>

    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('menu-icon');
        btn.addEventListener('click', () => {
            menu.classList.toggle('menu-open');
            icon.classList.toggle('fa-bars-staggered');
            icon.classList.toggle('fa-xmark');
        });

        function setTheme(themeName) {
            if (themeName === 'default') {
                document.documentElement.removeAttribute('data-theme');
            } else {
                document.documentElement.setAttribute('data-theme', themeName);
            }
            localStorage.setItem('manager-theme', themeName);
        }

        window.onload = () => {
            const savedTheme = localStorage.getItem('manager-theme');
            if (savedTheme) setTheme(savedTheme);
        };
    </script>
</body>

</html>