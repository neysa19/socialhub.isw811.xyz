<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'SocialHub') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Parche rápido para inputs/tabla en dark mode --}}
  <style>
    .dark input[type="text"],
    .dark input[type="file"],
    .dark input[type="datetime-local"],
   .dark textarea {
    color: #000000;            /* texto */
    background-color: #111827; /* fondo */
    border: 1px solid #374151; /* borde */
  }
  .dark textarea::placeholder {
    color: #9ca3af;            /* placeholder */
  }

    .dark table { color:#e5e7eb; }
    .dark thead th {
      background:#1f2937;         /* bg-gray-800 */
      color:#d1d5db;              /* text-gray-300 */
    }
  </style>
</head>

<body class="font-sans antialiased text-gray-900 dark:text-gray-100">
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

    <!-- Navbar -->
    <x-navbar />

    <!-- Page Heading -->
    @if (isset($header))
      <header class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8
                    text-gray-900 dark:text-gray-100">
          {{ $header }}
        </div>
      </header>
    @endif

    @yield('header')

    <!-- Page Content -->
    <main class="text-gray-900 dark:text-gray-100">
      @isset($slot)
        {{ $slot }}
      @else
        @yield('content')
      @endisset
    </main>
  </div>
</body>
</html>
