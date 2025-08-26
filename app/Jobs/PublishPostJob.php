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

    public int $postId;

    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    public function handle(TwitterV2Publisher $twitter): void
    {
        $post   = Publication::findOrFail($this->postId);
        $target = PostTarget::where('publication_id', $post->id)
                    ->where('provider','twitter')->firstOrFail();

        try {
            $tweetId = $twitter->publish($post, $target);

            $target->status = 'posted';
            $target->provider_post_id = $tweetId;
            $target->error = null;
            $target->save();

            $post->status = 'posted';
            $post->published_at = now();
            $post->save();

            Log::info('Tweet published', ['publication_id' => $post->id, 'tweet_id' => $tweetId]);
        } catch (\Throwable $e) {
            $target->status = 'failed';
            $target->error  = substr($e->getMessage(), 0, 2000);
            $target->save();

            Log::error('Tweet publish failed', ['publication_id' => $post->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
