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
        $post = Publication::with('targets')->find($this->publicationId);
        if (!$post) return;

        try {
            foreach ($post->targets as $target) {
                if ($target->status !== 'pending') continue;

                switch ($target->provider) {
                    case 'twitter':
                        $tweetId = $twitter->publish($post->content, $post->image_path);
                        $target->update([
                            'provider_post_id' => $tweetId,
                            'status'           => 'published',
                        ]);
                        break;

                    // case 'linkedin': ... (si luego agregas)
                }
            }

            $allOk = $post->targets()->where('status','!=','published')->count() === 0;
            $post->update([
                'status' => $allOk ? 'published' : 'failed',
                'error'  => $allOk ? null : 'Some targets failed',
            ]);
        } catch (\Throwable $e) {
            Log::error('Publish error', ['id'=>$post->id, 'e'=>$e->getMessage()]);
            $post->update(['status'=>'failed','error'=>$e->getMessage()]);
        }
    }
}
