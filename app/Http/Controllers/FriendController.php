<?php

namespace App\Http\Controllers;

use App\User;
use App\Chat;
use App\UserUser;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function inviteFriend(Request $request, $user)
    {
        $initiator = $request->user();
        $friend = $initiator->users()->where('user_id', $user);
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
            $initiator->users()->attach($user, ['status' => 'pending']);
            return response(['message' => 'invitation sent']);
        }
    }

    public function approveFriend(Request $request, User $initiator)
    {
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
        $friends = User::whereIn('id', $ids)->paginate(10);

        return $friends;
    }

    public function withoutDialog(Request $request)
    {
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

        $with0ut_dialog = $friends->whereDoesntHave('chats', function ($query) use($request) {
            $query->where('type','dialog')->whereHas('users', function ($query) use($request) {
                $query->where('id', $request->user()->id);
            });
        })->paginate(10);
        return $with0ut_dialog;
    }

}
