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

                return response(['status' => 'Dialog already exists'],422);

            } else {

                $chat->type = 'dialog';
                $chat->save();

                $data = [
                    'type' => 'chat_invite',
                    'data' => $chat,
                ];

                $chat->users()->attach($request->user()->id);
                $chat->users()->attach($request->users);
                User::find($request->users)->userPush($data);

                return $chat;
            }

        } else {

            $chat->type = 'chat';
            $chat->save();

            $data = [
                'type' => 'chat_invite',
                'data' => $chat,
            ];

            $chat->users()->attach($request->user()->id);
            if ($request->users !== null) {
                $chat->users()->syncWithoutDetaching($request->users);
                foreach ($request->users as $id){
                    User::find($id)->userPush($data);
                }
            }
            return $chat;
        }
    }

    public function inviteUser(Request $request, $chat, User $user) {

        $chat_check = $request->user()->chats()->findOrFail($chat);
        if($chat_check) {
            $user_chat = $chat_check->users()->where('id',$user->id)->exists();
            if(!$user_chat) {
                $chat_check->users()->attach($user->id);
                $data_chat_invite = [
                    'type' => 'chat_invite',
                    'data' => $chat_check,
                ];

                $user->userPush($data_chat_invite);

                $data_new_invited = [
                    'type' => 'new_invited',
                    'data' => $chat_check,
                ];

                $users_push = $chat_check->users()->where('id', '<>', $request->user()->id)->where('id', '<>', $user->id)->get();
                foreach ($users_push as $user){
                    $user->userPush($data_new_invited);
                }

                return response(['status'=>'ok']);

            } else {

                return response(['status'=>'User is already in chat'], 422);

            }
        }
    }

    public function allChats(Request $request) {

        $chat = $request->user()->chats()->paginate(10);
        return $chat;

    }

    public function inviteChatList(Request $request, $chat)
    {

        $user_chat = $request->user()->chats()->findOrFail($chat);
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
            return $inviteChatList;

        } else {
            return response(['status' => 'Chat not found'], 404);
        }
    }

}
