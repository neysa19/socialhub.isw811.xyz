<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function enableTwoFactor()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        // Generar clave secreta
        $secretKey = $google2fa->generateSecretKey();
        $user->google2fa_secret = $secretKey;
        $user->save();

        // URL del QR para la app de Google Authenticator
        $QRImage = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('2fa.enable', compact('user', 'QRImage'));
    }

    public function verifyTwoFactor(Request $request)
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        $request->validate([
            'one_time_password' => 'required|numeric',
        ]);

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            // Confirmación exitosa del 2FA
            session(['two_factor_authenticated' => true]);
            return redirect()->route('dashboard')->with('status', 'Autenticación 2FA habilitada correctamente.');
        }

        return back()->withErrors(['one_time_password' => 'El código proporcionado no es válido.']);
    }
}
