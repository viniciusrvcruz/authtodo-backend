<?php

namespace App\Providers;

use App\Models\Task;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configRateLimiters();
        $this->configRouteModelBindings();
    }

    /**
     * Register all custom rate limiters for the application.
     */
    private function configRateLimiters(): void
    {
        RateLimiter::for('otp-group', function (Request $request) {
            // Unique rate limiting key based on IP and route name
            $key = $request->ip() . '|' . $request->route()?->getName();

            return Limit::perMinute(5)->by($key);
        });
    }

    /**
     * Register all custom route model bindings.
     */
    private function configRouteModelBindings(): void
    {
        Route::bind('task', function (string $id) {
            return Task::where('user_id', Auth::id())->findOrFail($id);
        });
    }
}
