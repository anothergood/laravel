<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;

use App\Traits\AccessTokenTrait;

class LoginController extends Controller
{
    use AccessTokenTrait;

    public function store(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user->verified == 1){
            if (Hash::check($request->password, $user->password)) {
                $date_now = Carbon::now();
                $userId = $user->id;
                $name = 'MyToken';

                $token = $this->createAccessToken($userId, $name, '');
                $date_expires = $token->token->expires_at;
                $expires_in = $date_expires->diffInSeconds($date_now);
                $values = [
                    'token_type' => "Bearer",
                    'expires_in' => $expires_in,
                    'access_token' => $token->accessToken,
                ];
                return response([
                    'user' => $user,
                    'token' => $values,
                ]);

            } else {
                return response(['message' => 'Wrong password.'],400); 
            }
        } else {
            return response(['message' => 'Your e-mail is not verified.'], 422);
        }
    }
}
