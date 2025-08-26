@extends('layouts.app')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Publicación de Entradas') }}
  </h2>
@endsection

@section('content')
<div class="py-4">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

      {{-- Flash & errores --}}
      @if (session('ok'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('ok') }}</div>
      @endif
      @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      {{-- Conectar X --}}
      <div class="mb-6 flex items-center gap-3">
        @php
          $hasTwitter = \App\Models\SocialAccount::where('user_id', auth()->id())->where('provider','twitter')->exists();
        @endphp
        @if ($hasTwitter)
          <span class="px-3 py-1 rounded bg-green-100 text-green-800">X conectado</span>
          <form method="POST" action="{{ route('twitter.disconnect') }}">
            @csrf @method('DELETE')
            <button class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">Desconectar X</button>
          </form>
        @else
          <a href="{{ route('twitter.redirect') }}" class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
            Conectar cuenta de X
          </a>
        @endif
      </div>

      {{-- Formulario nueva publicación --}}
      <form method="POST" action="{{ route('publications.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
          <label class="block text-sm font-medium">Título</label>
          <input name="title" type="text" class="w-full mt-1 border rounded p-2" value="{{ old('title') }}">
        </div>

        <div>
          <label class="block text-sm font-medium">Contenido</label>
          <textarea name="content" rows="3" class="w-full mt-1 border rounded p-2" required>{{ old('content') }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-medium">Imagen (opcional)</label>
          <input type="file" name="media" accept="image/*" class="mt-1">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Publicar en</label>
          <label class="mr-4">
            <input type="checkbox" name="providers[]" value="twitter" {{ old('providers') && in_array('twitter', old('providers', [])) ? 'checked' : '' }}>
            X (Twitter)
          </label>
          {{-- Aquí podrás añadir Facebook/Instagram luego --}}
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Modo</label>
          <label class="mr-4">
            <input type="radio" name="mode" value="instant" {{ old('mode','instant')==='instant'?'checked':'' }}> Instantánea
          </label>
          <label class="mr-4">
            <input type="radio" name="mode" value="queued" {{ old('mode')==='queued'?'checked':'' }}> A la cola
          </label>
          <label class="mr-2">
            <input type="radio" name="mode" value="scheduled" {{ old('mode')==='scheduled'?'checked':'' }}> Programada
          </label>
          <input type="datetime-local" name="scheduled_at" class="border rounded p-1"
                 value="{{ old('scheduled_at') }}">
        </div>

        <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
          Guardar publicación
        </button>
      </form>

      <hr class="my-6">

      {{-- Listado --}}
      <h3 class="text-lg font-semibold dark:text-gray-200 mb-4">Lista de publicaciones</h3>

      @if($publications->isEmpty())
        <p class="text-gray-700 dark:text-gray-200">No hay publicaciones registradas.</p>
      @else
        <table class="w-full bg-gray-100 dark:bg-gray-700 rounded-md">
          <thead>
            <tr>
              <th class="px-4 py-2 text-left">Título</th>
              <th class="px-4 py-2 text-left">Contenido</th>
              <th class="px-4 py-2 text-left">Modo / Estado</th>
              <th class="px-4 py-2 text-left">Fecha</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($publications as $publication)
              <tr class="border-t">
                <td class="px-4 py-2">{{ $publication->title }}</td>
                <td class="px-4 py-2">{{ Str::limit($publication->content, 120) }}</td>
                <td class="px-4 py-2">
                  {{ $publication->mode }} / {{ $publication->status }}
                </td>
                <td class="px-4 py-2">
                  {{ optional($publication->created_at)->format('d/m/Y H:i') }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif

    </div>
  </div>
</div>
@endsection
