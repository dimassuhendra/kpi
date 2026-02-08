<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - KPI System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Bree+Serif:wght@400;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981',
                        secondary: '#0f172a',
                        accent: '#34d399',
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
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.2);
        }

        .glass-nav {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
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
            background-color: #10b981;
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
            <div class="flex justify-between items-center h-20 transition-all duration-300" id="nav-content">

                <div class="flex items-center">
                    <a href="#" class="flex-shrink-0 flex items-center group">
                        <img class="h-10 w-auto md:h-12 logo-silhouette group-hover:scale-110 transition-transform"
                            src="{{ asset('img/logo.png') }}"
                            alt="Logo KPI System">
                    </a>

                    <div class="hidden lg:ml-10 lg:flex lg:space-x-2">
                        <a href="{{ route('staff.dashboard') }}"
                            class="nav-link px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('staff.dashboard') ? 'active text-primary' : 'text-slate-300 hover:text-primary' }}">
                            <i class="fas fa-th-large mr-1"></i> Main Station
                        </a>

                        <a href="{{ route('staff.input') }}"
                            class="nav-link px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('staff.input') ? 'active text-primary' : 'text-slate-300 hover:text-primary' }}">
                            <i class="fas fa-edit mr-1"></i> Input Daily Case
                        </a>

                        <a href="{{ route('staff.kpi.logs') }}"
                            class="nav-link px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('staff.kpi.logs') ? 'active text-primary' : 'text-slate-300 hover:text-primary' }}">
                            <i class="fas fa-history mr-1"></i> Submission Logs
                        </a>

                        <a href="#"
                            class="nav-link px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('staff.stats') ? 'active text-primary' : 'text-slate-300 hover:text-primary' }}">
                            <i class="fas fa-chart-line mr-1"></i> Achievement Stats
                        </a>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <div class="flex flex-col items-end mr-2 text-right">
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold leading-none">
                            {{ Auth::user()->division->name ?? 'TAC DIVISION' }}
                        </span>
                        <span class="text-sm font-header font-semibold text-slate-200">
                            {{ Auth::user()->name ?? 'Staff Name' }}
                        </span>
                    </div>

                    <div class="relative group cursor-pointer">
                        <div class="w-11 h-11 bg-gradient-to-tr from-primary to-emerald-700 rounded-2xl flex items-center justify-center border-2 border-white/10 shadow-lg group-hover:rotate-6 transition-all">
                            <i class="fas fa-user-astronaut text-xl text-white"></i>
                        </div>
                    </div>

                    <div class="h-8 w-[1px] bg-white/10 mx-2"></div>

                    <form action="#" method="POST">
                        @csrf
                        <button type="submit" class="group flex items-center justify-center w-11 h-11 bg-red-500/10 hover:bg-red-500 rounded-2xl transition-all duration-300">
                            <i class="fas fa-power-off text-red-500 group-hover:text-white"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center lg:hidden">
                    <button id="menu-btn" class="inline-flex items-center justify-center p-2 rounded-2xl bg-white/5 hover:bg-white/10 focus:outline-none transition-all">
                        <i id="menu-icon" class="fas fa-bars-staggered text-xl text-primary"></i>
                    </button>
                </div>

            </div>
        </div>

        <div id="mobile-menu" class="lg:hidden bg-secondary border-t border-white/5 shadow-2xl">
            <div class="px-4 pt-4 pb-8 space-y-2">
                <a href="{{ route('staff.dashboard') }}"
                    class="flex items-center px-4 py-4 rounded-3xl transition-all {{ request()->routeIs('staff.dashboard') ? 'bg-primary/10 text-primary font-bold border border-primary/20' : 'text-slate-300 hover:bg-white/5' }}">
                    <i class="fas fa-home mr-3"></i> Main Station
                </a>

                <a href="{{ route('staff.input') }}"
                    class="flex items-center px-4 py-4 rounded-3xl transition-all {{ request()->routeIs('staff.input') ? 'bg-primary/10 text-primary font-bold border border-primary/20' : 'text-slate-300 hover:bg-white/5' }}">
                    <i class="fas fa-edit mr-3"></i> Input Daily Case
                </a>

                <a href="{{ route('staff.kpi.logs') }}"
                    class="flex items-center px-4 py-4 rounded-3xl transition-all {{ request()->routeIs('staff.kpi.logs') ? 'bg-primary/10 text-primary font-bold border border-primary/20' : 'text-slate-300 hover:bg-white/5' }}">
                    <i class="fas fa-history mr-3"></i> Submission Logs
                </a>

                <a href="#"
                    class="flex items-center px-4 py-4 rounded-3xl transition-all {{ request()->routeIs('staff.stats') ? 'bg-primary/10 text-primary font-bold border border-primary/20' : 'text-slate-300 hover:bg-white/5' }}">
                    <i class="fas fa-chart-line mr-3"></i> Achievement Stats
                </a>

                <div class="my-6 border-t border-white/5 pt-6">
                    <div class="flex items-center px-4 py-2 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-tr from-primary to-emerald-700 rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-emerald-500/20">
                            <a href="#"><i class="fas fa-user text-xl text-white"></i></a>
                        </div>
                        <div>
                            <p class="text-base font-bold text-white">{{ Auth::user()->name ?? 'Staff Name' }}</p>
                            <p class="text-xs text-slate-500 uppercase tracking-wider">{{ Auth::user()->division->name ?? 'TAC' }}</p>
                        </div>
                    </div>
                    <form action="#" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-4 rounded-3xl bg-red-500/10 text-red-500 font-bold border border-red-500/20 hover:bg-red-500 hover:text-white transition-all">
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

    <footer class="p-8 text-center bg-secondary/50">
        <div class="max-w-7xl mx-auto border-t border-white/5 pt-8">
            <p class="text-slate-600 text-sm font-medium italic">
                &copy; 2026 <span class="text-primary font-bold">MyBolo KPI System</span> • All Rights Reserved •
            </p>
        </div>
    </footer>

    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('menu-icon');
        const navContent = document.getElementById('nav-content');

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

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navContent.classList.replace('h-20', 'h-16');
            } else {
                navContent.classList.replace('h-16', 'h-20');
            }
        });
    </script>
</body>

</html>