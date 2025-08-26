<?php

use App\Models\User;
use App\Notifications\CustomOneTimePasswordNotification;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    Notification::fake();
    Session::start();

    $this->routes = ['auth.otp.send', 'auth.otp.verify'];
});

// Send OTP
describe('OTP Send', function () {
    it('sends otp and creates a new user if not exists', function () {
        $email = 'newuser@example.com';

        $response = $this->postJson(route('auth.otp.send'), [
            'email' => $email,
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['email' => $email]);

        Notification::assertSentTo(
            User::whereEmail($email)->firstOrFail(),
            CustomOneTimePasswordNotification::class
        );
    });

    it('does not duplicate user when sending otp again', function () {
        $email = 'existing@example.com';
        $user = User::factory()->create(['email' => $email]);

        $this->postJson(route('auth.otp.send'), [
            'email' => $email,
        ])->assertOk();

        $this->assertEquals(1, User::where('email', $email)->count());

        Notification::assertSentTo($user, CustomOneTimePasswordNotification::class);
    });

    it('fails validation when email is missing', function () {
        $this->postJson(route('auth.otp.send'))->assertStatus(422);
    });

    it('fails validation when email is invalid', function () {
        $this->postJson(route('auth.otp.send'), [
            'email' => 'not-an-email',
        ])->assertJsonValidationErrors(['email']);
    });
});

// Verify OTP
describe('OTP Verify', function () {
    it('logs in successfully with a valid otp', function () {
        $user = User::factory()->create(['email' => 'valid@example.com']);
        $otp = $user->createOneTimePassword();

        $response = $this->postJson(route('auth.otp.verify'), [
            'email' => $user->email,
            'code' => $otp->password,
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertAuthenticatedAs($user);
    });

    it('fails with an invalid otp code', function () {
        $user = User::factory()->create(['email' => 'invalid@example.com']);
        $user->createOneTimePassword();

        $this->postJson(route('auth.otp.verify'), [
            'email' => $user->email,
            'code' => '999999',
        ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'invalid_otp_password',
            ]);
    });

    it('cannot reuse an already consumed otp', function () {
        $user = User::factory()->create(['email' => 'reuse@example.com']);
        $otp = $user->createOneTimePassword();

       // 1st time: success
        $this->postJson(route('auth.otp.verify'), [
            'email' => $user->email,
            'code' => $otp->password,
        ])->assertOk();

        // 2nd time: failed
        $this->postJson(route('auth.otp.verify'), [
            'email' => $user->email,
            'code' => $otp->password,
        ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'invalid_otp_password',
            ]);
    });

    it('fails if otp is expired', function () {
        $user = User::factory()->create(['email' => 'expired@example.com']);
        $otp = $user->createOneTimePassword();
        $otp->expires_at = now()->subMinutes(5);
        $otp->save();

        $this->postJson(route('auth.otp.verify'), [
            'email' => $user->email,
            'code' => $otp->password,
        ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'invalid_otp_password',
            ]);
    });

    it('fails validation when code is missing', function () {
        $user = User::factory()->create(['email' => 'nocode@example.com']);

        $this->postJson(route('auth.otp.verify'), [
            'email' => $user->email,
        ])->assertJsonValidationErrors(['code']);
    });

    it('fails validation when email is missing', function () {
        $otp = User::factory()->create()->createOneTimePassword();

        $this->postJson(route('auth.otp.verify'), [
            'code' => $otp->password,
        ])->assertJsonValidationErrors(['email']);
    });

    it('fails if email does not exist in database', function () {
        $this->postJson(route('auth.otp.verify'), [
            'email' => 'ghost@example.com',
            'code' => '123456',
        ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'invalid_otp_password',
            ]);
    });
});

describe('OTP Send Throttle Middleware', function () {
    it('allows up to 10 requests per minute', function () {
        foreach ($this->routes as $route) {
            foreach (range(1, 5) as $i) {
                $this->postJson(route($route))->assertStatus(422);
            }
        }
    });

    it('blocks the 11th request within one minute', function () {
        foreach ($this->routes as $route) {
            foreach (range(1, 5) as $i) {
                $response = $this->postJson(route($route))->assertStatus(422);
            }

            // 11th request should block
            $response = $this->postJson(route($route));

            $response->assertStatus(429);
            $response->assertHeader('Retry-After');
            $response->assertHeader('X-RateLimit-Limit', '5');
        }
    });

    it('resets the limit after one minute', function () {
        foreach ($this->routes as $route) {
            foreach (range(1, 5) as $i) {
                $this->postJson(route($route))->assertStatus(422);
            }

            $this->postJson(route($route))->assertStatus(429);

            // Fast forward 61 seconds
            Date::setTestNow(now()->addSeconds(61));

            $response = $this->postJson(route($route));

            $response->assertStatus(422);

            Date::setTestNow();
        }
    });
});
