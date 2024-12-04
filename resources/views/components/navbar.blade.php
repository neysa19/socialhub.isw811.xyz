<nav class="bg-gray-800 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-white text-2xl font-bold">SocialHub</a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('publications.index') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        Publicación de Entradas
                    </a>
                    <a href="{{ route('schedules.index') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        Horarios de Publicación
                    </a>
                    <a href="{{ route('queue.index') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        Cola de Publicaciones
                    </a>
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center sm:ml-6">
                <a href="{{ route('profile.show') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                    Mi Perfil
                </a>
                <form method="POST" action="{{ route('logout') }}" class="ml-3">
                    @csrf
                    <button type="submit" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
