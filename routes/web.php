<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\QueueController;

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

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.show');

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

Route::middleware(['auth'])->group(function () {

    // Rutas para publicaciones de entradas
    Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');
    Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');

    // Rutas para los horarios de publicación
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    // Otras rutas protegidas...
});

Route::middleware(['auth'])->group(function () {
    Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
});

require __DIR__.'/auth.php';
