<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable;

class HideComment extends TemplateMailable
{
    use Queueable, SerializesModels;
    protected static $templateModelClass = UserMailTemplate::class;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $name; 
    public $reporter_name;
    public $comment;
    public $thread;
    public $thread_link;
    public $comment_link;
    protected $mailTemplateId;

    public function __construct($name, $reporter_name, $comment, $thread, $thread_link, $comment_link, $mailTemplateId)
    {
        $this->name = $name;
        $this->reporter_name = $reporter_name;
        $this->comment = $comment;
        $this->thread = $thread;
        $this->thread_link = $thread_link;
        $this->comment_link = $comment_link;
        $this->mailTemplateId = $mailTemplateId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function getHtmlLayout(): string
    {
        /** 
         * In your application you might want to fetch the layout from an external file or Blade view.
         * 
         * External file: `return file_get_contents(storage_path('mail-layouts/main.html'));`
         * 
         * Blade view: `return view('mailLayouts.main', $data)->render();`
         */
        
        return '{{{ body }}}';
    }

    public function getTemplateId(): int
    {
        return $this->mailTemplateId;
    }
    
}
