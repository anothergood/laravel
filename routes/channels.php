<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('channel.{channel}', function ($user, $channel) {
    // return (int) $user->id === (int) $channel;
    // \Log::info("Channel_check");
    return true;
});

Broadcast::channel('chat.{chat}', function ($user, $chat) {

    $chat_check = $user->chats()->where('id', $chat)->exists();

    if($chat_check) {
        return true;
    }
});
