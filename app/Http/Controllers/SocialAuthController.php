<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    public function redirectToProvider()
    {
    
        return Socialite::driver('linkedin')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
        $linkedinUser = Socialite::driver('linkedin')->user();

        // Lógica para enlazar o registrar al usuario
        $user = User::updateOrCreate(
            ['linkedin_id' => $linkedinUser->id],
            [
                'name' => $linkedinUser->name,
                'email' => $linkedinUser->email,
                'linkedin_token' => $linkedinUser->token,
                'linkedin_refresh_token' => $linkedinUser->refreshToken ?? null,
            ]
        );
    } catch (\Exception $e) {
        return redirect('/')->with('error', 'Error al conectar con LinkedIn.');
    }
        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Cuenta de LinkedIn conectada exitosamente.');
    }
}
