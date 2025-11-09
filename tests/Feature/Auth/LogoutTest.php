<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

describe('Logout', function () {
    it('allows an authenticated user to log out successfully', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('auth.logout'));

        // Assert: check for 204 No Content and that the user is logged out
        $response->assertNoContent();
        expect(Auth::check())->toBeFalse();
    });

    it('does not allow logout when the user is not authenticated', function () {
        $response = $this->postJson(route('auth.logout'));

        $response->assertUnauthorized();
    });

    it('invalidates the session and regenerates the CSRF token after logout', function () {
        // Arrange: authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Store the old CSRF token before logout
        $oldToken = csrf_token();

        $response = $this->postJson(route('auth.logout'));

        $response->assertNoContent();

        // The session should be regenerated (new CSRF token)
        $newToken = csrf_token();
        expect($newToken)->not->toBe($oldToken);

        expect(Auth::check())->toBeFalse();
    });

    it('ensures the user is no longer authenticated after logout', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        expect(Auth::check())->toBeTrue();

        $response = $this->postJson(route('auth.logout'));
        $response->assertNoContent();

        expect(Auth::check())->tobeFalse();
    });
});
