<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI System - Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&family=Sniglet:wght@400;800&display=swap" rel="stylesheet">

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
                        header: ['"Bree Serif"', 'serif'],
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
            font-family: 'Bree Serif', serif;
        }
    </style>
</head>

<body>
    @yield('content')
</body>

</html>