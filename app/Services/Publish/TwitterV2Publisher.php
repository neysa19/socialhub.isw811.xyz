<?php
namespace App\Services\Publish;

use App\Models\Publication;
use App\Models\PostTarget;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwitterV2Publisher
{
    public function publish(Publication $post, PostTarget $target): string
    {
        $acct = SocialAccount::where('user_id', $post->user_id)
                 ->where('provider','twitter')->firstOrFail();

        // OJO: si guardaste un JSON en access_token, toma el valor real
        $token = $acct->access_token;
        $decoded = json_decode($token, true);
        if (is_array($decoded) && isset($decoded['access_token'])) {
            $token = $decoded['access_token']; // usar el token real
        }

        $payload = ['text' => trim($post->content ?: $post->title)];

        $res = Http::withToken($token)
            ->post('https://api.twitter.com/2/tweets', $payload);

        Log::info('X publish status', [
            'status' => $res->status(),
            'body'   => $res->body(),
            'pub_id' => $post->id,
        ]);

        if (!$res->successful()) {
            throw new \RuntimeException('X API error '.$res->status().': '.$res->body());
        }

        $tweetId = data_get($res->json(), 'data.id');
        if (!$tweetId) {
            throw new \RuntimeException('X API: missing tweet id: '.$res->body());
        }

        return $tweetId;
    }
}
