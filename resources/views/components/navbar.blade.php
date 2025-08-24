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
            <div class="flex items-center sm:ml-6 space-x-4">
                <!-- Redes Sociales Dropdown -->
                <div class="relative">
                    <button class="flex items-center text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium focus:outline-none focus:text-white focus:bg-gray-700">
                        <span>Conectar Redes Sociales</span>
                        <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 dark:bg-gray-700 z-20 hidden group-hover:block">
                        <a href="{{ url('auth/linkedin') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                            Conectar con LinkedIn
                        </a>
                        <a href="{{ url('/twitter/connect') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                            Conectar con Twitter
                        </a>
                        <a href="{{ url('/facebook/connect') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                            Conectar con Facebook
                        </a>
                        <a href="{{ url('/instagram/connect') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                            Conectar con Instagram
                        </a>
                    </div>
                </div>

                <!-- Mi Perfil y Logout -->
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

<!-- Script para manejar el menú desplegable -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdownButton = document.getElementById('dropdownButton');
        const dropdownMenu = document.getElementById('dropdownMenu');

        dropdownButton.addEventListener('click', function () {
            dropdownMenu.classList.toggle('hidden');
        });

        // Cierra el menú si se hace clic fuera de él
        window.addEventListener('click', function (e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    });
</script>
