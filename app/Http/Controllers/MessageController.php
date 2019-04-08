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
        $chat = $request->user()->chats()->findOrFail($chat);

        $message = new Message;
        $message->user_id = $request->user()->id;
        $message->chat_id = $chat->id;
        $message->body = $request->body;
        $message->save();

        $chat->users()->where('user_id', '<>', $request->user()->id)->increment('unread_messages');

        $data = [
            'type' => 'message',
            'data' => [
                'message' => $message,
                'chat' => $chat,
                'user' => $request->user(),
            ],
        ];

        $chat->usersNotification($data, $request->user()->id);

        return $data;

    }

    public function allChatMessages(Request $request, $chat) {

        $chat = $request->user()->chats()->findOrFail($chat);

        $messages = $chat->messages()->with('user')->orderBy('created_at', 'desc')->paginate(6);

        $mes_coll = $messages->getCollection()->reverse()->values();
        $messages->setCollection($mes_coll);

        return $messages;

    }

    public function resetUnreadMessages(Request $request, $chat) {

        $chat = $request->user()->chats()->findOrFail($chat);

        $chat->users()->updateExistingPivot($request->user()->id, ['unread_messages' => 0]);
        return $chat;

    }

}
