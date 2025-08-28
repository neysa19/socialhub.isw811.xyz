<?php

namespace App\Http\Controllers;

use App\Jobs\PublishPostJob;
use App\Models\PostTarget;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PublicationController extends Controller
{
    public function index()
    {
        $items = Publication::where('user_id', Auth::id())
            ->latest()->with('targets')->paginate(10);

        // lee si hay conexión activa de twitter/linkedIn (simplificado)
        $hasTwitter  = \App\Models\SocialAccount::where('user_id', Auth::id())->where('provider', 'twitter')->exists();
        $hasLinkedIn = \App\Models\SocialAccount::where('user_id', Auth::id())->where('provider', 'linkedin')->exists();

        return view('publications.index', compact('items', 'hasTwitter', 'hasLinkedIn'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:1000'],
            'image'   => ['nullable', 'image', 'max:2048'],
            'mode'    => ['required', Rule::in(['instant', 'queue', 'scheduled'])],
            'scheduled_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'targets' => ['required', 'array', 'min:1'],  // ['twitter','linkedin']
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // calcular hora para "queue": el siguiente slot disponible
        $runAt = null;

        if ($validated['mode'] === 'scheduled') {
            // El front manda YYYY-MM-DDTHH:mm (hora local). Guárdalo en UTC:
            $runAt = Carbon::createFromFormat('Y-m-d\TH:i', $validated['scheduled_at'], config('app.timezone'))
                ->utc();
        }

        if ($validated['mode'] === 'queue') {
            // toma el último pendiente programado y añade 1 min
            $last = Publication::where('user_id', Auth::id())
                ->whereNotNull('scheduled_at')
                ->whereIn('status', ['pending', 'queued'])
                ->orderByDesc('scheduled_at')
                ->first();

            $runAt = $last
                ? $last->scheduled_at->copy()->addMinute()
                : now()->addMinute()->utc();
        }

        $pub = Publication::create([
            'user_id'      => Auth::id(),
            'title'        => $validated['title'],
            'content'      => $validated['content'] ?? null,
            'image_path'   => $imagePath,
            'mode'         => $validated['mode'],
            'scheduled_at' => $runAt,            // null para instant
            'status'       => 'pending',         // lo actualiza el job
        ]);

        foreach ($validated['targets'] as $prov) {
            PostTarget::create([
                'publication_id' => $pub->id,
                'provider'       => $prov,
            ]);
        }

        // despachar el job según el modo
        if ($validated['mode'] === 'instant') {
            PublishPostJob::dispatch($pub->id);                 // ahora
        } else {
            PublishPostJob::dispatch($pub->id)->delay($runAt);  // programada / cola
        }

        return back()->with('ok', 'Publicación guardada.');
    }
}
