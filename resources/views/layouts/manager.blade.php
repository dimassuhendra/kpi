<!DOCTYPE html>
<html lang="id" class="dark">

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

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#0f172a',
                        accent: '#818cf8',
                        darkCard: '#1e293b',
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
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .font-header {
            font-family: 'Fredoka', sans-serif;
        }

        .logo-silhouette {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .organic-card {
            background: #1e293b;
            border-radius: 40px 15px 40px 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .glass-nav {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background-color: #6366f1;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 60%;
        }

        #mobile-menu {
            transition: all 0.4s ease;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        #mobile-menu.menu-open {
            max-height: 100vh;
            opacity: 1;
        }

        /* Custom Scrollbar for Manager */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6366f1;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    {{-- NAV FULL WIDTH --}}
    <nav class="glass-nav text-white sticky top-0 z-50 w-full px-6 lg:px-12">
        <div class="flex justify-between h-20 items-center">

            {{-- Brand --}}
            <div class="flex items-center gap-8">
                <a href="{{ route('manager.dashboard') }}" class="flex-shrink-0 flex items-center group">
                    <img class="h-10 w-auto logo-silhouette group-hover:rotate-12 transition-transform"
                        src="{{ asset('img/logo.png') }}" alt="Logo">
                    <span class="ml-3 font-header font-bold text-xl tracking-tighter text-white uppercase">
                        MyBolo <span class="text-primary italic">Console</span>
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden lg:flex lg:space-x-1 items-center">
                    <a href="{{ route('manager.dashboard') }}"
                        class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active text-primary bg-primary/5' : 'text-slate-400' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest hover:text-primary transition-all">
                        <i class="fas fa-layer-group mr-2 text-xs"></i> Overview
                    </a>

                    <a href="{{ route('manager.approval.index') }}"
                        class="nav-link {{ request()->is('manager/approval*') ? 'active text-primary bg-primary/5' : 'text-slate-400' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest hover:text-primary transition-all relative">
                        <i class="fas fa-shield-check mr-2 text-xs"></i> Validation
                        {{-- Badge Pending (Logic placeholder) --}}
                        <span class="ml-2 bg-rose-500 text-[9px] px-1.5 py-0.5 rounded-md text-white animate-pulse">4</span>
                    </a>

                    <a href="{{ route('manager.users.index') }}"
                        class="nav-link {{ request()->is('manager/users*') || request()->is('manager/divisi*') ? 'active text-primary bg-primary/5' : 'text-slate-400' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest hover:text-primary transition-all">
                        <i class="fas fa-users-cog mr-2 text-xs"></i> User Management
                    </a>

                    <a href="{{ route('manager.variables.index') }}"
                        class="nav-link {{ request()->is('manager/variables*') ? 'active text-primary bg-primary/5' : 'text-slate-400' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest hover:text-primary transition-all">
                        <i class="fas fa-sliders-h mr-2 text-xs"></i> KPI Config
                    </a>

                    <a href="{{ route('manager.reports.index') }}"
                        class="nav-link {{ request()->is('manager/reports*') ? 'active text-primary bg-primary/5' : 'text-slate-400' }} flex items-center px-4 py-2 rounded-xl text-[11px] font-bold uppercase tracking-widest hover:text-primary transition-all">
                        <i class="fas fa-file-download mr-2 text-xs"></i> Reports
                    </a>
                </div>
            </div>

            {{-- Right Side Profile --}}
            <div class="hidden md:flex items-center space-x-4">
                <div class="flex flex-col items-end mr-2 text-right">
                    <span class="text-[9px] uppercase tracking-[0.2em] text-primary font-bold leading-none mb-1">Access Level: Manager</span>
                    <span class="text-sm font-header font-semibold text-white">{{ Auth::user()->name }}</span>
                </div>

                <div class="w-10 h-10 bg-gradient-to-tr from-primary/20 to-primary/5 rounded-xl flex items-center justify-center border border-white/10 group">
                    <i class="fas fa-user-shield text-primary group-hover:scale-110 transition-transform"></i>
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
                <button id="menu-btn" class="p-2 rounded-xl bg-white/5 text-primary">
                    <i id="menu-icon" class="fas fa-bars-staggered text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="lg:hidden">
            <div class="py-6 space-y-1">
                <a href="{{ route('manager.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl {{ request()->routeIs('manager.dashboard') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-400' }}">
                    <i class="fas fa-home w-8 text-xs"></i> Overview
                </a>
                <a href="{{ route('manager.approval.index') }}" class="flex items-center px-4 py-3 rounded-2xl {{ request()->is('manager/approval*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-400' }}">
                    <i class="fas fa-check-circle w-8 text-xs"></i> Validation Center
                </a>
                <a href="{{ route('manager.users.index') }}" class="flex items-center px-4 py-3 rounded-2xl {{ request()->is('manager/users*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-400' }}">
                    <i class="fas fa-users w-8 text-xs"></i> User Management
                </a>
                <a href="{{ route('manager.variables.index') }}" class="flex items-center px-4 py-3 rounded-2xl {{ request()->is('manager/variables*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-400' }}">
                    <i class="fas fa-cogs w-8 text-xs"></i> KPI Configuration
                </a>

                <form action="{{ route('logout') }}" method="POST" class="pt-4 px-4 border-t border-white/5 mt-4">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-2xl bg-red-500/10 text-red-500 font-bold border border-red-500/20">
                        Logout Session
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow">
        <div class="max-w-[1600px] mx-auto py-10 px-6 lg:px-12">
            @yield('content')
        </div>
    </main>

    <footer class="p-8 text-center bg-secondary/30">
        <p class="text-slate-600 text-[10px] font-medium italic uppercase tracking-widest">
            &copy; 2026 <span class="text-primary font-bold">MyBolo Console</span> • Manager Intelligence Interface
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
    </script>
    @stack('scripts')
</body>

</html>