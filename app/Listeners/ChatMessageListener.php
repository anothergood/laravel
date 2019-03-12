<?php

namespace App\Listeners;

use App\Events\ChatMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Log;

class ChatMessageListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ChatMessage  $event
     * @return void
     */
    public function handle(ChatMessage $event)
    {
        Log::info("Message: ", [$event->message]);
    }
}
