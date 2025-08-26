@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Cola de Publicaciones') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Lista de Publicaciones Pendientes:</h3>

                @if($queue->isEmpty())
                    <p class="text-gray-800 dark:text-gray-200">No hay publicaciones en la cola.</p>
                @else
                    <table class="w-full bg-gray-100 dark:bg-gray-700 rounded-md">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Título</th>
                                <th class="px-4 py-2">Contenido</th>
                                <th class="px-4 py-2">Fecha Programada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queue as $publication)
                                <tr>
                                    <td class="border px-4 py-2">{{ $publication->title }}</td>
                                    <td class="border px-4 py-2">{{ $publication->content }}</td>
                                    <td class="border px-4 py-2">{{ $publication->scheduled_time ? $publication->scheduled_time->format('d/m/Y H:i') : 'Sin programar' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection