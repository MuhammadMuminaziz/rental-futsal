<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($message, $result = null, $code = 200, $token = null)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (!empty($result)) {
            $response['data'] = $result;
        }

        if(!empty($token)){
            $response['token'] = $token;
        }

        return response()->json($response, $code);
    }

    public function sendError($error, $errorMessage = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];

        if (!empty($errorMessage)) {
            $response['data'] = $errorMessage;
        }

        return response()->json($response, $code);
    }

    protected function message()
    {
        return [
            'user_id.required' => 'User id harus diisi',
            'name.required' => 'Nama harus diisi',
            'name.string' => 'Nama harus berbentuk string',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak falid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.string' => 'Password harus berbentuk string',
            'password.min' => 'Password minimal 5 karakter',
            'password.max' => 'Password maksimal 20 karakter',
            'password.confitm' => 'Password tidak cocok',
            'password_confirmation.required' => 'Password konfirmasi harus diisi',
            'photo.required' => 'Photo harus diisi',
            'photo.mimes' => 'Photo harus berbentuk gambar (jpg, jpeg, png)',
            'photo.max' => 'Photo tidak boleh melebihi 2Mb',
            'refresh_token.required' => 'Token harus diisi',
            'refresh_token.string.string' => 'Token harus berbentuk string'
        ];
    }
}
