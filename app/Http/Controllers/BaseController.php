<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;

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

        if (!empty($token)) {
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

    private function sendData($success, $data = [])
    {
        $response = [
            'success' => $success,
            'data' => $data
        ];
        return $response;
    }

    public function baseStore($request, $validData, $validasi, $message, $user = null)
    {
        $data = $request->only($validData);
        $validator = Validator::make($data, $validasi, $message);
        if ($validator->fails()) {
            return $this->sendData(false, $validator->errors());
        }

        if ($request->hasFile('avatar')) {
            if (!empty($user)) {
                if (!empty($user->avatar)) {
                    unlink('futsal/avatar/' . $user->avatar);
                }
            }
            $name = rand() . time() . "." . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move('futsal/avatar', $name);
            $data['avatar'] = $name;
        }

        if ($request->hasFile('photo')) {
            if (!empty($user)) {
                if (!empty($user->photo)) {
                    unlink('futsal/gallery/' . $user->photo);
                }
            }
            $name = rand() . time() . "." . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move('futsal/gallery', $name);
            $data['photo'] = $name;
        }

        if ($request->hasFile('doc_reviews')) {
            foreach ($request->file('doc_reviews') as $file) {
                if (!empty($user)) {
                    if (!empty($user->doc_reviews)) {
                        unlink('futsal/review/' . $user->doc_reviews);
                    }
                }
                $name = rand() . time() . "." . $file->getClientOriginalExtension();
                $file->move('futsal/review', $name);
                $doc[] = $name;
            }
            $data['doc_reviews'] = json_encode($doc);
        }

        return $this->sendData(true, $data);
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
