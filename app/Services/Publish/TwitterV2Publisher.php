<?php

namespace App\Services\Publish;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TwitterV2Publisher
{
    public function publish(?string $text, ?string $imagePath): ?string
    {
        // obtener token del usuario:
        $account = SocialAccount::where('user_id', Auth::id())
            ->where('provider','twitter')
            ->first();

        if (!$account) {
            throw new \RuntimeException('No hay cuenta de X conectada');
        }

        $token = $account->access_token; // si lo guardaste como string
        // si lo guardaste JSON: $token = json_decode($account->access_token, true)['access_token'];

        // 1) (opcional) subir media a X si tienes endpoint y permisos (varía por nivel del API)
        // 2) postear tweet
        $res = Http::withToken($token)
            ->post('https://api.twitter.com/2/tweets', [
                'text' => (string) $text,
            ]);

        if (!$res->successful()) {
            throw new \RuntimeException('X publish failed: '.$res->body());
        }

        return $res->json('data.id');
    }
}
