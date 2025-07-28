<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable;

class HideThread extends TemplateMailable
{
    use Queueable, SerializesModels;
    protected static $templateModelClass = UserMailTemplate::class;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $name;
    public $discussion_thread_name;
    protected $mailTemplateId;


    public function __construct( $discussion_thread_name, $name, $mailTemplateId)
    {
        $this->discussion_thread_name = $discussion_thread_name;
        $this->name = $name;
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
