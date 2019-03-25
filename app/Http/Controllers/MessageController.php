<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\FriendMessagesRequest;

class MessageController extends Controller
{
    public function sendMessage(StoreMessageRequest $request) {

        $user = User::find($request->user_id);
        $initiator_user = $request->user()->users()->where('user_id', '=', $user->id)->where('status', '=', 'approved')->exists();
        $initiator_friend = $user->users()->where('user_id', '=', $request->user()->id)->where('status', '=', 'approved')->exists();


        if ($initiator_user or $initiator_friend or $request->user()->id == $request->user_id) {

            $message = new Message;
            $message->user_id = $request->user_id;
            $message->from_user_id = $request->user()->id;
            $message->from_user_username = $request->user()->username;
            $message->body = $request->body;
            $message->save();

            return response(['message' => $message ]);
        } else {
            return response(['message' => 'Only friends can send messages'], 403);
        }
    }

    public function receivedFriendMessages(FriendMessagesRequest $request) {

        $user = User::find($request->user_id);
        $initiator_user = $request->user()->users()->where('user_id', '=', $user->id)->where('status', '=', 'approved')->exists();
        $initiator_friend = $user->users()->where('user_id', '=', $request->user()->id)->where('status', '=', 'approved')->exists();

        if ($initiator_user or $initiator_friend or $request->user()->id == $request->user_id) {

            return response(['messages' => $request->user()->messages()->where('from_user_id', '=', $user->id)->get() ]);

        } else {
            return response(['message' => 'This user is not your friend'], 403);
        }
    }

    public function sendedFriendMessages(FriendMessagesRequest $request) {

        $user = User::find($request->user_id);
        $initiator_user = $request->user()->users()->where('user_id', '=', $user->id)->where('status', '=', 'approved')->exists();
        $initiator_friend = $user->users()->where('user_id', '=', $request->user()->id)->where('status', '=', 'approved')->exists();

        if ($initiator_user or $initiator_friend or $request->user()->id == $request->user_id) {

            return response(['messages' => $user->messages()->where('from_user_id', '=', $request->user()->id)->get() ]);

        } else {
            return response(['message' => 'This user is not your friend'], 403);
        }
    }

    public function allFriendMessages(FriendMessagesRequest $request) {

        $user = User::find($request->user_id);
        $initiator_user = $request->user()->users()->where('user_id', '=', $user->id)->where('status', '=', 'approved')->exists();
        $initiator_friend = $user->users()->where('user_id', '=', $request->user()->id)->where('status', '=', 'approved')->exists();

        if ($initiator_user or $initiator_friend or $request->user()->id == $request->user_id) {

            $messages = Message::where('from_user_id', '=', $user->id)
                ->where('user_id', '=', $request->user()->id)
                ->orWhere('from_user_id', '=', $request->user()->id)
                ->where('user_id', '=', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(6);
                // ->get();

            // $to_user = $request->user()->messages()->where('from_user_id', '=', $user->id);
            // $to_friend = $user->messages()->where('from_user_id', '=', $request->user()->id);
            // $messages = $to_user->merge($to_friend);
            return response(["messages" => $messages]);

            // return response(['messages' => $user->messages()->where('from_user_id', '=', $request->user()->id)->get() ]);

        } else {
            return response(['message' => 'This user is not your friend'], 403);
        }
    }

}
