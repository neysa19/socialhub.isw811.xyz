<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TwitterOAuth2Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SocialAccount;


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
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::get('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

Route::middleware('auth')->post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Incluir las rutas adicionales de autenticación generadas por Laravel Breeze
// Conexión con LinkedIn (usando tu SocialAuthController)
    Route::get('/auth/linkedin',               [SocialAuthController::class, 'redirectToLinkedIn'])->name('linkedin.redirect');
    Route::get('/auth/linkedin/callback',      [SocialAuthController::class, 'handleLinkedInCallback'])->name('linkedin.callback');
    Route::delete('/auth/linkedin/disconnect', [SocialAuthController::class, 'disconnectLinkedIn'])->name('linkedin.disconnect');


Route::get('/dashboard', function () {
    $userId = Auth::id(); 
    $hasTw  = SocialAccount::where('user_id', $userId)->where('provider', 'twitter')->exists();
    $hasLi  = SocialAccount::where('user_id', $userId)->where('provider', 'linkedin')->exists();

    return view('dashboard', compact('hasTw', 'hasLi'));
})->middleware(['auth','verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/oauth/twitter/redirect', [TwitterOAuth2Controller::class, 'redirect'])->name('twitter.redirect');
    Route::get('/oauth/twitter/callback', [TwitterOAuth2Controller::class, 'callback'])->name('twitter.callback');
    Route::delete('/oauth/twitter/disconnect', [TwitterOAuth2Controller::class, 'disconnect'])->name('twitter.disconnect');
});


// Facebook routes
Route::get('/facebook/connect', [FacebookController::class, 'connectFacebook'])->name('facebook.connect');
Route::get('/facebook/callback', [FacebookController::class, 'handleFacebookCallback'])->name('facebook.callback');
Route::post('/facebook/post', [FacebookController::class, 'postToFacebook'])->name('facebook.post');

// Instagram routes
Route::get('/instagram/connect', [InstagramController::class, 'connectInstagram'])->name('instagram.connect');
Route::get('/instagram/callback', [InstagramController::class, 'handleInstagramCallback'])->name('instagram.callback');
Route::post('/instagram/post', [InstagramController::class, 'postToInstagram'])->name('instagram.post');

Route::middleware(['auth'])->group(function () {

    // Publicaciones (usar PostController)
    Route::get('/publications',  [PostController::class, 'index'])->name('publications.index');
    Route::post('/publications', [PostController::class, 'store'])->name('publications.store');


    // Rutas para los horarios de publicación
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Rutas para la cola de publicaciones
    Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
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
