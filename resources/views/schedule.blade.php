<!-- Inserta los archivos CSS y JS compilados -->
@vite(['resources/css/app.css', 'resources/js/app.js'])

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Horarios de Publicación') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Crear nuevo horario -->
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Crear Nuevo Horario:</h3>
                <form method="POST" action="{{ route('schedules.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="day" class="block text-gray-800 dark:text-gray-200">Día de la Semana:</label>
                        <select id="day" name="day" class="w-full mt-2 p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            <option value="L">Lunes</option>
                            <option value="K">Martes</option>
                            <option value="M">Miércoles</option>
                            <option value="J">Jueves</option>
                            <option value="V">Viernes</option>
                            <option value="S">Sábado</option>
                            <option value="D">Domingo</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="time" class="block text-gray-800 dark:text-gray-200">Hora del Día:</label>
                        <input type="time" id="time" name="time" class="w-full mt-2 p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>

                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Añadir Horario
                    </button>
                </form>

                <!-- Lista de horarios existentes -->
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4">Horarios Existentes:</h3>
                <table class="w-full bg-gray-100 dark:bg-gray-700 rounded-md">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Día</th>
                            <th class="px-4 py-2">Horas</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se iterarían los horarios desde la base de datos -->
                        <tr>
                            <td class="border px-4 py-2">Lunes</td>
                            <td class="border px-4 py-2">08:00, 18:00</td>
                            <td class="border px-4 py-2">
                                <button class="bg-red-
