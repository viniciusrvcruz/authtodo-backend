<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidOneTimePasswordException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'invalid_otp_password',
            'description' => 'The provided one-time password is invalid.'
        ], 422);
    }
}
