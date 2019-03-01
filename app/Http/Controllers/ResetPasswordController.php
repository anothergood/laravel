<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Cache;
use App\Notifications\MailConfirmation;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\NewPasswordRequest;

class ResetPasswordController extends Controller
{
    public function resetPassword(ResetPasswordRequest $request)
    {
        if (User::where('email','=',$request->email)->exists()) {
            $user = User::where('email','=',$request->email)->first();
            $token = sha1(str_random(70));
            $expiresAt = now()->addMinutes(10);
            Cache::put($token, $user->id, $expiresAt);
            $mail = new ResetPasswordMail($user, $token);
            $user->notify(new MailConfirmation($mail));
            return $mail;
            return response(['mail' => $request->email]);
        } else {
            return response(['message' => 'Sorry your email cannot be identified.'], 422);
        }
    }

    public function newPassword(NewPasswordRequest $request)
    {
        if (Cache::has($request->token)) {
            $user = User::find(Cache::get($request->token));
            $user->password = bcrypt($request->password);
            $user->save();
            return response(['message' => 'Password has been changed. You can log in now.']);

        } else {
            return response(['message' => 'Sorry your email cannot be identified.'], 422);
        }
    }


}
