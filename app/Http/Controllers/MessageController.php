<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreMessageRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\FriendMessagesRequest;

class MessageController extends Controller
{
    public function sendMessage(StoreMessageRequest $request, User $user) {

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
            $message = new Message;
            $message->user_id = $user->id;
            $message->from_user_id = $request->user()->id;
            $message->from_user_username = $request->user()->username;
            $message->body = $request->body;
            $message->save();

            return response(['message' => $message ], 200);

        } else {

            return response(['message' => 'Only friends can send messages'], 403);

        }
    }

    public function receivedFriendMessages(Request $request, User $user) {

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

            return response(['messages' => $request->user()->messages()->where('from_user_id', '=', $user->id)->get() ], 200);

        } else {
            return response(['message' => 'This user is not your friend'], 403);
        }
    }

    public function sendedFriendMessages(Request $request, User $user) {

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

            return response(['messages' => $user->messages()->where('from_user_id', '=', $request->user()->id)->get() ]);

        } else {
            return response(['message' => 'This user is not your friend'], 403);
        }
    }

    public function allFriendMessages(Request $request, User $user) {

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

            $messages = Message::where(function ($query) use($user, $request) {
                $query->where('user_id', $request->user()->id)
                      ->where('from_user_id', $user->id);
            })
            ->orWhere(function ($query) use($user, $request) {
                $query->where('user_id', $user->id)
                      ->where('from_user_id',$request->user()->id);
            })
                ->orderBy('created_at', 'desc')
                ->paginate(6);

            $mes_coll = $messages->getCollection()->reverse()->values();
            $messages->setCollection($mes_coll);

            return response(["messages" => $messages], 200);

        } else {

            return response(['message' => 'This user is not your friend'], 403);

        }
    }

}
