<?php

namespace App\Jobs;

use App\Models\Publication;
use App\Models\PostTarget;
use App\Services\Publish\TwitterV2Publisher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $publicationId) {}

    public function handle(TwitterV2Publisher $twitter): void
    {
        $pub = Publication::with('targets')->findOrFail($this->publicationId);

        // Si es scheduled y aún no toca, reprogramar
        if ($pub->scheduled_at && now('UTC')->lt($pub->scheduled_at)) {
            $this->release($pub->scheduled_at->diffInSeconds(now('UTC')) + 5);
            return;
        }

        $pub->update(['status' => 'running']);

        foreach ($pub->targets as $t) {
            if ($t->status !== 'pending') continue;

            try {
                $t->update(['status' => 'running']);

                $text = trim(($pub->title ? $pub->title."\n" : '').(string)$pub->content);

                if ($t->provider === 'twitter') {
                    $res = $twitter->tweet($text);
                    $t->update([
                        'provider_post_id' => $res['data']['id'] ?? null,
                        'status'           => 'done',
                    ]);
                } elseif ($t->provider === 'linkedin') {
                    // TODO: implementar publicador LinkedIn
                    // $linkedin->post($text, $pub->image_path);
                    $t->update(['status' => 'done']);
                }
            } catch (\Throwable $e) {
                Log::error('Publish failure', ['pub'=>$pub->id,'prov'=>$t->provider,'e'=>$e->getMessage()]);
                $t->update(['status' => 'failed','error_message'=>$e->getMessage()]);
            }
        }

        // Si todas OK => done; si alguna falló => failed parcial
        $fresh = $pub->refresh();
        if ($fresh->targets()->where('status','failed')->exists()) {
            $fresh->update(['status' => 'failed']);
        } elseif ($fresh->targets()->whereIn('status', ['pending','running'])->exists()) {
            // aun quedan; liberar de nuevo
            $this->release(15);
        } else {
            $fresh->update(['status' => 'done']);
        }
    }
}
