<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;

test('retrieve the logged in user', function () {
    $this->actAsUser();
 
    $response = $this->getJson('/api/user');
 
    $response->assertOk();
});

describe('social authentication routes', function () {
    it('redirects login route to correct Google URL', function () {
        $response = $this->get('/auth/google/redirect');

        $response->assertStatus(302);

        $redirectUrl = $response->getTargetUrl();

        $parsedQuery = [];

        parse_str(parse_url($redirectUrl)['query'] ?? '', $parsedQuery);

        expect($redirectUrl)->toStartWith(
            'https://accounts.google.com/o/oauth2/auth'
        );

        expect($parsedQuery)->toHaveKeys([
            'client_id',
            'redirect_uri',
            'scope',
            'response_type',
            'state'
        ]);
    });

    it('creates and authenticates a user that does not yet exist', function () {
        $user = User::factory()->make();

        $mockProvider = Mockery::mock(Provider::class);
        $mockProvider->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mockProvider);

        $this->get('/auth/google/callback');

        $this->assertAuthenticated();

        $this->assertEquals(Auth::user()->email, $user->email);
    });

    it('authenticates an existing user', function () {
        $userData = [
            'email' => fake()->email(),
            'name' => fake()->name(),
        ];

        $user = User::factory()->create($userData);

        $mockProvider = Mockery::mock(Provider::class);
        $mockProvider->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mockProvider);

        $this->get('/auth/google/callback');

        $this->assertAuthenticated();

        expect(Auth::id())->toBe($user->id);
    });

    it('redirects authenticated user to the homepage frontend url', function () {
        $mockProvider = Mockery::mock(Provider::class);
        $mockProvider->shouldReceive('user')->andReturn(User::factory()->create());

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($mockProvider);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticated();

        $response->assertRedirect(config('app.frontend_url'));
    });

    it('redirects to frontend error page when the oauth callback url is requested directly', function () {
        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(config('app.frontend_url') . '/login/error');
    });
});
