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
        return Socialite::driver('linkedin')
        ->scopes(['r_liteprofile', 'r_emailaddress', 'w_member_social'])
        ->redirect();
    }

    public function handleProviderCallback()
    {
        $linkedinUser = Socialite::driver('linkedin')->user();

        // Lógica para enlazar o registrar al usuario
        $user = User::updateOrCreate(
            ['linkedin_id' => $linkedinUser->id],
            [
                'name' => $linkedinUser->name,
                'email' => $linkedinUser->email,
                'linkedin_token' => $linkedinUser->token,
                'linkedin_refresh_token' => $linkedinUser->refreshToken,
            ]
        );

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Cuenta de LinkedIn conectada exitosamente.');
    }
}
