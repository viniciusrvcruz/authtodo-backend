<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function update(UpdateUserRequest $request)
    {
        $request->user()->update($request->validated());

        return response()->noContent();
    }
}
