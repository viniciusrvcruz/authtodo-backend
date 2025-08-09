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

describe('authentication routes', function () {
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

    // it('authenticates an existing user', function () {
    //     $oauth_user = new OAuth2User();
    //     $oauth_user->id = '12345';
    //     $oauth_user->name = 'Tyler Smith';
    //     $oauth_user->email = 'tyler.smith@example.com';
    //     $oauth_user->token = '123456789abcdef';
    //     $oauth_user->refreshToken = '123456789abcdef';

    //     $app_user = User::factory()->create([
    //         'email' => $oauth_user->email,
    //     ]);

    //     $mock_provider = Mockery::mock(SocialiteProvider::class);
    //     $mock_provider->shouldReceive('user')->andReturn($oauth_user);
    //     Socialite::shouldReceive('driver')
    //         ->with('google')
    //         ->andReturn($mock_provider);

    //     get('/auth/google/callback');

    //     assertAuthenticated();
    //     expect(User::count())->toBe(1);
    //     expect(Auth::id())->toBe($app_user->id);
    // });

    // it('redirects authenticated user to the homepage', function () {
    //     $oauth_user = new OAuth2User();
    //     $oauth_user->id = '12345';
    //     $oauth_user->name = 'Tyler Smith';
    //     $oauth_user->email = 'tyler.smith@example.com';
    //     $oauth_user->token = '123456789abcdef';
    //     $oauth_user->refreshToken = '123456789abcdef';

    //     $mock_provider = Mockery::mock(SocialiteProvider::class);
    //     $mock_provider->shouldReceive('user')->andReturn($oauth_user);
    //     Socialite::shouldReceive('driver')
    //         ->with('google')
    //         ->andReturn($mock_provider);

    //     $response = get('/auth/google/callback');

    //     assertAuthenticated();
    //     $response->assertRedirect('/');
    // });

    // it('returns 400 when the oauth callback url is requested directly', function () {
    //     $response = get('/auth/google/callback');

    //     $response->assertStatus(400);
    //     assertGuest();
    // });
});
