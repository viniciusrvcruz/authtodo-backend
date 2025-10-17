<?php

namespace App\Services\Auth;

use App\Enums\AuthProviderEnum;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService
{
    public function handleProviderCallback(AuthProviderEnum $provider): RedirectResponse
    {
        try {

            $providerUser = Socialite::driver($provider->value)->user();

            $user = User::firstOrCreate(
                [ 'email' => $providerUser->email ],
                [ 'name' => $providerUser->name ]
            );

        } catch (Exception $e) {
            return redirect(config('app.frontend_url') . '/login?error=true');
        }

        Auth::login($user);
        Session::regenerate();

        return redirect(config('app.frontend_url') . '/home');
    }
}
