<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Home route - shows welcome or redirects to dashboard
Route::get('/', [AuthController::class, 'index'])->name('home');

// Authentication routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/callback', [AuthController::class, 'callback'])->name('callback');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected routes
Route::middleware('kinde.auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});
