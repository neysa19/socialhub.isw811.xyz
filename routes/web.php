<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Ruta principal de la aplicación
Route::get('/', function () {
    return view('welcome'); // Página de inicio
});

// Ruta para el dashboard, accesible solo para usuarios autenticados y verificados
Route::get('/dashboard', function () {
    return view('dashboard'); // Vista del dashboard
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas de autenticación de usuario generadas por Laravel Breeze
// Ruta de login
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);

// Ruta de registro
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

// Ruta de logout
Route::middleware('auth')->post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Incluir las rutas adicionales de autenticación generadas por Laravel Breeze
require __DIR__.'/auth.php';
