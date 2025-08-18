<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AuthProviderEnum;
use App\Http\Controllers\Controller;
use App\Services\Auth\SocialAuthService;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function __construct(
        protected SocialAuthService $service
    ) {}

    public function redirect(AuthProviderEnum $provider)
    {
        return Socialite::driver($provider->value)->redirect();
    }

    public function callback(AuthProviderEnum $provider)
    {
        return $this->service->handleProviderCallback($provider);
    }
}
