<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'SocialHub') }}</title>

    <!-- Incluye el archivo CSS generado por Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

        <!-- Navbar -->
        <x-navbar></x-navbar>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif
  @yield('header')
        <!-- Page Content -->
       <main>
    @isset($slot)
        {{-- Estilo componente (Jetstream) --}}
        {{ $slot }}
    @else
        {{-- Estilo secciones (Breeze / @extends) --}}
        @yield('content')
    @endisset
</main>
    </div>
</body>
</html>
