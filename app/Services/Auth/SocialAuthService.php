<?php

namespace App\Services\Auth;

use App\Enums\AuthProviderEnum;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService
{
    public function handleProviderCallback(AuthProviderEnum $provider): RedirectResponse
    {
        try {

            $providerUser = Socialite::driver($provider->value)->user();

        } catch (Exception $e) {
            return redirect(config('app.frontend_url') . '/login/error');
        }

        $user = User::firstOrCreate(
            [
                'email' => $providerUser->email,
                'auth_provider' => $provider->value,
            ],
            [ 'name' => $providerUser->name ]
        );

        Auth::login($user);

        return redirect(config('app.frontend_url'));
    }
}
