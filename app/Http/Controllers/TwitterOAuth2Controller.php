<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TwitterOAuth2Controller extends Controller
{
    private function codeChallenge(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    public function redirect(Request $request)
    {
        $state    = Str::random(40);
        $verifier = Str::random(64);

        Session::put('tw_state', $state);
        Session::put('tw_code_verifier', $verifier);

        // lee SIEMPRE desde config()
        $clientId    = config('services.twitter.client_id');
        $redirectUri = config('services.twitter.redirect');
        $scopes      = env('TWITTER_SCOPES', 'tweet.read tweet.write users.read offline.access');

        $params = [
            'response_type'         => 'code',
            'client_id'             => $clientId,
            'redirect_uri'          => $redirectUri,
            'scope'                 => $scopes,
            'state'                 => $state,
            'code_challenge'        => $this->codeChallenge($verifier),
            'code_challenge_method' => 'S256',
        ];

        return redirect()->away('https://twitter.com/i/oauth2/authorize?'.http_build_query($params));
    }

   public function callback(Request $request)
{
    Log::info('TW callback HIT', $request->query());

    if ($request->has('error')) {
        return redirect()->route('dashboard')->with('error', 'Conexión con X cancelada.');
    }

    $code  = (string) $request->get('code');
    $state = (string) $request->get('state');

    if (!$code) {
        return redirect()->route('dashboard')->with('error', 'X no devolvió "code".');
    }

    // Validar state (CSRF)
    $expectedState = Session::pull('tw_state');
    if (!$expectedState || !hash_equals($expectedState, $state)) {
        return redirect()->route('dashboard')->with('error', 'State inválido.');
    }

    // PKCE verifier
    $verifier = (string) Session::pull('tw_code_verifier');
    if (!$verifier) {
        return redirect()->route('dashboard')->with('error', 'Falta code_verifier. Intenta de nuevo.');
    }

    // >>> AQUI estaba el problema: debes leerlos del config
    $clientId     = config('services.twitter.client_id');
    $clientSecret = config('services.twitter.client_secret');
    $redirectUri  = config('services.twitter.redirect');

    // Armar Authorization: Basic <base64(client_id:client_secret)>
    $basic = base64_encode($clientId . ':' . $clientSecret);
    Log::info('TW basic header len', ['len' => strlen($basic)]); // diagnóstico sin exponer el secreto

    $tokenRes = Http::asForm()
    ->withBasicAuth($clientId, $clientSecret)   // ← esto arma "Authorization: Basic <base64(id:secret)>"
    ->post('https://api.twitter.com/2/oauth2/token', [
        'grant_type'    => 'authorization_code',
        'code'          => (string) $request->query('code'),
        'redirect_uri'  => $redirectUri,
        'code_verifier' => (string) Session::pull('tw_code_verifier'),
        'client_id'     => $clientId,           // opcional, ayuda a depurar
    ]);

    Log::info('TW token status/body', [$tokenRes->status(), $tokenRes->body()]);

    if (!$tokenRes->successful()) {
        Log::error('TW token error', [
            'status' => $tokenRes->status(),
            'body'   => $tokenRes->body(),
        ]);
        return redirect()->route('dashboard')
            ->with('error', 'No se pudo obtener token de X: ' . $tokenRes->body());
    }

    $tok = $tokenRes->json();

    // Leer perfil
    $meRes = Http::withToken($tok['access_token'])
        ->get('https://api.twitter.com/2/users/me');

    if (!$meRes->successful()) {
        Log::error('TW me error', ['status' => $meRes->status(), 'body' => $meRes->body()]);
        return redirect()->route('dashboard')
            ->with('error', 'No se pudo leer el perfil de X: ' . $meRes->body());
    }

    $me = $meRes->json('data');

    SocialAccount::updateOrCreate(
        ['user_id' => Auth::id(), 'provider' => 'twitter'],
        [
            'provider_user_id' => $me['id'] ?? null,
            'access_token'     => $tok['access_token'] ?? null,
            'refresh_token'    => $tok['refresh_token'] ?? null,
            'token_expires_at' => now()->addSeconds((int)($tok['expires_in'] ?? 7200)),
            'scopes'           => $tok['scope'] ?? null,
            'meta'             => json_encode([
                'username' => $me['username'] ?? null,
                'name'     => $me['name'] ?? null,
            ]),
        ]
    );

    return redirect()->route('dashboard')->with('status', '✅ Cuenta de X conectada');
}
    public function disconnect()
    {
        SocialAccount::where('user_id', Auth::id())
            ->where('provider', 'twitter')
            ->delete();

        return back()->with('status', 'Cuenta de X desconectada.');
    }
}
