<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\InstagramController;

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

Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

Route::middleware('auth')->post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Incluir las rutas adicionales de autenticación generadas por Laravel Breeze

Route::get('auth/linkedin', [SocialAuthController::class, 'redirectToLinkedIn']);
Route::get('auth/linkedin/callback', [SocialAuthController::class, 'handleLinkedInCallback']);

Route::get('/twitter/connect', [TwitterController::class, 'connectTwitter'])->name('twitter.connect');
Route::get('/twitter/callback', [TwitterController::class, 'handleTwitterCallback'])->name('twitter.callback');
Route::post('/twitter/post', [TwitterController::class, 'postTweet'])->name('twitter.post');

// Facebook routes
Route::get('/facebook/connect', [FacebookController::class, 'connectFacebook'])->name('facebook.connect');
Route::get('/facebook/callback', [FacebookController::class, 'handleFacebookCallback'])->name('facebook.callback');
Route::post('/facebook/post', [FacebookController::class, 'postToFacebook'])->name('facebook.post');

// Instagram routes
Route::get('/instagram/connect', [InstagramController::class, 'connectInstagram'])->name('instagram.connect');
Route::get('/instagram/callback', [InstagramController::class, 'handleInstagramCallback'])->name('instagram.callback');
Route::post('/instagram/post', [InstagramController::class, 'postToInstagram'])->name('instagram.post');


require __DIR__.'/auth.php';
