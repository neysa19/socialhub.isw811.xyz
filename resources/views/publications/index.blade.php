@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">

    @if(session('ok'))
        <div class="bg-green-600 text-white px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-600 text-white px-4 py-2 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-gray-800 text-gray-100 p-4 rounded mb-6">
        <h2 class="text-xl font-semibold mb-4">Nueva publicación</h2>

        <form action="{{ route('publications.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-4">
            @csrf

            <div>
                <label class="block text-sm mb-1">Título</label>
                <input name="title" class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm mb-1">Contenido</label>
                <textarea name="content" rows="4" class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2"></textarea>
            </div>

            <div>
                <label class="block text-sm mb-1">Imagen (opcional)</label>
                <input type="file" name="image" class="text-sm">
            </div>

            <div>
                <label class="block text-sm mb-1">Publicar en</label>
                <div class="flex gap-6">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="targets[]" value="twitter" {{ $hasTwitter? '' : 'disabled' }}>
                        <span>X (Twitter) {{ $hasTwitter? '' : '(no conectada)' }}</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="targets[]" value="linkedin" {{ $hasLinkedIn? '' : 'disabled' }}>
                        <span>LinkedIn {{ $hasLinkedIn? '' : '(no conectada)' }}</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm mb-1">Modo</label>
                <div class="flex items-center gap-5">
                    <label class="inline-flex items-center gap-2"><input type="radio" name="mode" value="instant" checked> Instantánea</label>
                    <label class="inline-flex items-center gap-2"><input type="radio" name="mode" value="queue"> A la cola</label>
                    <label class="inline-flex items-center gap-2"><input id="mode_sched" type="radio" name="mode" value="scheduled"> Programada</label>
                    <input id="when" type="datetime-local" name="scheduled_at" class="bg-gray-900 border border-gray-700 rounded px-2 py-1 ml-3" disabled>
                </div>
            </div>

            <div>
                <button class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">Guardar</button>
            </div>
        </form>
    </div>

    <div class="bg-gray-800 text-gray-100 p-4 rounded">
        <h2 class="text-xl font-semibold mb-4">Lista de publicaciones</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-700">
                    <th class="py-2">Título</th>
                    <th>Modo</th>
                    <th>Programada</th>
                    <th>Estado</th>
                    <th>Targets</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            @foreach($items as $p)
                <tr class="border-b border-gray-800">
                    <td class="py-2">{{ $p->title }}</td>
                    <td>{{ $p->mode }}</td>
                    <td>{{ $p->scheduled_at? $p->scheduled_at->timezone(config('app.timezone'))->format('Y-m-d H:i') : '—' }}</td>
                    <td>
                        <span class="px-2 py-1 rounded bg-gray-700">{{ $p->status }}</span>
                        @if($p->error)
                            <div class="text-red-400">{{ Str::limit($p->error, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        @foreach($p->targets as $t)
                            <div>{{ $t->provider }} — {{ $t->status }}</div>
                        @endforeach
                    </td>
                    <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</div>

<script>
const modeSched = document.getElementById('mode_sched');
const when      = document.getElementById('when');
document.addEventListener('change', (e) => {
  if (e.target.name === 'mode') {
    when.disabled = e.target.value !== 'scheduled';
  }
});
</script>
@endsection
