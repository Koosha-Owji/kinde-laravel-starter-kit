<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'index'])->name('home');


Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/callback', [AuthController::class, 'callback'])->name('callback');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});


Route::middleware('kinde.auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});
