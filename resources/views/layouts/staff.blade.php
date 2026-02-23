<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - MyBolo Console</title>

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
                            700: '#047857',
                            800: '#064e3b',
                            900: '#064e3b',
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
            --primary: #059669;
            --bg-body: #f0fdf4;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            overflow-x: hidden;
            background-image: url("https://www.transparenttextures.com/patterns/islamic-art.png");
        }

        .nav-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 3px solid #fbbf24;
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
                background: linear-gradient(to right, #059669, #fbbf24);
                border-radius: 50px 50px 0 0;
            }
        }

        .hero-container {
            position: relative;
            height: auto;
            min-height: 220px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(5, 150, 105, 0.25);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .hero-image {
            background: url("https://images.unsplash.com/photo-1519491056120-d03f1598ca06?q=80&w=2071&auto=format&fit=crop") no-repeat center center;
            background-size: cover;
            position: absolute;
            inset: 0;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.9) 0%, rgba(2, 6, 23, 0.7) 100%);
        }

        .mosque-bg {
            position: fixed;
            bottom: -30px;
            right: -30px;
            font-size: 220px;
            color: rgba(5, 150, 105, 0.06);
            pointer-events: none;
            z-index: 0;
            animation: mosqueFloat 8s ease-in-out infinite;
        }

        @keyframes mosqueFloat {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-15px) rotate(2deg);
            }
        }

        @keyframes swing {

            0%,
            100% {
                transform: rotate(-6deg);
            }

            50% {
                transform: rotate(6deg);
            }
        }

        .ornament-body {
            width: 40px;
            height: 40px;
            background: #064e3b;
            border-radius: 10px;
            border: 2px solid #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbbf24;
            font-size: 18px;
            box-shadow: 0 0 15px rgba(251, 191, 36, 0.3);
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

        #star-canvas {
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

<body class="min-h-screen flex flex-col antialiased" x-data="{ mobileMenu: false }">

    <canvas id="star-canvas"></canvas>

    <div class="ornament-side left-8 hidden xl:flex">
        <div class="animate-[swing_4s_ease-in-out_infinite] origin-top">
            <div class="w-0.5 h-24 bg-emerald-700 mx-auto"></div>
            <div class="ornament-body"><i class="fas fa-moon"></i></div>
        </div>
    </div>

    <div class="ornament-side right-8 hidden xl:flex">
        <div class="animate-[swing_3s_ease-in-out_infinite] origin-top">
            <div class="w-0.5 h-32 bg-gold-500 mx-auto"></div>
            <div class="ornament-body"><i class="fas fa-star"></i></div>
        </div>
    </div>

    <div class="mosque-bg"><i class="fas fa-mosque"></i></div>

    <nav class="nav-glass sticky top-0 z-[60] w-full">
        <div class="max-w-[1440px] mx-auto px-4 lg:px-10">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-4 lg:gap-12">
                    <a href="#" class="flex items-center group">
                        <div
                            class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg border border-gold-400 group-hover:scale-110 transition-transform">
                            <i class="fas fa-kaaba text-gold-400 text-lg"></i>
                        </div>
                        <span class="ml-3 font-header font-black text-lg tracking-tighter text-slate-800 uppercase">
                            MyBolo<span class="text-emerald-600">Staff</span>
                        </span>
                    </a>

                    <div class="hidden lg:flex items-center space-x-8 mt-1">
                        <a href="{{ route('staff.dashboard') }}"
                            class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }} uppercase">Main
                            Station</a>
                        <a href="{{ route('staff.input') }}"
                            class="nav-link {{ request()->routeIs('staff.input') ? 'active' : '' }} uppercase">Input
                            Case</a>
                        <a href="{{ route('staff.kpi.logs') }}"
                            class="nav-link {{ request()->routeIs('staff.kpi.logs') ? 'active' : '' }} uppercase">Logs</a>
                        @if (Auth::user()->divisi_id != 6)
                            <a href="{{ route('staff.kpi.achievements') }}"
                                class="nav-link {{ request()->routeIs('staff.kpi.achievements') ? 'active' : '' }} uppercase">Stats</a>
                        @endif
                        <a href="{{ route('staff.profile.edit') }}"
                            class="nav-link {{ request()->routeIs('staff.profile.*') ? 'active' : '' }} uppercase">Profile</a>
                        <a href="{{ route('updates.index') }}" onclick="markUpdateRead()"
                            class="nav-link {{ request()->routeIs('updates.index') ? 'active' : '' }} uppercase flex items-center gap-1">
                            Pembaruan
                            <span id="update-badge-staff"
                                class="hidden h-2 w-2 rounded-full bg-gold-500 animate-ping">BARU</span>
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3 lg:gap-6">
                    <div class="hidden sm:block text-right border-r border-slate-100 pr-6">
                        <p class="text-[9px] text-emerald-600 font-black uppercase tracking-widest leading-none mb-1">
                            {{ Auth::user()->divisi->nama_divisi ?? 'Staff Member' }}
                        </p>
                        <p class="text-xs font-bold text-slate-800 tracking-tight">{{ Auth::user()->nama_lengkap }}</p>
                    </div>

                    <div class="hidden lg:block">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-wider border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all">
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
            class="lg:hidden bg-white border-t border-gold-400 px-4 py-6 shadow-2xl relative z-[70]">
            <div class="flex flex-col space-y-2">
                <a href="{{ route('staff.dashboard') }}"
                    class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('staff.dashboard') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                    <span class="flex items-center gap-3"><i class="fas fa-home w-5"></i> Main Station</span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                <a href="{{ route('staff.input') }}"
                    class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('staff.input') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                    <span class="flex items-center gap-3"><i class="fas fa-edit w-5"></i> Input Daily Case</span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                <a href="{{ route('staff.kpi.logs') }}"
                    class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('staff.kpi.logs') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                    <span class="flex items-center gap-3"><i class="fas fa-history w-5"></i> Submission Logs</span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                @if (Auth::user()->divisi_id != 6)
                    <a href="{{ route('staff.kpi.achievements') }}"
                        class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('staff.kpi.achievements') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                        <span class="flex items-center gap-3"><i class="fas fa-chart-line w-5"></i> Achievement
                            Stats</span>
                        <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                    </a>
                @endif

                <a href="{{ route('staff.profile.edit') }}"
                    class="flex items-center justify-between p-4 rounded-2xl transition-all {{ request()->routeIs('staff.profile.*') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'text-slate-600 hover:bg-slate-50' }} font-bold text-sm uppercase tracking-wide">
                    <span class="flex items-center gap-3"><i class="fas fa-user-circle w-5"></i> My Profile</span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                <a href="{{ route('updates.index') }}" onclick="markUpdateRead()"
                    class="flex items-center justify-between p-4 rounded-2xl {{ request()->routeIs('updates.index') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }} font-bold text-sm uppercase">
                    <span class="flex items-center gap-3">
                        Pembaruan
                        <span id="update-badge-mobile-staff"
                            class="hidden text-[9px] bg-gold-400 text-emerald-900 px-1.5 py-0.5 rounded-full">Baru</span>
                    </span>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </a>

                <div class="pt-4 mt-2 border-t border-slate-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-3 p-4 bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-emerald-200">
                            <i class="fas fa-power-off"></i> Sign Out
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
                        <div
                            class="px-3 py-1.5 rounded-full bg-emerald-900/40 backdrop-blur-md border border-gold-500/30 text-white text-[10px] font-bold uppercase flex items-center gap-2">
                            <i class="fas fa-clock text-gold-400 animate-pulse"></i>
                            <span id="next-prayer">Memuat Jadwal...</span>
                        </div>
                        <div class="text-right text-white">
                            <div id="digital-clock"
                                class="font-mono text-xl lg:text-3xl font-bold tracking-tighter text-gold-400">00:00:00
                            </div>
                            <div id="current-date"
                                class="text-[9px] font-black text-emerald-300 uppercase tracking-widest mt-1 opacity-80">
                                Loading...</div>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xl lg:text-3xl font-black text-white tracking-tight uppercase leading-tight">
                            Selamat Berpuasa, </br><span class="text-gold-400">{{ Auth::user()->nama_lengkap }}</span>
                        </h1>
                        <p class="text-slate-300 text-[9px] font-bold uppercase tracking-[0.2em] mt-1 italic">
                            "Dedikasi Anda adalah kunci kesuksesan tim kami."
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
            <p class="text-[10px] font-black text-slate-400 tracking-widest uppercase">
                &copy; 2026 MyBolo <span class="text-emerald-600">Staff Station</span>
            </p>
            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest flex items-center gap-2">
                Kinerja Barokah <div class="w-1.5 h-1.5 rounded-full bg-gold-500 animate-ping"></div>
            </div>
        </div>
    </footer>

    <script>
        // Digital Clock
        function updateTime() {
            const now = new Date();
            if (document.getElementById('digital-clock')) {
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
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Stars Effect
        const canvas = document.getElementById('star-canvas');
        const ctx = canvas.getContext('2d');
        let stars = [];

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        class Star {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.size = Math.random() * 2.5;
                this.speedX = (Math.random() - 0.5) * 1;
                this.speedY = (Math.random() - 0.5) * 1;
                this.alpha = 1;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                this.alpha -= 0.015;
            }
            draw() {
                ctx.fillStyle = `rgba(251, 191, 36, ${this.alpha})`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        window.addEventListener('mousemove', (e) => {
            for (let i = 0; i < 2; i++) stars.push(new Star(e.clientX, e.clientY));
        });

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            stars.forEach((s, i) => {
                if (s.alpha <= 0) stars.splice(i, 1);
                else {
                    s.update();
                    s.draw();
                }
            });
            requestAnimationFrame(animate);
        }
        animate();

        // Prayer Logic
        async function updatePrayerSchedule() {
            const prayerElement = document.getElementById('next-prayer');
            try {
                const response = await fetch(
                    `https://api.aladhan.com/v1/timingsByCity?city=Bandar Lampung&country=Indonesia&method=11`);
                const data = await response.json();
                const timings = data.data.timings;
                const schedule = [{
                        name: "Imsak",
                        time: timings.Imsak
                    },
                    {
                        name: "Subuh",
                        time: timings.Fajr
                    },
                    {
                        name: "Dzuhur",
                        time: timings.Dhuhr
                    },
                    {
                        name: "Ashar",
                        time: timings.Asr
                    },
                    {
                        name: "Maghrib",
                        time: timings.Maghrib
                    },
                    {
                        name: "Isya",
                        time: timings.Isha
                    }
                ];
                const now = new Date();
                const currentTime = now.getHours() * 60 + now.getMinutes();
                let nextPrayer = schedule[0];
                let found = false;
                for (let item of schedule) {
                    const [h, m] = item.time.split(':').map(Number);
                    if ((h * 60 + m) > currentTime) {
                        nextPrayer = item;
                        found = true;
                        break;
                    }
                }
                prayerElement.innerHTML =
                    `<span class="text-gold-400">${found ? nextPrayer.name : 'Imsak Besok'} ${nextPrayer.time}</span>`;
            } catch (e) {
                prayerElement.innerText = "Jadwal Offline";
            }
        }
        updatePrayerSchedule();

        // ============================================
        // Logic JS untuk modul Pembaruan Sistem
        // ============================================
        // ============================================
        // Logic JS untuk modul Pembaruan Sistem
        // ============================================
        const SYSTEM_VERSION = "1.3.0";

        function checkSystemUpdate() {
            const lastSeenVersion = localStorage.getItem('last_seen_version');

            // Jika user belum pernah buka atau versinya lama
            if (lastSeenVersion !== SYSTEM_VERSION) {
                // Mencari SEMUA elemen yang ID-nya dimulai dengan 'update-badge' atau 'update-text'
                document.querySelectorAll('[id^="update-badge"], [id^="update-text"]').forEach(el => {
                    el.classList.remove('hidden');
                });
            }
        }

        function markUpdateRead() {
            // Simpan versi saat ini ke storage
            localStorage.setItem('last_seen_version', SYSTEM_VERSION);

            // Sembunyikan semua badge di halaman secara instan
            document.querySelectorAll('[id^="update-badge"], [id^="update-text"]').forEach(el => {
                el.classList.add('hidden');
            });
        }

        // Jalankan saat dokumen siap
        document.addEventListener('DOMContentLoaded', checkSystemUpdate);
    </script>
</body>

</html>
