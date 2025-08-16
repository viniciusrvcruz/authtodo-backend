<?php

namespace App\Providers;

use App\Enums\AuthProviderEnum;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::bind('provider', fn (string $value) =>
            AuthProviderEnum::tryFrom($value) ?? abort(404, "Provider not found")
        );
    }
}
