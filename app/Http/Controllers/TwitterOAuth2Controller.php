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
    /** Genera code_challenge (S256) a partir del verifier */
    private function codeChallenge(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    /** Paso 1: Redirige a X (Twitter) con PKCE */
    public function redirect(Request $request)
    {
        $state    = Str::random(40);
        $verifier = Str::random(64); // code_verifier

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

    /** Paso 2: Callback -> Intercambia code por tokens, guarda en social_accounts y vuelve al dashboard */
    public function callback(Request $request)
    {
        Log::info('TW callback HIT', $request->all());

        if ($request->has('error')) {
            return redirect()->route('dashboard')->with('error', 'Conexión con X cancelada.');
        }

        $code  = (string) $request->string('code');
        $state = (string) $request->string('state');

        if (!$code) {
            return redirect()->route('dashboard')->with('error', 'X no devolvió "code".');
        }

        // Validar state (protección CSRF)
        $expectedState = Session::pull('tw_state');
        if (!$expectedState || !hash_equals($expectedState, $state)) {
            return redirect()->route('dashboard')->with('error', 'State inválido en el callback de X.');
        }

        // Recuperar el PKCE verifier (¡solo una vez!)
        $verifier = Session::pull('tw_code_verifier');
        if (!$verifier) {
            return redirect()->route('dashboard')->with('error', 'Falta code_verifier (PKCE). Intenta conectar de nuevo.');
        }

        try {
            // Construir cabecera Basic <client_id:client_secret> para Confidential client
            $basic = base64_encode(
                rawurlencode(env('TWITTER_CLIENT_ID')) . ':' .
                rawurlencode(env('TWITTER_CLIENT_SECRET'))
            );

            // Intercambio code -> tokens
            $tokenRes = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Basic ' . $basic,
                    'Accept'        => 'application/json',
                ])
                ->post('https://api.twitter.com/2/oauth2/token', [
                    'grant_type'    => 'authorization_code',
                    'code'          => $code,
                    'redirect_uri'  => env('TWITTER_REDIRECT_URI'),
                    'code_verifier' => $verifier,
                ]);

            if (!$tokenRes->successful()) {
                Log::error('TW token error', ['status' => $tokenRes->status(), 'body' => $tokenRes->body()]);
                return redirect()->route('dashboard')
                    ->with('error', 'No se pudo obtener token de X: ' . $tokenRes->body());
            }

            $tok = $tokenRes->json(); // access_token, refresh_token, expires_in, scope,...

            // Leer perfil del usuario
            $meRes = Http::withToken($tok['access_token'])->get('https://api.twitter.com/2/users/me');
            if (!$meRes->successful()) {
                Log::error('TW me error', ['status' => $meRes->status(), 'body' => $meRes->body()]);
                return redirect()->route('dashboard')
                    ->with('error', 'No se pudo leer el perfil de X: ' . $meRes->body());
            }
            $me = $meRes->json('data'); // ['id','username','name',...]

            // Guardar/actualizar la conexión en social_accounts
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
        } catch (\Throwable $e) {
            Log::error('TW callback exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('dashboard')->with('error', 'No se pudo conectar con X');
        }
    }

    /** Desconectar */
    public function disconnect()
    {
        SocialAccount::where('user_id', Auth::id())
            ->where('provider', 'twitter')
            ->delete();

        return back()->with('status', 'Cuenta de X desconectada.');
    }
}
