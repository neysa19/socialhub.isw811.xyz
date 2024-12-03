<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SocialAuthController;

// Ruta principal de la aplicación
Route::get('/', function () {
    return view('welcome'); // Página de inicio
});

// Ruta para el dashboard, accesible solo para usuarios autenticados y verificados
Route::get('/dashboard', function () {
    return view('dashboard'); // Vista del dashboard
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas de autenticación de usuario generadas por Laravel Breeze
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::get('/forgot-password', [AuthenticatedSessionController::class, 'showLinkRequestForm'])->name('password.request');


Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

Route::middleware('auth')->post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Incluir las rutas adicionales de autenticación generadas por Laravel Breeze

Route::get('auth/linkedin', [SocialAuthController::class, 'redirectToProvider']);
Route::get('auth/linkedin/callback', [SocialAuthController::class, 'handleProviderCallback']);



require __DIR__.'/auth.php';
