<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login')->name('auth.login');
    Route::post('auth/register', 'register')->name('auth.register');
    Route::delete('auth/logout', 'logout')->name('auth.logout')->middleware('auth:api');
});
