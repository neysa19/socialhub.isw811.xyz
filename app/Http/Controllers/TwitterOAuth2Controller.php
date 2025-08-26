<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TwitterOAuth2Controller extends Controller
{
    private function codeChallenge(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $verifier = Str::random(64);

        Session::put('tw_state', $state);
        Session::put('tw_code_verifier', $verifier);

        $params = [
            'response_type'         => 'code',
            'client_id'             => env('TWITTER_CLIENT_ID'),
            'redirect_uri'          => env('TWITTER_REDIRECT_URI'),
            'scope'                 => env('TWITTER_SCOPES', 'tweet.read tweet.write users.read offline.access'),
            'state'                 => $state,
            'code_challenge'        => $this->codeChallenge($verifier),
            'code_challenge_method' => 'S256',
        ];

        $url = 'https://twitter.com/i/oauth2/authorize?' . http_build_query($params);
        return redirect()->away($url);
    }

    public function callback(Request $request)
    {
        $state = Session::pull('tw_state');
        $verifier = Session::pull('tw_code_verifier');

        if (!$state || $state !== $request->get('state')) {
            return redirect()->route('publications.index')->withErrors('Estado OAuth inválido.');
        }
        if (!$request->has('code')) {
            return redirect()->route('publications.index')->withErrors('No se recibió código de autorización.');
        }

        // Intercambiar code -> tokens
        $tokenRes = Http::asForm()->post('https://api.twitter.com/2/oauth2/token', [
            'client_id'     => env('TWITTER_CLIENT_ID'),
            'client_secret' => env('TWITTER_CLIENT_SECRET'),
            'grant_type'    => 'authorization_code',
            'code'          => $request->get('code'),
            'redirect_uri'  => env('TWITTER_REDIRECT_URI'),
            'code_verifier' => $verifier,
        ]);

        if (!$tokenRes->successful()) {
            return redirect()->route('publications.index')->withErrors('No se pudo obtener token: '.$tokenRes->body());
        }

        $tok = $tokenRes->json(); // access_token, refresh_token, expires_in, scope, token_type

        // Obtener el perfil del usuario autenticado
        $meRes = Http::withToken($tok['access_token'])
            ->get('https://api.twitter.com/2/users/me');
        if (!$meRes->successful()) {
            return redirect()->route('publications.index')->withErrors('No se pudo leer el perfil: '.$meRes->body());
        }
        $me = $meRes->json('data'); // id, name, username

        // Guardar en social_accounts
        SocialAccount::updateOrCreate(
            ['user_id' => Auth::id(), 'provider' => 'twitter'],
            [
                'provider_user_id' => $me['id'] ?? null,
                'access_token'     => json_encode([
                    'access_token'  => $tok['access_token'],
                    'refresh_token' => $tok['refresh_token'] ?? null,
                    'scope'         => $tok['scope'] ?? null,
                ]),
                'refresh_token'   => $tok['refresh_token'] ?? null, // opcional duplicado
                'token_expires_at'=> Carbon::now()->addSeconds((int)($tok['expires_in'] ?? 7200)),
                'scopes'          => $tok['scope'] ?? null,
                'meta'            => json_encode(['username' => $me['username'] ?? null, 'name' => $me['name'] ?? null]),
            ]
        );

        return redirect()->route('publications.index')->with('ok', 'Cuenta de X conectada correctamente.');
    }

    public function disconnect()
    {
        SocialAccount::where('user_id', Auth::id())->where('provider', 'twitter')->delete();
        return back()->with('ok', 'Cuenta de X desconectada.');
    }
}
