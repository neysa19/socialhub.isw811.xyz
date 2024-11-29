<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Social Hub Manager</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div>
            <header class="flex justify-between p-4 bg-gray-800 text-white">
                <h1>Social Hub Manager</h1>
                <nav>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-4">Iniciar Sesión</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4">Registrarse</a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </header>

            <main class="p-6">
                <h2 class="text-2xl font-bold">Bienvenido a Social Hub Manager</h2>
                <p>Gestiona tus redes sociales desde una sola aplicación.</p>
            </main>

            <footer class="text-center p-4 bg-gray-800 text-white">
                Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
            </footer>
        </div>
    </body>
</html>
