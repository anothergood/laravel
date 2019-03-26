<?php

namespace App\Http\Controllers;

use App\User;
use App\Mail\VerifyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Notifications\MailConfirmation;
use App\Http\Requests\StoreUserRequest;

class RegisterController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        $user = new User;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password =  bcrypt($request ->password);
        $user->save();

        $verify_token = sha1(str_random(70));
        $expiresAt = now()->addMinutes(10);
        Cache::put($verify_token, $user->id, $expiresAt);

        $mail = new VerifyMail($user, $verify_token);
        $user->notify(new MailConfirmation($mail));

        return response($user);
    }

    public function verifyUser($token)
    {
        if (Cache::has($token)) {
            $user = User::find(Cache::get($token));
            if (!$user->verified) {
                $user->verified = 1;
                $user->save();
                Cache::forget($token);
                return response(['message' => 'Your e-mail is verified. You can now login.']);
            } else {
                return response(['message' => 'Your e-mail is not verified.'], 422);
            }
        } else {
            return response(['message' => 'Sorry your email cannot be identified.'], 422);
        }
    }
}
