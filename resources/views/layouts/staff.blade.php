<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - KPI System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Bree+Serif:wght@400;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#09637E',
                        secondary: '#088395',
                        accent: '#7AB2B2',
                        background: '#EBF4F6',
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
        body {
            font-family: 'Bree Serif', serif;
            scroll-behavior: smooth;
        }

        .font-header {
            font-family: 'Fredoka', sans-serif;
        }

        /* Navbar Glassmorphism */
        .glass-nav {
            background: rgba(9, 99, 126, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Active Link Animation */
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #7AB2B2;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        #mobile-menu {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 0;
            opacity: 0;
        }

        #mobile-menu.menu-open {
            max-height: 600px;
            opacity: 1;
        }
    </style>
</head>

<body class="bg-background min-h-screen flex flex-col">

    <nav class="glass-nav text-white sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="{{ route('staff.dashboard') }}" class="flex-shrink-0 flex items-center group">
                        <img class="h-10 w-auto md:h-12 drop-shadow-md group-hover:scale-110 transition-transform"
                            src="{{ asset('img/logo.png') }}"
                            alt="Logo KPI System">
                    </a>

                    <div class="hidden lg:ml-10 lg:flex lg:space-x-2">
                        <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->is('dashboard/staff') ? 'active text-accent' : '' }} px-4 py-2 rounded-xl text-sm font-medium hover:text-accent">
                            <i class="fas fa-grid-2 mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('staff.kpi.create') }}" class="nav-link px-4 py-2 rounded-xl text-sm font-medium hover:text-accent">
                            <i class="fas fa-pen-to-square mr-1"></i> Input KPI
                        </a>
                        <a href="{{ route('staff.kpi.history') }}" class="nav-link px-4 py-2 rounded-xl text-sm font-medium hover:text-accent">
                            <i class="fas fa-rectangle-list mr-1"></i> Riwayat
                        </a>
                        <a href="{{ route('staff.performance') }}" class="nav-link px-4 py-2 rounded-xl text-sm font-medium hover:text-accent">
                            <i class="fas fa-chart-simple mr-1"></i> Performa
                        </a>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <div class="flex flex-col items-end mr-2">
                        <span class="text-[10px] uppercase tracking-widest opacity-60 font-bold">{{ Auth::user()->division->name }}</span>
                        <span class="text-sm font-header font-semibold">{{ Auth::user()->name }}</span>
                    </div>

                    <div class="relative group cursor-pointer">
                        <div class="w-11 h-11 bg-gradient-to-tr from-accent to-secondary rounded-2xl flex items-center justify-center border-2 border-white/20 shadow-lg group-hover:rotate-6 transition-all">
                            <i class="fas fa-user-astronaut text-xl"></i>
                        </div>
                    </div>

                    <div class="h-8 w-[1px] bg-white/10 mx-2"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="group flex items-center justify-center w-11 h-11 bg-red-500/10 hover:bg-red-500 rounded-2xl transition-all duration-300">
                            <i class="fas fa-power-off text-red-500 group-hover:text-white"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center lg:hidden">
                    <button id="menu-btn" class="inline-flex items-center justify-center p-2 rounded-2xl bg-white/10 hover:bg-white/20 focus:outline-none transition-all">
                        <i id="menu-icon" class="fas fa-bars-staggered text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="lg:hidden bg-primary shadow-2xl overflow-hidden">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="{{ route('staff.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl bg-white/10 text-accent font-bold">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                <a href="{{ route('staff.kpi.create') }}" class="flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition">
                    <i class="fas fa-edit mr-3"></i> Input KPI Harian
                </a>
                <a href="{{ route('staff.kpi.history') }}" class="flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition">
                    <i class="fas fa-history mr-3"></i> Riwayat Laporan
                </a>
                <a href="{{ route('staff.performance') }}" class="flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition">
                    <i class="fas fa-chart-line mr-3"></i> Performa Saya
                </a>

                <div class="my-4 border-t border-white/10 pt-4">
                    <div class="flex items-center px-4 py-2 mb-4">
                        <div class="w-10 h-10 bg-accent rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">{{ Auth::user()->name }}</p>
                            <p class="text-xs opacity-60">{{ Auth::user()->division->name }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-3 rounded-2xl bg-red-500 text-white font-bold shadow-lg shadow-red-500/30">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="min-h-[70vh]">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="p-8 text-center">
        <div class="max-w-7xl mx-auto border-t border-gray-200 pt-8">
            <p class="text-gray-400 text-sm font-medium italic">
                &copy; 2024 <span class="text-primary font-bold">KPI System</span> • IT Department.
            </p>
        </div>
    </footer>

    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('menu-icon');

        btn.addEventListener('click', () => {
            menu.classList.toggle('menu-open');
            if (menu.classList.contains('menu-open')) {
                icon.classList.remove('fa-bars-staggered');
                icon.classList.add('fa-xmark');
                btn.classList.add('rotate-90');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars-staggered');
                btn.classList.remove('rotate-90');
            }
        });

        // Effect navbar mengecil saat scroll
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('h-16');
                nav.classList.remove('h-20');
            } else {
                nav.classList.add('h-20');
                nav.classList.remove('h-16');
            }
        });
    </script>
</body>

</html>