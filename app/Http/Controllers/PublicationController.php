<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PostTarget;
use App\Jobs\PublishPostJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PublicationController extends Controller
{
    public function create()
    {
        return view('publications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => ['nullable','string','max:255'],
            'content'       => ['nullable','string','max:1000'],
            'image'         => ['nullable','image','max:3072'],
            'providers'     => ['required','array','min:1'],
            'providers.*'   => [Rule::in(['twitter','linkedin'])],
            'mode'          => [Rule::in(['instant','queue','scheduled'])],
            'scheduled_at'  => ['nullable','date'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('publications', 'public');
        }

        // Si es "scheduled", convertimos a UTC
        $scheduledAt = null;
        if (($data['mode'] ?? 'instant') === 'scheduled' && !empty($data['scheduled_at'])) {
            $scheduledAt = \Carbon\Carbon::parse($data['scheduled_at'], auth()->user()->timezone ?? config('app.timezone'))
                ->timezone('UTC');
        }

        // Si es "queue", calculamos un hueco FIFO simple (opcional)
        if (($data['mode'] ?? 'instant') === 'queue') {
            $gap = (int) (config('social.queue_gap_minutes', 3)); // crea config/social.php si gustas
            $last = Publication::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->whereNotNull('scheduled_at')
                ->orderByDesc('scheduled_at')
                ->first();

            $base = $last?->scheduled_at ?? now();
            $scheduledAt = $base->copy()->addMinutes($gap);
            $data['mode'] = 'scheduled'; // la cola la tratamos como scheduled
        }

        $pub = Publication::create([
            'user_id'      => auth()->id(),
            'title'        => $data['title'] ?? null,
            'content'      => $data['content'] ?? null,
            'image_path'   => $imagePath,
            'mode'         => $data['mode'] ?? 'instant',
            'scheduled_at' => $scheduledAt,
            'status'       => 'pending',
        ]);

        foreach ($data['providers'] as $prov) {
            PostTarget::create([
                'publication_id' => $pub->id,
                'provider'       => $prov,
                'status'         => 'pending',
            ]);
        }

        // Disparar ahora o programar
        if ($pub->mode === 'instant') {
            dispatch(new PublishPostJob($pub->id)); // sale por queue:database
        }

        return redirect()->route('publications.index')
            ->with('ok', 'Publicación registrada.');
    }

    public function index()
    {
        $rows = Publication::with('targets')
            ->where('user_id', auth()->id())
            ->latest('id')
            ->paginate(20);

        return view('publications.index', compact('rows'));
    }
}