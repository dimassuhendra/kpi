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
        }

        .font-header {
            font-family: 'Fredoka', sans-serif;
        }

        /* Transisi halus untuk menu mobile */
        #mobile-menu {
            transition: max-height 0.3s ease-in-out;
            overflow: hidden;
        }

        .menu-closed {
            max-height: 0;
        }

        .menu-open {
            max-height: 500px;
        }
    </style>
</head>

<body class="bg-background min-h-screen flex flex-col">

    <nav class="bg-primary text-white shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <h2 class="font-header text-xl font-bold tracking-wide">KPI STAFF</h2>
                    </div>
                    <div class="hidden md:ml-8 md:flex md:space-x-4">
                        <a href="{{ route('staff.dashboard') }}" class="px-3 py-2 rounded-lg text-sm {{ request()->is('dashboard/staff') ? 'bg-secondary' : 'hover:bg-white/10' }}">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('staff.kpi.create') }}" class="px-3 py-2 rounded-lg text-sm hover:bg-white/10">
                            <i class="fas fa-edit mr-1"></i> Input KPI
                        </a>
                        <a href="{{ route('staff.kpi.history') }}" class="px-3 py-2 rounded-lg text-sm hover:bg-white/10">
                            <i class="fas fa-history mr-1"></i> Riwayat
                        </a>
                        <a href="{{ route('staff.performance') }}" class="px-3 py-2 rounded-lg text-sm hover:bg-white/10">
                            <i class="fas fa-chart-line mr-1"></i> Performa
                        </a>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <div class="text-right mr-2">
                        <p class="text-xs opacity-75 leading-none">{{ Auth::user()->division->name }}</p>
                        <p class="text-sm font-bold">{{ Auth::user()->name }}</p>
                    </div>
                    <div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center border-2 border-white/20">
                        <i class="fas fa-user"></i>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 hover:bg-red-500 rounded-lg transition" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center md:hidden">
                    <button id="menu-btn" class="p-2 rounded-md hover:bg-secondary focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="md:hidden bg-primary/95 border-t border-white/10 menu-closed">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('staff.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium bg-secondary">Dashboard</a>
                <a href="{{ route('staff.kpi.create') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10">Input KPI Harian</a>
                <a href="{{ route('staff.kpi.history') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10">Riwayat Laporan</a>
                <a href="{{ route('staff.performance') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10">Performa Saya</a>

                <hr class="border-white/10 my-2">

                <div class="flex items-center px-3 py-3">
                    <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold">{{ Auth::user()->name }}</p>
                        <p class="text-xs opacity-70">{{ Auth::user()->division->name }}</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-300 hover:bg-red-500 hover:text-white transition">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="font-header text-2xl text-primary font-bold">
                    Divisi: <span class="text-secondary">{{ Auth::user()->division->name }}</span>
                </h1>
            </div>

            <div class="bg-white rounded-3xl shadow-sm p-6 min-h-[60vh]">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="p-6 text-center text-gray-500 text-sm">
        &copy; 2024 KPI Monitoring System - IT Department.
    </footer>

    <script>
        // Script untuk toggle hamburger menu
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            if (menu.classList.contains('menu-closed')) {
                menu.classList.remove('menu-closed');
                menu.classList.add('menu-open');
                btn.innerHTML = '<i class="fas fa-times text-xl"></i>';
            } else {
                menu.classList.add('menu-closed');
                menu.classList.remove('menu-open');
                btn.innerHTML = '<i class="fas fa-bars text-xl"></i>';
            }
        });
    </script>
</body>

</html>