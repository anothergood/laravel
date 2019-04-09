<?php

namespace App\Http\Controllers;

use App\User;
use App\Chat;
use App\UserUser;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function inviteFriend(Request $request, User $user)
    {
        $user_init = $request->user()->friends()->where('id', $user->id)->first();
        $friend_init = $user->friends()->where('id', $request->user()->id)->first();
        $status = '';
        if ($user_init) {
            $status = $user_init->pivot->status;
            if ($status == 'pending') {
                return response(['message' => 'request already exists'], 422);
            } elseif ($status == 'approved') {
                return response(['message' => 'user is already a friend'], 422);
            } elseif ($status == 'denied') {
                return response(['message' => 'request denied'], 422);
            }
        } elseif ($friend_init){
            $status = $friend_init->pivot->status;
            if ($status == 'pending') {
                $user->friends()->updateExistingPivot($request->user()->id, ['status' => 'approved']);
                $request->user()->friends()->attach($user->id, ['status' => 'approved']);
                return response(['message' => 'invitation approved']);
            } elseif ($status == 'approved') {
                return response(['message' => 'user is already a friend'], 422);
            } elseif ($status == 'denied') {
                return response(['message' => 'request denied'], 422);
            }
        } else {
            $request->user()->friends()->attach($user->id, ['status' => 'pending']);
            return response(['message' => 'invitation sent']);
        }

    }

    public function allFriends(Request $request)
    {

        return $request->user()->friends()->paginate(10);

    }

    public function withoutDialog(Request $request)
    {

        return $request->user()->friends()->whereDoesntHave('chats', function ($query) use($request) {
            $query->where('type','dialog')->whereHas('users', function ($query) use($request) {
                $query->where('id', $request->user()->id);
            });
        })->paginate(10);

    }

}
