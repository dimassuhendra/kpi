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

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1', // Indigo untuk Manager
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

        .organic-card:hover {
            border-radius: 15px 40px 15px 40px;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3);
        }

        .glass-nav {
            background: rgba(15, 23, 42, 0.85);
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
            width: 80%;
        }

        #mobile-menu {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        #mobile-menu.menu-open {
            max-height: 100vh;
            opacity: 1;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <nav class="glass-nav text-white sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 transition-all duration-300" id="nav-content">
                <div class="flex items-center">
                    <a href="{{ route('manager.dashboard') }}" class="flex-shrink-0 flex items-center group">
                        <img class="h-10 w-auto md:h-12 logo-silhouette group-hover:rotate-12 transition-transform"
                            src="{{ asset('img/logo.png') }}" alt="Logo">
                    </a>

                    <div class="hidden lg:ml-10 lg:flex lg:space-x-2">
                        <a href="{{ route('manager.dashboard') }}"
                            class="nav-link {{ request()->is('manager/dashboard') ? 'active text-primary' : '' }} px-4 py-2 rounded-xl text-sm font-medium hover:text-primary">
                            <i class="fas fa-chart-pie mr-1"></i> Overview
                        </a>

                        <a href="{{ route('manager.approval.index') }}"
                            class="nav-link {{ request()->is('manager/approval*') ? 'active text-primary' : '' }} px-4 py-2 rounded-xl text-sm font-medium hover:text-primary">
                            <i class="fas fa-check-double mr-1"></i> Approval Team
                        </a>

                        <a href="#" class="nav-link px-4 py-2 rounded-xl text-sm font-medium hover:text-primary">
                            <i class="fas fa-sliders-h mr-1"></i> Variabel KPI
                        </a>
                        <a href="#" class="nav-link px-4 py-2 rounded-xl text-sm font-medium hover:text-primary">
                            <i class="fas fa-users-cog mr-1"></i> Staff IT
                        </a>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <div class="flex flex-col items-end mr-2 text-right">
                        <span class="text-[10px] uppercase tracking-widest text-indigo-400 font-bold leading-none">Manager {{ Auth::user()->division->name }}</span>
                        <span class="text-sm font-header font-semibold text-slate-200">{{ Auth::user()->name }}</span>
                    </div>

                    <div class="relative group">
                        <div class="w-11 h-11 bg-gradient-to-tr from-primary to-indigo-800 rounded-2xl flex items-center justify-center border-2 border-white/10 shadow-lg transition-all group-hover:scale-110">
                            <i class="fas fa-user-shield text-xl text-white"></i>
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
                    <button id="menu-btn" class="inline-flex items-center justify-center p-2 rounded-2xl bg-white/5 hover:bg-white/10 focus:outline-none">
                        <i id="menu-icon" class="fas fa-bars-staggered text-xl text-primary"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="lg:hidden bg-secondary border-t border-white/5">
            <div id="mobile-menu" class="lg:hidden bg-secondary border-t border-white/5">
                <div class="px-4 pt-4 pb-8 space-y-2">
                    <a href="{{ route('manager.dashboard') }}"
                        class="flex items-center px-4 py-4 rounded-3xl {{ request()->is('manager/dashboard') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-300' }}">
                        <i class="fas fa-home mr-3"></i> Dashboard Manager
                    </a>

                    <a href="{{ route('manager.approval.index') }}"
                        class="flex items-center px-4 py-4 rounded-3xl {{ request()->is('manager/approval*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-300' }} hover:bg-white/5 transition">
                        <i class="fas fa-check-circle mr-3"></i> Approval
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="pt-4">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-4 rounded-3xl bg-red-500/10 text-red-500 font-bold border border-red-500/20">
                            <i class="fas fa-sign-out-alt mr-2"></i> Keluar Sistem
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

    <footer class="p-8 text-center bg-secondary/50">
        <div class="max-w-7xl mx-auto border-t border-white/5 pt-8">
            <p class="text-slate-600 text-sm font-medium italic">
                &copy; 2026 <span class="text-primary font-bold">MyBolo Console</span> • Manager Access •
            </p>
        </div>
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
</body>

</html>