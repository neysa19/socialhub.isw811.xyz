<?php

// app/Services/Publish/TwitterV2Publisher.php
namespace App\Services\Publish;

use App\Models\SocialAccount;
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

        // TODO: refresh token si expira ($account->refresh_token)

        // Nota: esto publica SOLO texto. Subida de media va aparte.
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
