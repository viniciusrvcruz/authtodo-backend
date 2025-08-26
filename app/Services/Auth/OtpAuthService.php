<?php

namespace App\Services\Auth;

use App\Exceptions\InvalidOneTimePasswordException;
use App\Models\User;
use App\ValueObjects\Email;

class OtpAuthService
{
    public function send(Email $email): void
    {
        $user = $this->firstOrCreateUser($email);

        $user->sendOneTimePassword();
    }

    public function verify(string $code, Email $email): void
    {
        $user = $this->firstOrCreateUser($email);

        $result = $user->attemptLoginUsingOneTimePassword($code);

        if (! $result->isOk()) throw new InvalidOneTimePasswordException('Invalid code');
    }

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
