<?php

namespace App\Services\Auth;

use App\Models\User;
use App\ValueObjects\Email;

class OtpAuthService
{
    public function send(Email $email): void
    {
        $user = $this->firstOrCreateUser($email);

        $user->sendOneTimePassword();
    }

    // public function validate(string $code, Email $email): bool
    // {
    //     return $this->otp->for($user)->validate($code);
    // }

    private function firstOrCreateUser(Email $email): User
    {
        $temporaryName = strstr($email->getEmail(), '@', true);

        return User::where('email', $email->getEmail())
            ->firstOrCreate(
                [ 'email' => $email->getEmail() ],
                [ 'name' => $temporaryName ]
            );
    }
}
