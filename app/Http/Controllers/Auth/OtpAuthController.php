<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpAuthRequest;
use App\Services\Auth\OtpAuthService;
use App\ValueObjects\Email;
use Illuminate\Http\Request;

class OtpAuthController extends Controller
{
    public function __construct(
        protected OtpAuthService $service
    ) {}

    public function send(SendOtpAuthRequest $request)
    {
        $email = new Email($request->input('email'));

        $this->service->send($email);

        return response()->json([ 'success' => true ]);
    }

    public function verify(Request $request)
    {
        //
    }
}
