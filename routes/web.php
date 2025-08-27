<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    ProfileController,
    SocialAuthController,
    FacebookController,
    InstagramController,
    PublicationController,
    ScheduleController,
    QueueController,
    TwitterOAuth2Controller
};


// Redirige la raíz al dashboard
Route::redirect('/', '/dashboard');

// Todo lo “de la app” va autenticado y verificado
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (una sola vez)
    Route::get('/dashboard', function () {
        $userId = Auth::id(); // int|null
        $hasTw  = \App\Models\SocialAccount::where('user_id', $userId)->where('provider', 'twitter')->exists();
        $hasLi  = \App\Models\SocialAccount::where('user_id', $userId)->where('provider', 'linkedin')->exists();

        return view('dashboard', compact('hasTw', 'hasLi'));
    })->name('dashboard');

    // Perfil
    Route::get('/profile',        [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',        [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',     [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Publicaciones
    Route::get('/publications',   [PublicationController::class, 'index'])->name('publications.index');
    Route::post('/publications',  [PublicationController::class, 'store'])->name('publications.store');

    // Horarios
    Route::get('/schedules',      [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules',     [ScheduleController::class, 'store'])->name('schedules.store');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Cola
    Route::get('/queue',          [QueueController::class, 'index'])->name('queue.index');

    // X / Twitter
    Route::get('/oauth/twitter/redirect',   [TwitterOAuth2Controller::class, 'redirect'])->name('twitter.redirect');
    Route::get('/oauth/twitter/callback',   [TwitterOAuth2Controller::class, 'callback'])->name('twitter.callback');
    Route::delete('/oauth/twitter/disconnect', [TwitterOAuth2Controller::class, 'disconnect'])->name('twitter.disconnect');

    // LinkedIn
    Route::get('/auth/linkedin',               [SocialAuthController::class, 'redirectToLinkedIn'])->name('linkedin.redirect');
    Route::get('/auth/linkedin/callback',      [SocialAuthController::class, 'handleLinkedInCallback'])->name('linkedin.callback');
    Route::delete('/auth/linkedin/disconnect', [SocialAuthController::class, 'disconnectLinkedIn'])->name('linkedin.disconnect');

    // Facebook (si lo usas)
    Route::get('/facebook/connect',   [FacebookController::class, 'connectFacebook'])->name('facebook.connect');
    Route::get('/facebook/callback',  [FacebookController::class, 'handleFacebookCallback'])->name('facebook.callback');
    Route::post('/facebook/post',     [FacebookController::class, 'postToFacebook'])->name('facebook.post');

    // Instagram (si lo usas)
    Route::get('/instagram/connect',  [InstagramController::class, 'connectInstagram'])->name('instagram.connect');
    Route::get('/instagram/callback', [InstagramController::class, 'handleInstagramCallback'])->name('instagram.callback');
    Route::post('/instagram/post',    [InstagramController::class, 'postToInstagram'])->name('instagram.post');
});


require __DIR__ . '/auth.php';
