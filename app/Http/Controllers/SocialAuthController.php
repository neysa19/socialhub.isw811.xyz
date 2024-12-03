<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use SocialiteProviders\Manager\Config;


class SocialAuthController extends Controller
{
    public function redirectToLinkedIn()
    {
        return Socialite::buildProvider(
            \SocialiteProviders\LinkedIn\Provider::class,
            new Config(
                config('services.linkedin.client_id'),
                config('services.linkedin.client_secret'),
                config('services.linkedin.redirect')
            )
        )
        ->scopes(['r_liteprofile', 'r_emailaddress', 'w_member_social']) // Scopes necesarios
        ->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $linkedinUser = Socialite::driver('linkedin')->user();
            $user = User::where('email', $linkedinUser->email)->first();

            if ($user) {
                // Actualizar información del usuario si es necesario
            } else {
                // Crear un nuevo usuario
                $user = User::create([
                    'name' => $linkedinUser->name,
                    'email' => $linkedinUser->email,
                    'linkedin_token' => $linkedinUser->token,
                'linkedin_refresh_token' => $linkedinUser->refreshToken ?? null,
                    // Otros campos necesarios
                ]);
            }

            Auth::login($user);
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            // Manejo de errores
            return redirect('/login')->withErrors(['msg' => 'Error al autenticar con LinkedIn.']);
        }
}
}