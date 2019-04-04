<?php

namespace App\Http\Controllers;

use Log;
use App\Chat;
use App\User;
use App\Message;
use App\UserUser;
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
            if ($request->users !== null) {
                // return 'blar';
                foreach ($request->users as $id){
                    $chat->users()->attach($id);
                    User::find($id)->userPush($data);
                }
            }
            return response(['chat' => $chat ]);
        }
    }

    public function inviteUser(Request $request, Chat $chat, User $user) {

        $user_chat = $chat->users()->where('id', $request->user()->id)->exists();
        if($user_chat) {
            $chat_check = $user->chats()->where('id', $chat)->exists();
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
        } else {
            return response(['status' => 'chat not found'], 404);
        }
    }

    public function allChats(Request $request) {

        $chat = $request->user()->chats()->paginate(10);
        return response([ 'chats' => $chat ]);

    }

    public function inviteChatList(Request $request, $chat)
    {

        $user_chat = $request->user()->chats()->where('id', $chat)->exists();
        if($user_chat) {

            $friends_init = UserUser::where('status', 'approved')
                                    ->Where('user_initiator_id', $request->user()->id)
                                    ->get();
            $friends_recip = UserUser::where('status', 'approved')
                                     ->where('user_id', $request->user()->id)
                                     ->get();

            $friends_init_id = [];
            $friends_recip_id = [];
            foreach ($friends_init as $friend) {
                $friends_init_id[] = $friend->user_id;
            }

            foreach ($friends_recip as $friend) {
                $friends_recip_id[] = $friend->user_initiator_id;
            }
            $ids = array_merge($friends_init_id,$friends_recip_id);
            $friends = User::whereIn('id', $ids);

            $inviteChatList = $friends->whereDoesntHave('chats', function ($query) use($request, $chat) {
                $query->where('id', $chat);
            })->paginate(10);
            return response(['invite_chat_list' => $inviteChatList]);

        } else {
            return response(['status' => 'chat not found'], 404);
        }
    }

}
