<?php

use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/otp/send', [OtpAuthController::class, 'send'])->name('auth.otp.send');
    // Route::post('/otp/verify', [OtpAuthController::class, 'verify'])->name('auth.otp.verify');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
 
    Route::apiResource('tasks', TaskController::class);
});
