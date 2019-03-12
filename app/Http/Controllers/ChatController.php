<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(\Illuminate\Http\Request $request){

        // \App\Events\ChatMessage::dispatch("Message?");
        event(new \App\Events\ChatMessage($request->input('message')));

    }
}
