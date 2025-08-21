<?php

namespace App\ValueObjects;

use InvalidArgumentException;

final class Email
{
    private string $email;
 
    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not a valid email address.',
                $email
            ));
        }
 
        $this->email = $email;
    }
 
    public function getEmail(): string
    {
        return $this->email;
    }
}
