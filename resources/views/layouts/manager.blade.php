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
                        emerald: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857'
                        },
                        slate: {
                            950: '#020617'
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
            --primary: #059669;
            --bg-body: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
        }

        .nav-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
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

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -28px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary);
            border-radius: 50px 50px 0 0;
        }

        /* Hero Section Styling */
        .hero-container {
            position: relative;
            height: 220px;
            /* Diperkecil agar tidak mengganggu */
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(5, 150, 105, 0.15);
        }

        .hero-image {
            background: url("{{ asset('img/3.jpg') }}") no-repeat center center;
            background-size: cover;
            position: absolute;
            inset: 0;
            transition: transform 0.7s ease;
        }

        .hero-container:hover .hero-image {
            transform: scale(1.03);
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(2, 6, 23, 0.9) 0%, rgba(2, 6, 23, 0.4) 100%);
        }

        .tech-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 25px 25px;
        }

        #digital-clock {
            text-shadow: 0 0 15px rgba(52, 211, 153, 0.4);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased">

    <nav class="nav-glass sticky top-0 z-[60] w-full">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-12">
                    <a href="{{ route('manager.dashboard') }}" class="flex items-center group">
                        <div
                            class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-200 group-hover:rotate-12 transition-transform">
                            <i class="fas fa-bolt text-white text-lg"></i>
                        </div>
                        <span class="ml-3 font-header font-black text-lg tracking-tighter text-slate-800 uppercase">
                            MyBolo<span class="text-emerald-600">Console</span>
                        </span>
                    </a>

                    <div class="hidden lg:flex items-center space-x-8 mt-1">
                        <a href="{{ route('manager.dashboard') }}"
                            class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }} uppercase">Dashboard</a>
                        <a href="{{ route('manager.approval.index') }}"
                            class="nav-link {{ request()->routeIs('manager.approval.*') ? 'active' : '' }} uppercase">Validation</a>
                        <a href="{{ route('manager.users.index') }}"
                            class="nav-link {{ request()->routeIs('manager.users.*') ? 'active' : '' }} uppercase">Users</a>
                        <a href="{{ route('manager.reports.index') }}"
                            class="nav-link {{ request()->routeIs('manager.reports.*') ? 'active' : '' }} uppercase">Archive</a>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right hidden sm:block border-r border-slate-100 pr-6">
                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1">Lead
                            Manager</p>
                        <p class="text-xs font-bold text-slate-800 tracking-tight">{{ Auth::user()->name }}</p>
                    </div>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-all">
                            <i class="fas fa-user-shield text-sm"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-3 w-48 bg-white border border-slate-100 shadow-xl rounded-2xl py-2 z-50">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-xs font-bold text-rose-500 hover:bg-rose-50 flex items-center gap-2 uppercase tracking-wider">
                                    <i class="fas fa-sign-out-alt"></i> Sign Out System
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-6">

            <div class="hero-container mb-10">
                <div class="hero-image"></div>
                <div class="hero-overlay"></div>
                <div class="tech-pattern"></div>

                <div class="relative z-10 h-full p-8 lg:p-10 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div
                                class="px-3 py-1.5 rounded-full bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 text-emerald-400 text-[10px] font-black tracking-widest uppercase flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                                System Online
                            </div>
                            <div
                                class="px-3 py-1.5 rounded-full bg-white/5 backdrop-blur-md border border-white/10 text-white text-[10px] font-bold uppercase flex items-center gap-2">
                                <i class="fas fa-sun text-amber-400"></i>
                                <span>29°C Bandar Lampung</span>
                            </div>
                        </div>

                        <div class="text-right text-white">
                            <div id="digital-clock"
                                class="font-mono text-2xl lg:text-3xl font-bold tracking-tighter leading-none">00:00:00
                            </div>
                            <div id="current-date"
                                class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mt-1 opacity-80">
                                Loading...</div>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-black text-white tracking-tight uppercase">
                                Welcome, <span
                                    class="text-emerald-400">{{ explode(' ', Auth::user()->name)[0] }}</span>
                            </h1>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-slate-300 text-[10px] font-bold uppercase tracking-[0.2em]">Manager
                                    Console</span>
                                <span class="text-white/20">•</span>
                                <span class="text-emerald-500 text-[10px] font-black uppercase tracking-[0.2em]">
                                    {{ request()->routeIs('manager.dashboard') ? 'Overview' : str_replace('manager.', '', request()->route()->getName()) }}
                                </span>
                            </div>
                        </div>

                        @if (request()->routeIs('manager.dashboard'))
                            <div class="hidden md:flex gap-6">
                                <div class="text-right">
                                    <span class="block text-[9px] font-black text-emerald-500 uppercase">Pending</span>
                                    <span class="text-white font-mono text-lg font-bold leading-none">12 Task</span>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="block text-[9px] font-black text-emerald-500 uppercase">Efficiency</span>
                                    <span class="text-white font-mono text-lg font-bold leading-none">94%</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="min-h-[400px]">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="mt-auto py-8 border-t border-slate-200 bg-white">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                    <i class="fas fa-terminal text-xs"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 tracking-widest uppercase">
                    &copy; 2026 MyBolo <span class="text-emerald-600">Console</span> • Build v2.5
                </p>
            </div>

            <div class="flex gap-6 items-center">
                <div class="text-right">
                    <span class="block text-[9px] font-black text-slate-300 uppercase tracking-widest">Server
                        Status</span>
                    <span
                        class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest flex items-center gap-2 justify-end">
                        Optimal <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function updateTime() {
            const now = new Date();

            // Time
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('digital-clock').textContent = `${h}:${m}:${s}`;

            // Date
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
        }

        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>

</html>
