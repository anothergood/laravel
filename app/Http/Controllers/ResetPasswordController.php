<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Notifications\MailConfirmation;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\NewPasswordRequest;

use App\Traits\AccessTokenTrait;

class ResetPasswordController extends Controller
{
    use AccessTokenTrait;

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email','=',$request->email)->first();
        $token = sha1(str_random(70));
        $expiresAt = now()->addMinutes(10);
        Cache::put($token, $user->id, $expiresAt);
        $mail = new ResetPasswordMail($user, $token);
        $user->notify(new MailConfirmation($mail));
        return response(['mail' => $request->email]);
    }

    public function newPassword(NewPasswordRequest $request)
    {
        if (Cache::has($request->token)) {
            $user = User::find(Cache::get($request->token));
            Cache::forget($request->token);
            $user->password = bcrypt($request->password);
            $user->save();
            DB::table('oauth_access_tokens')->where('user_id', '=', $user->id)->delete();
            $token = $this->createAccessToken($user->id, 'MyToken', '');
            return response([
                'message' => 'Password has been changed.',
                'access_token' => $token->accessToken,
            ]);
        } else {
            return response(['message' => 'Sorry your email cannot be identified.'], 422);
        }
    }
}
