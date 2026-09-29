<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jiwanta')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'jiwanta': '#2d5a4a',
                        'jiwanta-dark': '#234a3d',
                        'light-blue': '#dce8ed',
                        'bg-green': '#c8dcc4',
                    },
                },
            },
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #e5e7eb;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    {{-- Container Mobile Frame --}}
    @yield('content')

    @stack('scripts')

</body>
</html>
