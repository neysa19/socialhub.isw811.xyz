<?php

namespace App\Services\Publish;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TwitterV2Publisher
{
    public function publishForUser(int $userId, string $text, ?string $imagePath = null): string
    {
        $account = SocialAccount::where('user_id', $userId)
            ->where('provider', 'twitter')
            ->first();

        if (!$account) {
            throw new \RuntimeException('No hay cuenta de X conectada');
        }

        // TODO: refrescar token si hace falta usando $account->refresh_token

        // Ejemplo muy simple de publicación (ajústalo a tu implementación real)
        $res = Http::withToken($account->access_token)
            ->post('https://api.twitter.com/2/tweets', [
                'text' => $text,
            ]);

        if (!$res->successful()) {
            throw new \RuntimeException('X error: '.$res->body());
        }

        return (string) data_get($res->json(), 'data.id');
    }
}
