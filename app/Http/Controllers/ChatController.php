<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request){

        // \App\Events\ChatMessage::dispatch("Message?");
        event(new \App\Events\ChatMessage($request->input('message')));

    }

    public function sendPrivateMessage(Request $request){
        // event(new \App\Events\ChatPrivateMessage($request->input('body')));
        // return $request->all();
        // \Illuminate\Support\Facades\Log::info($request->all());
        // \App\Events\ChatPrivateMessage::dispatch($request->all());

        \Illuminate\Support\Facades\Log::info($request->all());

        event(new \App\Events\ChatPrivateMessage($request->all()));
    }

}
