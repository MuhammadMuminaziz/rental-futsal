<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends BaseController
{
    public function register(Request $request)
    {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation']);

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|max:20|string|confirmed',
            'password_confirmation' => 'required'
        ], $this->message());

        if ($validator->fails()) {
            return $this->sendError("Bad Request", $validator->errors(), 400);
        }

        unset($data['password_confirmation']);
        $data['role_id'] = 4;
        $user = User::create($data);

        Auth::login($user);
        $token = $user->createToken($user->name)->plainTextToken;
        return $this->sendResponse("Success", new UserResource($user), token: $token);
    }

    public function changePassword(Request $request)
    {
        // 
    }
}
