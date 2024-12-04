<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !session('two_factor_authenticated')) {
            return redirect()->route('2fa.enable');
        }

        return $next($request);
    }
}
