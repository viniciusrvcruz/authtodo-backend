<?php

use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware(['throttle:otp-group'])->group(function () {
    Route::post('/otp/send', [OtpAuthController::class, 'send'])->name('auth.otp.send');
    Route::post('/otp/verify', [OtpAuthController::class, 'verify'])->name('auth.otp.verify');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', fn (Request $request) => $request->user())->name('user.show');
    Route::put('/user/update', [UserController::class, 'update'])->name('user.update');

    Route::apiResource('tasks', TaskController::class);
});
