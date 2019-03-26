<?php

namespace App\Http\Controllers;

use App\User;
use App\UserUser;
use Illuminate\Http\Request;
use App\Http\Requests\StoreFriendRequest;

class FriendController extends Controller
{
    public function inviteFriend(StoreFriendRequest $request)
    {
        $initiator = $request->user();
        $friend = $initiator->users()->where('user_id', $request->user_id);
        if ($friend->exists()) {
            $status = $friend->first()->pivot->status;
            if ($status == 'pending') {
                return response(['message'=>'request already exists'], 422);
            } elseif ($status == 'approved') {
                return response(['message'=>'user is already a friend'], 422);
            } elseif ($status == 'denied') {
                return response(['message'=>'request denied'], 422);
            }
        } else {
            $initiator->users()->attach($request->user_id, ['status' => 'pending']);
            return response(['message' => 'invitation sent']);
        }
    }

    public function approveFriend(StoreFriendRequest $request)
    {
        $initiator = User::find($request->user_id);
        $friend = $initiator->users()->where('user_id', $request->user()->id);
        if ($friend->exists()) {
            $status = $friend->first()->pivot->status;
            if ($status == 'pending') {
                $initiator->users()->updateExistingPivot($request->user()->id, ['status' => 'approved']);
                return response(['message' => 'invitation approved']);
            } elseif ($status == 'approved') {
                return response(['message'=>'user is already a friend'], 422);
            } elseif ($status == 'denied') {
                return response(['message'=>'request denied'], 422);
            }
        } else {
            return response(['message'=>'there is no request from this user'], 422);
        }
    }

    public function allFriends(Request $request)
    {
        $friends_init = UserUser::where('status', '=', 'approved')
                                ->Where('user_initiator_id', '=', $request->user()->id)
                                ->get();
        $friends_recip = UserUser::where('status', '=', 'approved')
                                 ->where('user_id', '=', $request->user()->id)
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
        $friends = User::whereIn('id', $ids)->get();

        return response(['friends' => $friends]);
    }
}
