<?php

// app/Jobs/PublishPostJob.php
namespace App\Jobs;

use App\Models\Publication;
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
        Log::info('JOB start', [
            'pub_id'   => $post->id,
            'user_id'  => $post->user_id,
            'targets'  => $post->targets->pluck('provider', 'id')->all(),
            'status'   => $post->status,
            'when'     => now('UTC')->toDateTimeString(),
        ]);
        try {
            foreach ($post->targets as $target) {
                if ($target->status !== 'pending') continue;

                try {
                    switch ($target->provider) {
                        case 'twitter':
                            $tweetId = $twitter->publishForUser(
                                $post->user_id,
                                (string) $post->content,
                                $post->image_path
                            );

                            $target->update([
                                'provider_post_id' => $tweetId,
                                'status'           => 'published',
                                'error'            => null,
                            ]);
                            break;

                            // case 'linkedin': ...
                    }
                } catch (\Throwable $te) {
                    // marca SOLO el target fallido
                    $target->update([
                        'status' => 'failed',
                        'error'  => $te->getMessage(),
                    ]);
                }
            }

            // estado del post según si todos los targets se publicaron
            $allOk = $post->targets()->where('status', '!=', 'published')->count() === 0;
            $post->update([
                'status' => $allOk ? 'published' : 'failed',
                'error'  => $allOk ? null : 'Hay targets con error',
            ]);
        } catch (\Throwable $e) {
            Log::error('Publish error', ['id' => $post->id, 'e' => $e->getMessage()]);
            $post->update(['status' => 'failed', 'error' => $e->getMessage()]);
        }
    }
}
