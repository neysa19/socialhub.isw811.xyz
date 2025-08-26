<?php

namespace App\Services\Publish;

use App\Models\Publication;
use App\Models\PostTarget;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TwitterV2Publisher
{
    protected function tokenForUser(int $userId): ?array
    {
        $sa = SocialAccount::where('user_id', $userId)->where('provider', 'twitter')->first();
        if (!$sa) return null;

        // Si está vencido y tengo refresh, renuevo
        if ($sa->token_expires_at && Carbon::parse($sa->token_expires_at)->isPast() && $sa->refresh_token) {
            $res = Http::asForm()->post('https://api.twitter.com/2/oauth2/token', [
                'client_id'     => env('TWITTER_CLIENT_ID'),
                'client_secret' => env('TWITTER_CLIENT_SECRET'),
                'grant_type'    => 'refresh_token',
                'refresh_token' => $sa->refresh_token,
            ]);
            if ($res->successful()) {
                $data = $res->json();
                $sa->access_token = json_encode([
                    'access_token'  => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? $sa->refresh_token,
                    'scope'         => $data['scope'] ?? null,
                ]);
                $sa->refresh_token    = $data['refresh_token'] ?? $sa->refresh_token;
                $sa->token_expires_at = Carbon::now()->addSeconds((int)($data['expires_in'] ?? 7200));
                $sa->save();
            }
        }

        $tok = json_decode($sa->access_token, true);
        return $tok ?: null;
    }

    public function publish(Publication $post, PostTarget $target): array
    {
        $tok = $this->tokenForUser($post->user_id);
        if (!$tok || empty($tok['access_token'])) {
            return ['ok' => false, 'error' => 'Cuenta de X no conectada o token inválido.'];
        }

        // Solo texto por ahora (imágenes las añadimos luego)
        $resp = Http::withToken($tok['access_token'])
            ->post('https://api.twitter.com/2/tweets', ['text' => $post->content]);

        if ($resp->successful()) {
            $id = $resp->json('data.id');
            return ['ok' => true, 'provider_post_id' => $id];
        }
        return ['ok' => false, 'error' => $resp->body()];
    }
}
