<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpAuthRequest;
use App\Http\Requests\VerifyOtpAuthRequest;
use App\Services\Auth\OtpAuthService;
use App\ValueObjects\Email;

class OtpAuthController extends Controller
{
    public function __construct(
        protected OtpAuthService $service
    ) {}

    public function send(SendOtpAuthRequest $request)
    {
        $email = new Email($request->string('email'));

        $this->service->send($email);

        return response()->json([ 'success' => true ]);
    }

    public function verify(VerifyOtpAuthRequest $request)
    {
        $email = new Email($request->string('email'));
        $code = $request->string('code');

        $this->service->verify($code, $email);

        return response()->json([ 'success' => true ]);
    }
}
