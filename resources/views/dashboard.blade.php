<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("¡Has iniciado sesión!") }}
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('Conectar con Redes Sociales') }}</h3>
                    
                    <div class="flex flex-col space-y-4">
                        <a href="{{ url('auth/linkedin') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Conectar con LinkedIn
                        </a>

                        <a href="{{ url('/twitter/connect') }}" class="bg-blue-400 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                            Conectar con Twitter
                        </a>

                        <a href="{{ url('/facebook/connect') }}" class="bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 px-4 rounded">
                            Conectar con Facebook
                        </a>

                        <a href="{{ url('/instagram/connect') }}" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded">
                            Conectar con Instagram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
