<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use App\Chat;
use App\UserChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreMessageRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\FriendMessagesRequest;

class MessageController extends Controller
{

    public function sendMessage(StoreMessageRequest $request, $chat) {
        $chat_check = $request->user()->chats()->findOrFail($chat);
        if($chat_check) {

            $message = new Message;
            $message->user_id = $request->user()->id;
            $message->chat_id = $chat_check->id;
            $message->body = $request->body;
            $message->save();

            $chat_check->users()->where('user_id', '<>', $request->user()->id)->increment('unread_messages');

            $data = [
                'type' => 'message',
                'data' => [
                    'message' => $request->body,
                    'from_user' => $request->user(),
                    'created_at' => $message->created_at,
                    'chat_id' => $chat_check->id,
                ],
            ];

            $users = $chat_check->users()->where('user_id', '<>', $request->user()->id)->get();

            foreach ($users as $user){
                User::find($user->id)->userPush($data);
            }

            return $data;
        } else {
            return response(['message' => 'Forbidden'], 403);
        }

    }

    public function allChatMessages(Request $request, $chat) {

        $chat_check = $request->user()->chats()->findOrFail($chat);

        if($chat_check) {
            $messages = $chat_check->messages()->with('user')->orderBy('created_at', 'desc')->paginate(6);

            $mes_coll = $messages->getCollection()->reverse()->values();
            $messages->setCollection($mes_coll);

            return $messages;

        } else {

            return response(['message' => 'Forbidden'], 403);

        }
    }

    public function resetUnreadMessages(Request $request, $chat) {

        $chat_check = $request->user()->chats()->where('id', $chat);

        if($chat_check->exists()) {

            $chat_check->first()->users()->updateExistingPivot($request->user()->id, ['unread_messages' => 0]);
            return $chat_check->first();

        } else {

            return response(['message' => 'Not found'], 404);

        }
    }

}
