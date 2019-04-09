<?php

namespace App\Http\Controllers;

use Log;
use App\Chat;
use App\User;
use App\Message;
use App\Events\PublicChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreChatRequest;

class ChatController extends Controller
{
    public function sendMessage(Request $request) {

        event(new PublicChat($request->input('message')));

    }

    public function createChat(StoreChatRequest $request) {

        $chat = new Chat;
        $chat->title = $request->title;

        if ($request->type == 'dialog') {

            $chat_check = $request->user()->chats()
                ->where('type', 'dialog')
                ->whereHas('users', function ($query) use($request) {
                    $query->where('id', $request->users);
                });
            if($chat_check->exists()){

                return response(['status' => 'Dialog already exists'],422);

            } else {

                $chat->type = 'dialog';
                $chat->save();

                $chat->users()->attach($request->user()->id);
                $chat->users()->attach($request->users);

                $data = [
                    'type' => 'chat_invite',
                    'data' => $chat->load('users'),
                ];
                $chat->usersNotification($data, $request->user()->id);

                return $chat;
            }

        } else {

            $chat->type = 'chat';
            $chat->save();

            $data = [
                'type' => 'chat_invite',
                'data' => $chat->load('users'),
            ];

            $chat->users()->attach($request->user()->id);
            if ($request->users) {
                $chat->users()->syncWithoutDetaching($request->users);
                $chat->usersNotification($data, $request->user()->id);
            }
            return $chat;
        }
    }

    public function inviteUser(Request $request, $chat, User $user) {

        $chat = $request->user()->chats()->findOrFail($chat);
        $user_check = $chat->users()->where('id',$user->id)->first();
        if(!$user_check) {
            $chat->users()->attach($user->id);
            $data_chat_invite = [
                'type' => 'chat_invite',
                'data' => $chat->load('users'),
            ];
            $chat->userNotification($data_chat_invite, $user->id);

            $data_new_invited = [
                'type' => 'new_invited',
                'data' => $chat,
            ];

            $chat->usersNotification($data_new_invited, $request->user()->id);

            return response(['status'=>'ok']);

        } else {

            return response(['status'=>'User is already in chat'], 422);

        }
    }

    public function allChats(Request $request) {

        $chat = $request->user()->chats()->paginate(10);
        return $chat;

    }

    public function inviteChatList(Request $request, $chat) {

        $chat = $request->user()->chats()->findOrFail($chat);

        $inviteChatList = $request->user()->friends()->whereDoesntHave('chats', function ($query) use($chat) {
            $query->where('id', $chat->id);
        })->paginate(10);

        return $inviteChatList;

    }

}
