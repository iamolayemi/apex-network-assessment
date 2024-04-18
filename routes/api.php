<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login')->name('auth.login');
    Route::post('auth/register', 'register')->name('auth.register');
    Route::delete('auth/logout', 'logout')->name('auth.logout')->middleware('auth:api');
});

Route::middleware('auth:api')->group(function () {
    Route::controller(AccountController::class)->group(function () {
        Route::get('account', 'show')->name('account.show');
        Route::patch('account', 'update')->name('account.update');
        Route::post('account/change-password', 'changePassword')->name('account.change-password');
    });

    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/resetPassword', [UserController::class, 'resetPassword'])->name('users.reset-password');
});
