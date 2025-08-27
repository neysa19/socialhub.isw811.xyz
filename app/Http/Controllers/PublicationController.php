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
        $hasTwitter  = \App\Models\SocialAccount::where('user_id', Auth::id())->where('provider','twitter')->exists();
        $hasLinkedIn = \App\Models\SocialAccount::where('user_id', Auth::id())->where('provider','linkedin')->exists();

        return view('publications.index', compact('items','hasTwitter','hasLinkedIn'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => ['required','string','max:255'],
            'content' => ['nullable','string','max:1000'],
            'image'   => ['nullable','image','max:2048'],
            'mode'    => ['required', Rule::in(['instant','queue','scheduled'])],
            'scheduled_at' => ['nullable','date_format:Y-m-d\TH:i'],
            'targets' => ['required','array','min:1'],  // ['twitter','linkedin']
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // calcular hora para "queue": el siguiente slot disponible
        $scheduledAt = null;
        if ($validated['mode'] === 'scheduled') {
            // guardamos en UTC (front manda local, ajusta si quieres)
            $scheduledAt = Carbon::parse($validated['scheduled_at'], config('app.timezone'))
                ->clone()->timezone('UTC');
        } elseif ($validated['mode'] === 'queue') {
            $last = Publication::where('user_id', Auth::id())
                ->where('status','pending')
                ->whereNotNull('scheduled_at')
                ->orderByDesc('scheduled_at')
                ->first();

            $next = $last? $last->scheduled_at->clone()->addMinute() : now('UTC')->addMinute();
            $scheduledAt = $next;
        }

        $pub = Publication::create([
            'user_id'      => Auth::id(),
            'title'        => $validated['title'],
            'content'      => $validated['content'] ?? null,
            'image_path'   => $imagePath,
            'mode'         => $validated['mode'],
            'scheduled_at' => $scheduledAt,
            'status'       => $validated['mode']==='instant' ? 'queued' : 'pending',
        ]);

        foreach ($validated['targets'] as $prov) {
            PostTarget::create([
                'publication_id' => $pub->id,
                'provider'       => $prov,
            ]);
        }

        if ($validated['mode'] === 'instant') {
            // publica ya mismo
            dispatch(new PublishPostJob($pub->id));
        }

        return back()->with('ok','Publicación guardada.');
    }
}
