<?php

use App\Enums\AuthProviderEnum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;

// Dataset with all enum providers
dataset('authProviders', fn () => array_map(
    fn ($provider) => $provider->value,
    AuthProviderEnum::cases())
);

describe('social authentication routes', function () {
    it('redirects login route to correct URL', function (string $provider) {
        $response = $this->get("/auth/{$provider}/redirect");
    
        $response->assertStatus(302);
    
        $redirectUrl = $response->getTargetUrl();
        $parsedQuery = [];
        parse_str(parse_url($redirectUrl)['query'] ?? '', $parsedQuery);
    
        $expectedBaseUrls = [
            AuthProviderEnum::GOOGLE->value => 'https://accounts.google.com/o/oauth2/auth',
            AuthProviderEnum::GITHUB->value => 'https://github.com/login/oauth/authorize',
        ];
    
        expect($redirectUrl)->toStartWith($expectedBaseUrls[$provider]);
        expect($parsedQuery)->toHaveKeys([
            'client_id',
            'redirect_uri',
            'scope',
            'response_type',
            'state'
        ]);
    })->with('authProviders');
    
    it('creates and authenticates a user that does not yet exist', function (string $provider) {
        $user = User::factory()->make();
    
        $mockProvider = Mockery::mock(Provider::class);
        $mockProvider->shouldReceive('user')->andReturn($user);
    
        Socialite::shouldReceive('driver')
            ->with($provider)
            ->andReturn($mockProvider);
    
        $this->get("/auth/{$provider}/callback");
    
        $this->assertAuthenticated();
        $this->assertEquals(Auth::user()->email, $user->email);
    })->with('authProviders');
    
    it('authenticates an existing user', function (string $provider) {
        $userData = [
            'email' => fake()->email(),
            'name' => fake()->name(),
        ];
    
        $user = User::factory()->create($userData);
    
        $mockProvider = Mockery::mock(Provider::class);
        $mockProvider->shouldReceive('user')->andReturn($user);
    
        Socialite::shouldReceive('driver')
            ->with($provider)
            ->andReturn($mockProvider);
    
        $this->get("/auth/{$provider}/callback");
    
        $this->assertAuthenticated();

        expect(Auth::id())->toBe($user->id);
    })->with('authProviders');
    
    it('redirects authenticated user to the homepage frontend url', function (string $provider) {
        $mockProvider = Mockery::mock(Provider::class);
        $mockProvider->shouldReceive('user')->andReturn(User::factory()->create());
    
        Socialite::shouldReceive('driver')
            ->with($provider)
            ->andReturn($mockProvider);
    
        $response = $this->get("/auth/{$provider}/callback");
    
        $this->assertAuthenticated();
        $response->assertRedirect(config('app.frontend_url'));
    })->with('authProviders');
    
    it('redirects to frontend error page when the oauth callback url is requested directly', function (string $provider) {
        $response = $this->get("/auth/{$provider}/callback");
    
        $response->assertRedirect(config('app.frontend_url') . '/login/error');
    })->with('authProviders');
    
    it('returns 404 if the passed provider is invalid', function () {
        $response = $this->get('/auth/invalid_provider/callback');
    
        $response->assertNotFound();
    });
});
