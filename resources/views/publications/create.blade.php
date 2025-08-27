<x-app-layout>
    <div class="max-w-5xl mx-auto py-6">
        <h2 class="text-xl font-semibold mb-4">Publicación de entradas</h2>

        @if ($errors->any())
            <div class="bg-red-600 text-white p-3 mb-4 rounded">
                <ul class="list-disc ml-6">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('publications.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Título</label>
                <input name="title" class="w-full rounded" value="{{ old('title') }}">
            </div>

            <div>
                <label class="block text-sm font-medium">Contenido</label>
                <textarea name="content" rows="4" class="w-full rounded">{{ old('content') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium">Imagen (opcional)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="pt-2">
                <label class="block text-sm font-medium mb-1">Publicar en</label>
                <label class="mr-4">
                    <input type="checkbox" name="providers[]" value="twitter" {{ old('providers') && in_array('twitter', old('providers', [])) ? 'checked' : '' }}>
                    X (Twitter)
                </label>
                <label class="mr-4">
                    <input type="checkbox" name="providers[]" value="linkedin" {{ old('providers') && in_array('linkedin', old('providers', [])) ? 'checked' : '' }}>
                    LinkedIn
                </label>
            </div>

            <div class="pt-2">
                <label class="block text-sm font-medium mb-1">Modo</label>

                <label class="mr-4">
                    <input type="radio" name="mode" value="instant" {{ old('mode','instant')==='instant' ? 'checked' : '' }}>
                    Instantánea
                </label>

                <label class="mr-4">
                    <input type="radio" name="mode" value="queue" {{ old('mode')==='queue' ? 'checked' : '' }}>
                    A la cola
                </label>

                <label class="mr-2">
                    <input type="radio" name="mode" value="scheduled" {{ old('mode')==='scheduled' ? 'checked' : '' }}>
                    Programada
                </label>

                <input type="datetime-local" name="scheduled_at"
                       value="{{ old('scheduled_at') }}"
                       class="rounded ml-2">
                <span class="text-xs text-gray-400">(tu zona horaria)</span>
            </div>

            <div class="pt-4">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar publicación</button>
            </div>
        </form>
    </div>
</x-app-layout>