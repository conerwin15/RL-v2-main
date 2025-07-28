<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FollowerMail extends Mailable
{
    use Queueable, SerializesModels;
    public $follower;
    public $reply;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($follower, $reply)
    {
        // $follower['reply'] = $reply;
        // dd($reply);
        $this->follower = $follower;
        $this->reply = $reply;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "New reply on ". $this->follower->thread->title. " thread on ReallyLesson.";
		return $this->view('Email.follower_mail')->subject("Reallylesson mail")->with(['data' => $this->follower, 'reply' => $this->reply]);
        // return $this->view('view.name');
        // return $this->subject('Mail from reallylesson')
        //             ->view('Email.follower_mail');
    }
}
