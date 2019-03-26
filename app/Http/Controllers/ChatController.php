<?php

namespace App\Http\Controllers;

use Log;
use App\User;
use App\Events\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreMessageRequest;

class ChatController extends Controller
{
    public function sendMessage(Request $request){

        event(new ChatMessage($request->input('message')));

    }

    public function sendPrivateMessage(StoreMessageRequest $request, User $user) {

        $friend = DB::table('user_user')
        ->where(function ($query) use($user, $request) {
            $query->where('user_id', $user->id)
                  ->where('user_initiator_id', $request->user()->id)
                  ->where('status', 'approved');
        })
        ->orWhere(function ($query) use($user, $request) {
            $query->where('user_id', $request->user()->id)
                  ->where('user_initiator_id', $user->id)
                  ->where('status', 'approved');
        })
        ->exists();

        if ($friend or $request->user()->id == $user->id) {

            $data = [
                'push_type' => 'private_chat',
                'message' => $request->body,
                'from_user_id' => $request->user()->id,
                'from_user_username' => $request->user()->username,
            ];
            $user->userPush($data);
        }
    }

}
