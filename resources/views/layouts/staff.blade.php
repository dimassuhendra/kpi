<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - KPI System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredokaf&family=Sniglet:wght@400;800&display=swap" rel="stylesheet">
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
                        body: ['"Sniglet"']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Sniglet', sans-serif;
        }

        .font-header {
            font-family: 'Bree Serif', serif;
        }
    </style>
</head>

<body class="bg-background flex">

    <aside class="w-64 bg-primary min-h-screen text-white flex flex-col shadow-xl">
        <div class="p-6 text-center border-b border-white/10">
            <h2 class="font-header text-2xl">KPI STAFF</h2>
        </div>

        <nav class="flex-grow p-4 space-y-2 mt-4">
            <a href="{{ route('staff.dashboard') }}" class="flex items-center p-3 rounded-xl {{ request()->is('dashboard/staff') ? 'bg-secondary' : 'hover:bg-white/10' }}">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>

            <div class="pt-4 pb-2 px-3 text-xs uppercase opacity-50 font-bold">Manajemen KPI</div>
            <a href="{{ route('staff.kpi.create') }}" class="flex items-center p-3 rounded-xl hover:bg-white/10">
                <i class="fas fa-edit mr-3"></i> Input KPI Harian
            </a>
            <a href="{{ route('staff.kpi.history') }}" class="flex items-center p-3 rounded-xl hover:bg-white/10">
                <i class="fas fa-history mr-3"></i> Riwayat Laporan
            </a>

            <div class="pt-4 pb-2 px-3 text-xs uppercase opacity-50 font-bold">Analitik</div>
            <a href="{{ route('staff.performance') }}" class="flex items-center p-3 rounded-xl hover:bg-white/10">
                <i class="fas fa-chart-line mr-3"></i> Performa Saya
            </a>
        </nav>

        <div class="p-4 border-t border-white/10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center p-3 rounded-xl hover:bg-red-500 transition">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-grow flex flex-col">
        <header class="bg-white shadow-sm p-4 flex justify-between items-center px-8">
            <div class="flex items-center">
                <button class="lg:hidden mr-4 text-primary"><i class="fas fa-bars"></i></button>
                <h1 class="font-header text-xl text-primary">Divisi: {{ Auth::user()->division->name }}</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-secondary font-bold">{{ Auth::user()->name }}</span>
                <div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <main class="p-8">
            @yield('content')
        </main>

        <footer class="mt-auto p-6 text-center text-gray-500 text-sm">
            &copy; 2024 KPI Monitoring System - IT Department.
        </footer>
    </div>
</body>

</html>