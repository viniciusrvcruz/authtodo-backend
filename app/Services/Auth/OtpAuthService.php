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
        return User::firstOrCreate(['email' => $email->getEmail()]);
    }
}
