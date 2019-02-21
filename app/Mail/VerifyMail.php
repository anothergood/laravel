<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $verify_token)
    {
        $this->user = $user;
        $this->token = $verify_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()

    {
        return $this->view('emails.verify_user');
    }
}
