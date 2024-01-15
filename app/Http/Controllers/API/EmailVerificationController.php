<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class EmailVerificationController extends BaseController
{
    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return $this->sendError('Verification email fails');
        }
        $user = User::find($id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return $this->sendResponse('Email has been verified');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->sendResponse('Email has been verified');
        }
        $request->user()->sendEmailVerificationNotification();
        return $this->sendResponse('Link verification email has been sended');
    }
}
