<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TwoFactorAuthController extends Controller
{
    public function setup2FA()
    {
        $google2fa = new Google2FA();
        $user = Auth::user();
        
        if (!$user || !$user instanceof User) {
            return back()->withErrors(['error' => 'User is not authenticated or invalid.']);
        }
        if ($user && !$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        if (!$user) {
            return back()->withErrors(['error' => 'User is not authenticated.']);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('auth.2fa', ['qrCodeUrl' => $qrCodeUrl]);
    }

    public function verifyOTP(Request $request)
    {
        $google2fa = new Google2FA();
        $user = Auth::user();

        if ($user && $google2fa->verifyKey($user->google2fa_secret, $request->input('otp'))) {
            session(['2fa_verified' => true]);
            return redirect('/dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid OTP.']);
    }
}
