<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI System - Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&family=Sniglet:wght@400;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0065cf',
                        secondary: '#2f8de4',
                        accent: '#7fb5f0',
                        background: '#EBF4F6',
                    },
                    fontFamily: {
                        header: ['"Fredoka"', 'serif'],
                        body: ['"Sniglet"', 'system-ui'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Sniglet', sans-serif;
            background-color: #EBF4F6;
        }

        h1,
        h2,
        h3,
        h4 {
            /* Diubah dari Bree Serif ke Fredoka agar sesuai dengan font yang diimpor */
            font-family: 'Fredoka', serif;
        }
    </style>
</head>

{{-- Tambahkan class w-full (width 100%), min-h-screen (tinggi minimal selayar), dan overflow-x-hidden untuk mencegah scroll horizontal --}}

<body class="w-full min-h-screen m-0 p-0 overflow-x-hidden antialiased text-slate-800">

    {{-- Bungkus content dengan main tag yang lebarnya 100% --}}
    <main class="w-full h-full block">
        @yield('content')
    </main>

</body>

</html>
