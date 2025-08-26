<?php
namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PostTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\PublishPostJob;

class PostController extends Controller
{
    public function index()
    {
        $publications = Publication::where('user_id', Auth::id())
            ->latest()->with('targets')->get();

        return view('publications.index', compact('publications'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'nullable|string|max:255',
            'content'      => 'required|string',
            'media'        => 'nullable|image|max:4096',
            'mode'         => 'required|in:instant,queued,scheduled',
            'scheduled_at' => 'nullable|date',
            'providers'    => 'required|array|min:1',
            'providers.*'  => 'in:twitter', // añade otros cuando integremos
        ]);

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('posts', 'public'); // storage/app/public/posts
        }

        $post = Publication::create([
            'user_id'      => Auth::id(),
            'title'        => $data['title'] ?? null,
            'content'      => $data['content'],
            'media_path'   => $mediaPath,
            'mode'         => $data['mode'],
            'scheduled_at' => $data['mode']==='scheduled' ? $data['scheduled_at'] : null,
            'status'       => 'queued',
        ]);

        foreach ($data['providers'] as $p) {
            PostTarget::create(['publication_id' => $post->id, 'provider' => $p, 'status' => 'pending']);
        }

        if ($data['mode'] === 'instant') {
            PublishPostJob::dispatch($post->id);
        }

        return redirect()->route('publications.index')->with('ok', 'Publicación registrada.');
    }
}
