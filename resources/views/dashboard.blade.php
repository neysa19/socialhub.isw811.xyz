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

    @php
  $hasTw = \App\Models\SocialAccount::where('user_id', auth()->id())
            ->where('provider','twitter')->exists();
  $hasLi = \App\Models\SocialAccount::where('user_id', auth()->id())
            ->where('provider','linkedin')->exists();
@endphp


     <div class="p-6 flex gap-3">
  {{-- X (Twitter) --}}
  @if ($hasTw)
    <form method="POST" action="{{ route('twitter.disconnect') }}">
      @csrf @method('DELETE')
      <button class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">Desconectar X</button>
    </form>
  @else
    <a href="{{ route('twitter.redirect') }}" class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
      Conectar con X
    </a>
  @endif
  @if (session('status'))
  <div class="bg-green-600 text-white px-3 py-2 rounded mb-3">{{ session('status') }}</div>
@endif

@if (session('error'))
  <div class="bg-red-600 text-white px-3 py-2 rounded mb-3">{{ session('error') }}</div>
@endif


  {{-- LinkedIn --}}
  @if ($hasLi)
    <form method="POST" action="{{ route('linkedin.disconnect') }}">
      @csrf @method('DELETE')
      <button class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">Desconectar LinkedIn</button>
    </form>
  @else
    <a href="{{ route('linkedin.redirect') }}" class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
      Conectar con LinkedIn
    </a>
  @endif
</div>

      </div>
    </div>
  </div>
</x-app-layout>
