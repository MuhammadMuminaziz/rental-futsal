<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string'
        ], $this->message());

        if ($validator->fails()) {
            return $this->sendError('Bad Request', $validator->errors(), code: 400);
        }

        if (Auth::guard('owner')->attempt($data)) {
            $user = Auth::guard('owner')->user();
            return $this->authenticateUser($user);
        } elseif (Auth::guard('user')->attempt($data)) {
            $user = Auth::guard('user')->user();
            return $this->authenticateUser($user);
        } else {
            return $this->sendError('Kredensial ini tidak sesuai dengan data kami.', code: 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse('Logout berhasil');
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken($user->name)->plainTextToken;
        return $this->sendResponse('Success', new UserResource($user), token: $token);
    }

    private function authenticateUser($user)
    {
        $token = $user->createToken($user->name)->plainTextToken;
        return $this->sendResponse('Success', new UserResource($user), token: $token);
    }
}
