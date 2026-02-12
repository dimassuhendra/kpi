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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        imperial: {
                            500: '#dc2626', // Red
                            600: '#b91c1c',
                            700: '#991b1b'
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706'
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
            --primary: #dc2626;
            --bg-body: #fffaf0;
            /* Creamy white for elegance */
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            overflow-x: hidden;
            cursor: crosshair;
            /* Menandakan bisa diklik untuk kembang api */
        }

        .nav-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 2px solid #fbbf24;
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
            height: 4px;
            background: linear-gradient(to right, #dc2626, #fbbf24);
            border-radius: 50px 50px 0 0;
        }

        /* Hero Section Styling */
        .hero-container {
            position: relative;
            height: 220px;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(220, 38, 38, 0.3);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .hero-image {
            background: url("{{ asset('img/3.jpg') }}") no-repeat center center;
            background-size: cover;
            position: absolute;
            inset: 0;
            transition: transform 0.7s ease;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(127, 29, 29, 0.9) 0%, rgba(2, 6, 23, 0.6) 100%);
        }

        /* Naga Watermark Animasi */
        .dragon-bg {
            position: fixed;
            bottom: -50px;
            right: -50px;
            font-size: 300px;
            color: rgba(220, 38, 38, 0.04);
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: 0;
            animation: dragonFloat 10s ease-in-out infinite;
        }

        @keyframes dragonFloat {

            0%,
            100% {
                transform: rotate(-15deg) translateY(0);
            }

            50% {
                transform: rotate(-10deg) translateY(-20px);
            }
        }

        /* Lampion & Ornamen */
        @keyframes swing {

            0%,
            100% {
                transform: rotate(-6deg);
            }

            50% {
                transform: rotate(6deg);
            }
        }

        .lampion-body {
            width: 35px;
            height: 30px;
            background: #dc2626;
            border-radius: 40%;
            border: 2px solid #fbbf24;
            position: relative;
            box-shadow: 0 0 15px rgba(220, 38, 38, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #fbbf24;
            font-size: 12px;
        }

        .ornament-side {
            position: fixed;
            top: 0;
            z-index: 70;
            display: flex;
            flex-direction: column;
            align-items: center;
            pointer-events: none;
        }

        /* Canvas Fireworks */
        #fireworks-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased">

    <canvas id="fireworks-canvas"></canvas>

    <div class="ornament-side left-8 hidden xl:flex">
        <div class="animate-[swing_3s_ease-in-out_infinite] origin-top">
            <div class="w-0.5 h-24 bg-red-600 mx-auto"></div>
            <div class="lampion-body">福</div>
        </div>
        <div class="animate-[swing_4s_ease-in-out_infinite] origin-top -mt-4" style="animation-delay: -1s;">
            <div class="w-0.5 h-12 bg-red-600 mx-auto"></div>
            <div class="lampion-body" style="width:25px; height:20px; font-size:8px;">吉</div>
        </div>
    </div>

    <div class="ornament-side right-8 hidden xl:flex">
        <div class="animate-[swing_3.5s_ease-in-out_infinite] origin-top">
            <div class="w-0.5 h-16 bg-red-600 mx-auto"></div>
            <div class="lampion-body">禄</div>
        </div>
        <div class="animate-[swing_4.5s_ease-in-out_infinite] origin-top -mt-2">
            <div class="w-0.5 h-32 bg-red-600 mx-auto"></div>
            <div class="lampion-body">寿</div>
        </div>
    </div>

    <div class="dragon-bg">
        <i class="fas fa-dragon"></i>
    </div>

    <nav class="nav-glass sticky top-0 z-[60] w-full">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-12">
                    <a href="{{ route('manager.dashboard') }}" class="flex items-center group">
                        <div
                            class="w-10 h-10 bg-imperial-500 rounded-xl flex items-center justify-center shadow-lg shadow-red-200 group-hover:rotate-12 transition-transform border border-gold-400">
                            <i class="fas fa-dragon text-gold-400 text-lg"></i>
                        </div>
                        <span class="ml-3 font-header font-black text-lg tracking-tighter text-slate-800 uppercase">
                            MyBolo<span class="text-imperial-500">Console</span>
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
                    <div class="hidden lg:block text-gold-500 animate-bounce">
                        <i class="fas fa-coins text-sm"></i>
                    </div>

                    <div class="text-right hidden sm:block border-r border-slate-100 pr-6">
                        <p class="text-[9px] text-imperial-500 font-black uppercase tracking-widest leading-none mb-1">
                            Lead Manager</p>
                        <p class="text-xs font-bold text-slate-800 tracking-tight">{{ Auth::user()->name }}</p>
                    </div>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="w-10 h-10 rounded-full bg-imperial-500 border-2 border-gold-400 flex items-center justify-center text-gold-400 hover:scale-110 transition-all shadow-lg">
                            <i class="fas fa-dragon text-sm"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-3 w-48 bg-white border border-red-100 shadow-2xl rounded-2xl py-2 z-50">
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

    <main class="flex-grow relative z-10">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-6">

            <div class="hero-container mb-10">
                <div class="hero-image"></div>
                <div class="hero-overlay"></div>
                <div class="tech-pattern"></div>

                <div class="relative z-10 h-full p-8 lg:p-10 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div
                                class="px-3 py-1.5 rounded-full bg-red-600/20 backdrop-blur-md border border-gold-500/30 text-white text-[10px] font-bold uppercase flex items-center gap-2">
                                <i class="fas fa-fire text-gold-400"></i>
                                <span>29°C Bandar Lampung</span>
                            </div>
                        </div>

                        <div class="text-right text-white">
                            <div id="digital-clock"
                                class="font-mono text-2xl lg:text-3xl font-bold tracking-tighter leading-none text-gold-400">
                                00:00:00</div>
                            <div id="current-date"
                                class="text-[10px] font-black text-red-400 uppercase tracking-widest mt-1 opacity-80">
                                Loading...</div>
                        </div>
                    </div>

                    <div class="flex justify-between items-end">
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-black text-white tracking-tight uppercase">
                                Welcome, <span class="text-gold-400">{{ explode(' ', Auth::user()->name)[0] }}</span>
                            </h1>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-slate-300 text-[10px] font-bold uppercase tracking-[0.2em]">Manager
                                    Console</span>
                                <span class="text-white/20">•</span>
                                <span class="text-gold-500 text-[10px] font-black uppercase tracking-[0.2em]">
                                    {{ request()->routeIs('manager.dashboard') ? 'Overview' : str_replace('manager.', '', request()->route()->getName()) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="min-h-[400px]">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="mt-auto py-8 border-t border-slate-200 bg-white relative z-10">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <div
                    class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center text-imperial-500 border border-red-100">
                    <i class="fas fa-fan text-xs"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 tracking-widest uppercase">
                    &copy; 2026 MyBolo <span class="text-imperial-600">Console</span> • Build v2.5
                </p>
            </div>

            <div class="flex gap-6 items-center">
                <div class="text-right">
                    <span class="block text-[9px] font-black text-slate-300 uppercase tracking-widest">Server
                        Status</span>
                    <span
                        class="text-[10px] font-bold text-imperial-600 uppercase tracking-widest flex items-center gap-2 justify-end">
                        Optimal <div class="w-1.5 h-1.5 rounded-full bg-gold-500 animate-ping"></div>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // 1. Digital Clock & Date
        function updateTime() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('digital-clock').textContent = `${h}:${m}:${s}`;
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

        // 2. Super Fireworks Effect on Click
        const canvas = document.getElementById('fireworks-canvas');
        const ctx = canvas.getContext('2d');
        let particles = [];

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        class Particle {
            constructor(x, y, color) {
                this.x = x;
                this.y = y;
                this.color = color;
                this.velocity = {
                    x: (Math.random() - 0.5) * 8,
                    y: (Math.random() - 0.5) * 8
                };
                this.alpha = 1;
                this.friction = 0.95;
            }
            draw() {
                ctx.globalAlpha = this.alpha;
                ctx.beginPath();
                ctx.arc(this.x, this.y, 2, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
            }
            update() {
                this.velocity.x *= this.friction;
                this.velocity.y *= this.friction;
                this.x += this.velocity.x;
                this.y += this.velocity.y;
                this.alpha -= 0.01;
            }
        }

        function createFirework(x, y) {
            const colors = ['#fbbf24', '#f59e0b', '#dc2626', '#ffffff'];
            for (let i = 0; i < 30; i++) {
                particles.push(new Particle(x, y, colors[Math.floor(Math.random() * colors.length)]));
            }
        }

        window.addEventListener('click', (e) => {
            createFirework(e.clientX, e.clientY);
        });

        function animate() {
            ctx.fillStyle = 'rgba(0,0,0,0)'; // Transparent clear
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach((p, i) => {
                if (p.alpha > 0) {
                    p.update();
                    p.draw();
                } else {
                    particles.splice(i, 1);
                }
            });
            requestAnimationFrame(animate);
        }
        animate();
    </script>
</body>

</html>
