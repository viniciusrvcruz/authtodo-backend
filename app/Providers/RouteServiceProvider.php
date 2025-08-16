<?php

namespace App\Providers;

use App\Enums\AuthProviderEnum;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::bind('provider', fn (string $value) =>
            AuthProviderEnum::tryFrom($value) ?? abort(404, "Provider not found")
        );

        Route::bind('task', fn (string $id) =>
            Task::where('user_id', Auth::id())->findOrFail($id)
        );
    }
}
