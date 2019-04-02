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
                    $query->where('user_id', $request->users);
                });

            if($chat_check->exists()){

                return response(['chat' => $chat_check->first() ]);

            } else {

                $chat->type = 'dialog';
                $chat->save();

                $data = [
                    'type' => 'chat_invite',
                    'data' => [
                        'chat_id' => $chat->id,
                        'chat_title' => $chat->title,
                        'chat_type' => $chat->type,
                    ],
                ];

                $chat->users()->attach($request->user()->id);
                $chat->users()->attach($request->users);
                $request->user()->userPush($data);
                User::find($request->users)->userPush($data);

                return response(['chat' => $chat ]);
            }

        } else {

            $chat->type = 'chat';
            $chat->save();

            $data = [
                'type' => 'chat_invite',
                'data' => [
                    'chat_id' => $chat->id,
                    'chat_title' => $chat->title,
                    'chat_type' => $chat->type,
                ],
            ];

            $chat->users()->attach($request->user()->id);
            $request->user()->userPush($data);
            foreach ($request->users as $id){
                $chat->users()->attach($id);
                User::find($id)->userPush($data);
            }

            return response(['chat' => $chat ]);
        }
    }

    public function inviteUser(Request $request, Chat $chat, User $user) {

        $chat_check = $user->chats()->where('id', $chat->id)->exists();
        if(!$chat_check) {
            $chat->users()->attach($user->id);

            $data = [
                'type' => 'chat_invite',
                'data' => [
                    'chat_id' => $chat->id,
                    'chat_title' => $chat->title,
                    'chat_type' => $chat->type,
                ],
            ];

            $user->userPush($data);

            return response(['status' => 'OK']);

        } else {

            return response(['status'=>'User is already in chat'], 422);

        }
    }

    public function allChats(Request $request) {

        $chat = $request->user()->chats;
        return response([ 'chats' => $chat ]);

    }

}
