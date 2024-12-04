<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Publicación de Entradas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Selección de redes sociales -->
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Seleccione las redes sociales para publicar:</h3>
                <div class="flex gap-4 mb-6">
                    <label><input type="checkbox" name="networks[]" value="facebook" class="mr-2">Facebook</label>
                    <label><input type="checkbox" name="networks[]" value="twitter" class="mr-2">Twitter</label>
                    <label><input type="checkbox" name="networks[]" value="instagram" class="mr-2">Instagram</label>
                    <label><input type="checkbox" name="networks[]" value="linkedin" class="mr-2">LinkedIn</label>
                </div>

                <!-- Formulario de publicación -->
                <form method="POST" action="{{ route('publications.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="content" class="block text-gray-800 dark:text-gray-200">Contenido de la Publicación:</label>
                        <textarea id="content" name="content" rows="4" class="w-full mt-2 p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" placeholder="Escribe aquí tu publicación..."></textarea>
                    </div>

                    <!-- Opciones de publicación -->
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Opciones de Publicación:</h3>
                    <div class="flex flex-col gap-4 mb-6">
                        <label><input type="radio" name="publication_type" value="instant" class="mr-2">Publicar ahora</label>
                        <label><input type="radio" name="publication_type" value="queue" class="mr-2">Añadir a la cola</label>
                        <label>
                            <input type="radio" name="publication_type" value="scheduled" class="mr-2">Programar publicación
                            <input type="datetime-local" name="scheduled_time" class="ml-4 p-1 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        </label>
                    </div>

                    <!-- Botón de publicar -->
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Publicar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
